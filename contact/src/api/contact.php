<?php
session_start();

// 環境変数の読み込み（.envファイルがある場合）
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

// PHPMailerの読み込み
require_once 'vendor/phpmailer/src/PHPMailer.php';
require_once 'vendor/phpmailer/src/SMTP.php';
require_once 'vendor/phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// 設定ファイルの存在確認
if (!file_exists('config/config.php')) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '設定ファイルが見つかりません']);
    error_log('Config file not found');
    exit;
}

$config = require_once 'config/config.php';

// 詳細なCORS設定
function setCorsHeadersDetailed($config) {
    $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
    $allowOrigin = false;
    
    if (in_array($origin, $config['cors']['allowed_origins'])) {
        $allowOrigin = true;
    } elseif (!empty($origin)) {
        if (strpos($origin, 'localhost') !== false || strpos($origin, '127.0.0.1') !== false) {
            $allowOrigin = true;
        } elseif (strpos($origin, 'file://') === 0) {
            $allowOrigin = true;
        } elseif ($origin === 'null') {
            $allowOrigin = true;
        }
    }
    
    if ($allowOrigin) {
        header('Access-Control-Allow-Origin: ' . $origin);
    } else {
        header('Access-Control-Allow-Origin: *');
    }
    
    header('Access-Control-Allow-Methods: ' . implode(', ', $config['cors']['allowed_methods']));
    header('Access-Control-Allow-Headers: ' . implode(', ', $config['cors']['allowed_headers']));
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: ' . $config['cors']['max_age']);
}

setCorsHeadersDetailed($config);

// ログディレクトリの作成
if (!is_dir($config['security']['log_directory'])) {
    mkdir($config['security']['log_directory'], 0755, true);
}
if (!is_dir($config['security']['rate_limit_data_dir'])) {
    mkdir($config['security']['rate_limit_data_dir'], 0755, true);
}

// 改善されたカスタムログ機能
function writeCustomLog($config, $logType, $message, $data = [], $level = 'info') {
    // ログレベルチェック
    if (!shouldLog($config, $logType, $level)) {
        return;
    }
    
    $logFile = '';
    switch ($logType) {
        case 'security':
            $logFile = $config['security']['log_directory'] . date('Y-m-d') . '_' . $config['security']['security_log_file'];
            break;
        case 'access':
            $logFile = $config['security']['log_directory'] . date('Y-m-d') . '_' . $config['security']['access_log_file'];
            break;
        default:
            return;
    }
    
    // ログファイルサイズ制限チェック
    $maxLogSize = $config['security']['max_log_size'] ?? 10 * 1024 * 1024; // 10MB
    if (file_exists($logFile) && filesize($logFile) > $maxLogSize) {
        // ローテーション: .1, .2, .3...として保存
        $rotateFile = $logFile . '.' . time();
        rename($logFile, $rotateFile);
        
        // 古いログファイルの削除（7日以上前）
        cleanOldLogs($config['security']['log_directory'], $config['security']['log_retention_days'] ?? 7);
    }
    
    $logEntry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'level' => strtoupper($level),
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
        'message' => $message,
        'data' => $data
    ];
    
    $logLine = json_encode($logEntry, JSON_UNESCAPED_UNICODE) . "\n";
    
    // 非同期的にログを書き込み（パフォーマンス向上）
    if (function_exists('fastcgi_finish_request')) {
        // FastCGI環境では応答を先に返してからログを書く
        register_shutdown_function(function() use ($logFile, $logLine) {
            file_put_contents($logFile, $logLine, FILE_APPEND | LOCK_EX);
        });
    } else {
        file_put_contents($logFile, $logLine, FILE_APPEND | LOCK_EX);
    }
}

// ログレベル制御機能
function shouldLog($config, $logType, $level = 'info') {
    $logLevels = ['debug' => 0, 'info' => 1, 'warning' => 2, 'error' => 3];
    $configLevel = $config['security']['log_level'] ?? 'info';
    
    return $logLevels[$level] >= $logLevels[$configLevel];
}

