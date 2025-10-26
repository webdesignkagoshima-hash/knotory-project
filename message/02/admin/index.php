<?php
// 設定ファイルを明示的に読み込む
require_once __DIR__ . '/../config/config.production.php';

// パスの修正：messagesディレクトリは01ディレクトリと同階層にあるため、2階層上に移動
require_once __DIR__ . '/../../messages/functions.php';

initSecurity();

// CSRFトークンの生成
$csrf_token = generateCSRFToken();

$login_error = '';
$success_message = '';

// 管理者ログイン処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_login'])) {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $login_error = '不正なリクエストです。';
    } else {
        $entered_password = $_POST['admin_password'] ?? '';
        
        $login_result = loginAdmin($entered_password);
        
        if ($login_result['success']) {
            header('Location: index.php');
            exit;
        } else {
            $login_error = $login_result['message'];
        }
    }
}

// 管理者ログアウト処理
if (isset($_GET['admin_logout'])) {
    if (validateCSRFToken($_GET['token'] ?? '')) {
        logoutAdmin();
        header('Location: index.php');
        exit;
    }
}

// ダミーデータ生成処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_dummy'])) {
    if (!validateAdminSession()) {
        $login_error = '管理者としてログインしてください。';
    } elseif (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $login_error = '不正なリクエストです。';
    } else {
        try {
            $result = generateDummyMessages(120);
            if ($result) {
                $success_message = 'ダミーデータ120件を生成しました。';
                // ページをリロードして最新のデータを表示
                header('Location: index.php?success=dummy_generated');
                exit;
            } else {
                $login_error = 'ダミーデータの生成に失敗しました。ログファイルを確認してください。';
            }
        } catch (Exception $e) {
            $login_error = 'エラーが発生しました: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        }
    }
}

// 成功メッセージの処理
if (isset($_GET['success']) && $_GET['success'] === 'dummy_generated') {
    $success_message = 'ダミーデータ120件を生成しました。';
}

// 管理者セッション確認
$isAdmin = validateAdminSession();

// ページネーション設定
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = in_array((int)($_GET['per_page'] ?? 10), [10, 30, 50, 100]) ? (int)($_GET['per_page'] ?? 10) : 10;

// メッセージ取得（管理者の場合）
$allMessages = [];
$paginationInfo = [];
$totalMessages = 0;

