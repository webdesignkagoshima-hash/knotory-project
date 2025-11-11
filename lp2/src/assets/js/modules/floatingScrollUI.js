/**
 * Floating Scroll UI Module
 * exampleセクションのカードアニメーション中のみスクロールを促すUIを表示
 */
export class FloatingScrollUI {
  constructor() {
    this.floatingScrollElement = null;
    this.exampleSection = null;
    this.isVisible = false;
    
    this.init();
  }

  /**
   * 初期化
   */
  init() {
    this.floatingScrollElement = document.querySelector('.js-floatingScroll');
    this.exampleSection = document.querySelector('.p-example');
    
    if (!this.floatingScrollElement || !this.exampleSection) {
      console.warn('FloatingScroll要素またはexampleセクションが見つかりません');
      return;
    }

    this.setupScrollObserver();
    console.log('FloatingScrollUI initialized');
  }

  /**
   * スクロール監視の設定
   */
  setupScrollObserver() {
    // Intersection Observer API をサポートしているかチェック
    if (!('IntersectionObserver' in window)) {
      console.warn('IntersectionObserver is not supported');
      return;
    }

    // exampleセクションの監視を設定
    const observer = new IntersectionObserver(
      (entries) => this.handleIntersection(entries),
      {
        threshold: [0, 0.1, 0.9, 1],
        rootMargin: '0px'
      }
    );

    observer.observe(this.exampleSection);
  }

  /**
   * Intersection Observer のコールバック処理
   */
  handleIntersection(entries) {
    entries.forEach(entry => {
      const { isIntersecting, intersectionRatio } = entry;
      
      // exampleセクションが表示されている間（カードアニメーション中）のみ表示
      if (isIntersecting && intersectionRatio > 0.1 && intersectionRatio < 0.9) {
        this.showFloatingScroll();
      } else {
        this.hideFloatingScroll();
      }
    });
  }

  /**
   * フローティングスクロールUIを表示
   */
  showFloatingScroll() {
    if (!this.isVisible) {
      this.floatingScrollElement.classList.add('is-visible');
      this.isVisible = true;
      console.log('FloatingScroll UI shown');
    }
  }

  /**
   * フローティングスクロールUIを非表示
   */
  hideFloatingScroll() {
    if (this.isVisible) {
      this.floatingScrollElement.classList.remove('is-visible');
      this.isVisible = false;
      console.log('FloatingScroll UI hidden');
    }
  }

  /**
   * 破棄メソッド
   */
  destroy() {
    if (this.observer) {
      this.observer.disconnect();
      console.log('FloatingScrollUI destroyed');
    }
  }
}