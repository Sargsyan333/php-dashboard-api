<?php

namespace Riconas\RiconasApi\Components\MontageOntPhoto\Service;

use Riconas\RiconasApi\Components\MontageJob\Service\MontagePhotoStorageService;

class MontageOntPhotoStorageService extends MontagePhotoStorageService
{
    private const BASE_RELATIVE_PATH = 'montage_jobs/ont_photos';

    public function __construct()
    {
        parent::__construct(self::BASE_RELATIVE_PATH);
    }
}