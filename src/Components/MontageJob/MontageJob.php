<?php

namespace Riconas\RiconasApi\Components\MontageJob;

use DateTimeImmutable;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity, Table(name: 'montage_jobs')]
class MontageJob
{
    #[Id, Column(type: 'string'), GeneratedValue(strategy: 'AUTO')]
    private string $id;

    #[Column(name: 'nvt_id', type: 'string', nullable: false)]
    private string $nvtId;

    #[Column(name: 'address_line1', type: 'string', nullable: false)]
    private string $addressLine1;

    #[Column(name: 'address_line2', type: 'string', nullable: false)]
    private string $addressLine2;

    #[Column(name: 'building_type', type: 'string', nullable: false, enumType: BuildingType::class)]
    private BuildingType $buildingType;

    #[Column(name: 'coworker_id', type: 'string', nullable: true)]
    private ?string $coworkerId;

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

    public function getNvtId(): string
    {
        return $this->nvtId;
    }

    public function setNvtId(string $nvtId): self
    {
        $this->nvtId = $nvtId;

        return $this;
    }

    public function getAddressLine1(): string
    {
        return $this->addressLine1;
    }

    public function setAddressLine1(string $addressLine1): self
    {
        $this->addressLine1 = $addressLine1;

        return $this;
    }

    public function getAddressLine2(): string
    {
        return $this->addressLine2;
    }

    public function setAddressLine2(string $addressLine2): self
    {
        $this->addressLine2 = $addressLine2;

        return $this;
    }

    public function getBuildingType(): BuildingType
    {
        return $this->buildingType;
    }

    public function setBuildingType(BuildingType $buildingType): self
    {
        $this->buildingType = $buildingType;

        return $this;
    }

    public function getCoworkerId(): ?string
    {
        return $this->coworkerId;
    }

    public function setCoworkerId(?string $coworkerId): self
    {
        $this->coworkerId = $coworkerId;

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}