<?php

namespace Riconas\RiconasApi\Components\MontageHup;

use DateTimeImmutable;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use Riconas\RiconasApi\Components\MontageJob\MontageJob;
use Riconas\RiconasApi\Components\MontageJobCustomer\MontageJobCustomer;

#[Entity, Table(name: 'montage_hups')]
class MontageHup
{
    #[Id, Column(type: 'string'), GeneratedValue(strategy: 'AUTO')]
    private string $id;

    #[Column(name: 'montage_job_id', type: 'string', nullable: false)]
    private string $jobId;

    #[Column(name: 'customer_id', type: 'string', nullable: false)]
    private string $customerId;

    #[Column(name: 'code', type: 'string', nullable: false)]
    private string $code;

    #[Column(name: 'hup_type', type: 'string', nullable: true, enumType: HupType::class)]
    private ?HupType $hupType;

    #[Column(name: 'location', type: 'string', nullable: true)]
    private ?string $location;

    #[Column(name: 'status', type: 'string', nullable: false, enumType: HupStatus::class)]
    private HupStatus $status;

    #[Column(name: 'created_at', type: 'datetimetz_immutable', nullable: false)]
    private DateTimeImmutable $createdAt;

    #[Column(name: 'preinstalled_at', type: 'datetimetz_immutable', nullable: true)]
    private ?DateTimeImmutable $preInstalledAt;

    #[Column(name: 'installed_at', type: 'datetimetz_immutable', nullable: true)]
    private ?DateTimeImmutable $installedAt;

    #[ManyToOne(targetEntity: MontageJobCustomer::class)]
    #[JoinColumn(name: 'customer_id', referencedColumnName: 'id')]
    private MontageJobCustomer $customer;

    #[ManyToOne(targetEntity: MontageJob::class, inversedBy: 'hup')]
    #[JoinColumn(name: 'montage_job_id', referencedColumnName: 'id')]
    private MontageJob $job;

    public function __construct()
    {
        $this->status = HupStatus::NOT_INSTALLED;
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

    public function getCustomerId(): string
    {
        return $this->customerId;
    }

    public function setCustomerId(string $customerId): self
    {
        $this->customerId = $customerId;

        return $this;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getHupType(): ?HupType
    {
        return $this->hupType;
    }

    public function setHupType(?HupType $hupType): self
    {
        $this->hupType = $hupType;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getStatus(): HupStatus
    {
        return $this->status;
    }

    public function setStatus(HupStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getPreInstalledAt(): ?DateTimeImmutable
    {
        return $this->preInstalledAt;
    }

    public function setPreInstalledAt(?DateTimeImmutable $preInstalledAt): self
    {
        $this->preInstalledAt = $preInstalledAt;

        return $this;
    }

    public function getInstalledAt(): ?DateTimeImmutable
    {
        return $this->installedAt;
    }

    public function setInstalledAt(?DateTimeImmutable $installedAt): self
    {
        $this->installedAt = $installedAt;

        return $this;
    }

    public function getCustomer(): MontageJobCustomer
    {
        return $this->customer;
    }

    public function setCustomer(MontageJobCustomer $customer): self
    {
        $this->customer = $customer;

        return $this;
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