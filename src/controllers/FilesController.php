<?php

require_once __DIR__ . '/../services/FilesService.php';

/**
 * Controlador para manejar las solicitudes HTTP relacionadas con Files
 * Maneja la lógica de respuesta y delega a servicios
 */
class FilesController
{
    private $service;

    /**
     * Constructor: inicializa el servicio
     */
    public function __construct()
    {
        $this->service = new FilesService();
    }

    /**
     * Obtiene todos los files
     */
    public function getAllFiles()
    {
        $files = $this->service->getAllFiles();
        $this->sendResponse(200, 1, 'Files retrieved successfully', $files);
    }

    /**
     * Crea un nuevo file
     */
    public function createFile()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            $this->sendResponse(400, 0, 'Invalid JSON data', null);
            return;
        }

        $file = $this->service->createFile($data);
        if ($file) {
            $this->sendResponse(201, 1, 'File created successfully', $file->toArray());
        } else {
            $this->sendResponse(400, 0, 'Validation error', null);
        }
    }

    /**
     * Actualiza un file existente (sube archivo)
     *
     * @param int $id
     */
    public function updateFile($id)
    {
        // Verificar si hay archivo en $_FILES
        if (!isset($_FILES['file'])) {
            $this->sendResponse(400, 0, 'No file uploaded', null);
            return;
        }

        $file = $_FILES['file'];
        $description = $_POST['description'] ?? '';

        $updatedFile = $this->service->updateFile($id, $file, $description);
        if ($updatedFile) {
            $this->sendResponse(200, 1, 'File updated successfully', $updatedFile->toArray());
        } else {
            $this->sendResponse(400, 0, 'Update error', null);
        }
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
