<?php

namespace Riconas\RiconasApi\Components\MontageJobPhoto;

use DateTimeImmutable;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use Riconas\RiconasApi\Components\MontageJob\MontageJob;

#[Entity, Table(name: 'montage_job_photos')]
class MontageJobPhoto
{
    #[Id, Column(type: 'string'), GeneratedValue(strategy: 'AUTO')]
    private readonly string $id;

    #[Column(name: 'montage_job_id', type: 'string', nullable: false)]
    private string $jobId;

    #[Column(name: 'photo_path', type: 'string', nullable: false)]
    private string $photoPath;

    #[Column(name: 'created_at', type: 'datetimetz_immutable', nullable: false)]
    private DateTimeImmutable $createdAt;

    #[ManyToOne(targetEntity: MontageJob::class, inversedBy: 'onts')]
    #[JoinColumn(name: 'montage_job_id', referencedColumnName: 'id')]
    private MontageJob $job;

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

    public function getPhotoPath(): string
    {
        return $this->photoPath;
    }

    public function setPhotoPath(string $photoPath): self
    {
        $this->photoPath = $photoPath;

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getJob(): MontageJob
    {
        return $this->job;
    }

    public function setJob(MontageJob $job): self
    {
        $this->job = $job;
        
        return $this;
    }
}
