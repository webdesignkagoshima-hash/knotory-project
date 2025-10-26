<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

// エラー設定
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// .env.local読み込み
function loadEnv($path) {
    if (!file_exists($path)) {
        return false;
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        if (preg_match('/^"(.*)"$/', $value, $matches)) {
            $value = $matches[1];
        }
        
        $value = str_replace('\\n', "\n", $value);
        putenv("$name=$value");
        $_ENV[$name] = $value;
    }
    
    return true;
}

loadEnv(__DIR__ . '/.env.local');

try {
    $messages = getMessagesFromGoogleSheets();
    
    echo json_encode([
        'success' => true,
        'messages' => $messages
    ]);
    
} catch (Exception $e) {
    error_log("Get messages error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'messages' => []
    ]);
}

function getMessagesFromGoogleSheets() {
    $spreadsheetId = getenv('GOOGLE_SHEETS_SPREADSHEET_ID');
    $clientEmail = getenv('GOOGLE_CLIENT_EMAIL');
    $privateKey = getenv('GOOGLE_PRIVATE_KEY');
    
    if (!$spreadsheetId || !$clientEmail || !$privateKey) {
        throw new Exception('Google Sheets設定が不完全です');
    }
    
    // JWT作成
    $header = json_encode(['typ' => 'JWT', 'alg' => 'RS256']);
    $now = time();
    $payload = json_encode([
        'iss' => $clientEmail,
        'scope' => 'https://www.googleapis.com/auth/spreadsheets.readonly',
        'aud' => 'https://oauth2.googleapis.com/token',
        'exp' => $now + 3600,
        'iat' => $now
    ]);
    
    $base64Header = rtrim(strtr(base64_encode($header), '+/', '-_'), '=');
    $base64Payload = rtrim(strtr(base64_encode($payload), '+/', '-_'), '=');
    
    $signature = '';
    $signatureData = $base64Header . '.' . $base64Payload;
    
    if (!openssl_sign($signatureData, $signature, $privateKey, 'SHA256')) {
        throw new Exception('JWT署名の作成に失敗しました');
    }
    
    $base64Signature = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');
    $jwt = $signatureData . '.' . $base64Signature;
    
    // アクセストークン取得
    $tokenResponse = file_get_contents('https://oauth2.googleapis.com/token', false, stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/x-www-form-urlencoded',
            'content' => http_build_query([
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt
            ])
        ]
    ]));
    
    if (!$tokenResponse) {
        throw new Exception('Google OAuth認証リクエストに失敗しました');
    }
    
    $tokenData = json_decode($tokenResponse, true);
    if (!$tokenData || !isset($tokenData['access_token'])) {
        throw new Exception('Google OAuth認証に失敗しました');
    }
    
    $accessToken = $tokenData['access_token'];
    
    // スプレッドシートからデータ取得
    $response = file_get_contents(
        "https://sheets.googleapis.com/v4/spreadsheets/{$spreadsheetId}/values/A:D",
        false,
        stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => 'Authorization: Bearer ' . $accessToken
            ]
        ])
    );
    
    if (!$response) {
        throw new Exception('Google Sheetsからのデータ取得に失敗しました');
    }
    
    $data = json_decode($response, true);
    
    if (!$data || !isset($data['values'])) {
        return [];
    }
    
    $messages = [];
    foreach ($data['values'] as $index => $row) {
        // ヘッダー行をスキップ
        if ($index === 0) continue;
        
        // 必要なデータが揃っているメッセージを表示
        if (isset($row[0]) && isset($row[1]) && isset($row[2])) {
            $messages[] = [
                'created_at' => $row[0],
                'name' => $row[1],
                'message' => $row[2]
            ];
        }
    }
    
    // 日付順で並び替え（新しい順）
    usort($messages, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
    
    return $messages;
}
?>
