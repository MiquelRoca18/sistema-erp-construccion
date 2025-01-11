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

        $budget = $this->model->getById($budgetId);
        return $this->responseFound($budget, 'Presupuesto encontrado');
    }

    public function createBudget($data) {
        // Validar campos obligatorios
        $error = $this->validator->validateRequiredFields(['equipos', 'mano_obra', 'materiales'], $data);
        if ($error) {
            return ['status' => 400, 'message' => $error];
        }

        // Validar si los datos son números
        $error = $this->validator->validateNumbers(['equipos', 'mano_obra', 'materiales'], $data);
        if ($error) {
            return ['status' => 400, 'message' => $error];
        }

        //Validar si el proyecto existe
        if (!empty($data->proyectos_id) && !$this->model->exists($data->proyectos_id)) {
            return ['status' => 404, 'message' => 'El proyecto no existe'];
        } elseif (empty($data->proyectos_id)) {
            return ['status' => 400, 'message' => 'El proyecto es obligatorio'];
        }

        $budget = $this->model->create($data);
        return $this->responseCreated($budget, 'Presupuesto creado');
    }

    public function updateBudget($budgetId, $data) {
        // Validar ID
        if ($error = $this->validateId($budgetId)) {
            return $error;
        }

        // Validar existencia del presupuesto
        if ($error = $this->validateExists($budgetId)) {
            return $error;
        }

        // Validar si los datos son números
        $error = $this->validator->validateNumbers(['equipos', 'mano_obra', 'materiales'], $data);
        if ($error) {
            return ['status' => 400, 'message' => $error];
        }

        //Validar si el proyecto existe
        if (!empty($data->proyectos_id) && !$this->model->exists($data->proyectos_id)) {
            return ['status' => 404, 'message' => 'El proyecto no existe'];
        } elseif (empty($data->proyectos_id)) {
            return ['status' => 400, 'message' => 'El proyecto es obligatorio'];
        }

        $budget = $this->model->update($budgetId, $data);
        return $budget ? $this->responseUpdated($budget, 'Presupuesto actualizado') : $this->responseError();
    }

    // public function deleteBudget($budgetId) {
    //     return [
    //         'status' => 200,
    //         'message' => 'DELETE budget ' . $budgetId,
    //         'data' => []
    //     ];
    // }
}
?>