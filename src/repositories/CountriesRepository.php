<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/CountriesModel.php';

/**
 * Repositorio para la entidad Countries
 * Encapsula las operaciones CRUD contra la base de datos
 */
class CountriesRepository
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
     * Obtiene todos los registros de countries
     *
     * @return array
     */
    public function findAll()
    {
        $stmt = $this->pdo->query("SELECT * FROM countries");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene un country por ID
     *
     * @param int $id
     * @return array|null
     */
    public function findById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM countries WHERE id_country = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Guarda un country (inserta si no tiene ID, actualiza si lo tiene)
     *
     * @param Countries $country
     */
    public function save(Countries $country)
    {
        if ($country->getIdCountry()) {
            // Actualizar
            $stmt = $this->pdo->prepare("UPDATE countries SET code = ?, name = ? WHERE id_country = ?");
            $stmt->execute([
                $country->getCode(),
                $country->getName(),
                $country->getIdCountry()
            ]);
        } else {
            // Insertar
            $stmt = $this->pdo->prepare("INSERT INTO countries (code, name) VALUES (?, ?)");
            $stmt->execute([
                $country->getCode(),
                $country->getName()
            ]);
            $country->setIdCountry($this->pdo->lastInsertId());
        }
    }

    /**
     * Elimina un country por ID
     *
     * @param int $id
     */
    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM countries WHERE id_country = ?");
        $stmt->execute([$id]);
    }
}
