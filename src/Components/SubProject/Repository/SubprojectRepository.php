<?php

namespace Riconas\RiconasApi\Components\SubProject\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Riconas\RiconasApi\Components\SubProject\Subproject;

class SubprojectRepository extends EntityRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, new ClassMetadata(Subproject::class));
    }

    public function findById(string $id): ?Subproject
    {
        return $this->findOneBy(['id' => $id]);
    }

    public function findByCodeAndProjectId(string $code, string $projectId): ?Subproject
    {
        return $this->findOneBy(['code' => $code, 'projectId' => $projectId]);
    }

    public function getListByProjectId(?string $projectId, int $offset, int $limit): array
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder
            ->select(
                's.id, s.code, s.createdAt, cw.id as coworkerId, cw.companyName as coworkerName, p.id as projectId, p.name as projectName'
            )
            ->from(Subproject::class, 's')
            ->leftJoin('s.coworker', 'cw')
            ->leftJoin('s.project', 'p')
        ;

        if (false === is_null($projectId)) {
            $queryBuilder
                ->where('s.projectId = :projectId')
                ->setParameter('projectId', $projectId);
        }

        $queryBuilder->setFirstResult($offset)->setMaxResults($limit);

        $query = $queryBuilder->getQuery();

        $result = $query->getResult();

        return $result;
    }
}
