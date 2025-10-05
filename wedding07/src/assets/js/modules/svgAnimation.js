/**
 * FV専用SVGアニメーション制御クラス
 * "Just Married"テキストのアニメーション専用
 */
export class SvgAnimation {
  constructor(options = {}) {
    // ストロークダッシュ配列の定数
    this.STROKE_DASH_ARRAY = 3250;
    
    this.svg = document.querySelector('.c-fvTextOverlay__svg');
    this.onComplete = options.onComplete || null;

    if (!this.svg) {
      console.warn('SVG element not found');
      return;
    }

    this.init();
  }

  init() {
    this.textElement = this.svg.querySelector('.filtered-text');
    this.maskPath = this.svg.querySelector('.mask-path');
    
    if (!this.textElement || !this.maskPath) {
      console.warn('Required SVG elements not found');
      return;
    }

    // 初期状態設定
    this.textElement.style.setProperty('--shadow-x', '0');
    this.textElement.style.setProperty('--shadow-y', '0');
    this.textElement.style.setProperty('--shadow-blur', '0');
    this.textElement.style.setProperty('--shadow-opacity', '0');
  }

  start() {
    if (!this.maskPath || !this.textElement) return;
    
    // ストロークアニメーション
    this.animateStroke();

    // マスク削除（4.5秒後）
    setTimeout(() => {
      this.textElement.style.mask = 'none';
      this.textElement.style.webkitMask = 'none';
      this.animateShadow();
    }, 4500);

    // アニメーション完了（4.5秒後）
    setTimeout(() => {
      if (this.onComplete) this.onComplete();
    }, 5500);
  }

  animateStroke() {
    const path = this.maskPath;
    path.style.strokeDasharray = this.STROKE_DASH_ARRAY.toString();
    path.style.strokeDashoffset = this.STROKE_DASH_ARRAY.toString();
    
    const startTime = performance.now();
    
    const animate = (currentTime) => {
      const elapsed = currentTime - startTime;
      const progress = Math.min(elapsed / 4500, 1);
      
      const easeInOut = progress < 0.5
        ? 2 * progress * progress
        : 1 - Math.pow(-2 * progress + 2, 2) / 2;
      
      const offset = this.STROKE_DASH_ARRAY - (this.STROKE_DASH_ARRAY * easeInOut);
      path.style.strokeDashoffset = offset;
      
      if (progress < 1) {
        requestAnimationFrame(animate);
      }
    };
    
    requestAnimationFrame(animate);
  }

  animateShadow() {
    const startTime = performance.now();
    
    const animate = (currentTime) => {
      const elapsed = currentTime - startTime;
      const progress = Math.min(elapsed / 1000, 1);
      
      const easeInOut = progress < 0.5
        ? 2 * progress * progress
        : 1 - Math.pow(-2 * progress + 2, 2) / 2;
      
      this.textElement.style.setProperty('--shadow-x', (5 * easeInOut).toString());
      this.textElement.style.setProperty('--shadow-y', (5 * easeInOut).toString());
      this.textElement.style.setProperty('--shadow-blur', (10 * easeInOut).toString());
      this.textElement.style.setProperty('--shadow-opacity', easeInOut.toString());
      
      if (progress < 1) {
        requestAnimationFrame(animate);
      }
    };
    
    requestAnimationFrame(animate);
  }
}