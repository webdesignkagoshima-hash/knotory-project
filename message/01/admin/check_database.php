<?php
/**
 * データベース状態確認スクリプト
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

// 現在のテンプレート番号を自動検出
$templateNum = basename(dirname(__DIR__));
$configPath = __DIR__ . '/../config/config.production.php';

// 設定ファイルを直接読み込む（共通のConfig::load()を使わない）
if (file_exists($configPath)) {
    require_once $configPath;
    echo "<!-- 設定ファイル読み込み: {$configPath} -->\n";
} else {
    echo "エラー: 設定ファイルが見つかりません: {$configPath}\n";
    exit(1);
}

// 共通関数を読み込む（Config::load()は既に実行済みなのでスキップされる）
require_once __DIR__ . '/../../messages/functions.php';

echo "<h1>データベース状態確認</h1>";
echo "<pre>";

$db = Database::getInstance();
if (!$db) {
    echo "エラー: データベース接続に失敗しました。\n";
    exit(1);
}

try {
    // データベースファイルのパスを取得（定数から直接取得）
    $dbPath = defined('DB_PATH') ? DB_PATH : null;
    echo "データベースファイル: " . ($dbPath ?? '(未設定)') . "\n";
    echo "ファイル存在: " . ($dbPath && file_exists($dbPath) ? 'Yes' : 'No') . "\n";
    if ($dbPath && file_exists($dbPath)) {
        echo "ファイルサイズ: " . filesize($dbPath) . " bytes\n";
        echo "最終更新: " . date('Y-m-d H:i:s', filemtime($dbPath)) . "\n";
    }
    echo "\n";
    
    // テーブル一覧
    echo "=== テーブル一覧 ===\n";
    $tables = $db->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $table) {
        echo "- {$table}\n";
    }
    echo "\n";
    
    // messagesテーブルの構造
    echo "=== messagesテーブルの構造 ===\n";
    $result = $db->query("PRAGMA table_info(messages)");
    $columns = $result->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $column) {
        echo sprintf("%-20s %-15s %s %s\n", 
            $column['name'], 
            $column['type'],
            $column['notnull'] ? 'NOT NULL' : '',
            $column['pk'] ? 'PRIMARY KEY' : ''
        );
    }
    echo "\n";
    
    // user_session_id カラムの存在確認
    $hasUserSessionId = false;
    foreach ($columns as $column) {
        if ($column['name'] === 'user_session_id') {
            $hasUserSessionId = true;
            break;
        }
    }
    
    if ($hasUserSessionId) {
        echo "✓ user_session_id カラムは存在します\n";
    } else {
        echo "✗ user_session_id カラムが見つかりません！\n";
        echo "\n";
        echo "【修正方法】\n";
        echo "1. データベースファイルを削除: {$dbPath}\n";
        echo "2. init.phpを実行して新しいデータベースを作成\n";
        echo "   または migrate_database.php を再実行\n";
    }
    echo "\n";
    
    // レコード数
    $count = $db->query("SELECT COUNT(*) FROM messages")->fetchColumn();
    echo "総メッセージ数: {$count}件\n";
    
    // サンプルデータ（最新5件）
    if ($count > 0) {
        echo "\n=== 最新5件のメッセージ ===\n";
        $stmt = $db->query("SELECT * FROM messages ORDER BY created_at DESC LIMIT 5");
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($messages as $msg) {
            echo "ID: {$msg['id']}\n";
            foreach ($msg as $key => $value) {
                if ($key !== 'id') {
                    echo "  {$key}: {$value}\n";
                }
            }
            echo "\n";
        }
    }
    
    // デバッグ情報
    echo "\n=== デバッグ情報 ===\n";
    echo "DB_PATH定数: " . (defined('DB_PATH') ? DB_PATH : '未定義') . "\n";
    echo "CONFIG_LOADED: " . (defined('CONFIG_LOADED') ? 'Yes' : 'No') . "\n";
    echo "現在のディレクトリ: " . __DIR__ . "\n";
    
} catch (PDOException $e) {
    echo "エラー: " . $e->getMessage() . "\n";
}

echo "</pre>";
