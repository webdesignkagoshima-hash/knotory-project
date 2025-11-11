/**
 * reCAPTCHA Badge Controller
 * ファーストビューが見えている間だけ.grecaptcha-badgeを非表示にする
 */

export class RecaptchaBadgeController {
  constructor() {
    this.fvSection = null;
    this.observer = null;
    this.isInitialized = false;
    
    this.init();
  }

  /**
   * 初期化
   */
  init() {
    // ファーストビューセクションを取得
    this.fvSection = document.querySelector('.p-fv');
    
    if (!this.fvSection) {
      console.warn('RecaptchaBadgeController: ファーストビューセクションが見つかりません');
      return;
    }

    // 初期状態：ファーストビューが見えているのでバッジを非表示
    this.hideBadge();
    
    this.setupIntersectionObserver();
    this.isInitialized = true;
    
    console.log('RecaptchaBadgeController initialized');
  }

  /**
   * Intersection Observer の設定
   */
  setupIntersectionObserver() {
    // Intersection Observer API をサポートしているかチェック
    if (!('IntersectionObserver' in window)) {
      console.warn('RecaptchaBadgeController: IntersectionObserver is not supported');
      return;
    }

    // ファーストビューの監視を設定
    this.observer = new IntersectionObserver(
      (entries) => this.handleIntersection(entries),
      {
        threshold: 0.1, // ファーストビューの10%が見えているかどうか
        rootMargin: '0px'
      }
    );

    this.observer.observe(this.fvSection);
  }

  /**
   * Intersection Observer のコールバック処理
   */
  handleIntersection(entries) {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        // ファーストビューが見えている間はバッジを非表示
        this.hideBadge();
      } else {
        // ファーストビューが見えなくなったらバッジを表示
        this.showBadge();
      }
    });
  }

  /**
   * reCAPTCHAバッジを非表示にする
   */
  hideBadge() {
    document.body.classList.add('is-fv-visible');
    console.log('RecaptchaBadgeController: reCAPTCHA badge hidden');
  }

  /**
   * reCAPTCHAバッジを表示する
   */
  showBadge() {
    document.body.classList.remove('is-fv-visible');
    console.log('RecaptchaBadgeController: reCAPTCHA badge shown');
  }

  /**
   * 破棄メソッド
   */
  destroy() {
    if (this.observer) {
      this.observer.disconnect();
      this.observer = null;
    }
    
    // クラスを削除してバッジを表示状態に戻す
    document.body.classList.remove('is-fv-visible');
    
    console.log('RecaptchaBadgeController destroyed');
  }
}

export default RecaptchaBadgeController;