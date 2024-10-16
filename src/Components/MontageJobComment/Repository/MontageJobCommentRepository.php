<?php

namespace Riconas\RiconasApi\Components\MontageJobComment\Repository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Riconas\RiconasApi\Components\MontageJobComment\MontageJobComment;

class MontageJobCommentRepository extends EntityRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, new ClassMetadata(MontageJobComment::class));
    }

    public function findByJobIdAndCoworkerId(string $jobId, string $coworkerId): ?MontageJobComment
    {
        return $this->findOneBy(['jobId' => $jobId, 'coworkerId' => $coworkerId]);
    }
}
