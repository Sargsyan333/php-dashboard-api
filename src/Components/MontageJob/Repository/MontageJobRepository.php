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

    public function getList(int $offset, int $limit): array
    {
        $fields = [
            'm.id',
            'm.nvtId',
            'm.addressLine1',
            'm.addressLine2',
            'm.hbFilePath',
            'm.createdAt',
            'n.code as nvtCode',
            's.id as subprojectId',
            's.code as subprojectCode',
            'p.id as projectId',
            'p.name as projectName',
            'c.id as coworkerId',
            'c.companyName as coworkerName',
            'cbl.cabelTypePlanned as cabelType',
            'cbl.cabelCodePlanned as cabelCode',
            'cbl.tubeColorPlanned as tubeColor',
            'h.code as hupCode',
            'h.id as hupId',
            'hc.id as hcId',
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
        $query = $queryBuilder->getQuery();

        $result = $query->getResult();

        return $result;
    }
}