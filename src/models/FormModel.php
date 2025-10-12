<?php

/**
 * Modelo para la tabla forms
 * Representa una entidad básica con campos: id_form, name, date, status
 */
class Form
{
    private $id_form;
    private $name;
    private $date;
    private $status;

    /**
     * Constructor de la clase Form
     *
     * @param int|null $id_form
     * @param string $name
     * @param string $date
     * @param int $status
     */
    public function __construct($id_form = null, $name = '', $date = '', $status = 0)
    {
        $this->id_form = $id_form;
        $this->name = $name;
        $this->date = $date;
        $this->status = $status;
    }

    // Getters
    public function getIdForm()
    {
        return $this->id_form;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function getStatus()
    {
        return $this->status;
    }

    // Setters
    public function setIdForm($id_form)
    {
        $this->id_form = $id_form;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setDate($date)
    {
        $this->date = $date;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Método para convertir el objeto a array (útil para JSON responses)
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'id_form' => $this->id_form,
            'name' => $this->name,
            'date' => $this->date,
            'status' => $this->status,
        ];
    }
}
