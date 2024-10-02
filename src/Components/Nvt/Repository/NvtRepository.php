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

    public function getList(?string $subprojectId, int $offset, int $limit): array
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder
            ->select(
                'b.id, n.code, n.createdAt, cw.id as coworkerId, cw.companyName as coworkerName, sp.id as subprojectId, sp.code as subprojectCode'
            )
            ->from(Nvt::class, 'n')
            ->leftJoin('n.coworker', 'cw')
            ->leftJoin('n.subproject', 'sp')
        ;

        if (false === is_null($subprojectId)) {
            $queryBuilder
                ->where('s.subprojectId = :subprojectId')
                ->setParameter('subprojectId', $subprojectId);
        }

        $queryBuilder->setFirstResult($offset)->setMaxResults($limit);

        $query = $queryBuilder->getQuery();

        $result = $query->getResult();

        return $result;
    }
}