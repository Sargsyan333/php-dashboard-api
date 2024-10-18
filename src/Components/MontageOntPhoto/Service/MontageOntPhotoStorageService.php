<?php

namespace Riconas\RiconasApi\Components\MontageOntPhoto\Service;

use Riconas\RiconasApi\Utility\StringUtility;

class MontageOntPhotoStorageService
{
    private const BASE_RELATIVE_PATH = 'montage_jobs/ont_photos';
    private const BASE_ABSOLUTE_PATH = "data/uploads/" . self::BASE_RELATIVE_PATH;

    public function getPhotoUrl(string $photoFileName): string
    {
        $photoUrlParts = [
            $_ENV['UPLOADS_DOMAIN'],
            self::BASE_RELATIVE_PATH,
            $photoFileName,
        ];

        return implode('/', $photoUrlParts);
    }

    public function getPathForUploadedPhotoFile(string $photoFileName): string
    {
        $uploadedFileExt = pathinfo($photoFileName, PATHINFO_EXTENSION);

        $randomString = StringUtility::generateRandomString(20);
        $targetFileName = "{$randomString}_photo.{$uploadedFileExt}";

        return $this->getPhotoAbsolutePath($targetFileName);
    }

    public function deletePhotoFile(string $photoFileName): bool
    {
        $fileAbsolutePath = $this->getPhotoAbsolutePath($photoFileName);

        if (unlink($fileAbsolutePath)) {
            return true;
        }

        return false;
    }

    private function getPhotoAbsolutePath(string $photoFileName): string
    {
        return $_ENV['ROOT_FILESYSTEM_PATH'] . '/' . self::BASE_ABSOLUTE_PATH . '/' . $photoFileName;
    }
}