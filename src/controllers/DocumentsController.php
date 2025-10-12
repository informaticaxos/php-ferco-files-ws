<?php

define('BASE_URL', 'http://localhost/git/');

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
        if (!$data || !isset($data['pk_form']) || !isset($data['title'])) {
            $this->sendResponse(400, 0, 'Invalid data or missing pk_form or title', null);
            return;
        }

        $document = $this->service->createDocument($data['pk_form'], $data['title']);
        if ($document) {
            $this->sendResponse(201, 1, 'Document created successfully', $document->toArray());
        } else {
            $this->sendResponse(400, 0, 'Validation error', null);
        }
    }

    /**
     * Sube un archivo para un document existente
     *
     * @param int $id
     */
    public function uploadFile($id)
    {
        if (!isset($_POST['description']) || !isset($_FILES['file'])) {
            $this->sendResponse(400, 0, 'Invalid data or missing description or file', null);
            return;
        }

        // Obtener el documento existente para borrar el archivo anterior
        $existing = $this->service->getDocumentById($id);
        if ($existing && !empty($existing['path'])) {
            // Extraer el nombre del archivo de la URL
            $oldPath = $existing['path'];
            $oldFilename = basename($oldPath);
            $oldFilePath = __DIR__ . '/../../uploads/documents/' . $oldFilename;
            if (file_exists($oldFilePath)) {
                unlink($oldFilePath);
            }
        }

        $description = $_POST['description'];
        $file = $_FILES['file'];

        // Obtener extensión
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $type = '.' . $extension;

        // Generar nombre de archivo: YYMMDDHHMMSS + extension
        $filename = date('ymdHis') . $type;

        // Directorio de subida
        $uploadDir = __DIR__ . '/../../uploads/documents/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $filepath = $uploadDir . $filename;

        // Mover archivo
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            $this->sendResponse(500, 0, 'Error uploading file', null);
            return;
        }

        // Path completo para guardar en DB
        $relativePath = BASE_URL . 'php-ferco-files-ws/uploads/documents/' . $filename;

        $document = $this->service->updateDocument($id, $description, $type, $relativePath);
        if ($document) {
            $this->sendResponse(200, 1, 'File uploaded and document updated successfully', $document->toArray());
        } else {
            $this->sendResponse(404, 0, 'Document not found', null);
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
