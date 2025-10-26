<?php
/**
 * メッセージ操作クラス
 */
class Message
{
    /**
     * メッセージ保存
     */
    public static function save(string $sessionId, string $userIp, string $userName, string $message): bool
    {
        $db = Database::getInstance();
        if (!$db) {
            error_log("Message save failed: Database instance is null");
            return false;
        }
        
        // 念のため前後のスペースを削除
        $userName = trim($userName);
        $message = trim($message);
        
        try {
            $stmt = $db->prepare("
                INSERT INTO messages (user_session_id, user_ip, user_name, message, created_at) 
                VALUES (?, ?, ?, ?, ?)
            ");
            
            // 日本時間で現在時刻を取得
            $createdAt = date('Y-m-d H:i:s');
            
            error_log("Attempting to save message: user={$userName}, session={$sessionId}");
            
            $result = $stmt->execute([$sessionId, $userIp, $userName, $message, $createdAt]);
            
            if ($result) {
                Logger::info('Message saved successfully from: ' . $userName);
                error_log("Message saved successfully: user={$userName}, id=" . $db->lastInsertId());
            } else {
                error_log("Message save returned false");
            }
            
            return $result;
        } catch (PDOException $e) {
            Logger::error('Failed to save message: ' . $e->getMessage());
            error_log('Failed to save message: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ユーザーのメッセージ取得
     */
    public static function getUserMessages(string $sessionId): array
    {
        $db = Database::getInstance();
        if (!$db) return [];
        
        try {
            $stmt = $db->prepare("
                SELECT id, user_name, message, created_at 
                FROM messages 
                WHERE user_session_id = ? 
                ORDER BY created_at DESC
            ");
            $stmt->execute([$sessionId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            Logger::error('Failed to get user messages: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * 管理者用：全メッセージ取得
     */
    public static function getAllMessages(): array
    {
        $db = Database::getInstance();
        if (!$db) return [];
        
        try {
            $stmt = $db->query("
                SELECT id, user_session_id, user_ip, user_name, message, created_at 
                FROM messages 
                ORDER BY created_at DESC
            ");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            Logger::error('Failed to get all messages: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * 管理者用：ページネーション付きメッセージ取得
     */
    public static function getAllMessagesWithPagination(int $page = 1, int $perPage = 10): array
    {
        $db = Database::getInstance();
        if (!$db) return [];
        
        $page = max(1, $page);
        $perPage = in_array($perPage, [10, 30, 50, 100]) ? $perPage : 10;
        $offset = ($page - 1) * $perPage;
        
        try {
            $stmt = $db->prepare("
                SELECT id, user_session_id, user_ip, user_name, message, created_at 
                FROM messages 
                ORDER BY created_at DESC 
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$perPage, $offset]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            Logger::error('Failed to get paginated messages: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * 総メッセージ数取得
     */
    public static function getTotalCount(): int
    {
        $db = Database::getInstance();
        if (!$db) return 0;
        
        try {
            $stmt = $db->query("SELECT COUNT(*) FROM messages");
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            Logger::error('Failed to get total message count: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * メッセージ検証
     */
    public static function validate(string $userName, string $message): array
    {
        $errors = [];
        
        if (empty($userName) || empty($message)) {
            $errors[] = '名前とメッセージを入力してください。';
        } elseif (strlen($userName) > 50) {
            $errors[] = '名前は50文字以内で入力してください。';
        } elseif (strlen($message) > 1000) {
            $errors[] = 'メッセージは1000文字以内で入力してください。';
        }
        
        return $errors;
    }
}