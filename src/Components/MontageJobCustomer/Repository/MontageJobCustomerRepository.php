<?php

namespace Riconas\RiconasApi\Components\MontageJobCustomer\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Riconas\RiconasApi\Components\MontageJobCustomer\MontageJobCustomer;

class MontageJobCustomerRepository extends EntityRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, new ClassMetadata(MontageJobCustomer::class));
    }
}