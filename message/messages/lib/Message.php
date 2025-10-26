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
        if (!$db) return false;
        
        try {
            $stmt = $db->prepare("
                INSERT INTO messages (user_session_id, user_ip, user_name, message, created_at) 
                VALUES (?, ?, ?, ?, ?)
            ");
            
            // 日本時間で現在時刻を取得
            $createdAt = date('Y-m-d H:i:s');
            
            $result = $stmt->execute([$sessionId, $userIp, $userName, $message, $createdAt]);
            
            if ($result) {
                Logger::info('Message saved successfully from: ' . $userName);
            }
            
            return $result;
        } catch (PDOException $e) {
            Logger::error('Failed to save message: ' . $e->getMessage());
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

    /**
     * ダミーデータ生成
     */
    public static function generateDummyData(int $count = 120): bool
    {
        $db = Database::getInstance();
        if (!$db) return false;
        
        try {
            $db->beginTransaction();
            
            $names = [
                '田中太郎', '佐藤花子', '鈴木一郎', '高橋美咲', '伊藤健太',
                '山田あかり', '中村雄大', '小林さくら', '加藤直樹', '吉田みどり',
                '山本光太', '松本真由', '井上大輔', '木村優子', '林志郎',
                '森田彩香', '清水誠', '山口恵美', '近藤拓海', '斎藤美穂'
            ];
            
            $messages = [
                'お疲れ様です。質問があります。',
                'いつもお世話になっております。',
                'システムの調子はいかがですか？',
                '新機能の件でご相談があります。',
                'バグを発見しましたので報告します。',
                '改善提案をお送りします。',
                'お忙しい中恐れ入ります。',
                'ご確認いただけますでしょうか。',
                'ありがとうございました。',
                'よろしくお願いいたします。',
                'データの更新をお願いします。',
                '設定変更の件でご連絡します。',
                'ユーザビリティについて意見があります。',
                'パフォーマンスが向上しました。',
                'セキュリティ対策について質問です。',
                '新しいアイデアを提案します。',
                'テスト結果をお知らせします。',
                '問題が解決しました。',
                '次回のミーティングについて',
                'プロジェクトの進捗報告です。'
            ];
            
            $stmt = $db->prepare("
                INSERT INTO messages (user_session_id, user_ip, user_name, message, created_at) 
                VALUES (?, ?, ?, ?, ?)
            ");
            
            for ($i = 0; $i < $count; $i++) {
                $sessionId = 'dummy_session_' . str_pad($i % 20, 3, '0', STR_PAD_LEFT);
                $userIp = '192.168.1.' . (rand(1, 254));
                $userName = $names[array_rand($names)];
                $message = $messages[array_rand($messages)];
                
                // ランダムな過去の日時を生成（過去30日間）
                $randomDays = rand(0, 29);
                $randomHours = rand(0, 23);
                $randomMinutes = rand(0, 59);
                $createdAt = date('Y-m-d H:i:s', strtotime("-{$randomDays} days -{$randomHours} hours -{$randomMinutes} minutes"));
                
                $stmt->execute([$sessionId, $userIp, $userName, $message, $createdAt]);
            }
            
            $db->commit();
            Logger::info("Generated {$count} dummy messages");
            return true;
            
        } catch (PDOException $e) {
            $db->rollBack();
            Logger::error('Failed to generate dummy data: ' . $e->getMessage());
            return false;
        }
    }
}