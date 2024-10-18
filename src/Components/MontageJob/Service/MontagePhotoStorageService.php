<?php

namespace Riconas\RiconasApi\Components\MontageJob\Service;

use Riconas\RiconasApi\Utility\StringUtility;

abstract class MontagePhotoStorageService
{
    protected string $fullPath;
    protected string $relativePath;

    private const BASE_ABSOLUTE_PATH = "data/uploads/";

    protected function __construct(string $relativePath)
    {
        $this->relativePath = $relativePath;
        $this->fullPath = self::BASE_ABSOLUTE_PATH . $relativePath;
    }

    public function getPhotoUrl(string $photoFileName): string
    {
        $photoUrlParts = [
            $_ENV['UPLOADS_DOMAIN'],
            $this->relativePath,
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
        return $_ENV['ROOT_FILESYSTEM_PATH'] . '/' . $this->fullPath . '/' . $photoFileName;
    }
}