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
        
        // 各テンプレート用の設定ファイルパスを動的に決定
        $template_config_paths = [];
        
        // 現在のファイルパスから適切なテンプレート設定を探す
        $current_dir = $_SERVER['SCRIPT_FILENAME'] ? dirname($_SERVER['SCRIPT_FILENAME']) : getcwd();
        
        // テンプレート01,02,03のconfig.phpを探す
        if (preg_match('/\/message\/(\d{2})\b/', $current_dir, $matches)) {
            $template_num = $matches[1];
            $template_config_paths[] = dirname($current_dir) . "/{$template_num}/config/config.php";
        }
        
        $config_paths = array_merge(
            $template_config_paths,
            [
                '/var/config/config.php',         // コンテナ内の設定ファイルパス（推奨）
                __DIR__ . '/../../01/config/config.php', // テンプレート01用
                __DIR__ . '/../../02/config/config.php', // テンプレート02用  
                __DIR__ . '/../../03/config/config.php', // テンプレート03用
                __DIR__ . '/../../config/config.php', // 開発環境用
                dirname(dirname(__DIR__)) . '/config/config.php',  // 一つ上のディレクトリ
                $_SERVER['DOCUMENT_ROOT'] . '/../config/config.php' // Webルートの外（最も安全）
            ]
        );
        
        foreach ($config_paths as $path) {
            if (file_exists($path) && is_readable($path)) {
                // デバッグ: どの設定ファイルが読み込まれたかをログに記録
                error_log("Config loaded from: {$path}");
                
                // 設定ファイルを読み込み
                include_once $path;
                $config_loaded = true;
                // 読み込み完了フラグを設定
                if (!defined('CONFIG_LOADED')) {
                    define('CONFIG_LOADED', true);
                }
                break; // 最初に見つかった設定ファイルを使用
            }
        }
        
        // 設定ファイルが読み込まれなかった場合のみデフォルト値を使用
        if (!$config_loaded && !defined('SITE_PASSWORD')) {
            error_log("Config not loaded, using defaults");
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
        define('SITE_PASSWORD', 'default_password_change_me');
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