if ($isAdmin) {
    $allMessages = getAllMessagesWithPagination($page, $perPage);
    $totalMessages = getTotalMessageCount();
    $paginationInfo = getPaginationInfo($totalMessages, $page, $perPage);
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理者ページ</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .pagination-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: var(--spacing-xl) 0;
            padding: var(--spacing-lg);
            background-color: var(--color-background-light);
            border-radius: var(--radius-md);
            border: 1px solid var(--color-border-light);
        }
        
        .per-page-selector {
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
        }
        
        .per-page-selector select {
            padding: var(--spacing-xs) var(--spacing-sm);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-sm);
            font-size: var(--font-size-sm);
        }
        
        .pagination-nav {
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
        }
        
        .pagination-nav a, .pagination-nav span {
            padding: var(--spacing-xs) var(--spacing-sm);
            text-decoration: none;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-sm);
            font-size: var(--font-size-sm);
            min-width: 40px;
            text-align: center;
        }
        
        .pagination-nav a {
            color: var(--color-primary);
            background-color: var(--color-background);
        }
        
        .pagination-nav a:hover {
            background-color: var(--color-background-dark);
        }
        
        .pagination-nav .current {
            background-color: var(--color-primary);
            color: var(--color-background);
            border-color: var(--color-primary);
        }
        
        .pagination-nav .disabled {
            color: var(--color-text-light);
            background-color: var(--color-background-light);
            border-color: var(--color-border-light);
            cursor: not-allowed;
        }
        
        .pagination-info {
            font-size: var(--font-size-sm);
            color: var(--color-text-light);
        }
        
        .generate-dummy-section {
            background-color: var(--color-background-light);
            padding: var(--spacing-xl);
            border-radius: var(--radius-lg);
            border: 1px solid var(--color-border-light);
            margin-bottom: var(--spacing-xl);
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (!$isAdmin): ?>
            <!-- 管理者ログインフォーム -->
            <h1>管理者ログイン</h1>
            
            <?php if ($login_error): ?>
                <div class="message message-error">
                    <?php echo htmlspecialchars($login_error, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="form">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">
                
                <div class="form-group">
                    <label for="admin_password" class="form-label">管理者パスワード</label>
                    <input type="password" id="admin_password" name="admin_password" required class="form-input">
                </div>
                
                <button type="submit" name="admin_login" class="btn btn-primary">ログイン</button>
            </form>
            
            <div style="text-align: center; margin-top: var(--spacing-xl);">
                <a href="index.php" class="btn btn-secondary">ユーザーページに戻る</a>
            </div>
            
        <?php else: ?>
            <!-- 管理者ダッシュボード -->
            <div class="header-actions">
                <a href="index.php" class="btn btn-secondary">ユーザーページ</a>
                <a href="?admin_logout=1&token=<?php echo urlencode($csrf_token); ?>" 
                   class="btn btn-primary" 
                   onclick="return confirm('管理者ログアウトしますか？')">
                    ログアウト
                </a>
            </div>
            
            <h1>管理者ダッシュボード</h1>
            
            <?php if ($success_message): ?>
                <div class="message message-success">
                    <?php echo htmlspecialchars($success_message, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>
            
            <!-- 統計情報 -->
            <div class="stats">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $totalMessages; ?></div>
                    <div class="stat-label">総メッセージ数</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo count($allMessages); ?></div>
                    <div class="stat-label">現在のページ</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $paginationInfo['total_pages'] ?? 0; ?></div>
                    <div class="stat-label">総ページ数</div>
                </div>
            </div>
            
            <!-- ダミーデータ生成セクション -->
            <div class="generate-dummy-section">
                <h3>ダミーデータ生成</h3>
                <p>テスト用のダミーメッセージ120件を生成します。</p>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">
                    <button type="submit" name="generate_dummy" class="btn btn-secondary" 
                            onclick="return confirm('ダミーデータを120件生成しますか？')">
                        ダミーデータ生成
                    </button>
                </form>
            </div>
            
            <h2>メッセージ一覧</h2>
            
            <?php if ($totalMessages === 0): ?>
                <div class="message message-info">
                    まだメッセージがありません
                </div>
            <?php else: ?>
                <!-- ページネーションコントロール（上部） -->
                <div class="pagination-controls">
                    <div class="per-page-selector">
                        <label for="per_page">表示件数:</label>
                        <select id="per_page" onchange="changePerPage(this.value)">
                            <option value="10" <?php echo $perPage === 10 ? 'selected' : ''; ?>>10件</option>
                            <option value="30" <?php echo $perPage === 30 ? 'selected' : ''; ?>>30件</option>
                            <option value="50" <?php echo $perPage === 50 ? 'selected' : ''; ?>>50件</option>
                            <option value="100" <?php echo $perPage === 100 ? 'selected' : ''; ?>>100件</option>
                        </select>
                    </div>
                    
                    <div class="pagination-info">
                        <?php echo $paginationInfo['start_item']; ?>-<?php echo $paginationInfo['end_item']; ?> / <?php echo $totalMessages; ?>件
                    </div>
                    
                    <div class="pagination-nav">
                        <?php if ($paginationInfo['has_prev']): ?>
                            <a href="?page=<?php echo $paginationInfo['prev_page']; ?>&per_page=<?php echo $perPage; ?>">前へ</a>
                        <?php else: ?>
                            <span class="disabled">前へ</span>
                        <?php endif; ?>
                        
                        <span class="current"><?php echo $page; ?></span>
                        
                        <?php if ($paginationInfo['has_next']): ?>
                            <a href="?page=<?php echo $paginationInfo['next_page']; ?>&per_page=<?php echo $perPage; ?>">次へ</a>
                        <?php else: ?>
                            <span class="disabled">次へ</span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="message-list">
                    <?php foreach ($allMessages as $msg): ?>
                        <div class="message-item">
                            <div class="message-header">
                                <span class="message-date"><?php echo date('Y年m月d日 H:i', strtotime($msg['created_at'])); ?></span>
                            </div>
                            
                            <div class="message-subject">
                                <?php echo htmlspecialchars($msg['user_name'], ENT_QUOTES, 'UTF-8'); ?>
                            </div>
                            
                            <div class="message-content">
                                <?php echo nl2br(htmlspecialchars($msg['message'], ENT_QUOTES, 'UTF-8')); ?>
                            </div>
                            
                            <div class="message-meta">
                                <span>IP: <?php echo htmlspecialchars($msg['user_ip'], ENT_QUOTES, 'UTF-8'); ?></span>
                                <span>セッション: <?php echo substr($msg['user_session_id'], 0, 10); ?>...</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- ページネーションコントロール（下部） -->
                <div class="pagination-controls">
                    <div class="pagination-info">
                        ページ <?php echo $page; ?> / <?php echo $paginationInfo['total_pages']; ?>
                    </div>
                    
                    <div class="pagination-nav">
                        <?php if ($paginationInfo['has_prev']): ?>
                            <a href="?page=1&per_page=<?php echo $perPage; ?>">最初</a>
                            <a href="?page=<?php echo $paginationInfo['prev_page']; ?>&per_page=<?php echo $perPage; ?>">前へ</a>
                        <?php endif; ?>
                        
                        <?php
                        $startPage = max(1, $page - 2);
                        $endPage = min($paginationInfo['total_pages'], $page + 2);
                        
                        for ($i = $startPage; $i <= $endPage; $i++):
                        ?>
                            <?php if ($i === $page): ?>
                                <span class="current"><?php echo $i; ?></span>
                            <?php else: ?>
                                <a href="?page=<?php echo $i; ?>&per_page=<?php echo $perPage; ?>"><?php echo $i; ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>
                        
                        <?php if ($paginationInfo['has_next']): ?>
                            <a href="?page=<?php echo $paginationInfo['next_page']; ?>&per_page=<?php echo $perPage; ?>">次へ</a>
                            <a href="?page=<?php echo $paginationInfo['total_pages']; ?>&per_page=<?php echo $perPage; ?>">最後</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    
    <script>
        function changePerPage(value) {
            const url = new URL(window.location);
            url.searchParams.set('per_page', value);
            url.searchParams.set('page', '1'); // Reset to first page when changing per_page
            window.location = url;
        }
    </script>
</body>
</html>