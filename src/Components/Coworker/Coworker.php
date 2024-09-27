<?php

namespace Riconas\RiconasApi\Components\Coworker;

use DateTimeImmutable;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity, Table(name: 'coworkers')]
class Coworker
{
    #[Id, Column(type: 'string'), GeneratedValue(strategy: 'AUTO')]
    private string $id;

    #[Column(name: 'user_id', type: 'string', nullable: false)]
    private string $userId;

    #[Column(name: 'company_name', type: 'string', nullable: false, length: 255)]
    private string $companyName;

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

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function setUserId(string $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getCompanyName(): string
    {
        return $this->companyName;
    }

    public function setCompanyName(string $companyName): self
    {
        $this->companyName = $companyName;

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
