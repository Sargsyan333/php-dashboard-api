<?php

namespace Riconas\RiconasApi\Components\MontageOntPhoto\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Riconas\RiconasApi\Components\MontageOntPhoto\MontageOntPhoto;

class MontageOntPhotoRepository extends EntityRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, new ClassMetadata(MontageOntPhoto::class));
    }
}