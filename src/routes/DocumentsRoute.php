<?php

/**
 * Definición de rutas para la API de Documents
 * Mapea métodos HTTP y paths a métodos de controlador
 */

// Definición de rutas REST para Documents
$routes = [
    'GET /documents' => ['DocumentsController', 'getAll'],
    'POST /documents' => ['DocumentsController', 'create'],
    'DELETE /documents/{id}' => ['DocumentsController', 'delete'],
];

