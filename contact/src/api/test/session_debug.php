<?php
// デバッグ用: エラー表示を有効化
error_reporting(E_ALL);
ini_set('display_errors', 1);

// CORS設定
$allowed_origins = [
    'https://talkroom-16064731.akky-cr.xyz',
    'https://knotory.jp',
    'http://localhost:5473',
    'http://localhost:5500',
    'http://127.0.0.1:5500'
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
    header('Access-Control-Allow-Credentials: true');
} else {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Credentials: false');
}

header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// セッション設定（get_csrf_token.phpと同じ設定に統一）
session_set_cookie_params([
    'lifetime' => 3600,
    'path' => '/',
    'domain' => '',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'None'
]);

session_start();

// セッションにテスト値を保存
if (!isset($_SESSION['test_value'])) {
    $_SESSION['test_value'] = 'session_test_' . time();
}

// CSRF トークンがあるかチェック
$csrf_exists = !empty($_SESSION['csrf_token']);

$debug_info = [
    'session_id' => session_id(),
    'session_status' => session_status(),
    'session_name' => session_name(),
    'cookie_params' => session_get_cookie_params(),
    'test_value' => $_SESSION['test_value'] ?? 'NOT_SET',
    'csrf_token_exists' => $csrf_exists,
    'csrf_token_length' => $csrf_exists ? strlen($_SESSION['csrf_token']) : 0,
    'csrf_token_time' => $_SESSION['csrf_token_time'] ?? 'NOT_SET',
    'all_session_keys' => array_keys($_SESSION),
    'headers_sent' => headers_sent(),
    'php_version' => PHP_VERSION,
    'server_info' => [
        'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'],
        'HTTP_ORIGIN' => $_SERVER['HTTP_ORIGIN'] ?? 'NOT_SET',
        'HTTP_USER_AGENT' => $_SERVER['HTTP_USER_AGENT'] ?? 'NOT_SET',
        'REMOTE_ADDR' => $_SERVER['REMOTE_ADDR'] ?? 'NOT_SET'
    ]
];

echo json_encode([
    'success' => true,
    'message' => 'Session debug info',
    'debug_info' => $debug_info
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>