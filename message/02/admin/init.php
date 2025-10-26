<?php
/**
 * データベース初期化スクリプト
 * レンタルサーバーでの初回セットアップ用
 * 
 * 使い方：ブラウザで /admin/init.php にアクセスするか、
 * コマンドラインで php init.php を実行
 */

// 設定ファイルの読み込み
require_once __DIR__ . '/../config/config.production.php';

// 管理者認証（Webアクセスの場合）
if (php_sapi_name() !== 'cli') {
    session_start();
    
    if (!isset($_SESSION['init_authenticated'])) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_password'])) {
            if ($_POST['admin_password'] === ADMIN_PASSWORD) {
                $_SESSION['init_authenticated'] = true;
            } else {
                $error = '管理者パスワードが違います';
            }
        }
        
        if (!isset($_SESSION['init_authenticated'])) {
            ?>
            <!DOCTYPE html>
            <html lang="ja">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>データベース初期化</title>
                <style>
                    body { font-family: sans-serif; max-width: 500px; margin: 50px auto; padding: 20px; }
                    input[type="password"] { width: 100%; padding: 10px; margin: 10px 0; }
                    button { background: #007bff; color: white; padding: 10px 20px; border: none; cursor: pointer; }
                    .error { color: red; margin: 10px 0; }
                </style>
            </head>
            <body>
                <h1>データベース初期化</h1>
                <p>管理者パスワードを入力してください</p>
                <?php if (isset($error)) echo "<p class='error'>{$error}</p>"; ?>
                <form method="post">
                    <input type="password" name="admin_password" required placeholder="管理者パスワード">
                    <button type="submit">認証</button>
                </form>
            </body>
            </html>
            <?php
            exit;
        }
    }
}

// データベースディレクトリの作成
$dataDir = dirname(DB_PATH);
if (!is_dir($dataDir)) {
    if (!mkdir($dataDir, 0755, true)) {
        die("エラー: データディレクトリの作成に失敗しました: {$dataDir}\n");
    }
    echo "データディレクトリを作成しました: {$dataDir}\n";
}

// ログディレクトリの作成
$logDir = dirname(LOG_FILE);
if (!is_dir($logDir)) {
    if (!mkdir($logDir, 0755, true)) {
        die("エラー: ログディレクトリの作成に失敗しました: {$logDir}\n");
    }
    echo "ログディレクトリを作成しました: {$logDir}\n";
}

// データベースの初期化
try {
    // 既存のデータベースファイルがある場合はバックアップ
    if (file_exists(DB_PATH)) {
        $backupPath = DB_PATH . '.' . date('YmdHis') . '.backup';
        if (copy(DB_PATH, $backupPath)) {
            echo "既存のデータベースをバックアップしました: {$backupPath}\n";
        }
    }
    
    // SQLiteデータベースに接続
    $pdo = new PDO('sqlite:' . DB_PATH);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // テーブルの作成
    $sql = "
    CREATE TABLE IF NOT EXISTS messages (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_session_id TEXT NOT NULL,
        user_ip TEXT NOT NULL,
        user_name TEXT NOT NULL,
        message TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );
    
    CREATE INDEX IF NOT EXISTS idx_user_session_id ON messages(user_session_id);
    CREATE INDEX IF NOT EXISTS idx_created_at ON messages(created_at);
    ";
    
    $pdo->exec($sql);
    
    // ファイルのパーミッション設定
    chmod(DB_PATH, 0644);
    
    echo "データベースの初期化が完了しました\n";
    echo "データベースパス: " . DB_PATH . "\n";
    
    // 接続テスト
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM messages");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "メッセージ件数: " . $result['count'] . "\n";
    
    if (php_sapi_name() !== 'cli') {
        echo "<hr>";
        echo "<p>セットアップが完了しました。</p>";
        echo "<p><strong>セキュリティのため、このファイル (init.php) を削除してください。</strong></p>";
        echo "<p><a href='../index.php'>サイトトップに戻る</a></p>";
    } else {
        echo "\nセットアップが完了しました。\n";
        echo "セキュリティのため、このファイル (init.php) を削除してください。\n";
    }
    
} catch (PDOException $e) {
    echo "エラー: " . $e->getMessage() . "\n";
    exit(1);
}
