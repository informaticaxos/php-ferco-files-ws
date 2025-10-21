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
     * @param string &$logs
     * @return array|null
     */
    public function login($email, $password, &$logs)
    {
        $logs .= "4. UserService::login called\n";
        $logs .= "4.1. Calling UserRepository::findByEmail with email: " . $email . "\n";
        $user = $this->repository->findByEmail($email);
        if (!$user) {
            $logs .= "4.2. User not found in repository\n";
            return null; // Usuario no encontrado
        }
        $logs .= "4.3. User found: " . json_encode($user) . "\n";

        // Comparación directa de contraseñas (sin hash)
        $logs .= "5. Comparing passwords: input '" . $password . "' vs stored '" . $user['password'] . "'\n";
        if ($password !== $user['password']) {
            $logs .= "5.1. Password mismatch\n";
            return null; // Contraseña incorrecta
        }
        $logs .= "5.2. Password match\n";

        // Verificar estado
        $logs .= "5.3. Checking user state: " . $user['state'] . "\n";
        if ($user['state'] != 1) {
            $logs .= "5.4. User inactive\n";
            return null; // Usuario inactivo
        }
        $logs .= "5.5. User active\n";

        // Retornar datos del usuario sin contraseña
        unset($user['password']);
        $logs .= "5.6. Returning user data without password\n";
        return $user;
    }
}
