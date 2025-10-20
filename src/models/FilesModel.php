<?php

/**
 * Modelo para la tabla files
 * Representa una entidad básica con campos: id_file, title, description, type, path, fk_form
 */
class Files
{
    private $id_file;
    private $title;
    private $description;
    private $type;
    private $path;
    private $fk_form;

    /**
     * Constructor de la clase Files
     *
     * @param int|null $id_file
     * @param string $title
     * @param string $description
     * @param string $type
     * @param string $path
     * @param int $fk_form
     */
    public function __construct($id_file = null, $title = '', $description = '', $type = '', $path = '', $fk_form = 0)
    {
        $this->id_file = $id_file;
        $this->title = $title;
        $this->description = $description;
        $this->type = $type;
        $this->path = $path;
        $this->fk_form = $fk_form;
    }

    // Getters
    public function getIdFile()
    {
        return $this->id_file;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getFkForm()
    {
        return $this->fk_form;
    }

    // Setters
    public function setIdFile($id_file)
    {
        $this->id_file = $id_file;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function setFkForm($fk_form)
    {
        $this->fk_form = $fk_form;
    }

    /**
     * Método para convertir el objeto a array (útil para JSON responses)
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'id_file' => $this->id_file,
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'path' => $this->path,
            'fk_form' => $this->fk_form,
        ];
    }
}
