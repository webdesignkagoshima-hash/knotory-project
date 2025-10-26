<?php
/**
 * 認証機能クラス
 */
class Auth
{
    /**
     * 管理者ログイン処理
     */
    public static function adminLogin(string $password): array
    {
        $result = ['success' => false, 'message' => ''];
        
        // デバッグ情報
        $expected_password = Config::get('ADMIN_PASSWORD');
        if (Config::isDebugMode()) {
            error_log("Admin login attempt");
            error_log("Expected password: " . $expected_password);
            error_log("Entered password: " . $password);
            error_log("Password match: " . (hash_equals($expected_password, $password) ? 'true' : 'false'));
        }
        
        if (hash_equals($expected_password, $password)) {
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