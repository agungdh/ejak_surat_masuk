<?php

namespace App\Service;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class FileService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function getFileSize(string $path)
    {
        return Storage::size($path);
    }

    public function setFileSize(&$file)
    {
        $file->size = $this->getFileSize($file->path);
    }

    public function setFileSizes(Collection &$files)
    {
        $files->each(function ($file) {
            $this->setFileSize($file);
        });
    }

    public function getFileUrl(string $path)
    {
        return Storage::disk('s3_public')->url($path);
    }

    public function setFileUrl(&$file)
    {
        $file->url = $this->getFileUrl($file->path);
    }

    public function setFileUrls(Collection &$files)
    {
        $files->each(function ($file) {
            $this->setFileUrl($file);
        });
    }

    public function getMimeTypeByFilename($filename)
    {
        $map = new \League\MimeTypeDetection\GeneratedExtensionToMimeTypeMap;
        $explodedFilename = explode('.', $filename);
        $mimeType = $map->lookupMimeType(end($explodedFilename));

        return $mimeType;
    }
}
