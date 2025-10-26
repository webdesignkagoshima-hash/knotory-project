<?php
/**
 * 設定管理クラス
 */
class Config
{
    private static $loaded = false;

    /**
     * 設定ファイルの読み込み
     */
    public static function load(): bool
    {
        // 既に設定ファイルが読み込まれている場合はスキップ
        if (self::$loaded || defined('CONFIG_LOADED')) {
            return true;
        }
        
        $config_loaded = false;
        
        // 呼び出し元のスクリプトディレクトリから設定ファイルを探す
        $script_dir = dirname($_SERVER['SCRIPT_FILENAME']);
        
        // リダイレクト元のパスを取得（管理画面の場合に有効）
        $redirect_url = $_GET['redirect'] ?? '';
        $redirect_dir = '';
        $session_number = '';
        
        if ($redirect_url) {
            $redirect_dir = dirname($_SERVER['DOCUMENT_ROOT'] . $redirect_url);
            
            // リダイレクトURLからセッション番号を抽出 (例: /message/01/admin/index.php -> 01)
            if (preg_match('#/message/(\d+)/#', $redirect_url, $matches)) {
                $session_number = $matches[1];
            }
        }
        
        // スクリプトのパスからもセッション番号を抽出（index.phpなど）
        if (!$session_number) {
            $script_path = $_SERVER['SCRIPT_FILENAME'] ?? '';
            if (preg_match('#/message/(\d+)/#', $script_path, $matches)) {
                $session_number = $matches[1];
            }
        }
        
        // 設定ファイルのパスリスト（本番環境用を優先）
        $config_paths = [];
        
        // セッション番号が判明している場合は、そのディレクトリを優先
        if ($session_number) {
            $session_config_dir = dirname(__DIR__, 2) . '/' . $session_number . '/config';
            $config_paths[] = $session_config_dir . '/config.production.php';
            $config_paths[] = $session_config_dir . '/config.php';
        }
        
        // その他の候補パス
        $config_paths = array_merge($config_paths, array_filter([
            // リダイレクト元から見た相対パス（管理画面用）
            $redirect_dir ? $redirect_dir . '/../config/config.production.php' : null,
            $redirect_dir ? $redirect_dir . '/../config/config.php' : null,
            // 呼び出し元のスクリプトから見た相対パス（admin/index.phpから ../config/）
            $script_dir . '/../config/config.production.php',
            $script_dir . '/../config/config.php',
            // 直接configディレクトリにいる場合
            $script_dir . '/config/config.production.php',
            $script_dir . '/config/config.php',
            // messagesと同階層のconfig
            __DIR__ . '/../../config/config.production.php',
            __DIR__ . '/../../config/config.php',
            // コンテナ内の設定
            '/var/config/config.production.php',
            '/var/config/config.php',
            // Webルートの外
            $_SERVER['DOCUMENT_ROOT'] . '/../config/config.production.php',
            $_SERVER['DOCUMENT_ROOT'] . '/../config/config.php'
        ]));
        
        foreach ($config_paths as $path) {
            // パスを正規化
            $normalized_path = realpath($path);
            
            if ($normalized_path && file_exists($normalized_path) && is_readable($normalized_path)) {
                // デバッグ: どの設定ファイルが読み込まれたかをログに記録
                error_log("Config loaded from: {$normalized_path}");
                
                // 設定ファイルを読み込み
                include_once $normalized_path;
                $config_loaded = true;
                // 読み込み完了フラグを設定
                if (!defined('CONFIG_LOADED')) {
                    define('CONFIG_LOADED', true);
                }
                break; // 最初に見つかった設定ファイルを使用
            }
        }
        
        // 設定ファイルが読み込まれなかった場合のみデフォルト値を使用
        if (!$config_loaded && !defined('ADMIN_PASSWORD')) {
            error_log("Config not loaded, using defaults. Tried paths: " . implode(', ', array_slice($config_paths, 0, 5)));
            self::setDefaults();
        }
        
        self::$loaded = true;
        return $config_loaded;
    }

    /**
     * デフォルト設定値の設定
     */
    private static function setDefaults()
    {
        define('MAX_LOGIN_ATTEMPTS', 5);
        define('LOCKOUT_TIME', 300);
        define('SESSION_TIMEOUT', 3600);
        define('SESSION_REGENERATE_INTERVAL', 300);
        define('DEBUG_MODE', false);
        define('ENABLE_LOGGING', false);
        define('ADMIN_PASSWORD', 'admin_default_change_me');
        define('DB_PATH', '/tmp/messages.db');
        define('LOG_FILE', '/tmp/security.log');
        define('ALLOWED_IPS', []);
        define('SECURITY_HEADERS', [
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'DENY',
            'X-XSS-Protection' => '1; mode=block',
            'Referrer-Policy' => 'strict-origin-when-cross-origin'
        ]);
        define('CONFIG_LOADED', true);
    }

    /**
     * 設定値の取得
     */
    public static function get(string $key, $default = null)
    {
        return defined($key) ? constant($key) : $default;
    }

    /**
     * デバッグモードかどうか
     */
    public static function isDebugMode(): bool
    {
        return self::get('DEBUG_MODE', false);
    }

    /**
     * ログが有効かどうか
     */
    public static function isLoggingEnabled(): bool
    {
        return self::get('ENABLE_LOGGING', false);
    }
}