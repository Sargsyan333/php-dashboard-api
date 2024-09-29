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

    public function findById(string $id): ?Coworker
    {
        return $this->findOneBy(['id' => $id]);
    }

    public function getList(int $offset, int $limit): array
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder
            ->select('c.id, c.companyName, c.createdAt, u.email, u.status')
            ->from(Coworker::class, 'c')
            ->join('c.user', 'u')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
        ;
        $query = $queryBuilder->getQuery();

        $result = $query->getResult();

        return $result;
    }
}