<?php

namespace App\Services\Uploader;

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class LocalUploader implements UploaderInterface
{
    /** @var string */
    private $targetDirectory;

    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    public function upload(UploadedFile $file)
    {
        $originalFilename = $this->clear(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $fileName = $originalFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move(
                $this->getTargetDirectory('videos_directory'),
                $fileName
            );
        } catch (FileException $e) {
        }

        return [$fileName, $originalFilename];
    }

    public function delete(string $path): bool
    {
        $fileSystem = new Filesystem();

        try {
            $fileSystem->remove('.'.$path);
        } catch (IOExceptionInterface $exception) {
            echo 'An error occurred while deleting your file at '.$exception->getPath();
        }

        return true;
    }

    private function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }

    public function clear(string $fileName): string
    {
        return preg_replace('/[^A-Za-z0-9-]+/', '', $fileName);
    }
}
