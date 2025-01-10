<?php
namespace App\Service;
use App\Model\Budget;
use App\Utils\BaseService;

class BudgetService extends BaseService {

    public function __construct() {
        parent::__construct(new Budget());
    }

    public function getBudgets() {
        $budgets = $this->model->get();
        return $budgets ? $this->responseFound($budgets, 'Presupuestos encontrados') : $this->responseNotFound();
    }

    public function getBudget($budgetId) {
        // Validar ID
        if ($error = $this->validateId($budgetId)) {
            return $error;
        }

        // Validar existencia del presupuesto
        if ($error = $this->validateExists($budgetId)) {
            return $error;
        }

        $budget = $this->model->getBudget($budgetId);
        return $this->responseFound($budget, 'Presupuesto encontrado');
    }

    // public function createBudget($data) {
    //     return [
    //         'status' => 201,
    //         'message' => 'POST budget',
    //         'data' => []
    //     ];
    // }

    // public function updateBudget($budgetId, $data) {
    //     return [
    //         'status' => 200,
    //         'message' => 'PUT budget ' . $budgetId,
    //         'data' => []
    //     ];
    // }

    // public function deleteBudget($budgetId) {
    //     return [
    //         'status' => 200,
    //         'message' => 'DELETE budget ' . $budgetId,
    //         'data' => []
    //     ];
    // }
}
?>