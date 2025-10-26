<?php
require_once __DIR__ . '/functions.php';
initSecurity();

// CSRFトークンの生成
$csrf_token = generateCSRFToken();

$error_message = '';
$login_attempt_info = getLoginAttemptInfo();
$is_locked_out = $login_attempt_info['is_locked_out'];

// ロックアウト状態のチェック
if ($is_locked_out) {
    $remaining_time = $login_attempt_info['remaining_time'];
    $error_message = "ログイン試行回数が上限に達しました。{$remaining_time}秒後に再試行してください。";
    writeSecurityLog("Account locked out, remaining time: {$remaining_time}s", 'WARNING');
}

// フォーム送信の処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$is_locked_out) {
    // CSRFトークンの確認
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error_message = '不正なリクエストです。';
        writeSecurityLog('CSRF token validation failed', 'WARNING');
    } else {
        $entered_password = $_POST['password'] ?? '';
        
        // ログイン処理
        $login_result = loginUser($entered_password);
        
        if ($login_result['success']) {
            // index.phpにリダイレクト
            header('Location: index.php');
            exit;
        } else {
            $error_message = $login_result['message'];
        }
    }
}

// 既にログイン済みの場合はindex.phpにリダイレクト
if (validateSession()) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン - サンプルウェブサイト</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Login specific styles */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        
        .login-container {
            width: 100%;
            max-width: 400px;
            padding: var(--spacing-2xl);
        }
        
        .password-input-container {
            position: relative;
            display: flex;
            align-items: center;
        }
        
        .form-input {
            padding-right: 50px;
        }
        
        .toggle-password {
            position: absolute;
            right: var(--spacing-md);
            background: none;
            border: none;
            cursor: pointer;
            font-size: var(--font-size-lg);
            color: var(--color-text-light);
            z-index: 2;
            transition: color var(--transition-normal);
        }
        
        .toggle-password:hover {
            color: var(--color-primary);
        }
        
        .submit-btn {
            width: 100%;
        }
        
        .submit-btn:disabled {
            background-color: var(--color-border);
            color: var(--color-text-light);
            cursor: not-allowed;
        }
        
        .attempts-info {
            font-size: var(--font-size-xs);
            color: var(--color-text-light);
            text-align: center;
            margin-top: var(--spacing-md);
        }
    </style>
</head>
<body>
    <div class="container login-container">
        <h1 style="text-align: center;">ログイン</h1>
        
        <?php if ($error_message): ?>
            <div class="message message-error">
                <?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="" class="form">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">
            
            <div class="form-group">
                <label for="password" class="form-label">パスワード</label>
                <div class="password-input-container">
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required 
                        class="form-input"
                        <?php echo $is_locked_out ? 'disabled' : ''; ?>
                        autocomplete="current-password"
                    >
                    <button type="button" class="toggle-password">👁️</button>
                </div>
            </div>
            
            <button 
                type="submit" 
                class="btn btn-primary submit-btn" 
                <?php echo $is_locked_out ? 'disabled' : ''; ?>
            >
                ログイン
            </button>
        </form>
        
        <div class="attempts-info">
            ログイン試行回数: <?php echo $login_attempt_info['attempts']; ?>/<?php echo $login_attempt_info['max_attempts']; ?>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const toggleButton = document.querySelector('.toggle-password');
            
            // パスワード表示切替
            toggleButton.addEventListener('click', function() {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    toggleButton.textContent = '🙈';
                } else {
                    passwordInput.type = 'password';
                    toggleButton.textContent = '👁️';
                }
            });
        });
    </script>
</body>
</html>