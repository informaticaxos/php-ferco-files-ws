<?php

require_once __DIR__ . '/../repositories/CountriesRepository.php';

/**
 * Servicio para la lógica de negocio de Countries
 */
class CountriesService
{
    private $repository;

    /**
     * Constructor: inyecta el repositorio
     */
    public function __construct()
    {
        $this->repository = new CountriesRepository();
    }

    /**
     * Obtiene todos los países
     *
     * @return array
     */
    public function getAllCountries()
    {
        return $this->repository->findAll();
    }

    /**
     * Obtiene un país por ID
     *
     * @param int $id
     * @return array|null
     */
    public function getCountryById($id)
    {
        return $this->repository->findById($id);
    }

    /**
     * Crea un nuevo país
     *
     * @param array $data
     * @return Countries
     */
    public function createCountry($data)
    {
        $country = new Countries(null, $data['code'], $data['name']);
        $this->repository->save($country);
        return $country;
    }

    /**
     * Actualiza un país existente
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateCountry($id, $data)
    {
        $existing = $this->repository->findById($id);
        if (!$existing) {
            return false;
        }
        $country = new Countries($id, $data['code'], $data['name']);
        $this->repository->save($country);
        return true;
    }

    /**
     * Elimina un país por ID
     *
     * @param int $id
     * @return bool
     */
    public function deleteCountry($id)
    {
        $existing = $this->repository->findById($id);
        if (!$existing) {
            return false;
        }
        $this->repository->delete($id);
        return true;
    }
}
