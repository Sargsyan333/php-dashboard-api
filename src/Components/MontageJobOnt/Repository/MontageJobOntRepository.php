<?php

namespace Riconas\RiconasApi\Components\MontageJobOnt\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Riconas\RiconasApi\Components\MontageJobOnt\MontageJobOnt;

class MontageJobOntRepository extends EntityRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, new ClassMetadata(MontageJobOnt::class));
    }
}