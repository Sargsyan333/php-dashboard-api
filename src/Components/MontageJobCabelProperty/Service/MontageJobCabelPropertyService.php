<?php

namespace Riconas\RiconasApi\Components\MontageJobCabelProperty\Service;

use Doctrine\ORM\EntityManager;
use Riconas\RiconasApi\Components\MontageJob\MontageJob;
use Riconas\RiconasApi\Components\MontageJobCabelProperty\MontageJobCabelProperty;

class MontageJobCabelPropertyService
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createProperty(MontageJob $job, array $propertyData): void
    {
        $montageCabelProperty = new MontageJobCabelProperty();
        $montageCabelProperty
            ->setJob($job)
            ->setCabelCodePlanned($propertyData['code'])
            ->setCabelTypePlanned($propertyData['type'])
            ->setTubeColorPlanned($propertyData['tube_color'])
        ;

        $this->entityManager->persist($montageCabelProperty);
        $this->entityManager->flush();
    }

    public function updatePropertyPlannedData(MontageJobCabelProperty $montageCabelProperty, array $propertyData): void
    {
        $montageCabelProperty
            ->setCabelCodePlanned($propertyData['code'])
            ->setCabelTypePlanned($propertyData['type'])
            ->setTubeColorPlanned($propertyData['tube_color'])
        ;

        $this->entityManager->persist($montageCabelProperty);
        $this->entityManager->flush();
    }

    public function updatePropertyCustomizableData(
        MontageJobCabelProperty $montageCabelProperty,
        array $propertyData
    ): void {
        $cabelTypeEdited = array_key_exists('cabel_type', $propertyData) ? $propertyData['cabel_type'] : null;
        if (false === is_null($cabelTypeEdited)) {
            if ($cabelTypeEdited === 'none') {
                $montageCabelProperty->setCabelTypeEdited(null);
            } else {
                $montageCabelProperty->setCabelTypeEdited($cabelTypeEdited);
            }
        }

        $cabelCodeEdited = array_key_exists('cabel_code', $propertyData) ? $propertyData['cabel_code'] : null;
        if (false === is_null($cabelCodeEdited)) {
            $montageCabelProperty->setCabelCodeEdited($cabelCodeEdited);
        }

        $tubeColorEdited = array_key_exists('tube_color', $propertyData) ? $propertyData['tube_color'] : null;
        if (false === is_null($tubeColorEdited)) {
            if ($tubeColorEdited === 'none') {
                $montageCabelProperty->setTubeColorEdited(null);
            } else {
                $montageCabelProperty->setTubeColorEdited($tubeColorEdited);
            }
        }

        $cabelLength = array_key_exists('cabel_length', $propertyData) ? $propertyData['cabel_length'] : null;
        if (false === is_null($cabelLength)) {
            $montageCabelProperty->setCabelLength($cabelLength);
        }

        $disabilityLength = array_key_exists('disability_length', $propertyData) ?
            $propertyData['disability_length'] :
            null;
        if (false === is_null($disabilityLength)) {
            $montageCabelProperty->setDisabilityLength($disabilityLength);
        }

        $cabelPosition = array_key_exists('cabel_position', $propertyData) ? $propertyData['cabel_position'] : null;
        if (false === is_null($cabelPosition)) {
            if ($cabelPosition === 'none') {
                $montageCabelProperty->setCabelPosition(null);
            } else {
                $montageCabelProperty->setCabelPosition($cabelPosition);
            }
        }

        $this->entityManager->persist($montageCabelProperty);
        $this->entityManager->flush();
    }
}