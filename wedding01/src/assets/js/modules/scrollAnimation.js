/**
 * スクロールアニメーションを管理するクラス
 */
export class ScrollAnimation {
  constructor(options = {}) {
    this.options = {
      threshold: 0.1, // 要素の10%が見えたらアニメーション開始
      rootMargin: '0px 0px -50px 0px', // 少し手前でアニメーション開始
      once: true, // 一度だけアニメーションを実行
      ...options
    };

    this.observer = null;
    this.animationElements = [];
    
    this.init();
  }

  /**
   * 初期化
   */
  init() {
    // JavaScriptが有効であることを示すクラスをhtmlタグに追加
    document.documentElement.classList.add('js');
    
    // Intersection Observer API をサポートしているかチェック
    if (!('IntersectionObserver' in window)) {
      console.warn('IntersectionObserver is not supported. Fallback to immediate animation.');
      this.fallbackAnimation();
      return;
    }

    this.setupObserver();
    this.findAnimationElements();
    this.observeElements();
  }

  /**
   * Intersection Observer の設定
   */
  setupObserver() {
    this.observer = new IntersectionObserver(
      (entries) => this.handleIntersection(entries),
      {
        threshold: this.options.threshold,
        rootMargin: this.options.rootMargin
      }
    );
  }

  /**
   * アニメーション対象の要素を検索
   */
  findAnimationElements() {
    const animationClasses = ['js-fadeIn', 'js-fadeUp', 'js-fadeDown', 'js-scaleUp', 'js-anime'];
    
    animationClasses.forEach(className => {
      const elements = document.querySelectorAll(`.${className}`);
      elements.forEach(element => {
        this.animationElements.push({
          element,
          animationClass: className.replace('js-', 'u-'),
          triggered: false
        });
      });
    });

    console.log(`Found ${this.animationElements.length} animation elements`);
  }

  /**
   * 要素の監視を開始
   */
  observeElements() {
    this.animationElements.forEach(item => {
      this.observer.observe(item.element);
    });
  }

  /**
   * Intersection Observer のコールバック処理
   */
  handleIntersection(entries) {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const animationItem = this.animationElements.find(
          item => item.element === entry.target
        );

        if (animationItem && !animationItem.triggered) {
          this.triggerAnimation(animationItem);
          
          // 一度だけ実行する場合は監視を停止
          if (this.options.once) {
            this.observer.unobserve(entry.target);
          }
        }
      }
    });
  }

  /**
   * アニメーションを実行
   */
  triggerAnimation(animationItem) {
    const { element, animationClass } = animationItem;
    
    // アニメーションクラスを追加
    element.classList.add(animationClass);
    animationItem.triggered = true;

    console.log(`Animation triggered: ${animationClass} on`, element);

    // アニメーション完了後のイベントリスナー（オプション）
    element.addEventListener('animationend', () => {
      console.log(`Animation completed: ${animationClass}`);
    }, { once: true });
  }

  /**
   * Intersection Observer 非対応ブラウザ用のフォールバック
   */
  fallbackAnimation() {
    this.findAnimationElements();
    
    // 即座にすべてのアニメーションを実行
    this.animationElements.forEach(item => {
      this.triggerAnimation(item);
    });
  }

  /**
   * 新しい要素を動的に追加する場合の処理
   */
  addElement(element, animationType) {
    const animationClass = `u-${animationType}`;
    const animationItem = {
      element,
      animationClass,
      triggered: false
    };

    this.animationElements.push(animationItem);
    
    if (this.observer) {
      this.observer.observe(element);
    } else {
      // フォールバック実行
      this.triggerAnimation(animationItem);
    }
  }

  /**
   * 監視を停止
   */
  destroy() {
    if (this.observer) {
      this.observer.disconnect();
      console.log('ScrollAnimation destroyed');
    }
  }

  /**
   * 手動でアニメーションをリセット（開発・デバッグ用）
   */
  resetAnimations() {
    this.animationElements.forEach(item => {
      item.element.classList.remove(item.animationClass);
      item.triggered = false;
    });
    
    // 再度監視を開始
    if (this.observer) {
      this.observeElements();
    }
  }
}

/**
 * シンプルな初期化関数（他のモジュールとの統一性のため）
 */
export function initScrollAnimation(options = {}) {
  return new ScrollAnimation(options);
}