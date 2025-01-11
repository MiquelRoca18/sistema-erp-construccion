<?php
    // Mostrar errores para depuración
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Cargar el autoload generado por Composer
    require_once __DIR__ . '/../vendor/autoload.php';

    // Usar el namespace correcto
    use App\Controller\EmployeeController;
    use App\Controller\ProjectController;
    use App\Controller\BudgetController;
    use App\Controller\TaskController;
    use App\Router;

    // Obtener el tipo de solicitud HTTP (GET, POST, DELETE, etc.)
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    $scriptName = dirname($_SERVER['SCRIPT_NAME']);
    $requestUri = str_replace($scriptName, '', $_SERVER['REQUEST_URI']);

    // Crear una instancia del router
    $router = new Router();

    // Crear la instancia de los controladores
    $employeeController = new EmployeeController();
    $projectController = new ProjectController();
    $budgetController = new BudgetController();
    $taskController = new TaskController();

    // Definir las rutas para los empleados
    $router->addRoute('GET', '/employees', [$employeeController, 'get']);
    $router->addRoute('GET', '/employees/([0-9]+)', [$employeeController, 'getEmployee']);
    $router->addRoute('POST', '/employees', [$employeeController, 'createEmployee']);
    $router->addRoute('PUT', '/employees/([0-9]+)', [$employeeController, 'updateEmployee']);
    $router->addRoute('DELETE', '/employees/([0-9]+)', [$employeeController, 'deleteEmployee']);

    // Definir las rutas para los proyectos
    $router->addRoute('GET', '/projects', [$projectController, 'get']);
    $router->addRoute('GET', '/projects/([0-9]+)', [$projectController, 'getProject']);
    $router->addRoute('POST', '/projects', [$projectController, 'createProject']);
    $router->addRoute('PUT', '/projects/([0-9]+)', [$projectController, 'updateProject']);
    $router->addRoute('DELETE', '/projects/([0-9]+)', [$projectController, 'deleteProject']);

    // Definir las rutas para los proyectos
    $router->addRoute('GET', '/budgets', [$budgetController, 'get']);
    $router->addRoute('GET', '/budgets/([0-9]+)', [$budgetController, 'getBudget']);
    $router->addRoute('POST', '/budgets', [$budgetController, 'createBudget']);
    $router->addRoute('PUT', '/budgets/([0-9]+)', [$budgetController, 'updateBudget']);
    $router->addRoute('DELETE', '/budgets/([0-9]+)', [$budgetController, 'deleteBudget']);

    // Definir las rutas para los proyectos
    $router->addRoute('GET', '/tasks', [$taskController, 'get']);
    $router->addRoute('GET', '/tasks/([0-9]+)', [$taskController, 'getTask']);
    $router->addRoute('POST', '/tasks', [$taskController, 'createTask']);
    $router->addRoute('PUT', '/tasks/([0-9]+)', [$taskController, 'updateTask']);
    $router->addRoute('DELETE', '/tasks/([0-9]+)', [$taskController, 'deleteTask']);

    // Disparar el despachador de rutas
    $router->dispatch($requestUri, $requestMethod);
?>
