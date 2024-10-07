<?php

namespace Riconas\RiconasApi\Components\MontageHup;

enum HupStatus: string {
    case NOT_INSTALLED = 'NOT_INSTALLED';
    case PREINSTALLED = 'PREINSTALLED';
    case INSTALLED = 'INSTALLED';
}