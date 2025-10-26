<?php
/**
 * メッセージ送信フォームテンプレート（表示専用）
 * 処理ロジックは process_message.php で実行済み
 */
?>

<!-- メッセージフォームセクション -->
<section class="p-message-form" id="message-form">
    <div class="p-message-form__container">
        <hgroup class="c-heading2 js-fadeIn">
            <h2 class="c-heading2__mainText">メッセージをお送りください</h2>
            <p class="c-heading2__subText">message</p>
        </hgroup>
        
        <div class="p-message-form__content js-fadeIn">
            <?php if ($message_result['success'] && $sent_message): ?>
                <!-- 送信成功メッセージ（目立つデザイン） -->
                <div class="p-message-form__success-large" id="message-success">
                    <div class="p-message-form__success-icon">
                        <svg width="60" height="60" viewBox="0 0 60 60" fill="none">
                            <circle cx="30" cy="30" r="28" fill="#28a745" stroke="#fff" stroke-width="4"/>
                            <path d="M20 30L26 36L40 22" stroke="#fff" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <h3 class="p-message-form__success-title">メッセージを送信いたしました</h3>
                    <p class="p-message-form__success-text">あたたかいお言葉をありがとうございます。<br>いただいたメッセージは、私たちの大切な宝物になります。</p>
                </div>
                
                <!-- 送信済みメッセージの表示 -->
                <div class="p-message-form__sent-message">
                    <h4 class="p-message-form__sent-title">送信されたメッセージ</h4>
                    <div class="p-message-form__sent-content">
                        <div class="p-message-form__sent-name">
                            <strong>お名前：</strong><?php echo htmlspecialchars($sent_message['user_name'], ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                        <div class="p-message-form__sent-text">
                            <strong>メッセージ：</strong>
                            <p><?php echo nl2br(htmlspecialchars($sent_message['message'], ENT_QUOTES, 'UTF-8')); ?></p>
                        </div>
                        <div class="p-message-form__sent-date">
                            送信日時：<?php echo htmlspecialchars($sent_message['sent_at'], ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- 未送信の場合はフォームを表示 -->
                <p class="p-message-form__description">
                    お祝いのメッセージやお気持ちをお聞かせください。<br>
                    いただいたメッセージは、私たちの大切な宝物になります。
                </p>

                <?php if ($message_result['error']): ?>
                    <div class="p-message-form__error">
                        <p><?php echo $message_result['error']; ?></p>
                    </div>
                <?php endif; ?>

                <form method="POST" action="#message-form" class="p-message-form__form" id="messageForm">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">
                    
                    <div class="p-message-form__group">
                        <label for="user_name" class="p-message-form__label">
                            お名前 <span class="p-message-form__required">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="user_name" 
                            name="user_name" 
                            required 
                            maxlength="50"
                            class="p-message-form__input"
                            placeholder="例）田中太郎"
                            value="<?php echo htmlspecialchars($_POST['user_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                        >
                    </div>
                    
                    <div class="p-message-form__group">
                        <label for="message" class="p-message-form__label">
                            メッセージ <span class="p-message-form__required">*</span>
                        </label>
                        <textarea 
                            id="message" 
                            name="message" 
                            required 
                            maxlength="1000"
                            rows="6"
                            class="p-message-form__textarea"
                            placeholder="お祝いのメッセージをお書きください（1000文字以内）"
                        ><?php echo htmlspecialchars($_POST['message'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                        <div class="p-message-form__counter">
                            <span id="charCount">0</span> / 1000文字
                        </div>
                    </div>
                    
                    <div class="p-message-form__submit">
                        <button type="submit" name="send_message" value="1" class="p-message-form__button">
                            メッセージを送信
                        </button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- メッセージフォーム用CSS -->
<style>
.p-message-form {
    padding: 60px 0;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
}

.p-message-form__container {
    max-width: 800px;
    margin: 0 auto;
    padding: 0 20px;
}

.p-message-form__content {
    background: #ffffff;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    margin-top: 40px;
}

.p-message-form__description {
    text-align: center;
    color: #666;
    line-height: 1.7;
    margin-bottom: 30px;
    font-size: 16px;
}

/* 送信成功メッセージ（大きく目立つデザイン） */
.p-message-form__success-large {
    text-align: center;
    padding: 40px 20px;
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    border-radius: 12px;
    margin-bottom: 30px;
    animation: successFadeIn 0.5s ease-out;
}

@keyframes successFadeIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.p-message-form__success-icon {
    margin-bottom: 20px;
    animation: successIconPop 0.6s ease-out 0.2s both;
}

@keyframes successIconPop {
    0% {
        transform: scale(0);
    }
    50% {
        transform: scale(1.1);
    }
    100% {
        transform: scale(1);
    }
}

.p-message-form__success-title {
    font-size: 24px;
    color: #155724;
    font-weight: 700;
    margin-bottom: 12px;
}

.p-message-form__success-text {
    font-size: 16px;
    color: #155724;
    line-height: 1.7;
}

/* 送信済みメッセージの表示 */
.p-message-form__sent-message {
    background: #f8f9fa;
    border: 2px solid #dee2e6;
    border-radius: 10px;
    padding: 25px;
    margin-top: 20px;
}

.p-message-form__sent-title {
    font-size: 18px;
    font-weight: 700;
    color: #333;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 2px solid #dee2e6;
}

.p-message-form__sent-content {
    color: #555;
}

.p-message-form__sent-name {
    font-size: 16px;
    margin-bottom: 15px;
}

.p-message-form__sent-text {
    font-size: 16px;
    margin-bottom: 15px;
}

.p-message-form__sent-text p {
    margin-top: 8px;
    padding: 15px;
    background: #fff;
    border-radius: 8px;
    line-height: 1.7;
    white-space: pre-wrap;
    word-wrap: break-word;
}

.p-message-form__sent-date {
    font-size: 14px;
    color: #888;
    text-align: right;
}

.p-message-form__success {
    background: #d4edda;
    border: 1px solid #c3e6cb;
    color: #155724;
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    text-align: center;
}

.p-message-form__error {
    background: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    text-align: center;
}

.p-message-form__form {
    max-width: 600px;
    margin: 0 auto;
}

.p-message-form__group {
    margin-bottom: 25px;
}

.p-message-form__label {
    display: block;
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
    font-size: 14px;
}

.p-message-form__required {
    color: #e74c3c;
}

.p-message-form__input,
.p-message-form__textarea {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 16px;
    font-family: inherit;
    transition: border-color 0.3s ease;
    box-sizing: border-box;
}

.p-message-form__input:focus,
.p-message-form__textarea:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
}

.p-message-form__textarea {
    resize: vertical;
    min-height: 120px;
}

.p-message-form__counter {
    text-align: right;
    font-size: 12px;
    color: #666;
    margin-top: 5px;
}

.p-message-form__submit {
    text-align: center;
    margin-top: 30px;
}

.p-message-form__button {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 15px 40px;
    border-radius: 50px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.p-message-form__button:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

.p-message-form__button:active {
    transform: translateY(0);
}

/* スマホ対応 */
@media (max-width: 768px) {
    .p-message-form {
        padding: 40px 0;
    }
    
    .p-message-form__content {
        padding: 30px 20px;
        margin-top: 30px;
    }
    
    .p-message-form__success-large {
        padding: 30px 15px;
    }
    
    .p-message-form__success-title {
        font-size: 20px;
    }
    
    .p-message-form__success-text {
        font-size: 14px;
    }
    
    .p-message-form__sent-message {
        padding: 20px 15px;
    }
    
    .p-message-form__sent-title {
        font-size: 16px;
    }
    
    .p-message-form__description {
        font-size: 14px;
    }
    
    .p-message-form__input,
    .p-message-form__textarea {
        font-size: 16px; /* iOSでズームを防ぐため */
    }
}
</style>

<!-- メッセージフォーム用JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const messageTextarea = document.getElementById('message');
    const charCount = document.getElementById('charCount');
    const messageForm = document.getElementById('messageForm');
    
    // 送信成功後のスムーススクロール
    if (window.location.hash === '#message-success') {
        setTimeout(function() {
            const successElement = document.getElementById('message-success');
            if (successElement) {
                successElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }, 100);
    }
    
    // 文字数カウンター
    if (messageTextarea && charCount) {
        function updateCharCount() {
            const currentLength = messageTextarea.value.length;
            charCount.textContent = currentLength;
            
            // 1000文字に近づいたら色を変える
            if (currentLength > 900) {
                charCount.style.color = '#e74c3c';
            } else if (currentLength > 800) {
                charCount.style.color = '#f39c12';
            } else {
                charCount.style.color = '#666';
            }
        }
        
        messageTextarea.addEventListener('input', updateCharCount);
        updateCharCount(); // 初期表示
    }
    
    // フォーム送信時のバリデーション
    if (messageForm) {
        messageForm.addEventListener('submit', function(e) {
            const userName = document.getElementById('user_name').value.trim();
            const message = document.getElementById('message').value.trim();
            
            if (!userName) {
                alert('お名前を入力してください。');
                e.preventDefault();
                return;
            }
            
            if (!message) {
                alert('メッセージを入力してください。');
                e.preventDefault();
                return;
            }
            
            if (message.length > 1000) {
                alert('メッセージは1000文字以内で入力してください。');
                e.preventDefault();
                return;
            }
            
            // 送信ボタンを無効化して二重送信を防ぐ
            const submitButton = this.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.textContent = '送信中...';
            }
        });
    }
});
</script>