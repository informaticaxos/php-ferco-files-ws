<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/FormModel.php';

/**
 * Repositorio para la entidad Form
 * Encapsula las operaciones CRUD contra la base de datos
 */
class FormRepository
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
     * Obtiene todos los registros de forms
     *
     * @return array
     */
    public function findAll()
    {
        $stmt = $this->pdo->query("SELECT * FROM forms");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene un form por ID
     *
     * @param int $id
     * @return array|null
     */
    public function findById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM forms WHERE id_form = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Guarda un form (inserta si no tiene ID, actualiza si lo tiene)
     *
     * @param Form $form
     */
    public function save(Form $form)
    {
        if ($form->getIdForm()) {
            // Actualizar
            $stmt = $this->pdo->prepare("UPDATE forms SET name = ?, date = ?, status = ?, phone = ?, country = ?, email = ? WHERE id_form = ?");
            $stmt->execute([
                $form->getName(),
                $form->getDate(),
                $form->getStatus(),
                $form->getPhone(),
                $form->getCountry(),
                $form->getEmail(),
                $form->getIdForm()
            ]);
        } else {
            // Insertar
            $stmt = $this->pdo->prepare("INSERT INTO forms (name, date, status, phone, country, email) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $form->getName(),
                $form->getDate(),
                $form->getStatus(),
                $form->getPhone(),
                $form->getCountry(),
                $form->getEmail()
            ]);
            $form->setIdForm($this->pdo->lastInsertId());
        }

        // Ejecutar checkFormFilesStatus y actualizar el status del form
        $newStatus = $this->checkFormFilesStatus($form->getIdForm());
        $form->setStatus($newStatus);
        // Actualizar el status en la BD
        $stmt = $this->pdo->prepare("UPDATE forms SET status = ? WHERE id_form = ?");
        $stmt->execute([$newStatus, $form->getIdForm()]);
    }

    /**
     * Verifica si todos los files relacionados con un form tienen path no null
     *
     * @param int $idForm
     * @return int 1 si todos los paths son válidos, 0 si alguno es null
     */
    public function checkFormFilesStatus($idForm)
    {
        $stmt = $this->pdo->prepare("SELECT path FROM files WHERE fk_form = ?");
        $stmt->execute([$idForm]);
        $files = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $state = 1;

        foreach ($files as $file) {
            if (is_null($file['path']) || $file['path'] === '') {
                $state = 0;
            }
        }

        return $state;
    }

    /**
     * Elimina un form por ID
     *
     * @param int $id
     */
    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM forms WHERE id_form = ?");
        $stmt->execute([$id]);
    }
}
