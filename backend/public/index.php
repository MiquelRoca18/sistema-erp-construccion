<?php
    // Mostrar errores para depuración
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Habilitar CORS
    header('Access-Control-Allow-Origin: https://sistema-erp-construccion-1.onrender.com');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    header('Access-Control-Allow-Credentials: true');
    
    // Manejar las solicitudes preflight OPTIONS
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit();
    }

    // Funciones de caché
    function getFromCache($key) {
        if (function_exists('apcu_exists') && apcu_exists($key)) {
            return apcu_fetch($key);
        }
        return null;
    }

    function saveToCache($key, $data, $ttl = 300) {
        if (function_exists('apcu_store')) {
            apcu_store($key, $data, $ttl);
        }
    }

    // Cargar el autoload generado por Composer
    require_once __DIR__ . '/../vendor/autoload.php';

    // Usar el namespace correcto
    use App\Controller\EmployeeController;
    use App\Controller\ProjectController;
    use App\Controller\BudgetController;
    use App\Controller\TaskController;
    use App\Controller\AuthController;
    use App\Controller\EmployeeTaskController;
    use App\Config\Database; 
    use App\Router;

    // Definir variables de entorno predeterminadas para entorno de producción
    if (!file_exists(__DIR__ . '/../.env')) {
        $_ENV['DB_HOST'] = getenv('DB_HOST') ?: getenv('MYSQLHOST') ?: 'localhost';
        $_ENV['DB_USER'] = getenv('DB_USER') ?: getenv('MYSQLUSER') ?: 'root';
        $_ENV['DB_PASS'] = getenv('DB_PASS') ?: getenv('MYSQLPASSWORD') ?: '';
        $_ENV['DB_NAME'] = getenv('DB_NAME') ?: getenv('MYSQLDATABASE') ?: 'ERP_CONSTRUCTION';
        $_ENV['DB_PORT'] = getenv('DB_PORT') ?: getenv('MYSQLPORT') ?: '3306';
        $_ENV['SECRET_KEY'] = getenv('SECRET_KEY') ?: 'tu_clave_secreta_predeterminada';
        $_ENV['JWT_EXPIRES_IN'] = getenv('JWT_EXPIRES_IN') ?: 3600;
    } else {
        // Cargar variables desde archivo .env si existe
        try {
            $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
            $dotenv->load();
        } catch (Exception $e) {
            
            // Establecer valores predeterminados solo si las variables no existen
            if (!isset($_ENV['DB_HOST'])) $_ENV['DB_HOST'] = getenv('MYSQLHOST') ?: 'localhost';
            if (!isset($_ENV['DB_USER'])) $_ENV['DB_USER'] = getenv('MYSQLUSER') ?: 'root';
            if (!isset($_ENV['DB_PASS'])) $_ENV['DB_PASS'] = getenv('MYSQLPASSWORD') ?: '';
            if (!isset($_ENV['DB_NAME'])) $_ENV['DB_NAME'] = getenv('MYSQLDATABASE') ?: 'ERP_CONSTRUCTION';
            if (!isset($_ENV['DB_PORT'])) $_ENV['DB_PORT'] = getenv('MYSQLPORT') ?: '3306';
            if (!isset($_ENV['SECRET_KEY'])) $_ENV['SECRET_KEY'] = 'tu_clave_secreta_predeterminada';
            if (!isset($_ENV['JWT_EXPIRES_IN'])) $_ENV['JWT_EXPIRES_IN'] = 3600;
        }
    }

    // Obtener el método de la solicitud HTTP
    $requestMethod = $_SERVER['REQUEST_METHOD'];

    // Obtener la ruta relativa
    $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $scriptName = dirname($_SERVER['SCRIPT_NAME']);

    // Eliminar el prefijo del script name de la URI
    $requestUri = preg_replace('#^' . preg_quote($scriptName, '#') . '#', '', $requestUri);

    // Eliminar barras iniciales
    $requestUri = ltrim($requestUri, '/');

    $router = new Router();

    $employeeController = new EmployeeController();
    $projectController = new ProjectController();
    $budgetController = new BudgetController();
    $taskController = new TaskController();
    $authController = new AuthController();
    $employeeTaskController = new EmployeeTaskController();
    
    try {
        // Ejecutar la configuración de la base de datos
        $database = new Database();
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
        $router->addRoute('GET', 'employees', [$employeeController, 'get']);
        $router->addRoute('GET', 'employees/([0-9]+)', [$employeeController, 'getEmployee']);
        $router->addRoute('POST', 'employees', [$employeeController, 'createEmployee']);
        $router->addRoute('PUT', 'employees/([0-9]+)', [$employeeController, 'updateEmployee']);
        $router->addRoute('DELETE', 'employees/([0-9]+)', [$employeeController, 'deleteEmployee']);

        // Definir las rutas para los proyectos
        $router->addRoute('GET', 'projects', [$projectController, 'get']);
        $router->addRoute('GET', 'projects/([0-9]+)', [$projectController, 'getProject']);
        $router->addRoute('POST', 'projects', [$projectController, 'createProject']);
        $router->addRoute('PUT', 'projects/([0-9]+)', [$projectController, 'updateProject']);
        $router->addRoute('DELETE', 'projects/([0-9]+)', [$projectController, 'deleteProject']);

        // Definir las rutas para los presupuestos
        $router->addRoute('GET', 'budgets', [$budgetController, 'get']);
        $router->addRoute('GET', 'budgets/([0-9]+)', [$budgetController, 'getBudget']);
        $router->addRoute('POST', 'budgets', [$budgetController, 'createBudget']);
        $router->addRoute('PUT', 'budgets/([0-9]+)', [$budgetController, 'updateBudget']);
        $router->addRoute('DELETE', 'budgets/([0-9]+)', [$budgetController, 'deleteBudget']);

        // Definir las rutas para las tareas
        $router->addRoute('GET', 'tasks', [$taskController, 'get']);
        $router->addRoute('GET', 'tasks/([0-9]+)', [$taskController, 'getTask']);
        $router->addRoute('POST', 'tasks', [$taskController, 'createTask']);
        $router->addRoute('PUT', 'tasks/([0-9]+)', [$taskController, 'updateTask']);
        $router->addRoute('DELETE', 'tasks/([0-9]+)', [$taskController, 'deleteTask']);
        
        // Definir las rutas para la autenticación
        $router->addRoute('POST', 'auth/login', [$authController, 'login']);
        $router->addRoute('POST', 'auth/logout', [$authController, 'logout']); 
        $router->addRoute('POST', 'auth/change-password', [$authController, 'changePassword']);

        // Definir las rutas para empleados_tareas
        $router->addRoute('POST', 'employee-tasks', [$employeeTaskController, 'addTaskToEmployee']);
        $router->addRoute('DELETE', 'employee-tasks', [$employeeTaskController, 'removeTaskFromEmployee']);
        $router->addRoute('GET', 'employee-tasks/employees/([0-9]+)', [$employeeTaskController, 'getTasksByEmployee']);
        $router->addRoute('GET', 'employee-tasks/tasks/([0-9]+)', [$employeeTaskController, 'getEmployeesByTask']);
        $router->addRoute('GET', 'employee-tasks/pending-tasks/([0-9]+)', [$employeeTaskController, 'getPendingTasksByEmployee']);
        $router->addRoute('GET', 'employee-tasks/responsible/([0-9]+)', [$employeeTaskController, 'getTasksByResponsible']);
        $router->addRoute('PUT', 'employee-tasks/assignment/([0-9]+)', [$employeeTaskController, 'updateAssignment']);
        $router->addRoute('POST', 'employee-tasks/manage/([0-9]+)', [$employeeTaskController, 'manageTaskAssignments']);

        $router->addRoute('GET', 'health', function() {
            
            header('Content-Type: application/json');
            echo json_encode(['status' => 'ok']);
            http_response_code(200);
            exit();
        });
        
        // Disparar el despachador de rutas
        $router->dispatch($requestUri, $requestMethod);
    } catch (Exception $e) {
        // Manejo global de errores
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode([
            'error' => true,
            'message' => 'Error en el servidor: ' . $e->getMessage()
        ]);
    }
?>