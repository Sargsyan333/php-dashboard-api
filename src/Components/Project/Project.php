<?php

namespace Riconas\RiconasApi\Components\Project;

use DateTimeImmutable;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use Riconas\RiconasApi\Components\Client\Client;
use Riconas\RiconasApi\Components\Coworker\Coworker;

#[Entity, Table(name: 'projects')]
class Project
{
    #[Id, Column(type: 'string'), GeneratedValue(strategy: 'AUTO')]
    private string $id;

    #[Column(name: 'code', type: 'string', length: 7, nullable: false)]
    private string $code;

    #[Column(name: 'name', type: 'string', length: 255, nullable: false)]
    private string $name;

    #[Column(name: 'client_id', type: 'string', nullable: false)]
    private string $clientId;

    #[Column(name: 'created_at', type: 'datetimetz_immutable', nullable: false)]
    private DateTimeImmutable $createdAt;

    #[ManyToOne(targetEntity: Client::class)]
    #[JoinColumn(name: 'client_id', referencedColumnName: 'id')]
    private Client $client;

    #[ManyToOne(targetEntity: Coworker::class)]
    #[JoinColumn(name: 'coworker_id', referencedColumnName: 'id')]
    private ?Coworker $coworker;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable('now');
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function setClientId(string $clientId): self
    {
        $this->clientId = $clientId;

        return $this;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function setClient(Client $client): Project
    {
        $this->client = $client;

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