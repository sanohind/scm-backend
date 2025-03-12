<?php

namespace App\Trait;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

trait StoreFile
{
    /**
     * save and store file and image to storage
     *
     * @param  mixed  $file
     * @return bool|string
     */
    public function saveFile($file, string $prefix, string $fileType, string $folder, string $disk = 'local')
    {
        $fileName = uniqid($prefix.'_').'_'.Carbon::now()->format('Ymd_his').'_'.str_replace(' ', '_', $file->getClientOriginalName());

        // Save file
        $filePath = Storage::disk($disk)->putFileAs("$fileType/$folder", $file, $fileName);

        return $filePath;
    }

    /**
     * Delete file and image from storage
     *
     * @return bool
     */
    public function deleteFile(string $filePath, string $disk = 'local')
    {
        if (! Storage::disk($disk)->exists($filePath)) {
            return false;
        } else {
            Storage::disk($disk)->delete($filePath);

            return true;
        }
    }
}

