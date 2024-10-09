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
}