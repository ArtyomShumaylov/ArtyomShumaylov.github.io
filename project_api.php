<?php
// Включение полного вывода ошибок для отладки
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Логирование запросов
file_put_contents('api_debug.log', "\n\n" . date('Y-m-d H:i:s') . " - New Request\n", FILE_APPEND);

// Установка заголовков CORS и JSON
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-HTTP-Method-Override");
header('Content-Type: application/json');

// Запуск сессии
session_start();

// Подключение зависимостей
require_once __DIR__ . '/project_db.php';
require_once __DIR__ . '/project_validator.php';
require_once __DIR__ . '/project_auth.php';

try {
    // Определение метода запроса
    $method = $_SERVER['REQUEST_METHOD'];
    // Поддержка методов PUT/DELETE через POST с заголовком X-HTTP-Method-Override
    if ($method === 'POST' && isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
        $method = strtoupper($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
    }

    file_put_contents('api_debug.log', "Method: $method\n", FILE_APPEND);

    // Получение входных данных
    $input = json_decode(file_get_contents('php://input'), true);
    file_put_contents('api_debug.log', "Raw input: " . file_get_contents('php://input') . "\n", FILE_APPEND);
    file_put_contents('api_debug.log', "Parsed input: " . print_r($input, true) . "\n", FILE_APPEND);

    if (!$input) {
        throw new Exception('Invalid JSON input', 400);
    }

    // Валидация данных
    $errors = [];
    $validated = validate_project_form($input, $errors);
    
    if (!empty($errors)) {
        file_put_contents('api_debug.log', "Validation errors: " . print_r($errors, true) . "\n", FILE_APPEND);
        http_response_code(422);
        echo json_encode(['status' => 'error', 'errors' => $errors]);
        exit;
    }

    // Подключение к БД
    $pdo = connect_project_db();

    // Обработка запроса в зависимости от метода
    switch ($method) {
        case 'POST':
            // Регистрация нового пользователя
            $result = register_new_user($pdo, $validated);
            
            $_SESSION['user_id'] = $result['id'];
            $_SESSION['username'] = $result['login'];
            
            $response = [
                'status' => 'success',
                'message' => 'User created successfully',
                'login' => $result['login'],
                'password' => $result['password'],
                'profile_url' => "/project_profile.php?id=" . $result['id']
            ];
            break;

        case 'PUT':
            // Обновление данных существующего пользователя
            if (empty($_SESSION['user_id'])) {
                throw new Exception('Unauthorized', 401);
            }
            
            update_user_data($pdo, $_SESSION['user_id'], $validated);
            $response = [
                'status' => 'success',
                'message' => 'User data updated successfully'
            ];
            break;

        default:
            throw new Exception('Method not allowed', 405);
    }

    // Логирование успешного выполнения
    file_put_contents('api_debug.log', "Success response: " . print_r($response, true) . "\n", FILE_APPEND);
    
    // Отправка успешного ответа
    echo json_encode($response);

} catch (PDOException $e) {
    // Ошибки базы данных
    file_put_contents('api_debug.log', "Database error: " . $e->getMessage() . "\n", FILE_APPEND);
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'error' => 'Database error',
        'message' => $e->getMessage()
    ]);
} catch (Exception $e) {
    // Все остальные ошибки
    file_put_contents('api_debug.log', "Error: " . $e->getMessage() . "\n", FILE_APPEND);
    http_response_code($e->getCode() ?: 400);
    echo json_encode([
        'status' => 'error',
        'error' => 'Request processing error',
        'message' => $e->getMessage()
    ]);
}

exit;