<?php

namespace Riconas\RiconasApi\Components\MontageJobOnt\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Riconas\RiconasApi\Components\MontageJobOnt\MontageJobOnt;
use Riconas\RiconasApi\Exceptions\RecordNotFoundException;

class MontageJobOntRepository extends EntityRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, new ClassMetadata(MontageJobOnt::class));
    }

    public function findById(string $id): ?MontageJobOnt
    {
        return $this->findOneBy(['id' => $id]);
    }

    public function getById(string $id): ?MontageJobOnt
    {
        $ont = $this->findById($id);
        if (is_null($ont)) {
            throw new RecordNotFoundException('ONT with supplied id could not be found.');
        }

        return $ont;
    }

    /**
     * @return MontageJobOnt[]
     */
    public function findAllByJobId(string $jobId): array
    {
        return $this->findBy(['jobId' => $jobId]);
    }
}