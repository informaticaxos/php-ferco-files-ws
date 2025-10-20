<?php

require_once __DIR__ . '/../repositories/FilesRepository.php';
require_once __DIR__ . '/../models/FilesModel.php';

/**
 * Servicio para la lógica de negocio de Files
 * Actúa como intermediario entre controladores y repositorios
 */
class FilesService
{
    private $repository;

    /**
     * Constructor: inicializa el repositorio
     */
    public function __construct()
    {
        $this->repository = new FilesRepository();
    }

    /**
     * Obtiene todos los files
     *
     * @return array
     */
    public function getAllFiles()
    {
        $files = $this->repository->findAll();
        foreach ($files as &$file) {
            if (!empty($file['path'])) {
                $file['path'] = 'https://fercoadvancededucation.com/php-ferco-files-ws' . $file['path'];
            }
        }
        return $files;
    }

    /**
     * Crea un nuevo file
     *
     * @param array $data
     * @return Files|null
     */
    public function createFile($data)
    {
        // Validación básica (puede expandirse)
        if (empty($data['fk_form']) || empty($data['title'])) {
            return null; // Error de validación
        }

        $file = new Files(null, $data['title'], $data['description'] ?? '', $data['type'] ?? '', $data['path'] ?? '', $data['fk_form']);
        $this->repository->save($file);
        return $file;
    }

    /**
     * Actualiza un file existente subiendo un archivo
     *
     * @param int $id
     * @param array $uploadedFile
     * @param string $description
     * @return Files|null
     */
    public function updateFile($id, $uploadedFile, $description)
    {
        // Verificar que el archivo existe en BD
        $existing = $this->repository->findById($id);
        if (!$existing) {
            return null;
        }

        // Si ya hay un archivo anterior, borrarlo
        if (!empty($existing['path'])) {
            $oldFilePath = __DIR__ . '/../../' . $existing['path'];
            if (file_exists($oldFilePath)) {
                unlink($oldFilePath);
            }
        }

        // Generar nombre de archivo: DDMMYYYYHHMMSS.ext
        $timestamp = date('dmYHis');
        $extension = pathinfo($uploadedFile['name'], PATHINFO_EXTENSION);
        $filename = $timestamp . '.' . $extension;

        // Ruta de destino
        $uploadDir = __DIR__ . '/../../uploaded-files/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $filePath = $uploadDir . $filename;

        // Mover archivo (para archivos parseados manualmente, copiar en lugar de mover)
        if (file_exists($uploadedFile['tmp_name'])) {
            if (!copy($uploadedFile['tmp_name'], $filePath)) {
                return null;
            }
            // Limpiar archivo temporal
            unlink($uploadedFile['tmp_name']);
        } else {
            return null;
        }

        // Ruta relativa para guardar en BD
        $relativePath = '/uploaded-files/' . $filename;

        // Actualizar objeto
        $file = new Files($id, $existing['title'], $description, $extension, $relativePath, $existing['fk_form']);
        $this->repository->save($file);
        return $file;
    }

    /**
     * Elimina un file por ID (renombra el archivo y elimina el registro)
     *
     * @param int $id
     * @return bool
     */
    public function deleteFile($id)
    {
        // Verificar que el archivo existe en BD
        $existing = $this->repository->findById($id);
        if (!$existing) {
            return false;
        }

        // Si hay un archivo asociado, renombrarlo anteponiendo el ID
        if (!empty($existing['path'])) {
            $oldFilePath = __DIR__ . '/../../' . $existing['path'];
            if (file_exists($oldFilePath)) {
                // Extraer el nombre del archivo del path
                $filename = basename($existing['path']);
                // Nuevo nombre: id_filename
                $newFilename = $id . '_' . $filename;
                $newFilePath = dirname($oldFilePath) . '/' . $newFilename;
                rename($oldFilePath, $newFilePath);
            }
        }

        // Eliminar el registro de la BD
        return $this->repository->delete($id);
    }
}
