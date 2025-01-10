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
        $result = $this->budgetService->getBudgets();
        $this->sendResponse($result['status'], $result['message'], $result['data'] ?? null);
    }

    public function getBudget($id){
        $result = $this->budgetService->getBudget($id);
        $this->sendResponse($result['status'], $result['message'], $result['data'] ?? null);
    }

    // public function createBudget(){
    //     echo 'POST budget';
    // }

    // public function updateBudget($id){
    //     echo 'PUT budget ' . $id;
    // }

    // public function deleteBudget($id){
    //     echo 'DELETE budget ' . $id;
    // }
}
?>