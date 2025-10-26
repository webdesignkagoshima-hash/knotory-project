<?php
/**
 * メッセージ送信デバッグスクリプト
 * 問題を特定するためのテストツール
 */

// エラー表示を有効化
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>メッセージ送信デバッグ</h1>";
echo "<pre>";

// 1. ファイルパスの確認
echo "=== ファイルパスの確認 ===\n";
echo "現在のディレクトリ: " . __DIR__ . "\n";
echo "_chat_handler.php: " . (__DIR__ . '/../../messages/templates/_chat_handler.php') . "\n";
echo "ファイル存在: " . (file_exists(__DIR__ . '/../../messages/templates/_chat_handler.php') ? 'Yes' : 'No') . "\n\n";

// 2. _chat_handler.phpを読み込んでみる
echo "=== _chat_handler.php 読み込みテスト ===\n";
try {
    // auth.phpは認証が必要なのでスキップして、直接必要なファイルを読み込む
    require_once __DIR__ . '/../../messages/functions.php';
    
    // セッション開始（_chat_handler.phpと同じ処理）
    if (session_status() === PHP_SESSION_NONE) {
        initSecurity();
    }
    
    // 設定読み込み
    if (!defined('DB_PATH')) {
        loadConfig();
    }
    
    // CSRFトークン生成
    $csrf_token = generateCSRFToken();
    
    // メッセージ処理の結果を格納する変数
    $message_result = [
        'message' => '',
        'error' => '',
        'success' => false
    ];
    
    // POST送信の処理（_chat_handler.phpと同じロジック）
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
        if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
            $message_result['error'] = '不正なリクエストです。ページを再読み込みしてください。';
        } else {
            $user_name = trim($_POST['user_name'] ?? '');
            $user_message = trim($_POST['message'] ?? '');
            
            // validateMessage()は配列を返す（エラーがあればエラーメッセージの配列、なければ空配列）
            $validation_errors = validateMessage($user_name, $user_message);
            
            if (!empty($validation_errors)) {
                // エラーがある場合
                $message_result['error'] = implode('<br>', $validation_errors);
            } else {
                // エラーがない場合、メッセージを保存
                $session_id = session_id();
                $user_ip = $_SERVER['REMOTE_ADDR'] ?? '不明';
                
                if (saveMessage($session_id, $user_ip, $user_name, $user_message)) {
                    $message_result['success'] = true;
                    $message_result['message'] = 'メッセージを送信いたしました。ありがとうございます。';
                    writeSecurityLog("メッセージ送信成功: {$user_name} (IP: {$user_ip})");
                } else {
                    $message_result['error'] = 'メッセージの送信に失敗しました。しばらく後で再度お試しください。';
                    writeSecurityLog("メッセージ送信失敗: {$user_name} (IP: {$user_ip})", 'ERROR');
                }
            }
        }
    }
    
    echo "✓ 読み込み成功\n";
    echo "CSRFトークン: " . (isset($csrf_token) ? substr($csrf_token, 0, 20) . '...' : '未設定') . "\n";
    echo "message_result: " . print_r($message_result, true) . "\n";
} catch (Exception $e) {
    echo "✗ エラー: " . $e->getMessage() . "\n";
}

// 3. データベース確認
echo "\n=== データベース確認 ===\n";
require_once __DIR__ . '/../../messages/functions.php';
loadConfig();

echo "DB_PATH: " . (defined('DB_PATH') ? DB_PATH : '未定義') . "\n";
echo "DBファイル存在: " . (file_exists(DB_PATH) ? 'Yes' : 'No') . "\n";

try {
    $db = new SQLite3(DB_PATH);
    echo "✓ データベース接続成功\n";
    
    // テーブル確認
    $result = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='messages'");
    if ($result->fetchArray()) {
        echo "✓ messagesテーブル存在\n";
    } else {
        echo "✗ messagesテーブルが存在しません\n";
    }
    
    // メッセージ数確認
    $count = $db->querySingle("SELECT COUNT(*) FROM messages");
    echo "総メッセージ数: {$count}件\n";
    
    $db->close();
} catch (Exception $e) {
    echo "✗ データベースエラー: " . $e->getMessage() . "\n";
}

// 4. 関数の存在確認
echo "\n=== 関数の存在確認 ===\n";
$functions = ['validateMessage', 'saveMessage', 'generateCSRFToken', 'validateCSRFToken'];
foreach ($functions as $func) {
    echo $func . ": " . (function_exists($func) ? '✓ 存在' : '✗ 不在') . "\n";
}

// 5. テスト送信
echo "\n=== テスト送信 ===\n";
echo "POSTリクエストでテストしてください：\n";
echo "user_name=テストユーザー&message=テストメッセージ&send_message=1&csrf_token={$csrf_token}\n";

echo "</pre>";

// フォーム追加
?>
<h2>テスト送信フォーム</h2>
<form method="POST" action="">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token ?? '', ENT_QUOTES, 'UTF-8'); ?>">
    <div>
        <label>お名前:</label><br>
        <input type="text" name="user_name" value="テストユーザー" required>
    </div>
    <div>
        <label>メッセージ:</label><br>
        <textarea name="message" required>これはテストメッセージです。</textarea>
    </div>
    <div>
        <button type="submit" name="send_message">テスト送信</button>
    </div>
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    echo "<h2>送信結果</h2>";
    echo "<pre>";
    echo "POST データ:\n";
    print_r($_POST);
    echo "\n処理結果:\n";
    print_r($message_result ?? ['error' => '結果が取得できませんでした']);
    echo "</pre>";
}
?>
