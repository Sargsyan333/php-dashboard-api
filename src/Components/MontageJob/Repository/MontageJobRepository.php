<?php

namespace Riconas\RiconasApi\Components\MontageJob\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Riconas\RiconasApi\Components\MontageJob\MontageJob;

class MontageJobRepository extends EntityRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, new ClassMetadata(MontageJob::class));
    }

    public function findById(string $id): ?MontageJob
    {
        return $this->findOneBy(['id' => $id]);
    }

    public function getList(?string $projectId, int $offset, int $limit): array
    {
        $fields = [
            'm.id',
            'm.addressLine1',
            'm.addressLine2',
            'm.buildingType',
            'm.hbFilePath',
            'm.createdAt',
            'm.status',
            'n.code as nvtCode',
            's.code as subprojectCode',
            'p.name as projectName',
            'c.companyName as coworkerName',
            'cbl.cabelTypePlanned as cabelType',
            'cbl.cabelCodePlanned as cabelCode',
            'cbl.tubeColorPlanned as tubeColor',
            'h.code as hupCode',
            'hc.name as hupCustomerName',
            'hc.email as hupCustomerEmail',
            'hc.phoneNumber1 as hupCustomerPhoneNumber1',
            'hc.phoneNumber2 as hupCustomerPhoneNumber2',
        ];

        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder
            ->select(implode(', ', $fields))
            ->from(MontageJob::class, 'm')
            ->join('m.nvt', 'n')
            ->leftJoin('m.coworker', 'c')
            ->join('n.subproject', 's')
            ->join('s.project', 'p')
            ->leftJoin('m.cabelProperty', 'cbl')
            ->leftJoin('m.hup', 'h')
            ->leftJoin('h.customer', 'hc')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
        ;

        if (false === is_null($projectId)) {
            $queryBuilder
                ->where('s.projectId = :projectId')
                ->setParameter('projectId', $projectId)
            ;
        }

        $query = $queryBuilder->getQuery();

        $result = $query->getResult();

        return $result;
    }

    public function getTotalCount(?string $projectId): int
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder
            ->select('COUNT(m.id)')
            ->from(MontageJob::class, 'm')
            ->join('m.nvt', 'n')
            ->join('n.subproject', 's')
            ->join('s.project', 'p')
        ;

        if (false === is_null($projectId)) {
            $queryBuilder
                ->where('s.projectId = :projectId')
                ->setParameter('projectId', $projectId)
            ;
        }

        $query = $queryBuilder->getQuery();

        $totalCount = $query->getSingleScalarResult();

        return $totalCount;
    }
}