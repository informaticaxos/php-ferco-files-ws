<?php

/**
 * Definición de rutas para la API de Documents
 * Mapea métodos HTTP y paths a métodos de controlador
 */

// Definición de rutas REST para Documents
$routes = [
    'GET /files' => ['DocumentsController', 'getAll'],
    'POST /files' => ['DocumentsController', 'create'],
    'POST /files/upload/{id}' => ['DocumentsController', 'uploadFile'],
    'DELETE /files/{id}' => ['DocumentsController', 'delete'],
];

