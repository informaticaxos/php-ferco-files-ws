<?php

/**
 * Modelo para la tabla forms
 * Representa una entidad básica con campos: id_form, name, date, status, phone, country, email
 */
class Form
{
    private $id_form;
    private $name;
    private $date;
    private $status;
    private $phone;
    private $country;
    private $email;

    /**
     * Constructor de la clase Form
     *
     * @param int|null $id_form
     * @param string $name
     * @param string $date
     * @param int $status
     * @param string $phone
     * @param string $country
     * @param string $email
     */
    public function __construct($id_form = null, $name = '', $date = '', $status = 0, $phone = '', $country = '', $email = '')
    {
        $this->id_form = $id_form;
        $this->name = $name;
        $this->date = $date;
        $this->status = $status;
        $this->phone = $phone;
        $this->country = $country;
        $this->email = $email;
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

    public function getPhone()
    {
        return $this->phone;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function getEmail()
    {
        return $this->email;
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

    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    public function setCountry($country)
    {
        $this->country = $country;
    }

    public function setEmail($email)
    {
        $this->email = $email;
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
            'phone' => $this->phone,
            'country' => $this->country,
            'email' => $this->email,
        ];
    }
}
