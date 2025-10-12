<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/DocumentsModel.php';

/**
 * Repositorio para la entidad Document
 * Encapsula las operaciones CRUD contra la base de datos
 */
class DocumentsRepository
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
     * Obtiene todos los registros de documents
     *
     * @return array
     */
    public function findAll()
    {
        $stmt = $this->pdo->query("SELECT * FROM documents");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene un document por ID
     *
     * @param int $id
     * @return array|null
     */
    public function findById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM documents WHERE id_document = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Guarda un document (inserta si no tiene ID, actualiza si lo tiene)
     *
     * @param Document $document
     */
    public function save(Document $document)
    {
        if ($document->getIdDocument()) {
            // Actualizar
            $stmt = $this->pdo->prepare("UPDATE documents SET title = ?, description = ?, type = ?, path = ?, pk_form = ? WHERE id_document = ?");
            $stmt->execute([
                $document->getTitle(),
                $document->getDescription(),
                $document->getType(),
                $document->getPath(),
                $document->getPkForm(),
                $document->getIdDocument()
            ]);
        } else {
            // Insertar
            $stmt = $this->pdo->prepare("INSERT INTO documents (title, description, type, path, pk_form) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $document->getTitle(),
                $document->getDescription(),
                $document->getType(),
                $document->getPath(),
                $document->getPkForm()
            ]);
            $document->setIdDocument($this->pdo->lastInsertId());
        }
    }

    /**
     * Obtiene documentos por pk_form
     *
     * @param int $pk_form
     * @return array
     */
    public function findByPkForm($pk_form)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM documents WHERE pk_form = ?");
        $stmt->execute([$pk_form]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Elimina un document por ID
     *
     * @param int $id
     */
    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM documents WHERE id_document = ?");
        $stmt->execute([$id]);
    }
}
