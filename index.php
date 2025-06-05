<?php
require_once 'vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

function verifyToken() {
    $headers = getallheaders();
    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode(['error' => 'No token provided']);
        exit;
    }

    $token = str_replace('Bearer ', '', $headers['Authorization']);
    try {
        $decoded = JWT::decode($token, new Key('3N4In35mLc3U/0xgTjahEdcPbvbfuQg/NCEX7tSZu7P6MTs6VbMqPc7hxEvL59Mq', 'HS256'));
        return $decoded;
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid token']);
        exit;
    }
}

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$user = verifyToken();

if ($method === 'POST' && $path === '/addassignment') {
    $input = json_decode(file_get_contents('php://input'), true);
    echo json_encode(['success' => true, 'message' => 'Assignment added', 'data' => $input]);

} elseif ($method === 'GET' && $path === '/searchstudent') {
    $query = $_GET['q'] ?? '';
    echo json_encode(['success' => true, 'students' => [
        ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com'],
        ['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane@example.com']
    ], 'query' => $query]);

} elseif ($method === 'DELETE' && $path === '/removestudent') {
    parse_str(file_get_contents('php://input'), $input);
    $student_id = $input['student_id'] ?? $_GET['student_id'] ?? null;
    echo json_encode(['success' => true, 'message' => 'Student removed', 'student_id' => $student_id]);

} else {
    http_response_code(404);
    echo json_encode(['error' => 'Not found']);
}
?>
