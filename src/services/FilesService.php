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
        return $this->repository->findAll();
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
}
