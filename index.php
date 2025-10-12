<?php

// Punto de entrada de la aplicación API-REST
// Incluye las rutas y despacha las solicitudes

require_once 'src/routes/FormRoute.php';
$formRoutes = $routes;

require_once 'src/routes/DocumentsRoute.php';
$documentsRoutes = $routes;

$routes = array_merge($formRoutes, $documentsRoutes);

require_once 'src/controllers/FormController.php';
require_once 'src/controllers/DocumentsController.php';

// Obtener método y path de la solicitud
list($method, $path) = getRequestMethodAndPath();

// Hacer match con las rutas definidas
$match = matchRoute($routes, $method, $path);

if ($match) {
    list($handler, $params) = $match;
    list($controllerClass, $methodName) = $handler;

    // Instanciar el controlador y llamar al método
    $controller = new $controllerClass();
    call_user_func_array([$controller, $methodName], $params);
} else {
    // Ruta no encontrada
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 0,
        'message' => 'Ruta no encontrada',
        'data' => null
    ]);
}
