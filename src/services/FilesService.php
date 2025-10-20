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
}
