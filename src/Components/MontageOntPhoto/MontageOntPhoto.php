<?php

namespace Riconas\RiconasApi\Components\MontageOntPhoto;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use DateTimeImmutable;
use Riconas\RiconasApi\Components\MontageJobOnt\MontageOnt;

#[Entity, Table(name: 'montage_ont_photos')]
class MontageOntPhoto
{
    #[Id, Column(type: 'string'), GeneratedValue(strategy: 'AUTO')]
    private string $id;

    #[Column(name: 'montage_ont_id', type: 'string', nullable: false)]
    private string $ontId;

    #[Column(name: 'photo_path', type: 'string', nullable: false)]
    private string $photoPath;

    #[Column(name: 'created_at', type: 'datetimetz_immutable', nullable: false)]
    private DateTimeImmutable $createdAt;

    #[ManyToOne(targetEntity: MontageOnt::class, inversedBy: 'photos')]
    #[JoinColumn(name: 'montage_ont_id', referencedColumnName: 'id')]
    private MontageOnt $ont;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable('now');
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getOntId(): string
    {
        return $this->ontId;
    }

    public function setOntId(string $ontId): self
    {
        $this->ontId = $ontId;

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

    public function setOnt(MontageOnt $ont): self
    {
        $this->ont = $ont;

        return $this;
    }

    public function getOnt(): MontageOnt
    {
        return $this->ont;
    }
}