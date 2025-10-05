/**
 * Contact Form Handler
 * フォーム送信処理とreCAPTCHA v3機能を管理するモジュール
 */
class ContactForm {
  constructor() {
    this.form = document.querySelector('.p-contact__form');
    this.submitButton = document.querySelector('.p-contact__submitButton');
    this.recaptchaKey = '6LdXrtYrAAAAAOkzUFcWJ21HhNmmgdCiMUQyeW48'; // テスト用のreCAPTCHA v3サイトキー
    
    this.init();
  }

  init() {
    if (!this.form) return;
    
    this.loadCsrfToken();
    this.loadRecaptcha();
    this.bindEvents();
  }

  /**
   * Google reCAPTCHA v3を動的に読み込み
   */
  loadRecaptcha() {
    if (document.querySelector('#recaptcha-script')) return;
    
    const script = document.createElement('script');
    script.id = 'recaptcha-script';
    script.src = `https://www.google.com/recaptcha/api.js?render=${this.recaptchaKey}`;
    script.async = true;
    script.defer = true;
    document.head.appendChild(script);
  }

  /**
   * csrfトークンを取得してフォームにセット
   */
  async loadCsrfToken() {
    try {
        const response = await fetch('/api/get_csrf_token.php', {
            method: 'GET',
            credentials: 'include'
        });
        
        // レスポンスのContent-Typeを確認
        const contentType = response.headers.get('content-type');
        console.log('Response Content-Type:', contentType);
        console.log('Response Status:', response.status);
        
        // レスポンステキストを取得してログ出力
        const responseText = await response.text();
        console.log('Response Text:', responseText);
        
        // JSONとしてパース試行
        let data;
        try {
            data = JSON.parse(responseText);
        } catch (parseError) {
            console.error('JSONパースエラー:', parseError);
            console.error('レスポンス内容（最初の200文字）:', responseText.substring(0, 200));
            throw new Error('サーバーからの応答が正しいJSON形式ではありません');
        }
        
        if (data.success) {
            document.getElementById('csrfToken').value = data.csrf_token;
            console.log('CSRFトークンを正常に取得しました');
        } else {
            console.error('CSRFトークンの取得に失敗しました:', data.message);
            throw new Error(data.message || 'CSRFトークンの取得に失敗しました');
        }
    } catch (error) {
        console.error('CSRFトークンの取得でエラーが発生しました:', error);
        // ユーザーにエラーを表示
        window.alert('セキュリティトークンの取得に失敗しました。ページを再読み込みしてもう一度お試しください。');
    }
  }

  /**
   * イベントバインド
   */
  bindEvents() {
    this.form.addEventListener('submit', this.handleSubmit.bind(this));
  }

  /**
   * フォーム送信処理
   */
  async handleSubmit(e) {
    e.preventDefault();
    
    // バリデーションチェック
    if (!this.validateForm()) {
      return;
    }

    // reCAPTCHA v3検証
    const recaptchaToken = await this.verifyRecaptcha();
    if (!recaptchaToken) {
      window.alert('セキュリティ検証に失敗しました。もう一度お試しください。');
      return;
    }

    // 送信処理（実際のAPIエンドポイントに変更する必要があります）
    try {
      this.setSubmitButtonState(true);
      
      // フォームデータの取得
      const formData = new FormData(this.form);
      const data = Object.fromEntries(formData);
      
      // reCAPTCHAトークンを追加
      data.recaptchaToken = recaptchaToken;
      
      // 実際の送信処理（現在はシミュレーション）
      const result = await this.submitForm(data);
      
      if (result.success) {
        // 成功メッセージ
        window.alert('送信が完了しました。ありがとうございます。\n自動返信メールをお送りしましたのでご確認ください。');
        
        // フォームリセット
        this.form.reset();

        // CSRFトークンを再取得
        await this.loadCsrfToken();
      }
      
    } catch (error) {
      console.error('送信エラー:', error);
      window.alert('送信に失敗しました。もう一度お試しください。');
    } finally {
      this.setSubmitButtonState(false);
    }
  }

  /**
   * フォームバリデーション
   */
  validateForm() {
    const requiredFields = this.form.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
      if (!field.value.trim()) {
        field.classList.add('error');
        isValid = false;
      } else {
        field.classList.remove('error');
      }
    });
    
    // お問い合わせ項目のチェック
    const inquiryType = this.form.querySelector('input[name="inquiry-type"]:checked');
    if (!inquiryType) {
      window.alert('お問い合わせ項目を選択してください。');
      isValid = false;
    }
    
    return isValid;
  }

  /**
   * reCAPTCHA v3検証
   */
  async verifyRecaptcha() {
    return new Promise((resolve) => {
      // reCAPTCHAが読み込まれているかチェック
      if (typeof grecaptcha === 'undefined') {
        console.warn('reCAPTCHA not loaded');
        resolve(null); // 開発環境では仮のトークンを返す
        return;
      }
      
      // reCAPTCHA v3でトークンを取得
      grecaptcha.ready(() => {
        grecaptcha.execute(this.recaptchaKey, { action: 'contact_form' })
          .then((token) => {
            console.log('reCAPTCHA token generated:', token);
            resolve(token);
          })
          .catch((error) => {
            console.error('reCAPTCHA error:', error);
            resolve(null);
          });
      });
    });
  }

  /**
   * フォーム送信（シミュレーション）
   */
  async submitForm(data) {
    const formData = new FormData();

    // dataオブジェクトの各プロパティをFormDataに追加
    for (const [key, value] of Object.entries(data)) {
      formData.append(key, value);
    }
    console.log('Submitting form data:', Object.fromEntries(formData.entries()));
    
    // 実際のAPIエンドポイントに送信する処理
    const response = await fetch('/api/contact.php', {
      method: 'POST',
      body: formData,
      credentials: 'include'
    });

    const result = await response.json();
    return result;
  }

  /**
   * 送信ボタンの状態制御
   */
  setSubmitButtonState(isLoading) {
    if (isLoading) {
      this.submitButton.disabled = true;
      this.submitButton.textContent = '送信中...';
    } else {
      this.submitButton.disabled = false;
      this.submitButton.textContent = '送信する';
    }
  }
}

export default ContactForm;