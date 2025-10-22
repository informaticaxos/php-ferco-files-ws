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

        $form = new Form(null, $data['name'], $data['date'], 0, $data['phone'] ?? '', $data['country'] ?? '', $data['email'] ?? '');
        $this->repository->save($form);
        return $form;
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

        $form = new Form($id, $data['name'], $data['date'], $existing['status'], $data['phone'] ?? $existing['phone'], $data['country'] ?? $existing['country'], $data['email'] ?? $existing['email']);
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

    /**
     * Envía un email con el enlace del form
     *
     * @param int $id
     * @return bool
     */
    public function sendEmailForm($id)
    {
        $form = $this->repository->findById($id);
        if (!$form || empty($form['email'])) {
            return false;
        }

        $to = $form['email'];
        $subject = 'Enlace para subir documentos - FERCO ADVANCED EDUCATION';
        $link = 'https://fercoadvancededucation.com/php-ferco-files-ws/up-file/index.html?id_form=' . $id;
        $logoUrl = 'https://fercoadvancededucation.com/data/logo%20ferco%20normal.png';

        $message = "
        <html>
        <head>
            <title>Email from FERCO ADVANCED EDUCATION</title>
        </head>
        <body>
            <img src='{$logoUrl}' alt='FERCO Logo' style='width:200px;'>
            <p>Hola {$form['name']},</p>
            <p>Aquí se encuentra tu enlace para que puedas subir los documentos solicitados por FERCO ADVANCED EDUCATION:</p>
            <p><a href='{$link}'>{$link}</a></p>
            <p>Saludos,<br>Equipo de FERCO ADVANCED EDUCATION</p>
            <p>
                CORONEL CARDENAS CHRISTIAN FERNANDO<br>
                Asesora de Vinculación Internacional<br>
                ccoronel@fercoadvancededucation.com<br>
                Telf: +593 995 11 35 49<br>
                Whatsapp +34 655 25 56 95<br>
                www.fercoadvancededucation.com<br>
                <img src='{$logoUrl}' alt='FERCO Logo' style='width:100px;'>
            </p>
        </body>
        </html>
        ";

        $headers = 'From: no-reply@fercoadvancededucation.com' . "\r\n" .
                   'Reply-To: no-reply@fercoadvancededucation.com' . "\r\n" .
                   'Content-Type: text/html; charset=UTF-8' . "\r\n" .
                   'X-Mailer: PHP/' . phpversion();

        return mail($to, $subject, $message, $headers);
    }
}
