<?php
namespace App\Controller;

use App\Service\ProjectService;
use App\Utils\BaseController;

class ProjectController extends BaseController {
    private $projectService;

    public function __construct() {
        $this->projectService = new ProjectService();
    }

    public function get() {
        $result = $this->projectService->getProjects();
        $this->sendResponse($result['status'], $result['message'], $result['data'] ?? null);
    }

    public function getProject($projectId) {
        $result = $this->projectService->getProject($projectId);
        $this->sendResponse($result['status'], $result['message'], $result['data'] ?? null);
    }

    public function createProject() {
        $data = $this->getRequestData();
        $result = $this->projectService->createProject($data);
        $this->sendResponse($result['status'], $result['message']);
    }

    public function updateProject($projectId) {
        $data = $this->getRequestData();
        $result = $this->projectService->updateProject($projectId, $data);
        $this->sendResponse($result['status'], $result['message'], $result['data'] ?? null);
    }

    public function deleteProject($projectId) {
        $result = $this->projectService->deleteProject($projectId);
        $this->sendResponse($result['status'], $result['message'], $result['data'] ?? null);
    }
}
