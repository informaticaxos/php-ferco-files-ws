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
        $log = "Iniciando updateFile para ID: $id\n";

        // Para PUT requests con multipart/form-data, necesitamos parsear manualmente
        if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            $log .= "Es una solicitud PUT, parseando multipart data manualmente\n";
            $this->parseMultipartData();
            $log .= "Después de parsear: \$_FILES: " . print_r($_FILES, true) . "\n";
            $log .= "Después de parsear: \$_POST: " . print_r($_POST, true) . "\n";
        }

        // Verificar si hay archivo en $_FILES
        if (!isset($_FILES['file'])) {
            $log .= "No se encontró archivo en \$_FILES\n";
            $log .= "Contenido de \$_FILES: " . print_r($_FILES, true) . "\n";
            $log .= "Contenido de \$_POST: " . print_r($_POST, true) . "\n";
            $log .= "Contenido de \$_REQUEST: " . print_r($_REQUEST, true) . "\n";
            $this->sendResponse(400, 0, 'No file uploaded', null, $log);
            return;
        }

        $log .= "Archivo encontrado en \$_FILES: " . print_r($_FILES['file'], true) . "\n";

        $file = $_FILES['file'];
        $description = $_POST['description'] ?? '';
        $log .= "Descripción: $description\n";

        $updatedFile = $this->service->updateFile($id, $file, $description);
        if ($updatedFile) {
            $log .= "Archivo actualizado exitosamente\n";
            $this->sendResponse(200, 1, 'File updated successfully', $updatedFile->toArray(), $log);
        } else {
            $log .= "Error al actualizar el archivo - Verificando detalles...\n";
            $log .= "Archivo temporal existe: " . (file_exists($file['tmp_name']) ? 'Sí' : 'No') . "\n";
            $log .= "Directorio uploaded-files existe: " . (is_dir(__DIR__ . '/../uploaded-files/') ? 'Sí' : 'No') . "\n";
            $this->sendResponse(400, 0, 'Update error', null, $log);
        }
    }

    /**
     * Parsea datos multipart/form-data para PUT requests
     */
    private function parseMultipartData()
    {
        $input = file_get_contents('php://input');
        $boundary = $this->getBoundary();

        if (!$boundary) {
            return;
        }

        $parts = explode('--' . $boundary, $input);
        foreach ($parts as $part) {
            $part = trim($part);
            if (empty($part) || $part === '--') {
                continue;
            }

            // Separar headers y body
            $headerEnd = strpos($part, "\r\n\r\n");
            if ($headerEnd === false) {
                continue;
            }

            $headers = substr($part, 0, $headerEnd);
            $body = substr($part, $headerEnd + 4);

            // Parsear Content-Disposition
            if (preg_match('/Content-Disposition: form-data; name="([^"]+)";? ?(?:filename="([^"]+)")?/', $headers, $matches)) {
                $name = $matches[1];
                $filename = $matches[2] ?? null;

                if ($filename) {
                    // Es un archivo
                    $tmpFile = tempnam(sys_get_temp_dir(), 'upload');
                    file_put_contents($tmpFile, $body);

                    $_FILES[$name] = [
                        'name' => $filename,
                        'type' => $this->getContentType($headers),
                        'tmp_name' => $tmpFile,
                        'error' => UPLOAD_ERR_OK,
                        'size' => strlen($body)
                    ];
                } else {
                    // Es un campo de texto
                    $_POST[$name] = $body;
                }
            }
        }
    }

    /**
     * Obtiene el boundary del Content-Type
     */
    private function getBoundary()
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (preg_match('/boundary=([^;]+)/', $contentType, $matches)) {
            return trim($matches[1], '"');
        }
        return null;
    }

    /**
     * Obtiene el Content-Type de los headers
     */
    private function getContentType($headers)
    {
        if (preg_match('/Content-Type: ([^\r\n]+)/', $headers, $matches)) {
            return trim($matches[1]);
        }
        return 'application/octet-stream';
    }

    /**
     * Envía la respuesta HTTP
     *
     * @param int $httpStatus
     * @param int $status
     * @param string $message
     * @param mixed $data
     * @param string $log
     */
    private function sendResponse($httpStatus, $status, $message, $data, $log = '')
    {
        http_response_code($httpStatus);
        header('Content-Type: application/json');
        echo json_encode([
            'status' => $status,
            'message' => $message,
            'data' => $data,
            'log' => $log
        ]);
        exit;
    }
}
