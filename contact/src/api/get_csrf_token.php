<?php
// CORS設定を最初に処理（nginxが.htaccessを処理しない場合の対策）
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
    // 許可されていないオリジンの場合はCredentialsを無効にする
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Credentials: false');
}

header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With, Authorization, X-CSRF-Token');
header('Access-Control-Max-Age: 86400');

// OPTIONSリクエストの処理（プリフライトリクエスト）
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'CORS preflight successful']);
    exit;
}

session_start();

// 設定ファイルの絶対パスを生成
$config_path = __DIR__ . '/config/config.php';

// 設定ファイルの存在確認
if (!file_exists($config_path)) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'message' => '設定ファイルが見つかりません']);
    exit;
}

// 設定ファイル読み込み
try {
    $config = require_once $config_path;
} catch (Exception $e) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'message' => '設定ファイルの読み込みに失敗しました']);
    exit;
}

// Content-Typeヘッダーを設定
header('Content-Type: application/json; charset=utf-8');

// GETメソッドのみ許可
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    // CSRFトークンの生成
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    // トークンの有効期限を設定（1時間）
    $_SESSION['csrf_token_time'] = time();
    
    $response = [
        'success' => true,
        'csrf_token' => $_SESSION['csrf_token'],
        'expires_in' => 3600 // 1時間
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Token generation failed']);
}
?>