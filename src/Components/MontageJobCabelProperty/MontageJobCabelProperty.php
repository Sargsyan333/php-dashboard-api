<?php

namespace Riconas\RiconasApi\Components\MontageJobCabelProperty;

use DateTimeImmutable;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use Riconas\RiconasApi\Components\MontageJob\MontageJob;

#[Entity, Table(name: 'montage_job_cabel_properties')]
class MontageJobCabelProperty
{
    #[Id, Column(type: 'string'), GeneratedValue(strategy: 'AUTO')]
    private readonly string $id;

    #[Column(name: 'montage_job_id', type: 'string', nullable: false)]
    private string $jobId;

    #[Column(name: 'cabel_type_planned', type: 'string', nullable: false)]
    private string $cabelTypePlanned;

    #[Column(name: 'cabel_type_edited', type: 'string', nullable: true)]
    private ?string $cabelTypeEdited;

    #[Column(name: 'cabel_code_planned', type: 'string', nullable: false)]
    private string $cabelCodePlanned;

    #[Column(name: 'cabel_code_edited', type: 'string', nullable: true)]
    private ?string $cabelCodeEdited;

    #[Column(name: 'tube_color_planned', type: 'string', nullable: false)]
    private string $tubeColorPlanned;

    #[Column(name: 'tube_color_edited', type: 'string', nullable: true)]
    private ?string $tubeColorEdited;

    #[Column(name: 'cable_position', type: 'string', nullable: true)]
    private ?string $cabelPosition;

    #[Column(name: 'cabel_length', type: 'integer', nullable: true)]
    private ?int $cabelLength;

    #[Column(name: 'disability_length', type: 'integer', nullable: true)]
    private ?int $disabilityLength;

    #[Column(name: 'created_at', type: 'datetimetz_immutable', nullable: false)]
    private DateTimeImmutable $createdAt;

    #[ManyToOne(targetEntity: MontageJob::class, inversedBy: 'cabelProperty')]
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

    public function getCabelTypePlanned(): string
    {
        return $this->cabelTypePlanned;
    }

    public function setCabelTypePlanned(string $cabelTypePlanned): self
    {
        $this->cabelTypePlanned = $cabelTypePlanned;

        return $this;
    }

    public function getCabelTypeEdited(): ?string
    {
        return $this->cabelTypeEdited;
    }

    public function setCabelTypeEdited(string $cabelTypeEdited): self
    {
        $this->cabelTypeEdited = $cabelTypeEdited;

        return $this;
    }

    public function getCabelCodePlanned(): string
    {
        return $this->cabelCodePlanned;
    }

    public function setCabelCodePlanned(string $cabelCodePlanned): self
    {
        $this->cabelCodePlanned = $cabelCodePlanned;

        return $this;
    }

    public function getCabelCodeEdited(): ?string
    {
        return $this->cabelCodeEdited;
    }

    public function setCabelCodeEdited(string $cabelCodeEdited): self
    {
        $this->cabelCodeEdited = $cabelCodeEdited;

        return $this;
    }

    public function getTubeColorPlanned(): string
    {
        return $this->tubeColorPlanned;
    }

    public function setTubeColorPlanned(string $tubeColorPlanned): self
    {
        $this->tubeColorPlanned = $tubeColorPlanned;

        return $this;
    }

    public function getTubeColorEdited(): ?string
    {
        return $this->tubeColorEdited;
    }

    public function setTubeColorEdited(string $tubeColorEdited): self
    {
        $this->tubeColorEdited = $tubeColorEdited;

        return $this;
    }

    public function getCabelPosition(): ?string
    {
        return $this->cabelPosition;
    }

    public function setCabelPosition(string $cabelPosition): self
    {
        $this->cabelPosition = $cabelPosition;

        return $this;
    }

    public function getCabelLength(): ?int
    {
        return $this->cabelLength;
    }

    public function setCabelLength(int $cabelLength): self
    {
        $this->cabelLength = $cabelLength;

        return $this;
    }

    public function getDisabilityLength(): ?int
    {
        return $this->disabilityLength;
    }

    public function setDisabilityLength(int $disabilityLength): self
    {
        $this->disabilityLength = $disabilityLength;

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