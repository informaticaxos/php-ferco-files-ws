<?php

require_once __DIR__ . '/../repositories/FormRepository.php';
require_once __DIR__ . '/../models/FormModel.php';

/**
 * Servicio para la lógica de negocio de Form
 * Actúa como intermediario entre controladores y repositorios
 */
class FormService
{
    private $repository;

    /**
     * Constructor: inicializa el repositorio
     */
    public function __construct()
    {
        $this->repository = new FormRepository();
    }

    /**
     * Obtiene todos los forms
     *
     * @return array
     */
    public function getAllForms()
    {
        return $this->repository->findAll();
    }

    /**
     * Obtiene un form por ID
     *
     * @param int $id
     * @return array|null
     */
    public function getFormById($id)
    {
        return $this->repository->findById($id);
    }

    /**
     * Crea un nuevo form
     *
     * @param array $data
     * @return Form|null
     */
    public function createForm($data)
    {
        // Validación básica (puede expandirse)
        if (empty($data['name']) || empty($data['date'])) {
            return null; // Error de validación
        }

        // Validar fecha
        if (!strtotime($data['date'])) {
            return null;
        }

        $form = new Form(null, $data['name'], $data['date'], $data['status'] ?? 0, $data['phone'] ?? '', $data['country'] ?? '', $data['email'] ?? '');
        $savedForm = $this->repository->save($form);

        // Después de guardar el form, verificar los files relacionados
        if ($savedForm) {
            $this->checkAndUpdateFormStatus($savedForm->getIdForm());
        }

        return $savedForm;
    }

    /**
     * Actualiza un form existente
     *
     * @param int $id
     * @param array $data
     * @return Form|null
     */
    public function updateForm($id, $data)
    {
        $existing = $this->repository->findById($id);
        if (!$existing) {
            return null;
        }

        // Validación básica
        if (empty($data['name']) || empty($data['date'])) {
            return null;
        }

        // Validar fecha
        if (!strtotime($data['date'])) {
            return null;
        }

        $form = new Form($id, $data['name'], $data['date'], $data['status'] ?? 0, $data['phone'] ?? $existing['phone'], $data['country'] ?? $existing['country'], $data['email'] ?? $existing['email']);
        $this->repository->save($form);
        return $form;
    }

    /**
     * Actualiza el estado de un form
     *
     * @param int $id
     * @param int $state
     * @return Form|null
     */
    public function updateFormState($id, $state)
    {
        $existing = $this->repository->findById($id);
        if (!$existing) {
            return null;
        }

        $form = new Form($id, $existing['name'], $existing['date'], $state, $existing['phone'], $existing['country'], $existing['email']);
        $this->repository->save($form);
        return $form;
    }

    /**
     * Verifica y actualiza el status del form basado en los files relacionados
     *
     * @param int $formId
     */
    private function checkAndUpdateFormStatus($formId)
    {
        // Obtener todos los files relacionados con el form
        require_once __DIR__ . '/../repositories/FilesRepository.php';
        $filesRepository = new FilesRepository();
        $allFiles = $filesRepository->findAllByIdForm($formId);

        // Verificar uno a uno si path cumple la condición (no null o vacío)
        $allPathsValid = true;
        foreach ($allFiles as $fileItem) {
            if (empty($fileItem['path'])) {
                $allPathsValid = false;
                break;
            }
        }

        // Actualizar el status del form: 1 si todos completos, 0 si no
        $form = $this->repository->findById($formId);
        if ($form) {
            $newStatus = $allPathsValid ? 1 : 0;
            $formObj = new Form($form['id_form'], $form['name'], $form['date'], $newStatus, $form['phone'], $form['country'], $form['email']);
            $this->repository->save($formObj);
        }
    }

    /**
     * Elimina un form por ID (primero elimina los files asociados)
     *
     * @param int $id
     */
    public function deleteForm($id)
    {
        // Verificar que el form existe
        $form = $this->repository->findById($id);
        if (!$form) {
            return false;
        }

        // Obtener todos los files asociados al form
        require_once __DIR__ . '/FilesService.php';
        $filesService = new FilesService();
        $files = $filesService->getAllFilesByIdForm($id);

        // Eliminar cada file (renombrar archivo y eliminar registro)
        foreach ($files as $file) {
            $filesService->deleteFile($file['id_file']);
        }

        // Finalmente, eliminar el form
        $this->repository->delete($id);
        return true;
    }
}
