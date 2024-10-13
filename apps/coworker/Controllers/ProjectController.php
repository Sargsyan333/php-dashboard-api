<?php

namespace Riconas\RiconasApi\Coworker\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Riconas\RiconasApi\Components\Coworker\Repository\CoworkerRepository;
use Riconas\RiconasApi\Components\Nvt\Repository\NvtRepository;
use Riconas\RiconasApi\Components\Project\Repository\ProjectRepository;
use Riconas\RiconasApi\Components\Subproject\Repository\SubprojectRepository;
use Riconas\RiconasApi\Components\User\User;
use Riconas\RiconasApi\Exceptions\RecordNotFoundException;
use Slim\Http\ServerRequest;

class ProjectController extends BaseController
{
    private CoworkerRepository $coworkerRepository;
    private ProjectRepository $projectRepository;
    private SubprojectRepository $subprojectRepository;
    private NvtRepository $nvtRepository;

    public function __construct(
        CoworkerRepository $coworkerRepository,
        ProjectRepository $projectRepository,
        SubprojectRepository $subprojectRepository,
        NvtRepository $nvtRepository
    ) {
        $this->coworkerRepository = $coworkerRepository;
        $this->projectRepository = $projectRepository;
        $this->subprojectRepository = $subprojectRepository;
        $this->nvtRepository = $nvtRepository;
    }

    /**
     * @throws RecordNotFoundException
     */
    public function getListAction(ServerRequest $request, Response $response): Response
    {
        /** @var User $authenticatedUser */
        $authenticatedUser = $request->getAttribute('AuthUser');
        $coworker = $this->coworkerRepository->getByUserId($authenticatedUser->getId());

        $projects = $this->projectRepository->getListByCoworkerId($coworker->getId());

        $responseData = [];
        foreach ($projects as $project) {
            $subprojectData = [];
            $subprojects = $this->subprojectRepository->getAllByProjectId($project['id']);
            foreach ($subprojects as $subproject) {
                $nvtData = [];
                $nvts = $this->nvtRepository->getAllBySubprojectId($subproject['id']);
                foreach ($nvts as $nvt) {
                    $nvtData[] = [
                        'id' => $nvt['id'],
                        'code' => $nvt['code'],
                    ];
                }

                $subprojectData[] = [
                    'id' => $subproject['id'],
                    'code' => $subproject['code'],
                    'nvt' => $nvtData,
                ];
            }

            $responseData[] = [
                'id' => $project['id'],
                'name' => $project['name'],
                'code' => $project['code'],
                'subprojects' => $subprojectData,
            ];
        }

        return $response->withJson(['items' => $responseData], 200);
    }
}