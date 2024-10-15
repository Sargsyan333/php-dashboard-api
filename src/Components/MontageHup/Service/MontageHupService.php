<?php

namespace Riconas\RiconasApi\Components\MontageHup\Service;

use Doctrine\ORM\EntityManager;
use Riconas\RiconasApi\Components\MontageHup\HupStatus;
use Riconas\RiconasApi\Components\MontageHup\HupType;
use Riconas\RiconasApi\Components\MontageHup\MontageHup;
use Riconas\RiconasApi\Components\MontageJob\MontageJob;
use Riconas\RiconasApi\Components\MontageJobCustomer\MontageJobCustomer;

class MontageHupService
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createHup(MontageJob $job, array $hupData): void
    {
        $montageHupCustomer = new MontageJobCustomer();
        $montageHupCustomer
            ->setMontageJobId($job->getId())
            ->setEmail($hupData['customer_email'])
            ->setName($hupData['customer_name'])
            ->setPhoneNumber1($hupData['customer_phone_number1'])
            ->setPhoneNumber2($hupData['customer_phone_number2']);

        $this->entityManager->persist($montageHupCustomer);
        $this->entityManager->flush();

        $montageHup = new MontageHup();
        $montageHup
            ->setJob($job)
            ->setCode($hupData['code'])
            ->setCustomer($montageHupCustomer);

        $this->entityManager->persist($montageHup);
        $this->entityManager->flush();
    }

    public function updateHup(MontageHup $montageHup, array $hupData): void
    {
        $montageHupCustomer = $montageHup->getCustomer();
        $montageHupCustomer
            ->setEmail($hupData['customer_email'])
            ->setName($hupData['customer_name'])
            ->setPhoneNumber1($hupData['customer_phone_number1'])
            ->setPhoneNumber2($hupData['customer_phone_number2']);

        $this->entityManager->persist($montageHupCustomer);
        $this->entityManager->flush();

        $montageHup
            ->setCode($hupData['code'])
            ->setCustomer($montageHupCustomer);

        $this->entityManager->persist($montageHup);
        $this->entityManager->flush();
    }

    public function updateHupDetails(MontageHup $montageHup, array $hupData): void
    {
        $hupType = empty($hupData['hup_type']) || $hupData['hup_type'] === "none" ? null : $hupData['hup_type'];
        $location = empty($hupData['location']) || $hupData['location'] === "none" ? null : $hupData['location'];
        $isHupPreInstalled = $hupData['is_pre_installed'];
        $isHupInstalled = $hupData['is_installed'];

        $hupStatus = $isHupPreInstalled ?
            HupStatus::PREINSTALLED :
            (
                $isHupInstalled ? HupStatus::INSTALLED : HupStatus::NOT_INSTALLED
            );

        $montageHup
            ->setHupType(HupType::from($hupType))
            ->setLocation($location)
            ->setStatus($hupStatus)
        ;

        if ($isHupPreInstalled) {
            $montageHup->setPreInstalledAt(new \DateTimeImmutable('now'));
        } else if (!$isHupInstalled) {
            $montageHup->setPreInstalledAt(null);
        }

        if ($isHupInstalled) {
            $montageHup->setInstalledAt(new \DateTimeImmutable('now'));
        } else {
            $montageHup->setInstalledAt(null);
        }

        $this->entityManager->persist($montageHup);
        $this->entityManager->flush();
    }
}