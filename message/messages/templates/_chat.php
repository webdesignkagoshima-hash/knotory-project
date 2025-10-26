<?php
/**
 * メッセージ送信フォームテンプレート（表示専用）
 * 処理ロジックは process_message.php で実行済み
 */
?>

<!-- CSS読み込み -->
<style>
<?php include __DIR__ . '/../css/chat.css'; ?>
</style>

<!-- アクセシビリティのため見出しタグを非表示で残す -->
<h2 class="u-visuallyHidden">メッセージをお送りください</h2>

<section class="p-comments js-fadeIn">
    <div class="p-comments__container">
        <div class="p-comments__content">
            <?php if ($message_result['success'] && $sent_message): ?>
                <!-- 送信成功時の表示 -->
                <div class="p-comments__success-display" id="message-success">                    
                    <p class="p-comments__success-message">送信が無事完了しました！</p>
                    
                    <!-- 送信済みメッセージの表示 -->
                    <div class="p-comments__sent-message">
                        <h3 class="p-comments__sent-title">送信されたメッセージ</h3>
                        <div class="p-comments__sent-content">
                            <div class="p-comments__sent-name">
                                <strong>お名前：</strong><?php echo htmlspecialchars($sent_message['user_name'], ENT_QUOTES, 'UTF-8'); ?>
                            </div>
                            <div class="p-comments__sent-text">
                                <strong>メッセージ：</strong>
                                <p><?php echo nl2br(htmlspecialchars($sent_message['message'], ENT_QUOTES, 'UTF-8')); ?></p>
                            </div>
                            <div class="p-comments__sent-date">
                                送信日時：<?php echo htmlspecialchars($sent_message['sent_at'], ENT_QUOTES, 'UTF-8'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- アイコン追加 -->
                <div class="p-comments__icon">
                    <img src="../messages/image/message.png" alt="メッセージ">
                </div>
                
                <p class="p-comments__intro">みなさまからの温かい<br>お祝いメッセージをお待ちしています</p>
                <p class="p-comments__note">※メッセージは新郎新婦だけに届きます</p>
                
                <?php if ($message_result['error']): ?>
                    <div class="p-comments__message error">
                        <p><?php echo $message_result['error']; ?></p>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="#message-form" class="p-comments__form" id="commentForm">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">
                    
                    <div class="p-comments__field">
                        <label for="user_name" class="p-comments__label">
                            お名前（ニックネーム可）
                        </label>
                        <input 
                            type="text" 
                            id="user_name" 
                            name="user_name" 
                            class="p-comments__input" 
                            placeholder="例）田中　太郎" 
                            required
                            maxlength="50"
                            value="<?php echo htmlspecialchars($_POST['user_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                        >
                    </div>
                    
                    <div class="p-comments__field">
                        <label for="message" class="p-comments__label">
                            お祝いメッセージ
                        </label>
                        <textarea 
                            id="message" 
                            name="message" 
                            class="p-comments__textarea" 
                            placeholder="例）ご結婚おめでとうございます！" 
                            rows="4" 
                            required
                            maxlength="1000"
                        ><?php echo htmlspecialchars($_POST['message'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                        <div class="p-comments__counter">
                            <span id="charCount">0</span> / 1000文字
                        </div>
                    </div>
                    
                    <div class="p-comments__submit">
                        <button type="submit" name="send_message" value="1" class="p-comments__button" id="submitBtn">
                            この内容で送る
                        </button>
                    </div>
                    
                    <div id="submitMessage" class="p-comments__message" style="display: none;"></div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
// 文字数カウンター
document.addEventListener('DOMContentLoaded', function() {
  const messageTextarea = document.getElementById('message');
  const charCount = document.getElementById('charCount');
  
  if (messageTextarea && charCount) {
    // 初期値を設定
    charCount.textContent = messageTextarea.value.length;
    
    // 入力時に文字数を更新
    messageTextarea.addEventListener('input', function() {
        charCount.textContent = this.value.length;
    });
  }
});
</script>