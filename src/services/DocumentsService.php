<?php

require_once __DIR__ . '/../repositories/DocumentsRepository.php';
require_once __DIR__ . '/../models/DocumentsModel.php';
require_once __DIR__ . '/../services/FormService.php';

/**
 * Servicio para la lógica de negocio de Document
 * Actúa como intermediario entre controladores y repositorios
 */
class DocumentsService
{
    private $repository;

    /**
     * Constructor: inicializa el repositorio
     */
    public function __construct()
    {
        $this->repository = new DocumentsRepository();
    }

    /**
     * Obtiene todos los documents
     *
     * @return array
     */
    public function getAllDocuments()
    {
        return $this->repository->findAll();
    }

    /**
     * Obtiene un document por ID
     *
     * @param int $id
     * @return array|null
     */
    public function getDocumentById($id)
    {
        return $this->repository->findById($id);
    }

    /**
     * Crea un nuevo document
     *
     * @param int $pk_form
     * @param string $title
     * @return Document|null
     */
    public function createDocument($pk_form, $title)
    {
        // Validación básica
        if (empty($pk_form) || empty($title)) {
            return null; // Error de validación
        }

        $document = new Document(null, $title, null, null, null, $pk_form);
        $this->repository->save($document);
        return $document;
    }

    /**
     * Actualiza un document existente
     *
     * @param int $id
     * @param string $description
     * @param string $type
     * @param string $path
     * @return Document|null
     */
    public function updateDocument($id, $description, $type, $path)
    {
        $existing = $this->repository->findById($id);
        if (!$existing) {
            return null;
        }

        // Crear objeto con datos actualizados
        $document = new Document($id, $existing['title'], $description, $type, $path, $existing['pk_form']);
        $this->repository->save($document);

        // Verificar si todos los documentos del form tienen path no null
        $pk_form = $document->getPkForm();
        $allDocuments = $this->repository->findByPkForm($pk_form);
        $allHavePath = true;
        foreach ($allDocuments as $doc) {
            if (empty($doc['path'])) {
                $allHavePath = false;
                break;
            }
        }
        if ($allHavePath) {
            $formService = new FormService();
            $formService->updateFormState($pk_form, 1);
        }

        return $document;
    }

    /**
     * Elimina un document por ID
     *
     * @param int $id
     */
    public function deleteDocument($id)
    {
        $this->repository->delete($id);
    }
}
