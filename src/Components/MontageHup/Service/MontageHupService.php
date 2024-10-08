<?php

namespace Riconas\RiconasApi\Components\MontageHup\Service;

use Doctrine\ORM\EntityManager;
use Riconas\RiconasApi\Components\MontageHup\MontageHup;
use Riconas\RiconasApi\Components\MontageJobCustomer\MontageJobCustomer;

class MontageHupService
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createHup(string $jobId, array $hupData): void
    {
        $montageHupCustomer = new MontageJobCustomer();
        $montageHupCustomer
            ->setMontageJobId($jobId)
            ->setEmail($hupData['customer_email'])
            ->setName($hupData['customer_name'])
            ->setPhoneNumber1($hupData['customer_phone_number1'])
            ->setPhoneNumber2($hupData['customer_phone_number2']);

        $this->entityManager->persist($montageHupCustomer);
        $this->entityManager->flush();

        $montageHup = new MontageHup();
        $montageHup
            ->setJobId($jobId)
            ->setCode($hupData['code'])
            ->setCustomerId($montageHupCustomer->getId());

        $this->entityManager->persist($montageHup);
        $this->entityManager->flush();
    }
}