// 古いログファイル削除機能
function cleanOldLogs($logDirectory, $daysToKeep = 7) {
    $files = glob($logDirectory . '*.log*');
    $cutoffTime = time() - ($daysToKeep * 24 * 60 * 60);
    
    foreach ($files as $file) {
        if (filemtime($file) < $cutoffTime) {
            unlink($file);
        }
    }
}

// Rate Limiting機能
function checkRateLimit($config) {
    $clientIp = $_SERVER['REMOTE_ADDR'];
    $rateLimitFile = $config['security']['rate_limit_data_dir'] . 'rate_' . md5($clientIp) . '.json';
    $now = time();
    
    if (file_exists($rateLimitFile)) {
        $data = json_decode(file_get_contents($rateLimitFile), true);
        if ($data === null) {
            $data = ['count' => 0, 'first_attempt' => $now];
        }
    } else {
        $data = ['count' => 0, 'first_attempt' => $now];
    }
    
    if ($now - $data['first_attempt'] > $config['security']['rate_limit_window']) {
        $data = ['count' => 0, 'first_attempt' => $now];
    }
    
    if ($data['count'] >= $config['security']['max_requests_per_window']) {
        writeCustomLog($config, 'security', 'Rate limit exceeded', [
            'ip' => $clientIp,
            'attempts' => $data['count']
        ], 'warning');  // warningレベルで記録
        
        http_response_code(429);
        echo json_encode(['success' => false, 'message' => 'リクエスト数が制限を超えました。しばらくお待ちください。']);
        exit;
    }
    
    $data['count']++;
    file_put_contents($rateLimitFile, json_encode($data));
}

// CSRF Token検証機能
function validateCsrfToken($config) {
    $token = $_POST['csrf_token'] ?? '';
    $sessionToken = $_SESSION['csrf_token'] ?? '';
    $tokenTime = $_SESSION['csrf_token_time'] ?? 0;
    
    if (empty($token) || empty($sessionToken)) {
        writeCustomLog($config, 'security', 'CSRF token missing', [], 'warning');
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => '不正なリクエストです（トークンなし）']);
        exit;
    }
    
    if (time() - $tokenTime > 3600) {
        writeCustomLog($config, 'security', 'CSRF token expired', [], 'warning');
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'トークンの有効期限が切れています']);
        exit;
    }
    
    if (!hash_equals($sessionToken, $token)) {
        writeCustomLog($config, 'security', 'CSRF token mismatch', [], 'error');
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => '不正なリクエストです（トークン不一致）']);
        exit;
    }
}

// ハニーポット検証機能
function checkHoneypot($config) {
    $honeypotField = $config['security']['honeypot_field'];
    
    if (!empty($_POST[$honeypotField])) {
        writeCustomLog($config, 'security', 'Honeypot triggered');
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'お問い合わせを受け付けました']);
        exit;
    }
}

// 不審な入力パターンの検出
function detectSuspiciousInput($config, $allInputs) {
    $suspiciousPatterns = [
        '/(<script|<\/script>)/i' => 'XSS attempt',
        '/(javascript:|data:)/i' => 'JavaScript injection',
        '/(union|select|insert|update|delete|drop)/i' => 'SQL injection attempt',
        '/(\.\.|\/etc\/|\/var\/)/i' => 'Directory traversal attempt',
        '/(eval\(|exec\(|system\()/i' => 'Code execution attempt'
    ];
    
    foreach ($suspiciousPatterns as $pattern => $description) {
        if (preg_match($pattern, $allInputs)) {
            writeCustomLog($config, 'security', 'Suspicious input detected', [
                'description' => $description
            ]);
            
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => '不正な文字が含まれています']);
            exit;
        }
    }
}

