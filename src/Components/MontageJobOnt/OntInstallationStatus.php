<?php

namespace Riconas\RiconasApi\Components\MontageJobOnt;

enum OntInstallationStatus: string {
    case INSTALLATION_STATUS_NOT_INSTALLED = 'NOT_INSTALLED';
    case INSTALLATION_STATUS_PREINSTALLED = 'PREINSTALLED';
    case INSTALLATION_STATUS_INSTALLED = 'INSTALLED';
}