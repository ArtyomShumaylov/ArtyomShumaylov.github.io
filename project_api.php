<?php

// Очистка буфера вывода на случай лишних пробелов
while (ob_get_level()) ob_end_clean();

// Установка заголовков ДО любого вывода
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-HTTP-Method-Override");
header('Content-Type: application/json', true);

// Старт сессии после заголовков
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Логирование
file_put_contents('api_debug.log', "\n\n[" . date('Y-m-d H:i:s') . "] NEW REQUEST\n", FILE_APPEND);
file_put_contents('api_debug.log', "REQUEST METHOD: " . $_SERVER['REQUEST_METHOD'] . "\n", FILE_APPEND);

try {
    // Подключение зависимостей
    require_once __DIR__ . '/project_db.php';
    require_once __DIR__ . '/project_validator.php';
    require_once __DIR__ . '/project_auth.php';

    // Получение метода запроса
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method === 'POST' && isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
        $method = strtoupper($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
    }

    // Получение и логгирование входных данных
    $rawInput = file_get_contents('php://input');
    file_put_contents('api_debug.log', "RAW INPUT: " . $rawInput . "\n", FILE_APPEND);
    
    if (empty($rawInput)) {
        throw new Exception('Empty request body', 400);
    }

    $input = json_decode($rawInput, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON: ' . json_last_error_msg(), 400);
    }

    file_put_contents('api_debug.log', "PARSED INPUT: " . print_r($input, true) . "\n", FILE_APPEND);

    // Валидация
    $errors = [];
    $validated = validate_project_form($input, $errors);
    
    if (!empty($errors)) {
        file_put_contents('api_debug.log', "VALIDATION ERRORS: " . print_r($errors, true) . "\n", FILE_APPEND);
        http_response_code(422);
        echo json_encode(['status' => 'error', 'errors' => $errors]);
        exit;
    }

    // Подключение к БД
    $pdo = connect_project_db();
    if (!$pdo) {
        throw new Exception('Database connection failed', 500);
    }

    // Обработка запросов
    $response = [];
    
    if ($method === 'POST') {
        $result = register_new_user($pdo, $validated);
        $_SESSION['user_id'] = $result['id'];
        $_SESSION['username'] = $result['login'];
        
        $response = [
            'status' => 'success',
            'message' => 'User created',
            'login' => $result['login'],
            'password' => $result['password'],
            'profile_url' => "/profile.php?id=" . $result['id']
        ];
    } 
    elseif ($method === 'PUT') {
        if (empty($_SESSION['user_id'])) {
            throw new Exception('Unauthorized', 401);
        }
        
        update_user_data($pdo, $_SESSION['user_id'], $validated);
        $response = ['status' => 'success', 'message' => 'Data updated'];
    }
    else {
        throw new Exception('Method not allowed', 405);
    }

    // Успешный ответ
    file_put_contents('api_debug.log', "SUCCESS RESPONSE: " . print_r($response, true) . "\n", FILE_APPEND);
    echo json_encode($response);

} catch (PDOException $e) {
    file_put_contents('api_debug.log', "PDO EXCEPTION: " . $e->getMessage() . "\n", FILE_APPEND);
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    file_put_contents('api_debug.log', "EXCEPTION: " . $e->getMessage() . "\n", FILE_APPEND);
    http_response_code($e->getCode() >= 400 ? $e->getCode() : 500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

exit;