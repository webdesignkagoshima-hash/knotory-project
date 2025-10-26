<?php
/**
 * サイト設定ファイル
 * 環境に応じて自動的に本番環境/開発環境を判定
 */

// 環境判定を簡略化（より確実な方法）
// ローカル開発環境の判定
$isLocal = (
    in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1', 'localhost']) ||
    strpos($_SERVER['HTTP_HOST'] ?? '', 'localhost') !== false ||
    strpos($_SERVER['HTTP_HOST'] ?? '', '127.0.0.1') !== false
);

$isProduction = !$isLocal;

// 本番環境の設定ファイルが存在し、本番環境の場合はそちらを優先
if ($isProduction && file_exists(__DIR__ . '/config.production.php')) {
    require_once __DIR__ . '/config.production.php';
    return true;
}

// 以下、開発環境用設定（既存の設定を維持）

// セキュリティ設定
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_TIME', 300); // 5分間のロックアウト
define('SESSION_TIMEOUT', 3600); // 1時間のセッションタイムアウト
define('SESSION_REGENERATE_INTERVAL', 300); // 5分ごとのセッション再生成

// 管理者設定
define('ADMIN_PASSWORD', 'admin_secure_2025!'); // 管理者用パスワード

// SQLite設定（ローカル開発用）
define('DB_PATH', __DIR__ . '/../data/messages.db'); // SQLiteデータベースファイルパス

// ログ設定（ローカル開発用）
define('ENABLE_LOGGING', true);
define('LOG_FILE', __DIR__ . '/../logs/security.log');

// セキュリティヘッダー設定
define('SECURITY_HEADERS', [
    'X-Content-Type-Options' => 'nosniff',
    'X-Frame-Options' => 'DENY',
    'X-XSS-Protection' => '1; mode=block',
    'Referrer-Policy' => 'strict-origin-when-cross-origin'
]);

// IP制限設定（必要に応じて）
define('ALLOWED_IPS', [
    // '192.168.1.1', // 特定のIPからのみアクセスを許可する場合
]);

// デバッグモード（本番では false にする）
define('DEBUG_MODE', true); // ローカル開発用

// タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');

return true;
?>