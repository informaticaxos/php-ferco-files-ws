<?php

/**
 * Modelo para la tabla documents
 * Representa una entidad básica con campos: id_document, title, description, type, path, pk_form
 */
class Document
{
    private $id_document;
    private $title;
    private $description;
    private $type;
    private $path;
    private $pk_form;

    /**
     * Constructor de la clase Document
     *
     * @param int|null $id_document
     * @param string|null $title
     * @param string|null $description
     * @param string|null $type
     * @param string|null $path
     * @param int $pk_form
     */
    public function __construct($id_document = null, $title = null, $description = null, $type = null, $path = null, $pk_form = null)
    {
        $this->id_document = $id_document;
        $this->title = $title;
        $this->description = $description;
        $this->type = $type;
        $this->path = $path;
        $this->pk_form = $pk_form;
    }

    // Getters
    public function getIdDocument()
    {
        return $this->id_document;
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

    public function getPkForm()
    {
        return $this->pk_form;
    }

    // Setters
    public function setIdDocument($id_document)
    {
        $this->id_document = $id_document;
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

    public function setPkForm($pk_form)
    {
        $this->pk_form = $pk_form;
    }

    /**
     * Método para convertir el objeto a array (útil para JSON responses)
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'id_document' => $this->id_document,
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'path' => $this->path,
            'pk_form' => $this->pk_form,
        ];
    }
}
