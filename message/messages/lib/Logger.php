<?php
/**
 * ログ機能クラス
 */
class Logger
{
    /**
     * セキュリティログの記録
     */
    public static function write(string $message, string $level = 'INFO')
    {
        if (!Config::isLoggingEnabled()) {
            return;
        }
        
        $log_file = Config::get('LOG_FILE', '/tmp/security.log');
        $log_dir = dirname($log_file);
        
        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0755, true);
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        
        $log_entry = "[$timestamp] [$level] IP: $ip | $message | User-Agent: $user_agent\n";
        
        file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
    }

    /**
     * INFOレベルのログ
     */
    public static function info(string $message)
    {
        self::write($message, 'INFO');
    }

    /**
     * WARNINGレベルのログ
     */
    public static function warning(string $message)
    {
        self::write($message, 'WARNING');
    }

    /**
     * ERRORレベルのログ
     */
    public static function error(string $message)
    {
        self::write($message, 'ERROR');
    }
}