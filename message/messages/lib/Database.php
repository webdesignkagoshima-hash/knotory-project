<?php
/**
 * データベース操作クラス
 */
class Database
{
    private static $instance = null;

    /**
     * データベース接続の取得（シングルトン）
     */
    public static function getInstance(): ?PDO
    {
        if (self::$instance === null) {
            try {
                $db_path = Config::get('DB_PATH', '/tmp/messages.db');
                
                self::$instance = new PDO('sqlite:' . $db_path);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                
                // テーブル作成（存在しない場合）
                self::createTables();
                
            } catch (PDOException $e) {
                Logger::error('Database connection failed: ' . $e->getMessage());
                return null;
            }
        }
        
        return self::$instance;
    }

    /**
     * テーブル作成
     */
    private static function createTables()
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS messages (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_session_id TEXT NOT NULL,
                user_ip TEXT NOT NULL,
                user_name TEXT NOT NULL,
                message TEXT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ";
        
        self::$instance->exec($sql);
    }

    /**
     * 接続をリセット（テスト用）
     */
    public static function reset()
    {
        self::$instance = null;
    }
}