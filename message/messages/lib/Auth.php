<?php
/**
 * 認証機能クラス
 */
class Auth
{
    /**
     * ユーザーログイン処理
     */
    public static function login(string $password): array
    {
        $result = ['success' => false, 'message' => ''];
        
        // ロックアウト状態のチェック
        if (self::isLockedOut()) {
            $remaining_time = self::getRemainingLockoutTime();
            $result['message'] = "ログイン試行回数が上限に達しました。{$remaining_time}秒後に再試行してください。";
            return $result;
        }
        
        // パスワードの確認
        if (hash_equals(Config::get('SITE_PASSWORD'), $password)) {
            // ログイン成功
            $_SESSION['authenticated'] = true;
            $_SESSION['login_time'] = time();
            $_SESSION['login_attempts'] = 0; // 成功時にリセット
            $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
            
            // CSRFトークンを再生成（セッション固定攻撃対策）
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            
            Logger::info('Successful login');
            $result['success'] = true;
        } else {
            // ログイン失敗
            self::incrementLoginAttempts();
            $result['message'] = 'パスワードが正しくありません。';
            Logger::warning("Failed login attempt #{$_SESSION['login_attempts']}");
        }
        
        return $result;
    }

    /**
     * 管理者ログイン処理
     */
    public static function adminLogin(string $password): array
    {
        $result = ['success' => false, 'message' => ''];
        
        if (hash_equals(Config::get('ADMIN_PASSWORD'), $password)) {
            $_SESSION['admin_authenticated'] = true;
            $_SESSION['admin_login_time'] = time();
            Logger::info('Admin logged in');
            $result['success'] = true;
        } else {
            $result['message'] = '管理者パスワードが正しくありません。';
            Logger::warning('Failed admin login attempt');
        }
        
        return $result;
    }

    /**
     * ユーザーログアウト
     */
    public static function logout()
    {
        Logger::info('User logged out');
        session_destroy();
    }

    /**
     * 管理者ログアウト
     */
    public static function adminLogout()
    {
        Logger::info('Admin logged out');
        unset($_SESSION['admin_authenticated'], $_SESSION['admin_login_time']);
    }

    /**
     * ロックアウト状態の確認
     */
    private static function isLockedOut(): bool
    {
        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = 0;
            $_SESSION['last_attempt_time'] = 0;
        }

        if ($_SESSION['login_attempts'] >= Config::get('MAX_LOGIN_ATTEMPTS', 5)) {
            $time_since_last_attempt = time() - $_SESSION['last_attempt_time'];
            if ($time_since_last_attempt < Config::get('LOCKOUT_TIME', 300)) {
                return true;
            } else {
                // ロックアウト期間が過ぎたらリセット
                $_SESSION['login_attempts'] = 0;
                Logger::info('Lockout period expired, attempts reset');
            }
        }

        return false;
    }

    /**
     * 残りロックアウト時間の取得
     */
    private static function getRemainingLockoutTime(): int
    {
        $time_since_last_attempt = time() - $_SESSION['last_attempt_time'];
        return Config::get('LOCKOUT_TIME', 300) - $time_since_last_attempt;
    }

    /**
     * ログイン試行回数を増加
     */
    private static function incrementLoginAttempts()
    {
        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = 0;
        }
        $_SESSION['login_attempts']++;
        $_SESSION['last_attempt_time'] = time();
    }

    /**
     * ログイン試行情報の取得
     */
    public static function getLoginAttemptInfo(): array
    {
        return [
            'attempts' => $_SESSION['login_attempts'] ?? 0,
            'max_attempts' => Config::get('MAX_LOGIN_ATTEMPTS', 5),
            'is_locked_out' => self::isLockedOut(),
            'remaining_time' => self::isLockedOut() ? self::getRemainingLockoutTime() : 0
        ];
    }
}