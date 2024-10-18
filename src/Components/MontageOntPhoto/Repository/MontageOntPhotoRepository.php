<?php

namespace Riconas\RiconasApi\Components\MontageOntPhoto\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Riconas\RiconasApi\Components\MontageOntPhoto\MontageOntPhoto;
use Riconas\RiconasApi\Exceptions\RecordNotFoundException;

class MontageOntPhotoRepository extends EntityRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, new ClassMetadata(MontageOntPhoto::class));
    }

    public function getById(string $id): MontageOntPhoto
    {
        $hupPhoto = $this->findOneBy(['id' => $id]);
        if (is_null($hupPhoto)) {
            throw new RecordNotFoundException('Record not found');
        }

        return $hupPhoto;
    }
}