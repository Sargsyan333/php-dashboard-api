<?php

namespace Riconas\RiconasApi\Components\MontageJob\Service;

use Riconas\RiconasApi\Storage\StorageService;
use Riconas\RiconasApi\Utility\StringUtility;

class MontageJobStorageService extends StorageService
{
    private const STORAGE_BASE_PATH = 'data/uploads/montage_jobs';
    private const PHOTOS_BASE_PATH = 'montage_jobs/photos';

    public function storeTmpHbFile(string $tmpUploadedFileName): ?string
    {
        $hbFilePath = $this->getTmpFileAbsolutePath($tmpUploadedFileName);
        $hbFileNewName = StringUtility::generateRandomString() . '_hb_file.pdf';

        $targetFilePath = $_ENV['ROOT_FILESYSTEM_PATH'] . '/' . self::STORAGE_BASE_PATH . '/' . $hbFileNewName;

        if (rename($hbFilePath, $targetFilePath)) {
            return $hbFileNewName;
        }

        return null;
    }

    public function deleteHbFile(string $hbFileName): bool
    {
        $fileAbsolutePath = $_ENV['ROOT_FILESYSTEM_PATH'] . '/' . self::STORAGE_BASE_PATH . '/' . $hbFileName;

        if (unlink($fileAbsolutePath)) {
            return true;
        }

        return false;
    }
}