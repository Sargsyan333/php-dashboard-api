<?php

namespace Riconas\RiconasApi\Components\Subproject;

use DateTimeImmutable;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use Riconas\RiconasApi\Components\Coworker\Coworker;
use Riconas\RiconasApi\Components\Project\Project;

#[Entity, Table(name: 'subprojects')]
class Subproject
{
    #[Id, Column(type: 'string'), GeneratedValue(strategy: 'AUTO')]
    private string $id;

    #[Column(name: 'project_id', type: 'string', nullable: false)]
    private string $projectId;

    #[ManyToOne(targetEntity: Project::class)]
    #[JoinColumn(name: 'project_id', referencedColumnName: 'id')]
    private Project $project;

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

    public function getProjectId(): string
    {
        return $this->projectId;
    }

    public function setProjectId(string $projectId): self
    {
        $this->projectId = $projectId;

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(Project $project): self
    {
        $this->project = $project;

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