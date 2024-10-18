<?php

namespace Riconas\RiconasApi\Coworker\Controllers;

use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Psr\Http\Message\ResponseInterface as Response;
use Riconas\RiconasApi\Components\MontageJobOnt\Repository\MontageOntRepository;
use Riconas\RiconasApi\Components\MontageOntPhoto\Service\MontageOntPhotoService;
use Riconas\RiconasApi\Components\MontageOntPhoto\Service\MontageOntPhotoStorageService;
use Riconas\RiconasApi\Exceptions\RecordNotFoundException;
use Slim\Http\ServerRequest;
use Slim\Psr7\UploadedFile;

class OntPhotoController extends BaseController
{
    private MontageOntRepository $montageOntRepository;
    private MontageOntPhotoStorageService $montageOntPhotoStorageService;
    private MontageOntPhotoService $montageOntPhotoService;

    public function __construct(
        MontageOntRepository          $montageOntRepository,
        MontageOntPhotoStorageService $montageOntPhotoStorageService,
        MontageOntPhotoService        $montageOntPhotoService
    ) {
        $this->montageOntRepository = $montageOntRepository;
        $this->montageOntPhotoStorageService = $montageOntPhotoStorageService;
        $this->montageOntPhotoService = $montageOntPhotoService;
    }

    /**
     * @throws OptimisticLockException
     * @throws RecordNotFoundException
     * @throws ORMException
     */
    public function deleteOneAction(ServerRequest $request, Response $response): Response
    {
        $ontId = $request->getAttribute('id');
        $ont = $this->montageOntRepository->getById($ontId);

        $ontPhotoId = $request->getAttribute('photoId');
        $this->montageOntPhotoService->deletePhoto($ontPhotoId);

        return $response->withJson([], 204);
    }

    /**
     * @throws RecordNotFoundException
     */
    public function uploadAction(ServerRequest $request, Response $response): Response
    {
        $ontId = $request->getAttribute('id');
        $ont = $this->montageOntRepository->getById($ontId);

        $uploadedFiles = $request->getUploadedFiles();

        /** @var UploadedFile[] $uploadedPhotos */
        $uploadedPhotos = $uploadedFiles['files'];

        $photos = $this->montageOntPhotoService->insertPhotos(
            $ont,
            $uploadedPhotos
        );
        $photosData = [];
        foreach ($photos as $photo) {
            $photosData[] = [
                'id' => $photo->getId(),
                'path' => $this->montageOntPhotoStorageService->getPhotoUrl($photo->getPhotoPath()),
            ];
        }

        return $response->withJson(['items' => $photosData], 200);
    }
}