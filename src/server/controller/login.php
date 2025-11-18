<?php

require_once __DIR__ . '/../model/User.php';

// Usamos el namespace del modelo
use Model\User;

/**
 * Endpoint de Login
 * Recibe: JSON con "username" y "password".
 * Devuelve: JSON con "success", "token" y "user" (si es exitoso).
 */

// 1. Configurar cabeceras para responder con JSON
header('Content-Type: application/json');

// 2. Asegurarse de que el método es POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // 405 Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Método no permitido. Se esperaba POST.']);
    exit;
}

// 3. Obtener el cuerpo (body) de la petición
// Intentamos JSON primero; si no hay JSON, usamos $_POST (form-encoded)
$rawBody = file_get_contents('php://input');
error_log('DEBUG: rawBody = ' . var_export($rawBody, true));
error_log('DEBUG: $_POST = ' . var_export($_POST, true));
$input = json_decode($rawBody, true);
error_log('DEBUG: decoded input = ' . var_export($input, true));
if (!is_array($input)) {
    // Fallback a datos tradicionales de formulario
    $input = $_POST;
    error_log('DEBUG: Using $_POST fallback');
}

// 4. Normalizar claves y validar que tenemos usuario y contraseña
// Soportamos tanto `username` como `email` desde el cliente
$username = $input['username'] ?? $input['email'] ?? null;
$password = $input['password'] ?? null;

if (empty($username) || empty($password)) {
    http_response_code(400); // 400 Bad Request
    echo json_encode(['success' => false, 'message' => 'Faltan los campos "username" o "password".']);
    exit;
}

// --- ESTE ES EL ORDEN CORRECTO ---
// 5. Validar credenciales contra la BD
// (Aquí NO se comprueba ningún token, solo usuario y contraseña)
try {
    $userData = User::hasPermission($username, $password);

    if ($userData) {
        // ¡Éxito! Las credenciales son correctas.
        
        // 6. CREAR el Token (JWT)
        // (Este es un token de EJEMPLO. En producción se usaría una librería JWT real)
        $header = base64_encode(json_encode(['alg' => 'NONE', 'typ' => 'JWT']));
        $payload = base64_encode(json_encode([
            'id' => $userData['id'],
            'username' => $userData['username'],
            'role' => $userData['role'],
            'iat' => time(), // Issued at
            'exp' => time() + 3600 // Expira en 1 hora
        ]));
        $token = "$header.$payload."; // El punto final es la firma (vacía en este ejemplo)

        // 7. Devolver la respuesta exitosa con el token
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'token' => $token,
            'user' => [
                'id' => (int) $userData['id'],
                'username' => $userData['username'],
                'first_name' => $userData['first_name'],
                'last_name' => $userData['last_name'],
                'role' => $userData['role']
            ]
        ]);
        exit;

    } else {
        // Credenciales incorrectas
        http_response_code(401); // 401 Unauthorized
        echo json_encode(['success' => false, 'message' => 'Credenciales incorrectas.']);
        exit;
    }

} catch (Exception $e) {
    // Error de base de datos u otro
    http_response_code(500); // 500 Internal Server Error
    echo json_encode(['success' => false, 'message' => 'Error interno del servidor: ' . $e->getMessage()]);
    exit;
}