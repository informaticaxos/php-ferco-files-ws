<?php

define('BASE_URL', 'https://fercoadvancededucation.com/');

require_once __DIR__ . '/../services/DocumentsService.php';

/**
 * Controlador para manejar las solicitudes HTTP relacionadas con Document
 * Maneja la lógica de respuesta y delega a servicios
 */
class DocumentsController
{
    private $service;

    /**
     * Constructor: inicializa el servicio
     */
    public function __construct()
    {
        $this->service = new DocumentsService();
    }

    /**
     * Obtiene todos los documents
     */
    public function getAll()
    {
        $documents = $this->service->getAllDocuments();
        $this->sendResponse(200, 1, 'Documents retrieved successfully', $documents);
    }

    /**
     * Crea un nuevo document
     */
    public function create()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['pk_form'])) {
            $this->sendResponse(400, 0, 'Invalid data or missing pk_form', null);
            return;
        }

        $document = $this->service->createDocument($data['pk_form']);
        if ($document) {
            $this->sendResponse(201, 1, 'Document created successfully', $document->toArray());
        } else {
            $this->sendResponse(400, 0, 'Validation error', null);
        }
    }

    /**
     * Elimina un document por ID
     *
     * @param int $id
     */
    public function delete($id)
    {
        $this->service->deleteDocument($id);
        $this->sendResponse(200, 1, 'Document deleted successfully', null);
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
