<?php

namespace Riconas\RiconasApi\Components\Coworker\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Riconas\RiconasApi\Components\Coworker\Coworker;

class CoworkerRepository extends EntityRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, new ClassMetadata(Coworker::class));
    }

    public function findByCompanyName(string $companyName): ?Coworker
    {
        return $this->findOneBy(['companyName' => $companyName]);
    }
}