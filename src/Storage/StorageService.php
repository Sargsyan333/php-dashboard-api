<?php

namespace Riconas\RiconasApi\Storage;

use Riconas\RiconasApi\Utility\StringUtility;

class StorageService
{
    private const TMP_FILE_UPLOAD_BASE_PATH = 'data/uploads/tmp';

    public function getTmpFileUploadAbsolutePath(string $uploadedFileName): string
    {
        $uploadedFileExt = pathinfo($uploadedFileName, PATHINFO_EXTENSION);

        $randomString = StringUtility::generateRandomString(20);
        $targetFileName = "t{$randomString}.{$uploadedFileExt}";

        return $this->getAbsoluteBasePath() . '/' . $targetFileName;
    }

    public function getTmpFileAbsolutePath(string $targetFileName): string
    {
        return $this->getAbsoluteBasePath() . '/' . $targetFileName;
    }

    private function getAbsoluteBasePath(): string
    {
        return $_ENV['ROOT_FILESYSTEM_PATH'] . '/' . self::TMP_FILE_UPLOAD_BASE_PATH;
    }
}