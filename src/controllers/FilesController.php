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
