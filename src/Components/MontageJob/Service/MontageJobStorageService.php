<?php

namespace Riconas\RiconasApi\Components\MontageJob\Service;

use Riconas\RiconasApi\Storage\StorageService;
use Riconas\RiconasApi\Utility\StringUtility;

class MontageJobStorageService extends StorageService
{
    private const STORAGE_BASE_PATH = 'data/uploads/montage_jobs';

    public function storeTmpHbFile(string $tmpUploadedFile): ?string
    {
        $hbFilePath = $this->getTmpFileAbsolutePath($tmpUploadedFile);
        $hbFileNewName = StringUtility::generateRandomString() . '_hb_file.pdf';

        $targetFilePath = $_ENV['ROOT_FILESYSTEM_PATH'] . '/' . self::STORAGE_BASE_PATH . '/' . $hbFileNewName;

        if (rename($hbFilePath, $targetFilePath)) {
            return $hbFileNewName;
        }

        return null;
    }
}