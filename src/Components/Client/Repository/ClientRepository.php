<?php

namespace Riconas\RiconasApi\Components\Client\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Riconas\RiconasApi\Components\Client\Client;
use Riconas\RiconasApi\Components\Coworker\Coworker;

class ClientRepository extends EntityRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, new ClassMetadata(Client::class));
    }

    public function findById(string $id): ?Client
    {
        return $this->findOneBy(['id' => $id]);
    }

    public function findByName(string $name): ?Client
    {
        return $this->findOneBy(['name' => $name]);
    }

    public function getList(int $offset, int $limit): array
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder
            ->select('c.id, c.name, c.createdAt')
            ->from(Client::class, 'c')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
        ;
        $query = $queryBuilder->getQuery();

        $result = $query->getResult();

        return $result;
    }
}