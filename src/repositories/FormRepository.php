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
