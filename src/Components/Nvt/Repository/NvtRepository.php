<?php

namespace Riconas\RiconasApi\Components\Nvt\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Riconas\RiconasApi\Components\Nvt\Nvt;

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
}