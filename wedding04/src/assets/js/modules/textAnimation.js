/**
 * 文字ごとのフェードインアニメーション機能
 */
export class TextAnimation {
  constructor(selector, options = {}) {
    this.elements = document.querySelectorAll(selector);
    this.options = {
      delay: 100, // 文字間の遅延時間（ms）
      duration: 100, // アニメーション時間（ms）
      easing: 'cubic-bezier(0.4, 0.0, 0.2, 1)', // イージング
      threshold: 0.3, // IntersectionObserverのしきい値
      initDelay: 0, // アニメーション開始の初期遅延時間（ms）
      ...options
    };
    
    this.observers = [];
    this.init();
  }

  /**
   * 初期化
   */
  init() {
    if (this.elements.length === 0) return;

    this.elements.forEach(element => {
      this.setupElement(element);
      this.observeElement(element);
    });
  }

  /**
   * 要素をセットアップ（文字を分割してspan要素で囲む）
   */
  setupElement(element) {
    const text = element.textContent;
    const chars = Array.from(text);
    
    // 文字をspan要素で囲む（全体の初期遅延 + 文字間遅延）
    const wrappedChars = chars.map((char, index) => {
      if (char === ' ') {
        return '&nbsp;'; // スペースは&nbsp;に変換
      }
      return `<span class="js-char" style="--delay: ${this.options.initDelay + index * this.options.delay}ms">${char}</span>`;
    }).join('');
    
    element.innerHTML = wrappedChars;
    element.classList.add('u-textAnimation');
  }

  /**
   * IntersectionObserverで要素を監視
   */
  observeElement(element) {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          this.startAnimation(entry.target);
          observer.unobserve(entry.target); // 一度実行したら監視を停止
        }
      });
    }, {
      threshold: this.options.threshold
    });

    observer.observe(element);
    this.observers.push(observer);
  }

  /**
   * アニメーションを開始
   */
  startAnimation(element) {
    const chars = element.querySelectorAll('.js-char');
    element.classList.add('is-animating');
    
    chars.forEach(char => {
      char.classList.add('is-visible');
    });
  }

  /**
   * オブザーバーを破棄
   */
  destroy() {
    this.observers.forEach(observer => {
      observer.disconnect();
    });
    this.observers = [];
  }
}