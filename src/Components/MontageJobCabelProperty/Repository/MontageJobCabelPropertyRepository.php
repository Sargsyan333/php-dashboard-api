<?php

namespace Riconas\RiconasApi\Components\MontageJobCabelProperty\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Riconas\RiconasApi\Components\MontageJobCabelProperty\MontageJobCabelProperty;

class MontageJobCabelPropertyRepository extends EntityRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, new ClassMetadata(MontageJobCabelProperty::class));
    }
}
