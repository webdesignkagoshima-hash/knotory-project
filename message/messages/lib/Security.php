<?php
/**
 * セキュリティ機能クラス
 */
class Security
{
    /**
     * セキュリティヘッダーの設定
     */
    public static function setHeaders()
    {
        // ヘッダーが既に送信されている場合はスキップ
        if (headers_sent()) {
            return;
        }
        
        if (defined('SECURITY_HEADERS')) {
            foreach (SECURITY_HEADERS as $header => $value) {
                header("$header: $value");
            }
        }
        
        // セッションがまだ開始されていない場合のみ設定
        if (session_status() === PHP_SESSION_NONE) {
            // セッションクッキーの設定
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) ? 1 : 0);
            ini_set('session.cookie_samesite', 'Strict');
        }
    }

    /**
     * IP制限チェック
     */
    public static function checkIPRestriction(): bool
    {
        if (!defined('ALLOWED_IPS') || empty(ALLOWED_IPS)) {
            return true; // IP制限なし
        }
        
        $client_ip = $_SERVER['REMOTE_ADDR'] ?? '';
        
        // さくらのレンタルサーバーでのプロキシ対応
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $forwarded_ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $client_ip = trim($forwarded_ips[0]);
        } elseif (isset($_SERVER['HTTP_X_REAL_IP'])) {
            $client_ip = $_SERVER['HTTP_X_REAL_IP'];
        }
        
        return in_array($client_ip, ALLOWED_IPS);
    }

    /**
     * CSRF トークン生成
     */
    public static function generateCSRFToken(): string
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * CSRF トークン検証
     */
    public static function validateCSRFToken(string $token): bool
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * セッション検証
     */
    public static function validateSession(): bool
    {
        // ログイン状態の確認
        if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
            return false;
        }
        
        // セッションタイムアウト
        if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > SESSION_TIMEOUT) {
            session_destroy();
            Logger::write('Session timeout', 'INFO');
            return false;
        }
        
        // セッションハイジャック対策
        if (!isset($_SESSION['user_agent'])) {
            $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
        } elseif ($_SESSION['user_agent'] !== ($_SERVER['HTTP_USER_AGENT'] ?? '')) {
            session_destroy();
            Logger::write('Session hijacking attempt detected', 'WARNING');
            return false;
        }
        
        // セッション再生成
        if (!isset($_SESSION['last_regeneration']) || (time() - $_SESSION['last_regeneration']) > SESSION_REGENERATE_INTERVAL) {
            session_regenerate_id(true);
            $_SESSION['last_regeneration'] = time();
        }
        
        return true;
    }

    /**
     * 管理者認証チェック
     */
    public static function isAdmin(): bool
    {
        return isset($_SESSION['admin_authenticated']) && $_SESSION['admin_authenticated'] === true;
    }

    /**
     * 管理者セッション検証
     */
    public static function validateAdminSession(): bool
    {
        if (!self::isAdmin()) {
            return false;
        }
        
        // セッションディレクトリの整合性チェック
        $redirect_url = $_GET['redirect'] ?? '';
        $current_directory = '';
        
        if ($redirect_url && preg_match('#/message/(\d+)/#', $redirect_url, $matches)) {
            $current_directory = $matches[1];
        } else {
            // フォールバック: スクリプトのパスから推測
            $script_path = $_SERVER['SCRIPT_FILENAME'] ?? '';
            if (preg_match('#/message/(\d+)/#', $script_path, $matches)) {
                $current_directory = $matches[1];
            }
        }
        
        // セッションに保存されたディレクトリと現在のディレクトリが一致するかチェック
        if ($current_directory && isset($_SESSION['session_directory'])) {
            if ($_SESSION['session_directory'] !== $current_directory) {
                Logger::write("Session directory mismatch: session={$_SESSION['session_directory']}, current={$current_directory}", 'WARNING');
                session_destroy();
                return false;
            }
        }
        
        // セッションタイムアウト
        if (isset($_SESSION['admin_login_time']) && (time() - $_SESSION['admin_login_time']) > SESSION_TIMEOUT) {
            session_destroy();
            Logger::write('Admin session timeout', 'INFO');
            return false;
        }
        
        return true;
    }

    /**
     * 初期化処理
     */
    public static function init()
    {
        // リダイレクトURLからセッション番号を取得
        $redirect_url = $_GET['redirect'] ?? '';
        $session_number = '';
        
        if ($redirect_url && preg_match('#/message/(\d+)/#', $redirect_url, $matches)) {
            $session_number = $matches[1];
        }
        
        // セッション名を設定（ディレクトリごとに異なるセッション名を使用）
        if ($session_number) {
            $session_name = 'KNOTORY_MSG_' . $session_number;
        } else {
            // フォールバック: スクリプトのパスから推測
            $script_path = $_SERVER['SCRIPT_FILENAME'] ?? '';
            if (preg_match('#/message/(\d+)/#', $script_path, $matches)) {
                $session_number = $matches[1];
                $session_name = 'KNOTORY_MSG_' . $session_number;
            } else {
                $session_name = 'KNOTORY_MSG_DEFAULT';
            }
        }
        
        // セッション開始処理
        if (session_status() === PHP_SESSION_ACTIVE) {
            // 既にセッションが開始されている場合
            $current_session_name = session_name();
            
            // セッション名が異なる場合は切り替え
            if ($current_session_name !== $session_name) {
                // 既存のセッションデータを保存
                $existing_session_data = $_SESSION;
                
                // セッションを一旦閉じる
                session_write_close();
                
                // 新しいセッション名でセッション再開
                session_name($session_name);
                session_start();
                
                // 既存の認証情報を維持（pass/auth.phpの認証）
                if (isset($existing_session_data['authenticated'])) {
                    $_SESSION['authenticated'] = $existing_session_data['authenticated'];
                }
                
                // セッションにディレクトリ番号を保存
                if ($session_number && !isset($_SESSION['session_directory'])) {
                    $_SESSION['session_directory'] = $session_number;
                }
            } else {
                // セッション名が同じ場合は、ディレクトリ番号だけ確認
                if ($session_number && !isset($_SESSION['session_directory'])) {
                    $_SESSION['session_directory'] = $session_number;
                }
            }
        } else {
            // セッションが開始されていない場合は新規開始
            session_name($session_name);
            session_start();
            
            // セッションにディレクトリ番号を保存
            if ($session_number && !isset($_SESSION['session_directory'])) {
                $_SESSION['session_directory'] = $session_number;
            }
        }
        
        // 設定ファイル読み込み
        try {
            Config::load();
        } catch (Exception $e) {
            // デバッグモードの場合はエラーを表示
            if (defined('DEBUG_MODE') && DEBUG_MODE) {
                error_log('Config load error: ' . $e->getMessage());
            }
        }
        
        // セキュリティヘッダー設定
        self::setHeaders();
        
        // IP制限チェック
        if (!self::checkIPRestriction()) {
            Logger::write('IP restriction violation', 'WARNING');
            http_response_code(403);
            die('Access Denied');
        }
    }
}