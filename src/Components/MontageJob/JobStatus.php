<?php

namespace Riconas\RiconasApi\Components\MontageJob;

enum JobStatus: string {
    case STATUS_PUBLISHED = 'PUBLISHED';
    case STATUS_DRAFT = 'DRAFT';
}