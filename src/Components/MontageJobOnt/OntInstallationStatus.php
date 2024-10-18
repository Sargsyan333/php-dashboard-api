<?php

namespace Riconas\RiconasApi\Components\MontageJobOnt;

enum OntInstallationStatus: string {
    case STATUS_NOT_INSTALLED = 'NOT_INSTALLED';
    case STATUS_PREINSTALLED = 'PREINSTALLED';
    case STATUS_INSTALLED = 'INSTALLED';
}