<?php
    // Mostrar errores para depuración
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Habilitar CORS
    header('Access-Control-Allow-Origin: http://localhost:5173'); // Permitir solicitudes desde el origen del frontend
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE'); // Métodos HTTP permitidos
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With'); // Encabezados permitidos
    header('Access-Control-Allow-Credentials: true'); // Permitir envío de credenciales si es necesario
    
    // Cargar el autoload generado por Composer
    require_once __DIR__ . '/../vendor/autoload.php';

    // Usar el namespace correcto
    use App\Controller\EmployeeController;
    use App\Controller\ProjectController;
    use App\Controller\BudgetController;
    use App\Controller\TaskController;
    use App\Controller\AuthController;
    use App\Controller\EmployeeTaskController;
    use App\Config\Database; // Usar la nueva clase Database
    use App\Router;

    // Cargar las variables de entorno
    try {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();
    } catch (Exception $e) {
        echo 'Error cargando el archivo .env: ' . $e->getMessage();
        exit();
    }

    // Obtener el tipo de solicitud HTTP (GET, POST, DELETE, etc.)
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    if ($requestMethod === 'OPTIONS') {
        // Responder a la solicitud preflight
        header('Access-Control-Allow-Origin: http://localhost:5173');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        header('Access-Control-Allow-Credentials: true');
        http_response_code(200); // Respuesta exitosa
        exit(); // Terminar aquí para solicitudes OPTIONS
    }

    $scriptName = dirname($_SERVER['SCRIPT_NAME']);
    $requestUri = str_replace($scriptName, '', $_SERVER['REQUEST_URI']);

    // Crear una instancia del router
    $router = new Router();

    // Crear la instancia de los controladores
    $employeeController = new EmployeeController();
    $projectController = new ProjectController();
    $budgetController = new BudgetController();
    $taskController = new TaskController();
    $authController = new AuthController();
    $employeeTaskController = new EmployeeTaskController();
    
    // Ejecutar la configuración de la base de datos
    $database = new Database(); // Instancia de la clase Database
    $pdo = $database->getConnection();
    
    // Verificar si la base de datos y las tablas ya existen
    $sql = "SHOW TABLES LIKE 'empleados'";
    $stmt = $pdo->query($sql);

    // Si la tabla 'empleados' no existe, creamos la base de datos, las tablas y los datos
    if ($stmt->rowCount() == 0) {
        $database->createTables();
        $database->insertData();  
    }

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

    // Definir las rutas para los presupuestos
    $router->addRoute('GET', '/budgets', [$budgetController, 'get']);
    $router->addRoute('GET', '/budgets/([0-9]+)', [$budgetController, 'getBudget']);
    $router->addRoute('POST', '/budgets', [$budgetController, 'createBudget']);
    $router->addRoute('PUT', '/budgets/([0-9]+)', [$budgetController, 'updateBudget']);
    $router->addRoute('DELETE', '/budgets/([0-9]+)', [$budgetController, 'deleteBudget']);

    // Definir las rutas para las tareas
    $router->addRoute('GET', '/tasks', [$taskController, 'get']);
    $router->addRoute('GET', '/tasks/([0-9]+)', [$taskController, 'getTask']);
    $router->addRoute('POST', '/tasks', [$taskController, 'createTask']);
    $router->addRoute('PUT', '/tasks/([0-9]+)', [$taskController, 'updateTask']);
    $router->addRoute('DELETE', '/tasks/([0-9]+)', [$taskController, 'deleteTask']);

    // Definir las rutas para la autenticación
    $router->addRoute('POST', '/auth/login', [$authController, 'login']);
    $router->addRoute('POST', '/auth/logout', [$authController, 'logout']); 

    // Definir las rutas para empleados_tareas
    $router->addRoute('POST', '/employee-tasks', [$employeeTaskController, 'addTaskToEmployee']);
    $router->addRoute('DELETE', '/employee-tasks', [$employeeTaskController, 'removeTaskFromEmployee']);
    $router->addRoute('GET', '/employee-tasks/employees/([0-9]+)', [$employeeTaskController, 'getTasksByEmployee']);
    $router->addRoute('GET', '/employee-tasks/tasks/([0-9]+)', [$employeeTaskController, 'getEmployeesByTask']);
    $router->addRoute('GET', '/employee-tasks/pending-tasks/([0-9]+)', [$employeeTaskController, 'getPendingTasksByEmployee']);

    // Disparar el despachador de rutas
    $router->dispatch($requestUri, $requestMethod);
?>
