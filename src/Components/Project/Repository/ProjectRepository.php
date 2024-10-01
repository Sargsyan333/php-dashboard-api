<?php

namespace Riconas\RiconasApi\Components\Project\Repository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Riconas\RiconasApi\Components\Project\Project;

class ProjectRepository extends EntityRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, new ClassMetadata(Project::class));
    }

    public function findById(string $id): ?Project
    {
        return $this->findOneBy(['id' => $id]);
    }

    public function findByCode(string $code): ?Project
    {
        return $this->findOneBy(['code' => $code]);
    }

    public function findByName(string $name): ?Project
    {
        return $this->findOneBy(['name' => $name]);
    }

    public function getList(int $offset, int $limit): array
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder
            ->select(
                'p.id, p.name, p.code, p.createdAt, c.id as clientId, c.name as clientName, cw.id as coworkerId, cw.companyName as coworkerName'
            )
            ->from(Project::class, 'p')
            ->join('p.client', 'c')
            ->leftJoin('p.coworker', 'cw')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
        ;
        $query = $queryBuilder->getQuery();

        $result = $query->getResult();

        return $result;
    }

    public function searchByName(string $searchedName, int $limit): array
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder
            ->select('p.id, p.name')
            ->from(Project::class, 'p')
            ->where('p.name LIKE :searchedName')
            ->setParameter('searchedName', '%' . $searchedName . '%')
            ->setMaxResults($limit)
        ;
        $query = $queryBuilder->getQuery();

        $result = $query->getResult();

        return $result;
    }
}