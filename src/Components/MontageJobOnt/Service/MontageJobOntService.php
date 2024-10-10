<?php

namespace Riconas\RiconasApi\Components\MontageJobOnt\Service;

use Doctrine\ORM\EntityManager;
use Riconas\RiconasApi\Components\MontageJob\MontageJob;
use Riconas\RiconasApi\Components\MontageJobCustomer\MontageJobCustomer;
use Riconas\RiconasApi\Components\MontageJobOnt\MontageJobOnt;
use Riconas\RiconasApi\Components\MontageJobOnt\OntActivity;
use Riconas\RiconasApi\Components\MontageJobOnt\Repository\MontageJobOntRepository;

class MontageJobOntService
{
    private EntityManager $entityManager;

    private MontageJobOntRepository $montageJobOntRepository;

    public function __construct(EntityManager $entityManager, MontageJobOntRepository $montageJobOntRepository)
    {
        $this->entityManager = $entityManager;
        $this->montageJobOntRepository = $montageJobOntRepository;
    }

    public function createOnts(MontageJob $montageJob, array $ontData): void
    {
        foreach ($ontData as $ontDatum) {
            $this->createNewOnt($montageJob, $ontDatum);
        }

        $this->entityManager->flush();
    }

    public function updateOnts(MontageJob $montageJob, array $ontData): void
    {
        $currentOnts = $montageJob->getOnts()->getIterator();
        $currentOntIds = [];
        /** @var MontageJobCustomer $currentOnt */
        foreach ($currentOnts as $currentOnt) {
            $currentOntIds[] = $currentOnt->getId();
        }

        $newOntIds = array_filter(array_column($ontData, 'id'), function ($id) {
            return !empty($id);
        });

        foreach ($ontData as $ontDatum) {
            $ontType = $ontDatum['type'] === 'none' ? null : $ontDatum['type'];
            $ontActivity = $ontDatum['is_active'] ? OntActivity::STATUS_ACTIVE : OntActivity::STATUS_DISABLED;

            if (empty($ontDatum['id'])) {
                // Create new ONT
                $this->createNewOnt($montageJob, $ontDatum);
                continue;
            }

            // Update existing ONT
            $montageJobOnt = $this->montageJobOntRepository->getById($ontDatum['id']);

            // ONT customer update
            {
                if ($montageJobOnt->getCustomer()) {
                    $montageOntCustomer = $montageJobOnt->getCustomer();
                    // Delete customer, if the main fields are set empty by user
                    if ($this->areCustomerMainFieldsEmpty($ontDatum)) {
                        $this->entityManager->remove($montageOntCustomer);
                    } else {
                        $montageOntCustomer
                            ->setName($ontDatum['customer_name'])
                            ->setEmail($ontDatum['customer_email'])
                            ->setPhoneNumber1($ontDatum['customer_phone_number1'])
                            ->setPhoneNumber2($ontDatum['customer_phone_number2']);
                    }
                } else {
                    if (!$this->areCustomerMainFieldsEmpty($ontDatum)) {
                        $montageOntCustomer = new MontageJobCustomer();
                        $montageOntCustomer
                            ->setMontageJobId($montageJob->getId())
                            ->setName($ontDatum['customer_name'])
                            ->setEmail($ontDatum['customer_email'])
                            ->setPhoneNumber1($ontDatum['customer_phone_number1'])
                            ->setPhoneNumber2($ontDatum['customer_phone_number2']);

                        $this->entityManager->persist($montageOntCustomer);

                        $montageJobOnt->setCustomer($montageOntCustomer);
                    }
                }
                $this->entityManager->flush();
            }

            $montageJobOnt
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

        $toDeleteOntIds = array_diff($currentOntIds, $newOntIds);
        foreach ($toDeleteOntIds as $toDeleteOntId) {
            $toDeleteOnt = $this->montageJobOntRepository->getById($toDeleteOntId);
            $this->entityManager->remove($toDeleteOnt);
        }

        $this->entityManager->flush();
    }

    private function createNewOnt(MontageJob $montageJob, array $ontData): void
    {
        $montageOntCustomer = null;
        if (!$this->areCustomerMainFieldsEmpty($ontData)) {
            $montageOntCustomer = new MontageJobCustomer();
            $montageOntCustomer
                ->setMontageJobId($montageJob->getId())
                ->setName($ontData['customer_name'])
                ->setEmail($ontData['customer_email'])
                ->setPhoneNumber1($ontData['customer_phone_number1'])
                ->setPhoneNumber2($ontData['customer_phone_number2'])
            ;

            $this->entityManager->persist($montageOntCustomer);
            $this->entityManager->flush();
        }

        $ontType = $ontData['type'] === 'none' ? null : $ontData['type'];
        $ontActivity = $ontData['is_active'] ? OntActivity::STATUS_ACTIVE : OntActivity::STATUS_DISABLED;

        $montageJobOnt = new MontageJobOnt();
        $montageJobOnt
            ->setJob($montageJob)
            ->setCustomer($montageOntCustomer)
            ->setCode($ontData['code'])
            ->setType($ontType)
            ->setSplitterCode($ontData['splitter_code'])
            ->setSplitterFiber($ontData['splitter_fiber'])
            ->setOdfCodePlanned($ontData['odf_code'])
            ->setOdfPosPlanned($ontData['odf_pos'])
            ->setActivity($ontActivity)
        ;

        $this->entityManager->persist($montageJobOnt);
    }

    private function areCustomerMainFieldsEmpty(array $data): bool
    {
        return empty($data['customer_name']) || empty($data['customer_email']) || empty($data['customer_phone_number1']);
    }
}