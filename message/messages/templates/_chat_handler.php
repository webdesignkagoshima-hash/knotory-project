<?php
/**
 * メッセージ送信処理ハンドラー
 * index.phpのHTML出力前に読み込んで実行する
 */

try {
    // 共通処理の読み込み
    require_once __DIR__ . '/../functions.php';

    // セッションが開始されていない場合のみ初期化
    if (session_status() === PHP_SESSION_NONE) {
        initSecurity();
    }

    // 設定が読み込まれていない場合は読み込み
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

    // セッションに送信済みメッセージがあるかチェック
    $sent_message = null;
    if (isset($_SESSION['sent_message']) && is_array($_SESSION['sent_message'])) {
        $sent_message = $_SESSION['sent_message'];
    }

    // POST送信の処理
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['send_message']) || (isset($_POST['user_name']) && isset($_POST['message'])))) {
        // 既に送信済みの場合は処理しない
        if ($sent_message !== null) {
            $message_result['error'] = '既にメッセージを送信済みです。';
        } elseif (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
            $message_result['error'] = '不正なリクエストです。ページを再読み込みしてください。';
        } else {
            $user_name = trim($_POST['user_name'] ?? '');
            $user_message = trim($_POST['message'] ?? '');
            
            // バリデーション
            $validation_errors = validateMessage($user_name, $user_message);
            
            if (!empty($validation_errors)) {
                // エラーがある場合
                $message_result['error'] = implode('<br>', $validation_errors);
            } else {
                // メッセージ保存
                $session_id = session_id();
                $user_ip = $_SERVER['REMOTE_ADDR'] ?? '不明';
                
                $save_result = saveMessage($session_id, $user_ip, $user_name, $user_message);
                
                if ($save_result) {
                    // セッションに送信内容を保存
                    $_SESSION['sent_message'] = [
                        'user_name' => $user_name,
                        'message' => $user_message,
                        'sent_at' => date('Y-m-d H:i:s')
                    ];
                    
                    // セキュリティログ記録
                    writeSecurityLog("メッセージ送信成功: {$user_name} (IP: {$user_ip})");
                    
                    // POST後のリダイレクトでフォーム再送信を防止（ハッシュ付きでメッセージ位置にスクロール）
                    $redirect_url = strtok($_SERVER['REQUEST_URI'], '?') . '?sent=1#message-success';
                    
                    header('Location: ' . $redirect_url);
                    exit;
                } else {
                    $message_result['error'] = 'メッセージの送信に失敗しました。しばらく後で再度お試しください。';
                    writeSecurityLog("メッセージ送信失敗: {$user_name} (IP: {$user_ip})", 'ERROR');
                }
            }
        }
    }

    // GET パラメータでsent=1が設定されている場合は成功メッセージを表示
    if (isset($_GET['sent']) && $_GET['sent'] == '1' && $sent_message !== null) {
        $message_result['success'] = true;
        $message_result['message'] = 'メッセージを送信いたしました。ありがとうございます。';
    }

} catch (Exception $e) {
    $message_result['error'] = 'システムエラーが発生しました。しばらく後で再度お試しください。';
    writeSecurityLog("システムエラー: " . $e->getMessage(), 'ERROR');
}
