<?php

/**
 * Rutas para la entidad Countries
 * Solo define la ruta GET /countries para obtener todos los países
 */
$routes = [
    'GET /countries' => ['CountriesController', 'getAll'],
];
