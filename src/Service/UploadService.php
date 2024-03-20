<?php

namespace App\Service;

use App\Exception\UploadFileException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

class UploadService
{
    private const LINK = '/upload/book/%d/%s';
    public function __construct(private Filesystem $filesystem,  private string $uploadDir)
    {
    }

    public function deleteBookFile(int $id, string $name): void
    {
        $imagePath = $this->getPatch($id) . DIRECTORY_SEPARATOR . $name;
        $this->filesystem->remove($imagePath);
    }

    public function uploadBookFile($id, UploadedFile $file): string
    {
        $extention = $file->guessExtension();

        if ($extention === null) {
            throw new UploadFileException();
        }

        $uniqueName = Uuid::v4()->toRfc4122() . '.' . $extention;

        $file->move($this->getPatch($id), $uniqueName);

        return sprintf(self::LINK, $id, $uniqueName);
    }

    private function getPatch(int $id)
    {
        return $this->uploadDir . DIRECTORY_SEPARATOR . 'book' . DIRECTORY_SEPARATOR . $id;
    }
}