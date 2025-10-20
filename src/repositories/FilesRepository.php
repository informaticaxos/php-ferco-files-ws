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

    /**
     * Obtiene un file por ID
     *
     * @param int $id
     * @return array|null
     */
    public function findById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM files WHERE id_file = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Guarda un file (inserta si no tiene ID, actualiza si lo tiene)
     *
     * @param Files $file
     */
    public function save(Files $file)
    {
        if ($file->getIdFile()) {
            // Actualizar
            $stmt = $this->pdo->prepare("UPDATE files SET title = ?, description = ?, type = ?, path = ?, fk_form = ? WHERE id_file = ?");
            $stmt->execute([
                $file->getTitle(),
                $file->getDescription(),
                $file->getType(),
                $file->getPath(),
                $file->getFkForm(),
                $file->getIdFile()
            ]);
        } else {
            // Insertar
            $stmt = $this->pdo->prepare("INSERT INTO files (title, description, type, path, fk_form) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $file->getTitle(),
                $file->getDescription(),
                $file->getType(),
                $file->getPath(),
                $file->getFkForm()
            ]);
            $file->setIdFile($this->pdo->lastInsertId());
        }
    }

    /**
     * Obtiene todos los files relacionados con un id_form
     *
     * @param int $idForm
     * @return array
     */
    public function findAllByIdForm($idForm)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM files WHERE fk_form = ?");
        $stmt->execute([$idForm]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Elimina un file por ID (solo el registro, renombra el archivo)
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM files WHERE id_file = ?");
        return $stmt->execute([$id]);
    }
}
