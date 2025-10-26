<?php
/**
 * 本番環境用設定ファイル
 * レンタルサーバーでの運用に最適化
 */

// 既に設定が読み込まれている場合はスキップ
if (defined('CONFIG_LOADED')) {
    return true;
}

// 管理者設定
define('ADMIN_PASSWORD', 'admin_secure_2025!'); // 必ず変更してください

// 管理者ログインのセキュリティ設定
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_TIME', 300); // 5分間のロックアウト

// セッション管理設定
define('SESSION_TIMEOUT', 3600); // 1時間のセッションタイムアウト
define('SESSION_REGENERATE_INTERVAL', 300); // 5分ごとのセッション再生成

// SQLite設定（本番環境用）
define('DB_PATH', __DIR__ . '/../data/messages.db'); // SQLiteデータベースファイルパス

// ログ設定（本番環境用）
define('ENABLE_LOGGING', true);
define('LOG_FILE', __DIR__ . '/../logs/security.log');

// セキュリティヘッダー設定
define('SECURITY_HEADERS', [
    'X-Content-Type-Options' => 'nosniff',
    'X-Frame-Options' => 'DENY',
    'X-XSS-Protection' => '1; mode=block',
    'Referrer-Policy' => 'strict-origin-when-cross-origin'
]);

// デバッグモード（本番では必ず false にする）
define('DEBUG_MODE', false); // 本番環境では false に設定

// エラー表示設定（本番環境）
if (!DEBUG_MODE) {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    error_reporting(0);
} else {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
}

// タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');

// ディレクトリの存在確認と作成
$directories = [
    __DIR__ . '/../data',
    __DIR__ . '/../logs',
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        if (!mkdir($dir, 0755, true)) {
            error_log("ディレクトリの作成に失敗: {$dir}");
        }
    }
    
    $currentPerms = fileperms($dir) & 0777;
    if ($currentPerms !== 0755) {
        if (!@chmod($dir, 0755)) {
            error_log("ディレクトリのパーミッション変更に失敗: {$dir}");
        }
    }
}

// データベースファイルのパーミッション設定
if (file_exists(DB_PATH)) {
    $currentPerms = fileperms(DB_PATH) & 0777;
    if ($currentPerms !== 0644) {
        if (!@chmod(DB_PATH, 0644)) {
            error_log("データベースファイルのパーミッション変更に失敗: " . DB_PATH);
        }
    }
}

// ログファイルのパーミッション設定
if (file_exists(LOG_FILE)) {
    $currentPerms = fileperms(LOG_FILE) & 0777;
    if ($currentPerms !== 0644) {
        if (!@chmod(LOG_FILE, 0644)) {
            error_log("ログファイルのパーミッション変更に失敗: " . LOG_FILE);
        }
    }
}

// 設定読み込み完了フラグ
define('CONFIG_LOADED', true);

return true;
