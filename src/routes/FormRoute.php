<?php

/**
 * Definición de rutas para la API de Forms
 * Mapea métodos HTTP y paths a métodos de controlador
 */

// Definición de rutas REST para Forms
$routes = [
    'GET /' => ['FormController', 'getAll'],
    'GET /forms' => ['FormController', 'getAll'],
    'GET /forms/{id}' => ['FormController', 'getById'],
    'POST /forms' => ['FormController', 'create'],
    'PUT /forms/{id}' => ['FormController', 'update'],
    'DELETE /forms/{id}' => ['FormController', 'delete'],
];