// reCAPTCHA検証機能
function verifyRecaptcha($config, $recaptchaResponse) {
    if (empty($recaptchaResponse)) {
        writeCustomLog($config, 'security', 'reCAPTCHA response missing');
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'reCAPTCHA認証が必要です']);
        exit;
    }
    
    $secretKey = $config['recaptcha']['secret_key'] ?? '';
    if (empty($secretKey)) {
        writeCustomLog($config, 'security', 'reCAPTCHA secret key not configured');
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'システム設定エラーです']);
        exit;
    }
    
    $verifyUrl = 'https://www.google.com/recaptcha/api/siteverify';
    $data = [
        'secret' => $secretKey,
        'response' => $recaptchaResponse,
        'remoteip' => $_SERVER['REMOTE_ADDR'] ?? ''
    ];
    
    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data),
            'timeout' => 30
        ]
    ];
    
    $context = stream_context_create($options);
    $result = file_get_contents($verifyUrl, false, $context);
    
    if ($result === false) {
        writeCustomLog($config, 'security', 'reCAPTCHA verification request failed');
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'reCAPTCHA認証でエラーが発生しました']);
        exit;
    }
    
    $responseData = json_decode($result, true);
    
    if (!$responseData || !isset($responseData['success'])) {
        writeCustomLog($config, 'security', 'reCAPTCHA invalid response format');
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'reCAPTCHA認証でエラーが発生しました']);
        exit;
    }
    
    if (!$responseData['success']) {
        $errorCodes = $responseData['error-codes'] ?? [];
        writeCustomLog($config, 'security', 'reCAPTCHA verification failed', [
            'error_codes' => $errorCodes,
            'score' => $responseData['score'] ?? null
        ]);
        
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'reCAPTCHA認証に失敗しました']);
        exit;
    }
    
    // reCAPTCHA v3の場合はスコアもチェック
    if (isset($responseData['score'])) {
        $minScore = $config['recaptcha']['min_score'] ?? 0.5;
        if ($responseData['score'] < $minScore) {
            writeCustomLog($config, 'security', 'reCAPTCHA score too low', [
                'score' => $responseData['score'],
                'min_score' => $minScore
            ]);
            
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'reCAPTCHA認証のスコアが不十分です']);
            exit;
        }
    }
    
    writeCustomLog($config, 'access', 'reCAPTCHA verification successful', [
        'score' => $responseData['score'] ?? null
    ]);
    
    return true;
}

// セキュリティチェック実行
checkRateLimit($config);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

writeCustomLog($config, 'access', 'Contact form access');

validateCsrfToken($config);
checkHoneypot($config);

// reCAPTCHA検証
$recaptchaResponse = $_POST['g-recaptcha-response'] ?? $_POST['recaptcha_response'] ?? $_POST['recaptchaToken'] ?? '';
verifyRecaptcha($config, $recaptchaResponse);

// PHPMailerでメール送信する関数
function sendEmailWithPHPMailer($config, $toEmail, $toName, $subject, $body, $isAutoReply = false) {
    $mail = new PHPMailer(true);

    try {
        // SMTP設定
        if ($config['mail']['smtp']['enable']) {
            $mail->isSMTP();
            $mail->Host       = $config['mail']['smtp']['host'];
            $mail->SMTPAuth   = $config['mail']['smtp']['auth'];
            $mail->Username   = $config['mail']['smtp']['username'];
            $mail->Password   = $config['mail']['smtp']['password'];
            $mail->SMTPSecure = $config['mail']['smtp']['secure'];
            $mail->Port       = $config['mail']['smtp']['port'];
            $mail->Timeout    = $config['mail']['smtp']['timeout'];
        }

        // 文字エンコーディング設定
        $mail->CharSet = $config['mail']['smtp']['charset'];
        $mail->Encoding = 'base64';

        // 送信者設定
        $mail->setFrom($config['mail']['from_email'], $config['mail']['from_name']);
        
        // 受信者設定
        $mail->addAddress($toEmail, $toName);

        // 自動応答メールでない場合（管理者宛メール）は返信先を設定
        if (!$isAutoReply) {
            $mail->addReplyTo($toEmail, $toName);
        }

        // メール内容設定
        $mail->isHTML(false);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        // メール送信
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        writeCustomLog($config, 'security', 'PHPMailer error', [
            'error' => $mail->ErrorInfo,
            'exception' => $e->getMessage()
        ], 'error');
        return false;
    }
}

