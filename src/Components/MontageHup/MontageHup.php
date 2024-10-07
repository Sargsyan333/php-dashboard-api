<?php

namespace Riconas\RiconasApi\Components\MontageHup;

use DateTimeImmutable;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

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

    #[Column(name: 'status', type: 'string', nullable: false)]
    private HupStatus $status;

    #[Column(name: 'opened_hup_photo_path', type: 'string', nullable: true)]
    private ?string $openedHupPhotoPath;

    #[Column(name: 'closed_hup_photo_path', type: 'string', nullable: true)]
    private ?string $closedHupPhotoPath;

    #[Column(name: 'created_at', type: 'datetimetz_immutable', nullable: false)]
    private DateTimeImmutable $createdAt;

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

    public function setHupType(HupType $hupType): self
    {
        $this->hupType = $hupType;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
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

    public function getOpenedHupPhotoPath(): ?string
    {
        return $this->openedHupPhotoPath;
    }

    public function setOpenedHupPhotoPath(?string $openedHupPhotoPath): self
    {
        $this->openedHupPhotoPath = $openedHupPhotoPath;

        return $this;
    }

    public function getClosedHupPhotoPath(): ?string
    {
        return $this->closedHupPhotoPath;
    }

    public function setClosedHupPhotoPath(?string $closedHupPhotoPath): self
    {
        $this->closedHupPhotoPath = $closedHupPhotoPath;
        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}