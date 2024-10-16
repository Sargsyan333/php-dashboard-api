<?php

namespace Riconas\RiconasApi\Components\MontageJobComment\Service;
use Doctrine\ORM\EntityManager;
use Riconas\RiconasApi\Components\MontageJobComment\MontageJobComment;
use Riconas\RiconasApi\Components\MontageJobComment\Repository\MontageJobCommentRepository;

class MontageJobCommentService
{
    private EntityManager $entityManager;
    private MontageJobCommentRepository $montageJobCommentRepository;

    public function __construct(EntityManager $entityManager, MontageJobCommentRepository $montageJobCommentRepository)
    {
        $this->entityManager = $entityManager;
        $this->montageJobCommentRepository = $montageJobCommentRepository;
    }

    public function saveComment(string $jobId, string $coworkerId, string $commentText): void
    {
        $jobComment = $this->montageJobCommentRepository->findByJobIdAndCoworkerId($jobId, $coworkerId);
        if (is_null($jobComment)) {
            $jobComment = new MontageJobComment();
            $jobComment
                ->setJobId($jobId)
                ->setCoworkerId($coworkerId);
        }

        $jobComment
            ->setCommentText($commentText)
            ->setUpdatedAt(new \DateTimeImmutable('now'));
        ;

        $this->entityManager->persist($jobComment);
        $this->entityManager->flush();
    }
}