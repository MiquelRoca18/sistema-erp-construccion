<?php
namespace App\Controller;
use App\Utils\BaseController;
use App\Service\BudgetService;

class BudgetController extends BaseController{
    private $budgetService;

    public function __construct() {
        $this->budgetService = new BudgetService();
    }

    public function get(){
        $cacheKey = "budgets_all";
        $cachedData = getFromCache($cacheKey);
        
        if ($cachedData !== null) {
            $this->sendResponse(200, 'Presupuestos encontrados (caché)', $cachedData);
            return;
        }
        
        $result = $this->budgetService->getBudgets();
        
        if (isset($result['data'])) {
            saveToCache($cacheKey, $result['data'], 300); // 5 minutos
        }
        
        $this->sendResponse($result['status'], $result['message'], $result['data'] ?? null);
    }

    public function getBudget($id){
        $cacheKey = "budget_" . $id;
        $cachedData = getFromCache($cacheKey);
        
        if ($cachedData !== null) {
            $this->sendResponse(200, 'Presupuesto encontrado (caché)', $cachedData);
            return;
        }
        
        $result = $this->budgetService->getBudget($id);
        
        if (isset($result['data'])) {
            saveToCache($cacheKey, $result['data'], 300); // 5 minutos
        }
        
        $this->sendResponse($result['status'], $result['message'], $result['data'] ?? null);
    }

    public function createBudget(){
        $data = $this->getRequestData();
        $result = $this->budgetService->createBudget($data);
        
        // Limpiar caché
        if (function_exists('apcu_delete')) {
            apcu_delete("budgets_all");
        }
        
        $this->sendResponse($result['status'], $result['message'], $result['data'] ?? null);
    }

    public function updateBudget($id){
        $data = $this->getRequestData();
        $result = $this->budgetService->updateBudget($id, $data);
        
        // Limpiar caché
        if (function_exists('apcu_delete')) {
            apcu_delete("budgets_all");
            apcu_delete("budget_" . $id);
        }
        
        $this->sendResponse($result['status'], $result['message'], $result['data'] ?? null);    
    }

    public function deleteBudget($id){
        $result= $this->budgetService->deleteBudget($id);
        
        // Limpiar caché
        if (function_exists('apcu_delete')) {
            apcu_delete("budgets_all");
            apcu_delete("budget_" . $id);
        }
        
        $this->sendResponse($result['status'], $result['message'], $result['data'] ?? null);
    }
}
?>