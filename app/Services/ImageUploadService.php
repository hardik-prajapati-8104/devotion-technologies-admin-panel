<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageUploadService
{
    /**
     * Allowed MIME types across the whole admin panel.
     */
    private const ALLOWED_MIMES = ['image/jpeg', 'image/png', 'image/webp'];

    private const MAX_KILOBYTES = 4096; // 4MB

    /**
     * Store an uploaded image under storage/app/public/{directory}
     * and return the relative path to save on the model.
     *
     * @throws \InvalidArgumentException
     */
    public function upload(UploadedFile $file, string $directory): string
    {
        if (! in_array($file->getMimeType(), self::ALLOWED_MIMES, true)) {
            throw new \InvalidArgumentException('Only JPG, PNG, and WEBP images are allowed.');
        }

        if ($file->getSize() > self::MAX_KILOBYTES * 1024) {
            throw new \InvalidArgumentException('Image must be smaller than 4MB.');
        }

        // Defense in depth: a MIME type reported by the browser/client can be
        // spoofed (e.g. a renamed .php file sent with a fake image/jpeg
        // header). getimagesize() actually parses the file's binary header,
        // so this catches files that merely *claim* to be images.
        if (@getimagesize($file->getRealPath()) === false) {
            throw new \InvalidArgumentException('The uploaded file is not a valid image.');
        }

        // Never trust the original filename — generate our own.
        $filename = Str::uuid().'.'.$file->getClientOriginalExtension();

        return $file->storeAs($directory, $filename, 'public');
    }

    /**
     * Replace an existing image: upload the new one, then delete the old.
     */
    public function replace(UploadedFile $file, string $directory, ?string $oldPath = null): string
    {
        $newPath = $this->upload($file, $directory);

        if ($oldPath) {
            $this->delete($oldPath);
        }

        return $newPath;
    }

    public function delete(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
