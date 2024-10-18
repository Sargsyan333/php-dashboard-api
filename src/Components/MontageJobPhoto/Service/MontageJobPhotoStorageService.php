<?php

namespace Riconas\RiconasApi\Components\MontageJobPhoto\Service;

use Riconas\RiconasApi\Utility\StringUtility;

class MontageJobPhotoStorageService
{
    private const BASE_ABSOLUTE_PATH = 'data/uploads/montage_jobs/photos';
    private const BASE_RELATIVE_PATH = 'montage_jobs/photos';

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