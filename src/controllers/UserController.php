<?php

require_once __DIR__ . '/../services/UserService.php';

/**
 * Controlador para manejar las solicitudes HTTP relacionadas con User
 * Maneja la lógica de respuesta y delega a servicios
 */
class UserController
{
    private $service;

    /**
     * Constructor: inicializa el servicio
     */
    public function __construct()
    {
        $this->service = new UserService();
    }

    /**
     * Obtiene todos los users
     */
    public function getAll()
    {
        $users = $this->service->getAllUsers();
        $this->sendResponse(200, 1, 'Users retrieved successfully', $users);
    }

    /**
     * Obtiene un user por ID
     *
     * @param int $id
     */
    public function getById($id)
    {
        $user = $this->service->getUserById($id);
        if ($user) {
            unset($user['password']); // No devolver contraseña
            $this->sendResponse(200, 1, 'User retrieved successfully', $user);
        } else {
            $this->sendResponse(404, 0, 'User not found', null);
        }
    }

    /**
     * Crea un nuevo user
     */
    public function create()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            $this->sendResponse(400, 0, 'Invalid JSON data', null);
            return;
        }

        $user = $this->service->createUser($data);
        if ($user) {
            $response = $user->toArray();
            unset($response['password']); // No devolver contraseña
            $this->sendResponse(201, 1, 'User created successfully', $response);
        } else {
            $this->sendResponse(400, 0, 'Validation error or email already exists', null);
        }
    }

    /**
     * Actualiza un user existente
     *
     * @param int $id
     */
    public function update($id)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            $this->sendResponse(400, 0, 'Invalid JSON data', null);
            return;
        }

        $user = $this->service->updateUser($id, $data);
        if ($user) {
            $response = $user->toArray();
            unset($response['password']); // No devolver contraseña
            $this->sendResponse(200, 1, 'User updated successfully', $response);
        } else {
            $this->sendResponse(404, 0, 'User not found or validation error', null);
        }
    }

    /**
     * Actualiza el estado de un user
     *
     * @param int $id
     */
    public function updateState($id)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['state'])) {
            $this->sendResponse(400, 0, 'Invalid JSON data or missing state', null);
            return;
        }

        $user = $this->service->updateUserState($id, $data['state']);
        if ($user) {
            $response = $user->toArray();
            unset($response['password']); // No devolver contraseña
            $this->sendResponse(200, 1, 'User state updated successfully', $response);
        } else {
            $this->sendResponse(404, 0, 'User not found', null);
        }
    }

    /**
     * Elimina un user por ID
     *
     * @param int $id
     */
    public function delete($id)
    {
        $result = $this->service->deleteUser($id);
        if ($result) {
            $this->sendResponse(200, 1, 'User deleted successfully', null);
        } else {
            $this->sendResponse(404, 0, 'User not found', null);
        }
    }

    /**
     * Login de usuario
     */
    public function login()
    {
        $logs = "";
        $data = json_decode(file_get_contents('php://input'), true);
        $logs .= "1. Received JSON data: " . json_encode($data) . "\n";
        if (!$data || !isset($data['email']) || !isset($data['password'])) {
            $logs .= "2. Invalid JSON or missing email/password\n";
            $this->sendResponse(400, 0, 'Invalid JSON data or missing email/password', null, $logs);
            return;
        }

        $logs .= "3. Calling UserService::login with email: " . $data['email'] . "\n";
        $user = $this->service->login($data['email'], $data['password'], $logs);
        if ($user) {
            $logs .= "6. Login successful, user found\n";
            $this->sendResponse(200, 1, 'Login successful', $user, $logs);
        } else {
            $logs .= "6. Login failed, invalid credentials or user inactive\n";
            $this->sendResponse(401, 0, 'Invalid credentials or user inactive', null, $logs);
        }
    }

    /**
     * Envía la respuesta HTTP
     *
     * @param int $httpStatus
     * @param int $status
     * @param string $message
     * @param mixed $data
     * @param string $logs
     */
    private function sendResponse($httpStatus, $status, $message, $data, $logs = "")
    {
        http_response_code($httpStatus);
        header('Content-Type: application/json');
        echo json_encode([
            'status' => $status,
            'message' => $message,
            'data' => $data,
            'logs' => $logs
        ]);
        exit;
    }
}
