<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/FilesModel.php';

/**
 * Repositorio para la entidad Files
 * Encapsula las operaciones CRUD contra la base de datos
 */
class FilesRepository
{
    private $pdo;

    /**
     * Constructor: obtiene la conexión a la base de datos
     */
    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * Obtiene todos los registros de files
     *
     * @return array
     */
    public function findAll()
    {
        $stmt = $this->pdo->query("SELECT * FROM files");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
