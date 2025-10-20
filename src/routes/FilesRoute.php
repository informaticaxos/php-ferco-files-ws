<?php

/**
 * Definición de rutas para la API de Files
 * Mapea métodos HTTP y paths a métodos de controlador
 */

// Definición de rutas REST para Files
$routes = [
    'GET /files' => ['FilesController', 'getAllFiles'],
    'GET /files/form/{id}' => ['FilesController', 'getAllFilesByIdForm'],
    'POST /files' => ['FilesController', 'createFile'],
    'PUT /files/{id}' => ['FilesController', 'updateFile'],
    'DELETE /files/{id}' => ['FilesController', 'deleteFile'],
];
