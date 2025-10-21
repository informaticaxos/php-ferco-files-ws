<?php

require_once __DIR__ . '/../repositories/UserRepository.php';
require_once __DIR__ . '/../models/UserModel.php';

/**
 * Servicio para la lógica de negocio de User
 * Actúa como intermediario entre controladores y repositorios
 */
class UserService
{
    private $repository;

    /**
     * Constructor: inicializa el repositorio
     */
    public function __construct()
    {
        $this->repository = new UserRepository();
    }

    /**
     * Obtiene todos los users
     *
     * @return array
     */
    public function getAllUsers()
    {
        return $this->repository->findAll();
    }

    /**
     * Obtiene un user por ID
     *
     * @param int $id
     * @return array|null
     */
    public function getUserById($id)
    {
        return $this->repository->findById($id);
    }

    /**
     * Crea un nuevo user
     *
     * @param array $data
     * @return User|null
     */
    public function createUser($data)
    {
        // Validación básica (puede expandirse)
        if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
            return null; // Error de validación
        }

        // Verificar si el email ya existe
        if ($this->repository->findByEmail($data['email'])) {
            return null; // Email ya existe
        }

        // Contraseña sin hash (deshabilitado el cifrado)
        $plainPassword = $data['password'];

        $user = new User(null, $data['name'], $data['email'], $plainPassword, $data['state'] ?? '1');
        $this->repository->save($user);
        return $user;
    }

    /**
     * Actualiza un user existente
     *
     * @param int $id
     * @param array $data
     * @return User|null
     */
    public function updateUser($id, $data)
    {
        $existing = $this->repository->findById($id);
        if (!$existing) {
            return null;
        }

        // Validación básica
        if (empty($data['name']) || empty($data['email'])) {
            return null;
        }

        // Verificar si el email ya existe en otro usuario
        $existingEmail = $this->repository->findByEmail($data['email']);
        if ($existingEmail && $existingEmail['id_user'] != $id) {
            return null; // Email ya existe en otro usuario
        }

        $plainPassword = isset($data['password']) && !empty($data['password']) ? $data['password'] : $existing['password'];

        $user = new User($id, $data['name'], $data['email'], $plainPassword, $data['state'] ?? $existing['state']);
        $this->repository->save($user);
        return $user;
    }

    /**
     * Actualiza el estado de un user
     *
     * @param int $id
     * @param string $state
     * @return User|null
     */
    public function updateUserState($id, $state)
    {
        $existing = $this->repository->findById($id);
        if (!$existing) {
            return null;
        }

        $user = new User($id, $existing['name'], $existing['email'], $existing['password'], $state);
        $this->repository->save($user);
        return $user;
    }

    /**
     * Elimina un user por ID
     *
     * @param int $id
     */
    public function deleteUser($id)
    {
        // Verificar que el user existe
        $user = $this->repository->findById($id);
        if (!$user) {
            return false;
        }

        $this->repository->delete($id);
        return true;
    }

    /**
     * Login de usuario
     *
     * @param string $email
     * @param string $password
     * @return array|null
     */
    public function login($email, $password)
    {
        $user = $this->repository->findByEmail($email);
        if (!$user) {
            return null; // Usuario no encontrado
        }

        // Comparación directa de contraseñas (sin hash)
        if ($password !== $user['password']) {
            return null; // Contraseña incorrecta
        }

        // Verificar estado
        if ($user['state'] !== '1') {
            return null; // Usuario inactivo
        }

        // Retornar datos del usuario sin contraseña
        unset($user['password']);
        return $user;
    }
}
