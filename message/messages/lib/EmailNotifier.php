<?php
/**
 * メール通知クラス
 * PHPMailerを使用して管理者へメール通知を送信
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailNotifier
{
    /**
     * 新しいメッセージが保存されたことを管理者に通知
     * 
     * @param string $userName 投稿者名
     * @param string $message メッセージ内容
     * @param string $userIp 投稿者のIPアドレス
     * @param string $createdAt 投稿日時
     * @return bool 送信成功時true、失敗時false
     */
    public static function notifyNewMessage(string $userName, string $message, string $userIp, string $createdAt): bool
    {
        // メール通知が有効かチェック
        if (!Config::get('ENABLE_EMAIL_NOTIFICATION', false)) {
            Logger::info('Email notification is disabled');
            return true; // 無効の場合は成功として扱う
        }

        // 必要な設定が揃っているかチェック
        $adminEmail = Config::get('ADMIN_EMAIL');
        if (!$adminEmail) {
            Logger::warning('ADMIN_EMAIL is not configured');
            return false;
        }

        try {
            // PHPMailerのオートロード
            require_once __DIR__ . '/../vendor/phpmailer/src/Exception.php';
            require_once __DIR__ . '/../vendor/phpmailer/src/PHPMailer.php';
            require_once __DIR__ . '/../vendor/phpmailer/src/SMTP.php';

            $mail = new PHPMailer(true);

            // SMTP設定
            $mail->isSMTP();
            $mail->Host = Config::get('SMTP_HOST', 'localhost');
            $mail->SMTPAuth = Config::get('SMTP_AUTH', true);
            $mail->Username = Config::get('SMTP_USERNAME', '');
            $mail->Password = Config::get('SMTP_PASSWORD', '');
            $mail->SMTPSecure = Config::get('SMTP_SECURE', PHPMailer::ENCRYPTION_STARTTLS);
            $mail->Port = Config::get('SMTP_PORT', 587);
            $mail->CharSet = 'UTF-8';

            // 送信元設定
            $fromEmail = Config::get('SMTP_FROM_EMAIL', Config::get('SMTP_USERNAME'));
            $fromName = Config::get('SMTP_FROM_NAME', 'メッセージ通知システム');
            $mail->setFrom($fromEmail, $fromName);

            // 宛先設定
            $mail->addAddress($adminEmail);

            // メール内容（プレーンテキストのみ）
            $mail->isHTML(false);
            $mail->Subject = self::buildSubject($userName);
            $mail->Body = self::buildTextBody($userName, $message, $userIp, $createdAt);

            // メール送信
            $result = $mail->send();
            
            if ($result) {
                Logger::info('Email notification sent to: ' . $adminEmail);
            }
            
            return $result;

        } catch (Exception $e) {
            Logger::error('Failed to send email notification: ' . $e->getMessage());
            error_log('PHPMailer Error: ' . $e->getMessage());
            return false;
        } catch (\Exception $e) {
            Logger::error('Unexpected error in email notification: ' . $e->getMessage());
            error_log('Email Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * メールの件名を生成
     */
    private static function buildSubject(string $userName): string
    {
        return '【新着メッセージ】' . mb_substr($userName, 0, 20) . 'さんからメッセージが届きました';
    }

    /**
     * テキスト形式のメール本文を生成
     */
    private static function buildTextBody(string $userName, string $message, string $userIp, string $createdAt): string
    {
        return <<<TEXT
━━━━━━━━━━━━━━━━━━━━━━━━━━━━
  新着メッセージ通知
━━━━━━━━━━━━━━━━━━━━━━━━━━━━

投稿者: {$userName}
投稿日時: {$createdAt}
IPアドレス: {$userIp}

【メッセージ内容】
{$message}

━━━━━━━━━━━━━━━━━━━━━━━━━━━━
このメールは自動送信されています
TEXT;
    }
}
