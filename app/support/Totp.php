<?php

namespace App\Support;

/**
 * Minimal RFC 4226 (HOTP) / RFC 6238 (TOTP) implementation, compatible
 * with Google Authenticator, Authy, 1Password, etc.
 *
 * Written from scratch instead of pulling in pragmarx/google2fa (or
 * similar) — this is ~80 lines of standard-library-only PHP (hash_hmac +
 * base32), so it avoids adding a Composer dependency for something this
 * self-contained, and avoids depending on packagist.org being reachable
 * wherever this gets deployed/built.
 */
class Totp
{
    private const PERIOD = 30;   // seconds per code
    private const DIGITS = 6;
    private const BASE32_ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    /**
     * Generates a new random secret (base32-encoded, 160 bits of entropy).
     */
    public static function generateSecret(): string
    {
        return self::base32Encode(random_bytes(20));
    }

    /**
     * The otpauth:// URI to encode as a QR code in an authenticator app.
     */
    public static function provisioningUri(string $secret, string $accountLabel, string $issuer): string
    {
        $label = rawurlencode($issuer . ':' . $accountLabel);
        $params = http_build_query([
            'secret' => $secret,
            'issuer' => $issuer,
            'algorithm' => 'SHA1',
            'digits' => self::DIGITS,
            'period' => self::PERIOD,
        ]);

        return "otpauth://totp/{$label}?{$params}";
    }

    /**
     * The current 6-digit code for $secret, or for an arbitrary Unix
     * timestamp if given (mainly useful for tests).
     */
    public static function currentCode(string $secret, ?int $timestamp = null): string
    {
        $counter = intdiv($timestamp ?? time(), self::PERIOD);

        return self::hotp($secret, $counter);
    }

    /**
     * Verifies a user-submitted code, tolerating clock drift by also
     * checking the previous and next 30-second windows.
     */
    public static function verify(string $secret, string $code, int $window = 1): bool
    {
        $code = preg_replace('/\s+/', '', $code);
        $counter = intdiv(time(), self::PERIOD);

        for ($i = -$window; $i <= $window; $i++) {
            if (hash_equals(self::hotp($secret, $counter + $i), $code)) {
                return true;
            }
        }

        return false;
    }

    private static function hotp(string $base32Secret, int $counter): string
    {
        $key = self::base32Decode($base32Secret);
        $binaryCounter = pack('N*', 0, $counter); // 8-byte big-endian counter

        $hash = hash_hmac('sha1', $binaryCounter, $key, true);

        $offset = ord($hash[19]) & 0x0F;
        $truncated = (
            ((ord($hash[$offset]) & 0x7F) << 24) |
            ((ord($hash[$offset + 1]) & 0xFF) << 16) |
            ((ord($hash[$offset + 2]) & 0xFF) << 8) |
            (ord($hash[$offset + 3]) & 0xFF)
        );

        $code = $truncated % (10 ** self::DIGITS);

        return str_pad((string) $code, self::DIGITS, '0', STR_PAD_LEFT);
    }

    private static function base32Encode(string $binary): string
    {
        $bits = '';
        foreach (str_split($binary) as $char) {
            $bits .= str_pad(decbin(ord($char)), 8, '0', STR_PAD_LEFT);
        }

        $output = '';
        foreach (str_split($bits, 5) as $chunk) {
            $chunk = str_pad($chunk, 5, '0', STR_PAD_RIGHT);
            $output .= self::BASE32_ALPHABET[bindec($chunk)];
        }

        return $output;
    }

    private static function base32Decode(string $base32): string
    {
        $base32 = strtoupper(preg_replace('/[^A-Z2-7]/i', '', $base32));

        $bits = '';
        foreach (str_split($base32) as $char) {
            $pos = strpos(self::BASE32_ALPHABET, $char);
            if ($pos === false) {
                continue;
            }
            $bits .= str_pad(decbin($pos), 5, '0', STR_PAD_LEFT);
        }

        $binary = '';
        foreach (str_split($bits, 8) as $byte) {
            if (strlen($byte) === 8) {
                $binary .= chr(bindec($byte));
            }
        }

        return $binary;
    }

    /**
     * Generates a set of one-time recovery codes (e.g. 8 codes of
     * XXXX-XXXX format) for when the authenticator device is unavailable.
     */
    public static function generateRecoveryCodes(int $count = 8): array
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = strtoupper(substr(bin2hex(random_bytes(5)), 0, 4) . '-' . substr(bin2hex(random_bytes(5)), 4, 4));
        }

        return $codes;
    }
}
