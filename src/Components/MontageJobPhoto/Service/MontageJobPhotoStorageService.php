<?php

namespace Riconas\RiconasApi\Components\MontageJobPhoto\Service;

use Riconas\RiconasApi\Components\MontageJob\Service\MontagePhotoStorageService;

class MontageJobPhotoStorageService extends MontagePhotoStorageService
{
    private const BASE_RELATIVE_PATH = 'montage_jobs/photos';

    public function __construct()
    {
        parent::__construct(self::BASE_RELATIVE_PATH);
    }
}