<?php

namespace Riconas\RiconasApi\Components\MontageJobCustomer;

use DateTimeImmutable;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity, Table(name: 'montage_job_customers')]
class MontageJobCustomer
{
    #[Id, Column(type: 'string'), GeneratedValue(strategy: 'AUTO')]
    private string $id;

    #[Column(name: 'montage_job_id', type: 'string', nullable: false)]
    private string $montageJobId;

    #[Column(name: 'name', type: 'string', nullable: false)]
    private string $name;

    #[Column(name: 'email', type: 'string', nullable: false)]
    private string $email;

    #[Column(name: 'phone_number1', type: 'string', nullable: false)]
    private string $phoneNumber1;

    #[Column(name: 'phone_number2', type: 'string', nullable: true)]
    private ?string $phoneNumber2;

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

    public function getMontageJobId(): string
    {
        return $this->montageJobId;
    }

    public function setMontageJobId(string $montageJobId): MontageJobCustomer
    {
        $this->montageJobId = $montageJobId;

        return $this;
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

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = mb_strtolower($email);

        return $this;
    }

    public function getPhoneNumber1(): string
    {
        return $this->phoneNumber1;
    }

    public function setPhoneNumber1(string $phoneNumber1): self
    {
        $this->phoneNumber1 = $phoneNumber1;

        return $this;
    }

    public function getPhoneNumber2(): ?string
    {
        return $this->phoneNumber2;
    }

    public function setPhoneNumber2(?string $phoneNumber2): self
    {
        $this->phoneNumber2 = $phoneNumber2;
        
        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}