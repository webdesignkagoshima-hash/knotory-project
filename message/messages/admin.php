<?php
/**
 * 共通管理画面
 * 各プロジェクト（01, 02, 03）から呼び出される共通の管理画面
 * 
 * 使用方法：
 * 各プロジェクトのadmin/index.phpで以下を実行：
 * define('PROJECT_ID', '01'); // または '02', '03'
 * require_once __DIR__ . '/../../messages/admin.php';
 */

// プロジェクトIDが定義されているか確認
if (!defined('PROJECT_ID')) {
    die('エラー: PROJECT_IDが定義されていません。');
}

$projectId = PROJECT_ID;

// 管理者セッション確認
$isAdmin = validateAdminSession();

// ログインしていない場合は auth.php にリダイレクト
if (!$isAdmin) {
    $redirect_url = '/message/' . $projectId . '/admin/index.php';
    header('Location: /message/messages/auth.php?type=admin&redirect=' . urlencode($redirect_url));
    exit;
}

// CSRFトークンの生成
$csrf_token = generateCSRFToken();

$success_message = '';

// 管理者ログアウト処理
if (isset($_GET['admin_logout'])) {
    logoutAdmin();
    header('Location: /message/messages/auth.php?type=admin&redirect=' . urlencode('/message/' . $projectId . '/admin/index.php'));
    exit;
}

// ページネーション設定
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 10;

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
    
    <!-- favicon -->
    <link rel="icon" type="image/svg+xml" href="../favicon.svg">
    <link rel="icon" href="../favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="../apple-touch-icon.png">
    
    <link rel="stylesheet" href="/message/messages/css/admin.css">
</head>
<body>
    <div class="container">
        <!-- 管理者ダッシュボード -->
        <div class="header-actions">
            <div class="header-logo">
                <img src="/message/messages/image/logo.png" alt="logo">
            </div>
        </div>
        
        <h1 class="admin-title">管理者ダッシュボード</h1>
        
        <?php if ($success_message): ?>
            <div class="message message-success">
                <?php echo htmlspecialchars($success_message, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($totalMessages === 0): ?>
            <div class="message message-info">
                まだメッセージがありません
            </div>
        <?php else: ?>
            <div class="message-list">
                <?php foreach ($allMessages as $msg): ?>
                    <div class="message-item">
                        <div class="message-header">
                            <span class="message-date"><?php echo date('Y.m.d', strtotime($msg['created_at'])); ?></span>
                        </div>
                        
                        <div class="message-subject">
                            <?php echo htmlspecialchars($msg['user_name'], ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                        
                        <div class="message-content">
                            <p><?php echo nl2br(htmlspecialchars($msg['message'], ENT_QUOTES, 'UTF-8')); ?></p>
                        </div>
                        
                        <div class="message-meta">
                            <span>IP: <?php echo htmlspecialchars($msg['user_ip'], ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- ページネーションコントロール（下部） -->
            <div class="pagination-controls">
                <div class="pagination-nav">
                    <?php if ($paginationInfo['has_prev']): ?>
                        <a href="?page=<?php echo $paginationInfo['prev_page']; ?>">前へ</a>
                    <?php endif; ?>
                    
                    <?php
                    $startPage = max(1, $page - 2);
                    $endPage = min($paginationInfo['total_pages'], $page + 2);
                    
                    for ($i = $startPage; $i <= $endPage; $i++):
                    ?>
                        <?php if ($i === $page): ?>
                            <span class="current"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($paginationInfo['has_next']): ?>
                        <a href="?page=<?php echo $paginationInfo['next_page']; ?>">次へ</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- ログアウトボタン -->
        <div class="logout-section">
            <a href="?admin_logout=1" class="btn-logout">
                ログアウト
                <svg xmlns="http://www.w3.org/2000/svg" width="14.625" height="14.625" viewBox="0 0 14.625 14.625">
                    <g transform="translate(-3.375 -3.375)">
                        <path class="logout-icon" d="M14.614,10.18a.681.681,0,0,1,.96,0l3.354,3.364a.678.678,0,0,1,.021.935l-3.3,3.315a.677.677,0,1,1-.96-.956l2.809-2.851-2.879-2.851A.671.671,0,0,1,14.614,10.18Z" transform="translate(-5.52 -3.304)"/>
                        <path class="logout-icon" d="M3.375,10.688a7.313,7.313,0,1,0,7.313-7.312A7.311,7.311,0,0,0,3.375,10.688Zm1.125,0a6.194,6.194,0,1,1,1.814,4.373A6.134,6.134,0,0,1,4.5,10.688Z"/>
                    </g>
                </svg>
            </a>
        </div>
    </div>
    
    <script>
    </script>
</body>
</html>
