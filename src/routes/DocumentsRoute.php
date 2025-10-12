<?php

/**
 * Definición de rutas para la API de Documents
 * Mapea métodos HTTP y paths a métodos de controlador
 */

// Definición de rutas REST para Documents
$routes = [
    'GET /documents' => ['DocumentsController', 'getAll'],
    'POST /documents' => ['DocumentsController', 'create'],
    'POST /documents/upload/{id}' => ['DocumentsController', 'uploadFile'],
    'DELETE /documents/{id}' => ['DocumentsController', 'delete'],
];

// Función para obtener el método y path de la solicitud
function getRequestMethodAndPath()
{
    $method = $_SERVER['REQUEST_METHOD'];
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    // Remover el prefijo del subdirectorio
    $path = str_replace('/php-ferco-files-ws/index.php', '', $path);
    return [$method, $path];
}

// Función para hacer match de ruta
function matchRoute($routes, $method, $path)
{
    foreach ($routes as $route => $handler) {
        list($routeMethod, $routePath) = explode(' ', $route, 2);
        if ($routeMethod !== $method) {
            continue;
        }

        // Reemplazar {id} con regex
        $routePathRegex = preg_replace('/\{[^}]+\}/', '([^/]+)', $routePath);
        if (preg_match("#^$routePathRegex$#", $path, $matches)) {
            array_shift($matches); // Remover el match completo
            return [$handler, $matches];
        }
    }
    return null;
}
