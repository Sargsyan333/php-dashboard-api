<?php

namespace Riconas\RiconasApi\Components\MontageJobOnt\Service;

use Doctrine\ORM\EntityManager;
use Riconas\RiconasApi\Components\MontageJobCustomer\MontageJobCustomer;
use Riconas\RiconasApi\Components\MontageJobOnt\MontageJobOnt;
use Riconas\RiconasApi\Components\MontageJobOnt\OntActivity;

class MontageJobOntService
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createOnts(string $montageJobId, array $ontData): void
    {
        foreach ($ontData as $ontDatum) {
            $montageOntCustomerId = null;
            if (!empty($ontDatum['customer_name'] && $ontDatum['customer_email'] && $ontDatum['customer_phone_number1'])) {
                $montageOntCustomer = new MontageJobCustomer();
                $montageOntCustomer
                    ->setMontageJobId($montageJobId)
                    ->setName($ontDatum['customer_name'])
                    ->setEmail($ontDatum['customer_email'])
                    ->setPhoneNumber1($ontDatum['customer_phone_number1'])
                    ->setPhoneNumber2($ontDatum['customer_phone_number2'])
                ;

                $this->entityManager->persist($montageOntCustomer);
                $this->entityManager->flush();

                $montageOntCustomerId = $montageOntCustomer->getId();
            }

            $ontType = $ontDatum['type'] === 'none' ? null : $ontDatum['type'];
            $ontActivity = $ontDatum['is_active'] ? OntActivity::STATUS_ACTIVE : OntActivity::STATUS_DISABLED;

            $montageJobOnt = new MontageJobOnt();
            $montageJobOnt
                ->setJobId($montageJobId)
                ->setCustomerId($montageOntCustomerId)
                ->setCode($ontDatum['code'])
                ->setType($ontType)
                ->setSplitterCode($ontDatum['splitter_code'])
                ->setSplitterFiber($ontDatum['splitter_fiber'])
                ->setOdfCodePlanned($ontDatum['odf_code'])
                ->setOdfPosPlanned($ontDatum['odf_pos'])
                ->setActivity($ontActivity)
            ;

            $this->entityManager->persist($montageJobOnt);
        }

        $this->entityManager->flush();
    }
}