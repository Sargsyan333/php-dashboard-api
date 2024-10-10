<?php

namespace Riconas\RiconasApi\Components\Nvt;

use DateTimeImmutable;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use Riconas\RiconasApi\Components\Coworker\Coworker;
use Riconas\RiconasApi\Components\Subproject\Subproject;

#[Entity, Table(name: 'nvts')]
class Nvt
{
    #[Id, Column(type: 'string'), GeneratedValue(strategy: 'AUTO')]
    private string $id;

    #[Column(name: 'subproject_id', type: 'string', nullable: false)]
    private string $subprojectId;

    #[ManyToOne(targetEntity: Subproject::class)]
    #[JoinColumn(name: 'subproject_id', referencedColumnName: 'id')]
    private Subproject $subproject;

    #[Column(name: 'code', type: 'string', length: 7, nullable: false)]
    private string $code;

    #[ManyToOne(targetEntity: Coworker::class)]
    #[JoinColumn(name: 'coworker_id', referencedColumnName: 'id')]
    private ?Coworker $coworker;

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

    public function getSubprojectId(): string
    {
        return $this->subprojectId;
    }

    public function getSubproject(): Subproject
    {
        return $this->subproject;
    }

    public function setSubproject(Subproject $subproject): self
    {
        $this->subproject = $subproject;

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

    public function getCoworker(): ?Coworker
    {
        return $this->coworker;
    }

    public function setCoworker(?Coworker $coworker): self
    {
        $this->coworker = $coworker;

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}