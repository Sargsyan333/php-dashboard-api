<?php

namespace Riconas\RiconasApi\Components\Nvt\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Riconas\RiconasApi\Components\Nvt\Nvt;
use Riconas\RiconasApi\Exceptions\RecordNotFoundException;

class NvtRepository extends EntityRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, new ClassMetadata(Nvt::class));
    }

    public function findById(string $id): ?Nvt
    {
        return $this->findOneBy(['id' => $id]);
    }

    public function getById(string $id): Nvt
    {
        $nvt = $this->findById($id);
        if (is_null($nvt)) {
            throw new RecordNotFoundException('NVT not found');
        }

        return $nvt;
    }

    public function findByCodeAndSubprojectId(string $code, string $subprojectId): ?Nvt
    {
        return $this->findOneBy(['code' => $code, 'subprojectId' => $subprojectId]);
    }

    public function getList(?string $projectId, ?string $subprojectId, int $offset, int $limit): array
    {
        $fields = [
            'n.id',
            'n.code',
            'n.createdAt',
            'cw.id as coworkerId',
            'cw.companyName as coworkerName',
            'sp.id as subprojectId',
            'sp.code as subprojectCode',
            'sp.projectId as projectId',
            'p.name as projectName',
        ];

        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder
            ->select(implode(', ', $fields))
            ->from(Nvt::class, 'n')
            ->leftJoin('n.coworker', 'cw')
            ->join('n.subproject', 'sp')
            ->join('sp.project', 'p')
        ;

        if (false === is_null($subprojectId)) {
            $queryBuilder
                ->where('n.subprojectId = :subprojectId')
                ->setParameter('subprojectId', $subprojectId);
        } elseif (false === is_null($projectId)) {
            $queryBuilder
                ->where('sp.projectId = :projectId')
                ->setParameter('projectId', $projectId);
        }

        $queryBuilder->setFirstResult($offset)->setMaxResults($limit);

        $query = $queryBuilder->getQuery();

        $result = $query->getResult();

        return $result;
    }

    public function getTotalCount(?string $projectId, ?string $subprojectId): int
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder
            ->select('COUNT(n.id)')
            ->from(Nvt::class, 'n')
            ->join('n.subproject', 'sp')
            ->join('sp.project', 'p')
        ;

        if (false === is_null($subprojectId)) {
            $queryBuilder
                ->where('n.subprojectId = :subprojectId')
                ->setParameter('subprojectId', $subprojectId);
        } elseif (false === is_null($projectId)) {
            $queryBuilder
                ->where('sp.projectId = :projectId')
                ->setParameter('projectId', $projectId);
        }

        $query = $queryBuilder->getQuery();

        $result = $query->getSingleScalarResult();

        return $result;
    }

    public function searchBySubprojectId(string $searchedSubprojectId): array
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder
            ->select('n.id, n.code')
            ->from(Nvt::class, 'n')
            ->where('n.subprojectId = :subprojectId')
            ->setParameter('subprojectId', $searchedSubprojectId)
        ;
        $query = $queryBuilder->getQuery();

        $result = $query->getResult();

        return $result;
    }
}