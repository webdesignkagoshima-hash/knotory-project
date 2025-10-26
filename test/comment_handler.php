<?php
// エラーをログに出力し、画面には表示しない
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// .env.local ファイルを読み込み
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
        
        // クオートを除去
        if (preg_match('/^"(.*)"$/', $value, $matches)) {
            $value = $matches[1];
        }
        
        // 改行文字を実際の改行に変換
        $value = str_replace('\\n', "\n", $value);
        
        putenv("$name=$value");
        $_ENV[$name] = $value;
    }
    
    return true;
}

// 環境変数を読み込み
loadEnv(__DIR__ . '/.env.local');

// POSTリクエストのみ受け付け
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// 入力データの取得と検証
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    // FormDataの場合は$_POSTから取得
    $name = trim($_POST['name'] ?? '');
    $message = trim($_POST['message'] ?? '');
} else {
    // JSONの場合は入力データから取得
    $name = trim($input['name'] ?? '');
    $message = trim($input['message'] ?? '');
}

if (empty($name) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'お名前とメッセージは必須です']);
    exit;
}

// 文字数制限
if (mb_strlen($name) > 50) {
    echo json_encode(['success' => false, 'message' => 'お名前は50文字以内で入力してください']);
    exit;
}

if (mb_strlen($message) > 500) {
    echo json_encode(['success' => false, 'message' => 'メッセージは500文字以内で入力してください']);
    exit;
}

// 簡単なスパムチェック
$spam_keywords = ['http://', 'https://', 'www.', '.com', '.net'];
$combined_text = $name . ' ' . $message;
foreach ($spam_keywords as $keyword) {
    if (stripos($combined_text, $keyword) !== false) {
        echo json_encode(['success' => false, 'message' => 'URLを含むメッセージは送信できません']);
        exit;
    }
}

try {
    // Google Sheets API で保存
    $result = saveToGoogleSheets($name, $message);
    
    if (!$result) {
        throw new Exception('Google Sheetsへの保存に失敗しました');
    }
    
    // LINE通知を送信（エラーでも処理継続）
    try {
        sendLineNotification($name, $message);
    } catch (Exception $e) {
        error_log("LINE通知エラー: " . $e->getMessage());
        // LINE通知失敗でも成功扱い
    }
    
    echo json_encode([
        'success' => true, 
        'message' => 'メッセージを送信しました',
        'debug' => [
            'name' => $name,
            'message' => mb_substr($message, 0, 50) . '...'
        ]
    ]);
    
} catch (Exception $e) {
    error_log("Comment submission error: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage(),
        'debug' => [
            'error_type' => get_class($e)
        ]
    ]);
}

function saveToGoogleSheets($name, $message) {
    $spreadsheetId = getenv('GOOGLE_SHEETS_SPREADSHEET_ID');
    $clientEmail = getenv('GOOGLE_CLIENT_EMAIL');
    $privateKey = getenv('GOOGLE_PRIVATE_KEY');
    
    if (!$spreadsheetId || !$clientEmail || !$privateKey) {
        throw new Exception('Google Sheets設定が不完全です');
    }
    
    // JWT作成（修正版）
    $header = json_encode(['typ' => 'JWT', 'alg' => 'RS256']);
    $now = time();
    $payload = json_encode([
        'iss' => $clientEmail,
        'scope' => 'https://www.googleapis.com/auth/spreadsheets',
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
    $tokenData = json_encode([
        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
        'assertion' => $jwt
    ]);
    
    $tokenContext = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/x-www-form-urlencoded',
            'content' => http_build_query([
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt
            ])
        ]
    ]);
    
    $tokenResponse = file_get_contents('https://oauth2.googleapis.com/token', false, $tokenContext);
    
    if (!$tokenResponse) {
        throw new Exception('Google OAuth認証リクエストに失敗しました');
    }
    
    $tokenData = json_decode($tokenResponse, true);
    if (!$tokenData || !isset($tokenData['access_token'])) {
        error_log('Token response: ' . $tokenResponse);
        throw new Exception('Google OAuth認証に失敗しました');
    }
    
    $accessToken = $tokenData['access_token'];
    
    // 日本時間設定
    date_default_timezone_set('Asia/Tokyo');
    
    // スプレッドシートにデータを追加
    $values = [[
        date('Y-m-d H:i:s'),
        $name,
        $message,
    ]];
    
    $postData = json_encode(['values' => $values]);
    
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => [
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/json'
            ],
            'content' => $postData,
            'ignore_errors' => true
        ]
    ]);
    
    $appendResponse = file_get_contents(
        "https://sheets.googleapis.com/v4/spreadsheets/{$spreadsheetId}/values/A1:append?valueInputOption=USER_ENTERED",
        false,
        $context
    );
    
    if (!$appendResponse) {
        // HTTPレスポンスヘッダーを確認
        $headers = $http_response_header ?? [];
        error_log('HTTP headers: ' . print_r($headers, true));
        throw new Exception('Google Sheets APIリクエストに失敗しました');
    }
    
    $appendData = json_decode($appendResponse, true);
    
    if (!$appendData || isset($appendData['error'])) {
        error_log('Sheets error response: ' . $appendResponse);
        
        // 詳細エラー情報を抽出
        if (isset($appendData['error']['message'])) {
            throw new Exception('Google Sheets エラー: ' . $appendData['error']['message']);
        } else {
            throw new Exception('Google Sheetsへの書き込みに失敗しました: ' . $appendResponse);
        }
    }
    
    return true;
}

function sendLineNotification($name, $message) {
    $accessToken = getenv('LINE_CHANNEL_ACCESS_TOKEN');
    $userIds = explode(',', getenv('LINE_USER_IDS'));
    
    if (!$accessToken || !$userIds) {
        error_log('LINE設定が不完全です');
        return false;
    }
    
    $notificationMessage = "新しいメッセージが届きました！\n\n" .
                          "【お名前】\n{$name}\n\n" .
                          "【メッセージ】\n{$message}\n\n" .
                          "管理者画面で確認してください。";
    
    foreach ($userIds as $userId) {
        $userId = trim($userId);
        if (empty($userId)) continue;
        
        $data = [
            'to' => $userId,
            'messages' => [
                [
                    'type' => 'text',
                    'text' => $notificationMessage
                ]
            ]
        ];
        
        $postData = json_encode($data);
        
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => [
                    'Authorization: Bearer ' . $accessToken,
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($postData)
                ],
                'content' => $postData,
                'ignore_errors' => true
            ]
        ]);
        
        $response = file_get_contents('https://api.line.me/v2/bot/message/push', false, $context);
        
        if ($response === false) {
            error_log("LINE通知送信エラー: ユーザーID {$userId}");
        }
    }
    
    return true;
}
?>
