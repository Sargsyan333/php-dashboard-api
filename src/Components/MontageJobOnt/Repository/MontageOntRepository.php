<?php

namespace Riconas\RiconasApi\Components\MontageJobOnt\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Riconas\RiconasApi\Components\MontageJobOnt\MontageOnt;
use Riconas\RiconasApi\Exceptions\RecordNotFoundException;

class MontageOntRepository extends EntityRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, new ClassMetadata(MontageOnt::class));
    }

    public function findById(string $id): ?MontageOnt
    {
        return $this->findOneBy(['id' => $id]);
    }

    public function getById(string $id): MontageOnt
    {
        $ont = $this->findById($id);
        if (is_null($ont)) {
            throw new RecordNotFoundException('ONT with supplied id could not be found.');
        }

        return $ont;
    }

    /**
     * @return MontageOnt[]
     */
    public function findAllByJobId(string $jobId): array
    {
        return $this->findBy(['jobId' => $jobId]);
    }
}