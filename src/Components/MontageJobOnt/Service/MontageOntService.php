<?php

namespace Riconas\RiconasApi\Components\MontageJobOnt\Service;

use Doctrine\ORM\EntityManager;
use Riconas\RiconasApi\Components\MontageJob\MontageJob;
use Riconas\RiconasApi\Components\MontageJobCustomer\MontageJobCustomer;
use Riconas\RiconasApi\Components\MontageJobOnt\MontageOnt;
use Riconas\RiconasApi\Components\MontageJobOnt\OntActivity;
use Riconas\RiconasApi\Components\MontageJobOnt\OntInstallationStatus;
use Riconas\RiconasApi\Components\MontageJobOnt\Repository\MontageOntRepository;

class MontageOntService
{
    private EntityManager $entityManager;

    private MontageOntRepository $montageJobOntRepository;

    public function __construct(EntityManager $entityManager, MontageOntRepository $montageJobOntRepository)
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

    public function updateOntsPlannedData(MontageJob $montageJob, array $ontData): void
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

        $montageJobOnt = new MontageOnt();
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

    public function updateOntCustomizableData(MontageOnt $ont, array $data): void
    {
        $ontType = empty($data['ont_type']) || $data['ont_type'] === "none" ? null : $data['ont_type'];
        $odfCode = empty($data['odf_code']) || $data['odf_code'] === "none" ? null : $data['odf_code'];
        $odfPos = empty($data['odf_pos']) || $data['odf_pos'] === "none" ? null : $data['odf_pos'];
        $isHupPreInstalled = $data['is_pre_installed'];
        $isHupInstalled = $data['is_installed'];

        $ontStatus = $isHupPreInstalled ?
            OntInstallationStatus::STATUS_PREINSTALLED :
            (
            $isHupInstalled ? OntInstallationStatus::STATUS_INSTALLED : OntInstallationStatus::STATUS_NOT_INSTALLED
            );

        $ont
            ->setType($ontType)
            ->setOdfCodeEdited($odfCode)
            ->setOdfPosEdited($odfPos)
            ->setInstallationStatus($ontStatus)
        ;

        $this->entityManager->persist($ont);
        $this->entityManager->flush();
    }

    private function areCustomerMainFieldsEmpty(array $data): bool
    {
        return empty($data['customer_name']) || empty($data['customer_email']) || empty($data['customer_phone_number1']);
    }
}