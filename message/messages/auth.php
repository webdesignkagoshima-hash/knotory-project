<?php
require_once __DIR__ . '/functions.php';
initSecurity();

// 管理者ログインモードの判定
$is_admin_login = isset($_GET['type']) && $_GET['type'] === 'admin';
$redirect_url = $_GET['redirect'] ?? '';

// CSRFトークンの生成
$csrf_token = generateCSRFToken();

$error_message = '';

if ($is_admin_login) {
    // 管理者ログインの処理
    
    // redirect_urlが指定されていない場合はエラー
    if (!$redirect_url) {
        die('エラー: リダイレクト先が指定されていません。');
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // CSRFトークンの確認
        if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
            $error_message = '不正なリクエストです。';
            writeSecurityLog('CSRF token validation failed (admin login)', 'WARNING');
        } else {
            $entered_password = $_POST['admin_password'] ?? '';
            
            // 管理者ログイン処理
            $login_result = loginAdmin($entered_password);
            
            if ($login_result['success']) {
                // リダイレクト先に戻る
                header('Location: ' . $redirect_url);
                exit;
            } else {
                $error_message = $login_result['message'];
            }
        }
    }
    
    // 既に管理者ログイン済みの場合はリダイレクト
    if (validateAdminSession()) {
        header('Location: ' . $redirect_url);
        exit;
    }
} else {
    die('エラー: 無効なアクセスです。');
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="robots" content="noindex">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="format-detection" content="telephone=no">
  <title>管理者ログイン</title>
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/style.css">
</head>
<body class="p-password">
  <form method="POST" action="" class="p-password__inner">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">
    
    <p class="p-password__title">webで届ける、あたらしい結婚のカタチ</p>
    <div class="p-password__logo">
      <img src="image/logo.png" alt="logo">
    </div>
    <div class="p-password__title">
       <h1>コメント確認画面</h1>
    </div>
    <p class="p-password__text">パスワードをご入力ください</p>
    <div class="p-password__input">
      <input 
        id="password" 
        type="password" 
        name="admin_password" 
        placeholder="" 
        required 
        class="p-password__input__item"
        autocomplete="current-password"
      >
      <svg id="togglePassword" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
        <path d="M73 39.1C63.6 29.7 48.4 29.7 39.1 39.1C29.8 48.5 29.7 63.7 39 73.1L567 601.1C576.4 610.5 591.6 610.5 600.9 601.1C610.2 591.7 610.3 576.5 600.9 567.2L504.5 470.8C507.2 468.4 509.9 466 512.5 463.6C559.3 420.1 590.6 368.2 605.5 332.5C608.8 324.6 608.8 315.8 605.5 307.9C590.6 272.2 559.3 220.2 512.5 176.8C465.4 133.1 400.7 96.2 319.9 96.2C263.1 96.2 214.3 114.4 173.9 140.4L73 39.1zM236.5 202.7C260 185.9 288.9 176 320 176C399.5 176 464 240.5 464 320C464 351.1 454.1 379.9 437.3 403.5L402.6 368.8C415.3 347.4 419.6 321.1 412.7 295.1C399 243.9 346.3 213.5 295.1 227.2C286.5 229.5 278.4 232.9 271.1 237.2L236.4 202.5zM357.3 459.1C345.4 462.3 332.9 464 320 464C240.5 464 176 399.5 176 320C176 307.1 177.7 294.6 180.9 282.7L101.4 203.2C68.8 240 46.4 279 34.5 307.7C31.2 315.6 31.2 324.4 34.5 332.3C49.4 368 80.7 420 127.5 463.4C174.6 507.1 239.3 544 320.1 544C357.4 544 391.3 536.1 421.6 523.4L357.4 459.2z"/>
      </svg>
    </div>
    <button type="submit" class="p-password__button">
      ログイン<span></span>
    </button>
    
    <?php if ($error_message): ?>
      <p class='p-password__error'><?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>
  </form>

  <script>
    const passwordInput = document.getElementById("password");
    const togglePassword = document.getElementById("togglePassword");

    togglePassword.addEventListener("click", () => {
      const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
      passwordInput.setAttribute("type", type);
    });
  </script>
</body>
</html>