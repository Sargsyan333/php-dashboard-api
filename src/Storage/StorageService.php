<?php

namespace Riconas\RiconasApi\Storage;

use Riconas\RiconasApi\Utility\StringUtility;

class StorageService
{
    private const TMP_FILE_UPLOAD_ABSOLUTE_BASE_PATH = __DIR__ . '/../../data/uploads/tmp';

    public function getTmpFileUploadAbsolutePath(string $uploadedFileName): string
    {
        $uploadedFileExt = pathinfo($uploadedFileName, PATHINFO_EXTENSION);

        $randomString = StringUtility::generateRandomString(20);
        $targetFileName = "t{$randomString}.{$uploadedFileExt}";

        return self::TMP_FILE_UPLOAD_ABSOLUTE_BASE_PATH . '/' . $targetFileName;
    }

    public function getTmpFileAbsolutePath(string $targetFileName): string
    {
        return self::TMP_FILE_UPLOAD_ABSOLUTE_BASE_PATH . '/' . $targetFileName;
    }
}