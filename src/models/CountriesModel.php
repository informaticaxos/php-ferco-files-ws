<?php

/**
 * Modelo para la tabla countries
 * Representa una entidad básica con campos: id_country, code, name
 */
class Countries
{
    private $id_country;
    private $code;
    private $name;

    /**
     * Constructor de la clase Countries
     *
     * @param int|null $id_country
     * @param string $code
     * @param string $name
     */
    public function __construct($id_country = null, $code = '', $name = '')
    {
        $this->id_country = $id_country;
        $this->code = $code;
        $this->name = $name;
    }

    // Getters
    public function getIdCountry()
    {
        return $this->id_country;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getName()
    {
        return $this->name;
    }

    // Setters
    public function setIdCountry($id_country)
    {
        $this->id_country = $id_country;
    }

    public function setCode($code)
    {
        $this->code = $code;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Método para convertir el objeto a array (útil para JSON responses)
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'id_country' => $this->id_country,
            'code' => $this->code,
            'name' => $this->name,
        ];
    }
}
