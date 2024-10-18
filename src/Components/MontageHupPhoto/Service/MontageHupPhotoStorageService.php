<?php

namespace Riconas\RiconasApi\Components\MontageHupPhoto\Service;

use Riconas\RiconasApi\Components\MontageJob\Service\MontagePhotoStorageService;

class MontageHupPhotoStorageService extends MontagePhotoStorageService
{
    private const BASE_RELATIVE_PATH = 'montage_jobs/hup_photos';

    public function __construct()
    {
        parent::__construct(self::BASE_RELATIVE_PATH);
    }
}