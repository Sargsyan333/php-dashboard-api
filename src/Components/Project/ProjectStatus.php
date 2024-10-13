<?php

namespace Riconas\RiconasApi\Components\Project;

enum ProjectStatus: string {
    case STATUS_PUBLISHED = 'PUBLISHED';
    case STATUS_DRAFT = 'DRAFT';
}