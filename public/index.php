<?php 

require_once __DIR__ . '/../includes/app.php';

use MVC\Router;
use Controllers\LoginController;
use Controllers\TareaController;
use Controllers\DashboardController;
$router = new Router();


//Login
$router->get('/', [LoginController::class, 'login']);
$router->post('/', [LoginController::class, 'login']);
$router->get('/logout', [LoginController::class, 'logout']);

//Crear cuenta
$router->get('/crear', [LoginController::class, 'crear']);
$router->post('/crear', [LoginController::class, 'crear']);

//Formulario de olvidar password
$router->get('/passwordOlvidada', [LoginController::class, 'passwordOlvidada']);
$router->post('/passwordOlvidada', [LoginController::class, 'passwordOlvidada']);

//Restablecer password
$router->get('/restablecer', [LoginController::class, 'restablecer']);
$router->post('/restablecer', [LoginController::class, 'restablecer']);

//Confirmacion cuenta 
$router->get('/mensaje', [LoginController::class, 'mensaje']);
$router->get('/confirmar', [LoginController::class, 'confirmar']);

//zona de proyectos
$router->get('/dashboard', [DashboardController::class, 'index']);
$router->get('/crear-proyecto', [DashboardController::class, 'crearProyecto']);
$router->post('/crear-proyecto', [DashboardController::class, 'crearProyecto']);
$router->get('/proyecto', [DashboardController::class, 'proyecto']);
$router->get('/perfil', [DashboardController::class, 'perfil']);
$router->post('/perfil', [DashboardController::class, 'perfil']);
$router->get('/cambiar-password', [DashboardController::class, 'cambiarPassword']);
$router->post('/cambiar-password', [DashboardController::class, 'cambiarPassword']);

//API para las tareas
$router->get('/api/tareas', [TareaController::class, 'index']);
$router->post('/api/tarea', [TareaController::class, 'crear']);
$router->post('/api/tarea/actualizar', [TareaController::class, 'actualizar']);
$router->post('/api/tarea/eliminar', [TareaController::class, 'eliminar']);


// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();