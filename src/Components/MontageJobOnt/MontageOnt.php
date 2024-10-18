<?php

namespace Riconas\RiconasApi\Components\MontageJobOnt;

use DateTimeImmutable;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;
use Riconas\RiconasApi\Components\MontageJob\MontageJob;
use Riconas\RiconasApi\Components\MontageJobCustomer\MontageJobCustomer;
use Riconas\RiconasApi\Components\MontageOntPhoto\MontageOntPhoto;

#[Entity, Table(name: 'montage_onts')]
class MontageOnt
{
    #[Id, Column(type: 'string'), GeneratedValue(strategy: 'AUTO')]
    private string $id;

    #[Column(name: 'montage_job_id', type: 'string', nullable: false)]
    private string $jobId;

    #[Column(name: 'customer_id', type: 'string', nullable: true)]
    private ?string $customerId;

    #[Column(name: 'code', type: 'string', nullable: false)]
    private string $code;

    #[Column(name: 'splitter_code', type: 'string', nullable: true)]
    private ?string $splitterCode;

    #[Column(name: 'splitter_fiber', type: 'string', nullable: true)]
    private ?string $splitterFiber;

    #[Column(name: 'odf_code_planned', type: 'string', nullable: true)]
    private ?string $odfCodePlanned;

    #[Column(name: 'odf_code_edited', type: 'string', nullable: true)]
    private ?string $odfCodeEdited;

    #[Column(name: 'odf_pos_planned', type: 'string', nullable: true)]
    private ?string $odfPosPlanned;

    #[Column(name: 'odf_pos_edited', type: 'string', nullable: true)]
    private ?string $odfPosEdited;

    #[Column(name: 'activity', type: 'string', nullable: false, enumType: OntActivity::class)]
    private OntActivity $activity;

    #[Column(name: 'installation_status', type: 'string', nullable: false, enumType: OntInstallationStatus::class)]
    private OntInstallationStatus $installationStatus;

    #[Column(name: 'type', type: 'string', nullable: true)]
    private ?string $type;

    #[Column(name: 'created_at', type: 'datetimetz_immutable', nullable: false)]
    private DateTimeImmutable $createdAt;

    #[ManyToOne(targetEntity: MontageJob::class, inversedBy: 'onts')]
    #[JoinColumn(name: 'montage_job_id', referencedColumnName: 'id')]
    private MontageJob $job;

    #[ManyToOne(targetEntity: MontageJobCustomer::class)]
    #[JoinColumn(name: 'customer_id', referencedColumnName: 'id')]
    private ?MontageJobCustomer $customer;

    #[OneToMany(targetEntity: MontageOntPhoto::class, mappedBy: 'ont')]
    private Collection $photos;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable('now');
        $this->activity = OntActivity::STATUS_DISABLED;
        $this->installationStatus = OntInstallationStatus::STATUS_NOT_INSTALLED;
        $this->photos = new ArrayCollection();
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

    public function getJob(): MontageJob
    {
        return $this->job;
    }

    public function setJob(MontageJob $job): self
    {
        $this->job = $job;

        return $this;
    }

    public function getCustomer(): ?MontageJobCustomer
    {
        return $this->customer;
    }

    public function setCustomer(?MontageJobCustomer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getCustomerId(): ?string
    {
        return $this->customerId;
    }

    public function setCustomerId(?string $customerId): self
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

    public function getSplitterCode(): ?string
    {
        return $this->splitterCode;
    }

    public function setSplitterCode(string $splitterCode): self
    {
        $this->splitterCode = $splitterCode;

        return $this;
    }

    public function getSplitterFiber(): ?string
    {
        return $this->splitterFiber;
    }

    public function setSplitterFiber(string $splitterFiber): self
    {
        $this->splitterFiber = $splitterFiber;

        return $this;
    }

    public function getOdfCodePlanned(): ?string
    {
        return $this->odfCodePlanned;
    }

    public function setOdfCodePlanned(string $odfCodePlanned): self
    {
        $this->odfCodePlanned = $odfCodePlanned;

        return $this;
    }

    public function getOdfPosPlanned(): ?string
    {
        return $this->odfPosPlanned;
    }

    public function setOdfPosPlanned(string $odfPosPlanned): self
    {
        $this->odfPosPlanned = $odfPosPlanned;

        return $this;
    }

    public function getOdfCodeEdited(): ?string
    {
        return $this->odfCodeEdited;
    }

    public function setOdfCodeEdited(?string $odfCodeEdited): self
    {
        $this->odfCodeEdited = $odfCodeEdited;

        return $this;
    }

    public function getOdfPosEdited(): ?string
    {
        return $this->odfPosEdited;
    }

    public function setOdfPosEdited(?string $odfPosEdited): self
    {
        $this->odfPosEdited = $odfPosEdited;

        return $this;
    }

    public function getActivity(): OntActivity
    {
        return $this->activity;
    }

    public function setActivity(OntActivity $activity): self
    {
        $this->activity = $activity;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getInstallationStatus(): OntInstallationStatus
    {
        return $this->installationStatus;
    }

    public function setInstallationStatus(OntInstallationStatus $installationStatus): self
    {
        $this->installationStatus = $installationStatus;
        
        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getPhotos(): Collection
    {
        return $this->photos;
    }
}