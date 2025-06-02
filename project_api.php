<?php

require_once __DIR__ . '/project_db.php';
require_once __DIR__ . '/project_validator.php';
require_once __DIR__ . '/project_auth.php';

header('Content-Type: application/json');

session_start();

$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'POST' && isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
    $method = strtoupper($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    http_response_code(400);

    //add
    ob_start();
    var_dump($result); 
    $response = ob_get_clean();
    file_put_contents('debug.log', $response);
    //add

    echo json_encode(['error' => 'Invalid JSON']);
    exit;
}

$errors = [];
$input = validate_project_form($input, $errors);
if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(['errors' => $errors]);
    exit;
}

$pdo = connect_project_db();

try {
    if ($method === 'POST') {
        $result = register_new_user($pdo, $input);
        
        $_SESSION['user_id'] = $result['id'];
        $_SESSION['username'] = $result['login'];
        
        echo json_encode([
            'message' => 'User created',
            'login' => $result['login'],
            'password' => $result['password'],
            'profile_url' => "/project_profile.php?id=" . $result['id']
        ]);
        exit;
    }

    if ($method === 'PUT') {
        if (empty($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
        
        update_user_data($pdo, $_SESSION['user_id'], $input);
        echo json_encode(['message' => 'Data updated']);
        exit;
    }

    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}