try {
    // 入力データサイズ制限チェック
    $totalInputSize = strlen(serialize($_POST));
    if ($totalInputSize > $config['security']['max_input_length']) {
        writeCustomLog($config, 'security', 'Input size limit exceeded');
        throw new Exception('入力データが大きすぎます');
    }
    
    // 入力データの取得とサニタイジング
    $inquiryType = isset($_POST['inquiry-type']) ? htmlspecialchars(strip_tags(trim($_POST['inquiry-type'])), ENT_QUOTES, 'UTF-8') : '';
    $name = isset($_POST['name']) ? htmlspecialchars(strip_tags(trim($_POST['name'])), ENT_QUOTES, 'UTF-8') : '';
    $email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
    $phone = isset($_POST['phone']) ? htmlspecialchars(strip_tags(trim($_POST['phone'])), ENT_QUOTES, 'UTF-8') : '';
    $message = isset($_POST['message']) ? htmlspecialchars(strip_tags(trim($_POST['message'])), ENT_QUOTES, 'UTF-8') : '';

    // 不審な入力パターンの検出
    $allInputs = $inquiryType . ' ' . $name . ' ' . $email . ' ' . $phone . ' ' . $message;
    detectSuspiciousInput($config, $allInputs);

    // バリデーション
    $errors = [];
    
    if (empty($inquiryType)) {
        $errors[] = 'お問い合わせ項目は必須です';
    }
    if (empty($name)) {
        $errors[] = 'お名前は必須です';
    }
    if (empty($email)) {
        $errors[] = 'メールアドレスは必須です';
    }
    if (empty($message)) {
        $errors[] = 'お問い合わせ内容は必須です';
    }

    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'メールアドレスの形式が正しくありません';
    }

    if (!empty($phone) && !preg_match($config['validation']['phone_pattern'], $phone)) {
        $errors[] = '電話番号の形式が正しくありません';
    }

    if (mb_strlen($name) > $config['validation']['max_name_length']) {
        $errors[] = 'お名前は' . $config['validation']['max_name_length'] . '文字以内で入力してください';
    }
    if (mb_strlen($message) > $config['validation']['max_message_length']) {
        $errors[] = 'お問い合わせ内容は' . $config['validation']['max_message_length'] . '文字以内で入力してください';
    }

    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
        exit;
    }

    // メール送信設定
    mb_language("Japanese");
    mb_internal_encoding("UTF-8");
    ini_set('mbstring.substitute_character', 'none');

    // メール用のデータ配列
    $mailData = [
        'inquiry_type' => $inquiryType,
        'name' => $name,
        'email' => $email,
        'phone' => $phone ?: '未入力',
        'message' => $message,
        'datetime' => date('Y年m月d日 H:i:s')
    ];

    // 管理者への通知メール
    $adminSubject = $config['messages']['admin']['subject'];
    $adminBody = str_replace(
        ['{inquiry_type}', '{name}', '{email}', '{phone}', '{message}', '{datetime}'],
        [$mailData['inquiry_type'], $mailData['name'], $mailData['email'], $mailData['phone'], $mailData['message'], $mailData['datetime']],
        $config['messages']['admin']['body_template']
    );

    // 自動応答メール
    $autoReplySubject = $config['messages']['auto_reply']['subject'];
    $autoReplyBody = str_replace(
        ['{name}', '{inquiry_type}', '{email}', '{phone}', '{message}'],
        [$mailData['name'], $mailData['inquiry_type'], $mailData['email'], $mailData['phone'], $mailData['message']],
        $config['messages']['auto_reply']['body_template']
    );

    // PHPMailerでメール送信実行
    $adminMailSent = sendEmailWithPHPMailer($config, $config['mail']['admin_email'], $config['mail']['from_name'], $adminSubject, $adminBody, false);
    $autoReplyMailSent = sendEmailWithPHPMailer($config, $email, $name, $autoReplySubject, $autoReplyBody, true);

    if ($adminMailSent && $autoReplyMailSent) {
        writeCustomLog($config, 'access', 'Contact form submitted successfully', [
            'inquiry_type' => $inquiryType,
            'email' => $email
        ]);
        echo json_encode(['success' => true, 'message' => 'お問い合わせを受け付けました']);
    } else {
        throw new Exception('メール送信に失敗しました');
    }

} catch (Exception $e) {
    writeCustomLog($config, 'security', 'Contact form error', [
        'error_message' => $e->getMessage()
    ]);
    
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'システムエラーが発生しました']);
}

// CSRFトークンを使用後に再生成
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
$_SESSION['csrf_token_time'] = time();
?>

