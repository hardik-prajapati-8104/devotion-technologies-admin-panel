<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * Dumps and restores the current database connection to/from a gzipped
 * .sql file using only PDO — no shell-out to mysqldump/mysql. This is
 * deliberate: on Windows/XAMPP-style setups those binaries frequently
 * aren't on PATH (or are a different MySQL install entirely from the one
 * the app connects to), and detecting/configuring a binary path reliably
 * across environments is exactly the kind of thing that turns into a
 * support thread of its own. Plain PDO always works if the app itself
 * can already talk to the database, which it obviously can.
 *
 * Trade-off worth knowing: this reads/writes via PHP's userland loop
 * rather than a highly optimized C binary, so for very large databases
 * (multi-GB) it will be slower than mysqldump. For a typical admin-panel
 * database this is a non-issue.
 */
class DatabaseDumper
{
    private const CHUNK_SIZE = 500;

    public static function dump(string $absoluteGzPath): void
    {
        $fh = gzopen($absoluteGzPath, 'wb9');

        if ($fh === false) {
            throw new \RuntimeException("Could not open {$absoluteGzPath} for writing.");
        }

        gzwrite($fh, "-- Database dump generated " . now()->toDateTimeString() . "\n");
        gzwrite($fh, "SET FOREIGN_KEY_CHECKS=0;\n\n");

        $pdo = DB::connection()->getPdo();
        $tables = self::tableNames();

        foreach ($tables as $table) {
            self::dumpTable($fh, $pdo, $table);
        }

        gzwrite($fh, "SET FOREIGN_KEY_CHECKS=1;\n");
        gzclose($fh);
    }

    public static function restore(string $absoluteGzPath): void
    {
        $fh = gzopen($absoluteGzPath, 'rb');

        if ($fh === false) {
            throw new \RuntimeException("Could not open {$absoluteGzPath} for reading.");
        }

        $errors = [];

        while (! gzeof($fh)) {
            $line = gzgets($fh);

            if ($line === false) {
                break;
            }

            $line = rtrim($line, "\n");

            if ($line === '' || str_starts_with($line, '--')) {
                continue;
            }

            try {
                DB::unprepared($line);
            } catch (\Throwable $e) {
                // Collect and continue rather than aborting the whole
                // restore over one bad statement — partial data back is
                // far better than none, and the caller surfaces $errors
                // so nothing is silently lost.
                $errors[] = $e->getMessage();
            }
        }

        gzclose($fh);

        if ($errors) {
            throw new \RuntimeException(
                'Restore completed with ' . count($errors) . ' statement error(s). First: ' . $errors[0]
            );
        }
    }

    private static function tableNames(): array
    {
        $rows = DB::select('SHOW TABLES');

        return array_map(fn ($row) => array_values((array) $row)[0], $rows);
    }

    private static function dumpTable($fh, \PDO $pdo, string $table): void
    {
        $createRow = (array) DB::select("SHOW CREATE TABLE `{$table}`")[0];
        $createSql = $createRow['Create Table'] ?? array_values($createRow)[1];

        gzwrite($fh, "DROP TABLE IF EXISTS `{$table}`;\n");
        gzwrite($fh, $createSql . ";\n\n");

        $total = DB::table($table)->count();

        if ($total === 0) {
            return;
        }

        for ($offset = 0; $offset < $total; $offset += self::CHUNK_SIZE) {
            $rows = DB::table($table)->offset($offset)->limit(self::CHUNK_SIZE)->get();

            if ($rows->isEmpty()) {
                break;
            }

            $columns = array_keys((array) $rows->first());
            $columnList = '`' . implode('`,`', $columns) . '`';

            $valueGroups = $rows->map(function ($row) use ($pdo) {
                $values = collect((array) $row)->map(function ($value) use ($pdo) {
                    if (is_null($value)) {
                        return 'NULL';
                    }
                    if (is_bool($value)) {
                        $value = (int) $value;
                    }
                    return $pdo->quote((string) $value);
                });

                return '(' . $values->implode(',') . ')';
            })->implode(',');

            gzwrite($fh, "INSERT INTO `{$table}` ({$columnList}) VALUES {$valueGroups};\n");
        }

        gzwrite($fh, "\n");
    }
}
