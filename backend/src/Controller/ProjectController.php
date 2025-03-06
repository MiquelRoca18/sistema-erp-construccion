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
        $cacheKey = "projects_all";
        $cachedData = getFromCache($cacheKey);
        
        if ($cachedData !== null) {
            $this->sendResponse(200, 'Proyectos encontrados (caché)', $cachedData);
            return;
        }
        
        $result = $this->projectService->getProjects();
        
        if (isset($result['data'])) {
            saveToCache($cacheKey, $result['data'], 300); // 5 minutos
        }
        
        $this->sendResponse($result['status'], $result['message'], $result['data'] ?? null);
    }

    public function getProject($projectId) {
        $cacheKey = "project_" . $projectId;
        $cachedData = getFromCache($cacheKey);
        
        if ($cachedData !== null) {
            $this->sendResponse(200, 'Proyecto encontrado (caché)', $cachedData);
            return;
        }
        
        $result = $this->projectService->getProject($projectId);
        
        if (isset($result['data'])) {
            saveToCache($cacheKey, $result['data'], 300); // 5 minutos
        }
        
        $this->sendResponse($result['status'], $result['message'], $result['data'] ?? null);
    }

    public function createProject() {
        $data = $this->getRequestData();
        $result = $this->projectService->createProject($data);
        
        // Limpiar caché de proyectos
        if (function_exists('apcu_delete')) {
            apcu_delete("projects_all");
        }
        
        $this->sendResponse($result['status'], $result['message']);
    }

    public function updateProject($projectId) {
        $data = $this->getRequestData();
        $result = $this->projectService->updateProject($projectId, $data);
        
        // Limpiar caché de proyectos
        if (function_exists('apcu_delete')) {
            apcu_delete("projects_all");
            apcu_delete("project_" . $projectId);
        }
        
        $this->sendResponse($result['status'], $result['message'], $result['data'] ?? null);
    }

    public function deleteProject($projectId) {
        $result = $this->projectService->deleteProject($projectId);
        
        // Limpiar caché de proyectos
        if (function_exists('apcu_delete')) {
            apcu_delete("projects_all");
            apcu_delete("project_" . $projectId);
        }
        
        $this->sendResponse($result['status'], $result['message'], $result['data'] ?? null);
    }
}