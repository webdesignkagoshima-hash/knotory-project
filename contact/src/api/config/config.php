<?php
// メール設定
return [
    'mail' => [
        'from_email' => 'info@damp-saito-5510.main.jp',
        'admin_email' => 'info@damp-saito-5510.main.jp',
        'from_name' => 'お問い合わせフォーム',
        // SMTP設定
        'smtp' => [
            'enable' => true,
            'host' => $_ENV['SMTP_HOST'] ?? 'smtp.example.com',
            'port' => $_ENV['SMTP_PORT'] ?? 587,
            'auth' => true,
            'username' => $_ENV['SMTP_USERNAME'] ?? '',
            'password' => $_ENV['SMTP_PASSWORD'] ?? '',
            'secure' => $_ENV['SMTP_SECURE'] ?? 'tls',
            'charset' => 'UTF-8',
            'timeout' => 30,
        ],
    ],
    'messages' => [
        'admin' => [
            'subject' => 'お問い合わせを受け付けました',
            'body_template' => "新しいお問い合わせを受け付けました。\n\n"
                . "お問い合わせ項目: {inquiry_type}\n"
                . "お名前: {name}\n"
                . "メールアドレス: {email}\n"
                . "電話番号: {phone}\n"
                . "お問い合わせ内容:\n{message}\n\n"
                . "送信日時: {datetime}"
        ],
        'auto_reply' => [
            'subject' => 'お問い合わせありがとうございます',
            'body_template' => "{name} 様\n\n"
                . "この度は、お問い合わせいただきありがとうございます。\n"
                . "以下の内容でお問い合わせを受け付けいたしました。\n\n"
                . "お問い合わせ項目: {inquiry_type}\n"
                . "お名前: {name}\n"
                . "メールアドレス: {email}\n"
                . "電話番号: {phone}\n"
                . "お問い合わせ内容:\n{message}\n\n"
                . "内容を確認の上、担当者より改めてご連絡させていただきます。\n"
                . "今しばらくお待ちください。\n\n"
                . "このメールは自動送信されています。\n"
                . "返信いただく必要はございません。"
        ]
    ],
    'validation' => [
        'max_name_length' => 50,
        'max_message_length' => 2000,
        'phone_pattern' => '/^\d{10,11}$/'
    ],
    'security' => [
        // Rate Limiting設定
        'max_requests_per_window' => 5,    // 時間窓内の最大リクエスト数
        'rate_limit_window' => 300,        // 時間窓（秒）= 5分
        
        // 入力値制限
        'max_input_length' => 10000,       // 全体の入力データサイズ制限
        'honeypot_field' => 'website',     // ハニーポット用フィールド名
        
        // IPホワイトリスト（必要に応じて）
        'allowed_ips' => [],               // 空の場合は全IP許可
        
        // ログ設定
        'log_suspicious_activity' => true,
        'log_directory' => __DIR__ . '/../logs/',
        'security_log_file' => 'security.log',
        'access_log_file' => 'access.log',
        'rate_limit_data_dir' => __DIR__ . '/../logs/rate_limit/',
        
        // ログレベル制御（本番では'warning'か'error'を推奨）
        'log_level' => 'warning',             // debug, info, warning, error
        'max_log_size' => 10 * 1024 * 1024, // 10MB
        'log_retention_days' => 7,         // ログ保持日数
    ],
    'recaptcha' => [
        'secret_key' => $_ENV['RECAPTCHA_SECRET_KEY'] ?? '',  // 環境変数から取得
        'min_score' => 0.5, // reCAPTCHA v3用のスコア閾値（0.0-1.0）
    ],
    'cors' => [
        // 許可するドメイン（本番環境では実際のドメインに変更してください）
        'allowed_origins' => [
            'https://talkroom-16064731.akky-cr.xyz',
            'https://knotory.jp',
            'http://localhost:5473',  // 開発環境用
            'http://localhost:5500',  // 開発環境用
            'http://127.0.0.1:5500',  // 開発環境用
        ],
        'allowed_methods' => ['GET', 'POST', 'OPTIONS'],  // GETメソッドを追加
        'allowed_headers' => ['Content-Type', 'X-Requested-With', 'Authorization', 'X-CSRF-Token'],
        'max_age' => 86400  // プリフライトキャッシュ時間（秒）
    ]
];
?>