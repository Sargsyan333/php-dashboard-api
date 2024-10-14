<?php

namespace Riconas\RiconasApi\Coworker\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Riconas\RiconasApi\Components\Client\Client;
use Riconas\RiconasApi\Components\Coworker\Repository\CoworkerRepository;
use Riconas\RiconasApi\Components\Project\Project;
use Riconas\RiconasApi\Components\Project\Repository\ProjectRepository;
use Riconas\RiconasApi\Components\Subproject\Subproject;
use Riconas\RiconasApi\Components\User\User;
use Riconas\RiconasApi\Exceptions\RecordNotFoundException;
use Slim\Http\ServerRequest;

class ProjectController extends BaseController
{
    private CoworkerRepository $coworkerRepository;
    private ProjectRepository $projectRepository;

    public function __construct(
        CoworkerRepository $coworkerRepository,
        ProjectRepository $projectRepository
    ) {
        $this->coworkerRepository = $coworkerRepository;
        $this->projectRepository = $projectRepository;
    }

    /**
     * @throws RecordNotFoundException
     */
    public function getListAction(ServerRequest $request, Response $response): Response
    {
        /** @var User $authenticatedUser */
        $authenticatedUser = $request->getAttribute('AuthUser');
        $coworker = $this->coworkerRepository->getByUserId($authenticatedUser->getId());

        /** @var Client[] $clientsWithProjects */
        $clientsWithProjects = $this->projectRepository->getListByCoworkerId($coworker->getId());
        $responseData = [];
        foreach ($clientsWithProjects as $client) {
            /** @var Project[] $projects */
            $projects = $client->getProjects();
            $projectData = [];
            foreach ($projects as $project) {
                $subprojectData = [];
                /** @var Subproject[] $subprojects */
                $subprojects = $project->getSubprojects();
                foreach ($subprojects as $subproject) {
                    $nvtData = [];
                    $nvts = $subproject->getNvts();
                    foreach ($nvts as $nvt) {
                        $nvtData[] = [
                            'id' => $nvt->getId(),
                            'code' => $nvt->getCode(),
                        ];
                    }

                    $subprojectData[] = [
                        'id' => $subproject->getId(),
                        'code' => $subproject->getCode(),
                        'nvt' => $nvtData,
                    ];
                }

                $projectData[] = [
                    'id' => $project->getId(),
                    'name' => $project->getName(),
                    'code' => $project->getCode(),
                    'subprojects' => $subprojectData,
                ];
            }

            $responseData[] = [
                'id' => $client->getId(),
                'name' => $client->getName(),
                'projects' => $projectData,
            ];
        }

        return $response->withJson(['items' => $responseData], 200);
    }
}