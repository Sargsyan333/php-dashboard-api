<?php

namespace Riconas\RiconasApi\Components\MontageHupPhoto;

use DateTimeImmutable;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity, Table(name: 'montage_hup_photos')]
class MontageHupPhoto
{
    #[Id, Column(type: 'string'), GeneratedValue(strategy: 'AUTO')]
    private string $id;

    #[Column(name: 'hup_id', type: 'string', nullable: false)]
    private string $hupId;

    #[Column(name: 'photo_path', type: 'string', nullable: false)]
    private string $photoPath;

    #[Column(name: 'state', type: 'string', nullable: false, enumType: HupPhotoState::class)]
    private HupPhotoState $state;

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

    public function getHupId(): string
    {
        return $this->hupId;
    }

    public function setHupId(string $hupId): self
    {
        $this->hupId = $hupId;

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

    public function getState(): HupPhotoState
    {
        return $this->state;
    }

    public function setState(HupPhotoState $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}