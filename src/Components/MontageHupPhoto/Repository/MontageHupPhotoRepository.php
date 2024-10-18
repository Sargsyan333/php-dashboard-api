<?php

namespace Riconas\RiconasApi\Components\MontageHupPhoto\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Riconas\RiconasApi\Components\MontageHupPhoto\HupPhotoState;
use Riconas\RiconasApi\Components\MontageHupPhoto\MontageHupPhoto;
use Riconas\RiconasApi\Exceptions\RecordNotFoundException;

class MontageHupPhotoRepository extends EntityRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, new ClassMetadata(MontageHupPhoto::class));
    }

    public function findAllByHupIdAndState(string $hupId, HupPhotoState $state): array
    {
        return $this->findBy(['hupId' => $hupId, 'state' => $state]);
    }

    public function getById(string $id): MontageHupPhoto
    {
        $hupPhoto = $this->findOneBy(['id' => $id]);
        if (is_null($hupPhoto)) {
            throw new RecordNotFoundException('Record not found');
        }

        return $hupPhoto;
    }
}