<?php

require_once __DIR__ . '/../services/CountriesService.php';

/**
 * Controlador para manejar las solicitudes relacionadas con Countries
 */
class CountriesController
{
    private $service;

    /**
     * Constructor: inyecta el servicio
     */
    public function __construct()
    {
        $this->service = new CountriesService();
    }

    /**
     * Obtiene todos los países
     */
    public function getAll()
    {
        try {
            $countries = $this->service->getAllCountries();
            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 1,
                'message' => 'Países obtenidos exitosamente',
                'data' => $countries
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 0,
                'message' => 'Error al obtener países: ' . $e->getMessage(),
                'data' => null
            ]);
        }
    }

    /**
     * Obtiene un país por ID
     *
     * @param int $id
     */
    public function getById($id)
    {
        try {
            $country = $this->service->getCountryById($id);
            if ($country) {
                http_response_code(200);
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 1,
                    'message' => 'País obtenido exitosamente',
                    'data' => $country
                ]);
            } else {
                http_response_code(404);
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 0,
                    'message' => 'País no encontrado',
                    'data' => null
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 0,
                'message' => 'Error al obtener país: ' . $e->getMessage(),
                'data' => null
            ]);
        }
    }

    /**
     * Crea un nuevo país
     */
    public function create()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data || !isset($data['code']) || !isset($data['name'])) {
                http_response_code(400);
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 0,
                    'message' => 'Datos inválidos: se requieren code y name',
                    'data' => null
                ]);
                return;
            }
            $country = $this->service->createCountry($data);
            http_response_code(201);
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 1,
                'message' => 'País creado exitosamente',
                'data' => $country->toArray()
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 0,
                'message' => 'Error al crear país: ' . $e->getMessage(),
                'data' => null
            ]);
        }
    }

    /**
     * Actualiza un país existente
     *
     * @param int $id
     */
    public function update($id)
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data || !isset($data['code']) || !isset($data['name'])) {
                http_response_code(400);
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 0,
                    'message' => 'Datos inválidos: se requieren code y name',
                    'data' => null
                ]);
                return;
            }
            $success = $this->service->updateCountry($id, $data);
            if ($success) {
                http_response_code(200);
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 1,
                    'message' => 'País actualizado exitosamente',
                    'data' => null
                ]);
            } else {
                http_response_code(404);
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 0,
                    'message' => 'País no encontrado',
                    'data' => null
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 0,
                'message' => 'Error al actualizar país: ' . $e->getMessage(),
                'data' => null
            ]);
        }
    }

    /**
     * Elimina un país por ID
     *
     * @param int $id
     */
    public function delete($id)
    {
        try {
            $success = $this->service->deleteCountry($id);
            if ($success) {
                http_response_code(200);
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 1,
                    'message' => 'País eliminado exitosamente',
                    'data' => null
                ]);
            } else {
                http_response_code(404);
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 0,
                    'message' => 'País no encontrado',
                    'data' => null
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 0,
                'message' => 'Error al eliminar país: ' . $e->getMessage(),
                'data' => null
            ]);
        }
    }
}
