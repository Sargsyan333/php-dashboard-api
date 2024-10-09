<?php

namespace Riconas\RiconasApi\Components\MontageJob;

use DateTimeImmutable;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\Table;
use Riconas\RiconasApi\Components\Coworker\Coworker;
use Riconas\RiconasApi\Components\MontageHup\MontageHup;
use Riconas\RiconasApi\Components\MontageJobCabelProperty\MontageJobCabelProperty;
use Riconas\RiconasApi\Components\Nvt\Nvt;

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

    #[Column(name: 'hb_file_path', type: 'string', nullable: true)]
    private ?string $hbFilePath;

    #[Column(name: 'coworker_id', type: 'string', nullable: true)]
    private ?string $coworkerId;

    #[Column(name: 'created_at', type: 'datetimetz_immutable', nullable: false)]
    private DateTimeImmutable $createdAt;

    #[ManyToOne(targetEntity: Nvt::class)]
    #[JoinColumn(name: 'nvt_id', referencedColumnName: 'id')]
    private Nvt $nvt;

    #[ManyToOne(targetEntity: Coworker::class)]
    #[JoinColumn(name: 'coworker_id', referencedColumnName: 'id')]
    private ?Coworker $coworker;

    #[OneToOne(targetEntity: MontageJobCabelProperty::class, mappedBy: 'job')]
    private ?MontageJobCabelProperty $cabelProperty;

    #[OneToOne(targetEntity: MontageHup::class, mappedBy: 'job')]
    private ?MontageHup $hup;

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

    public function getNvt(): Nvt
    {
        return $this->nvt;
    }

    public function setNvt(Nvt $nvt): self
    {
        $this->nvt = $nvt;

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

    public function getHbFilePath(): ?string
    {
        return $this->hbFilePath;
    }

    public function setHbFilePath(?string $hbFilePath): self
    {
        $this->hbFilePath = $hbFilePath;

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


    public function getCoworker(): ?Coworker
    {
        return $this->coworker;
    }

    public function setCoworker(?Coworker $coworker): self
    {
        $this->coworker = $coworker;

        return $this;
    }

    public function getCabelProperty(): ?MontageJobCabelProperty
    {
        return $this->cabelProperty;
    }

    public function getHup(): ?MontageHup
    {
        return $this->hup;
    }
}