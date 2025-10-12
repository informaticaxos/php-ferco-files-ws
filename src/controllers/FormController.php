<?php

require_once __DIR__ . '/../services/FormService.php';

/**
 * Controlador para manejar las solicitudes HTTP relacionadas con Form
 * Maneja la lógica de respuesta y delega a servicios
 */
class FormController
{
    private $service;

    /**
     * Constructor: inicializa el servicio
     */
    public function __construct()
    {
        $this->service = new FormService();
    }

    /**
     * Obtiene todos los forms
     */
    public function getAll()
    {
        $forms = $this->service->getAllForms();
        $this->sendResponse(200, 1, 'Forms retrieved successfully', $forms);
    }

    /**
     * Obtiene un form por ID
     *
     * @param int $id
     */
    public function getById($id)
    {
        $form = $this->service->getFormById($id);
        if ($form) {
            $this->sendResponse(200, 1, 'Form retrieved successfully', $form);
        } else {
            $this->sendResponse(404, 0, 'Form not found', null);
        }
    }

    /**
     * Crea un nuevo form
     */
    public function create()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            $this->sendResponse(400, 0, 'Invalid JSON data', null);
            return;
        }

        $form = $this->service->createForm($data);
        if ($form) {
            $this->sendResponse(201, 1, 'Form created successfully', $form->toArray());
        } else {
            $this->sendResponse(400, 0, 'Validation error', null);
        }
    }

    /**
     * Actualiza un form existente
     *
     * @param int $id
     */
    public function update($id)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            $this->sendResponse(400, 0, 'Invalid JSON data', null);
            return;
        }

        $form = $this->service->updateForm($id, $data);
        if ($form) {
            $this->sendResponse(200, 1, 'Form updated successfully', $form->toArray());
        } else {
            $this->sendResponse(404, 0, 'Form not found or validation error', null);
        }
    }

    /**
     * Elimina un form por ID
     *
     * @param int $id
     */
    public function delete($id)
    {
        $this->service->deleteForm($id);
        $this->sendResponse(200, 1, 'Form deleted successfully', null);
    }

    /**
     * Envía la respuesta HTTP
     *
     * @param int $httpStatus
     * @param int $status
     * @param string $message
     * @param mixed $data
     */
    private function sendResponse($httpStatus, $status, $message, $data)
    {
        http_response_code($httpStatus);
        header('Content-Type: application/json');
        echo json_encode([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ]);
        exit;
    }
}
