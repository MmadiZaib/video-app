<?php

namespace App\Services\Uploader;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface UploaderInterface
{
    public function upload(UploadedFile $file);

    public function delete(string $path);
}
