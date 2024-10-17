<?php

namespace Riconas\RiconasApi\Components\MontageJobPhoto\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Riconas\RiconasApi\Components\MontageJobPhoto\MontageJobPhoto;
use Riconas\RiconasApi\Exceptions\RecordNotFoundException;

class MontageJobPhotoRepository extends EntityRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, new ClassMetadata(MontageJobPhoto::class));
    }

    public function getCountByJobId(string $jobId): int
    {
        return $this->count(['jobId' => $jobId]);
    }

    public function getById(string $id): MontageJobPhoto
    {
        $jobPhoto = $this->findOneBy(['id' => $id]);
        if (is_null($jobPhoto)) {
            throw new RecordNotFoundException('Record not found');
        }

        return $jobPhoto;
    }
}