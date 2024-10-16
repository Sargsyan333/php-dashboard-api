<?php

namespace Riconas\RiconasApi\Components\MontageJobComment;

use DateTimeImmutable;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity, Table(name: 'montage_job_comments')]
class MontageJobComment
{
    #[Id, Column(type: 'string'), GeneratedValue(strategy: 'AUTO')]
    private string $id;

    #[Column(name: 'job_id', type: 'string', nullable: false)]
    private string $jobId;

    #[Column(name: 'coworker_id', type: 'string', nullable: false)]
    private string $coworkerId;

    #[Column(name: 'comment', type: 'string', nullable: false)]
    private string $comment;

    #[Column(name: 'created_at', type: 'datetimetz_immutable', nullable: false)]
    private DateTimeImmutable $createdAt;

    #[Column(name: 'updated_at', type: 'datetimetz_immutable', nullable: true)]
    private ?DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable('now');
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getJobId(): string
    {
        return $this->jobId;
    }

    public function setJobId(string $jobId): self
    {
        $this->jobId = $jobId;

        return $this;
    }

    public function getCoworkerId(): string
    {
        return $this->coworkerId;
    }

    public function setCoworkerId(string $coworkerId): self
    {
        $this->coworkerId = $coworkerId;

        return $this;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}