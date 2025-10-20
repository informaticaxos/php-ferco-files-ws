<?php

require_once __DIR__ . '/../repositories/FilesRepository.php';
require_once __DIR__ . '/../repositories/FormRepository.php';
require_once __DIR__ . '/../models/FilesModel.php';
require_once __DIR__ . '/../models/FormModel.php';

/**
 * Servicio para la lógica de negocio de Files
 * Actúa como intermediario entre controladores y repositorios
 */
class FilesService
{
    private $filesRepository;
    private $formRepository;

    /**
     * Constructor: inicializa los repositorios
     */
    public function __construct()
    {
        $this->filesRepository = new FilesRepository();
        $this->formRepository = new FormRepository();
    }

    /**
     * Obtiene todos los files
     *
     * @return array
     */
    public function getAllFiles()
    {
        $files = $this->filesRepository->findAll();
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
        $this->filesRepository->save($file);
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
        $existing = $this->filesRepository->findById($id);
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
        $this->filesRepository->save($file);

        // Validación: verificar que el path no sea null o vacío
        if (empty($relativePath)) {
            return null;
        }

        // Obtener el fk_form del archivo
        $fkForm = $existing['fk_form'];

        // Buscar todos los files relacionados con ese form
        $allFiles = $this->filesRepository->findAllByIdForm($fkForm);

        // Verificar uno a uno si path cumple la condición (no null o vacío)
        $allPathsValid = true;
        foreach ($allFiles as $fileItem) {
            if (empty($fileItem['path'])) {
                $allPathsValid = false;
                break;
            }
        }

        // Si todos los files del form tienen path válido, cambiar status del form a 1 (completado)
        if ($allPathsValid) {
            $form = $this->formRepository->findById($fkForm);
            if ($form) {
                $formObj = new Form($form['id_form'], $form['name'], $form['date'], 1);
                $this->formRepository->save($formObj);
            }
        }

        return $file;
    }

    /**
     * Obtiene todos los files relacionados con un id_form
     *
     * @param int $idForm
     * @return array
     */
    public function getAllFilesByIdForm($idForm)
    {
        $files = $this->filesRepository->findAllByIdForm($idForm);
        foreach ($files as &$file) {
            if (!empty($file['path'])) {
                $file['path'] = 'https://fercoadvancededucation.com/php-ferco-files-ws' . $file['path'];
            }
        }
        return $files;
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
        $existing = $this->filesRepository->findById($id);
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
        return $this->filesRepository->delete($id);
    }
}
