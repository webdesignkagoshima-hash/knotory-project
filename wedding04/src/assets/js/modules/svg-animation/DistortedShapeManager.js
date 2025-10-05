import gsap from 'gsap';

/**
 * 複数のDistortedShapeを効率的に管理するマネージャークラス
 */
export class DistortedShapeManager {
  constructor() {
    this.shapes = new Map();
    this.isAnimating = false;
    this.animationId = null;
    this.performanceMonitor = {
      frameCount: 0,
      lastTime: performance.now(),
      enabled: false
    };
  }

  /**
   * 図形を追加
   * @param {string} selector - セレクタ
   * @param {object} options - オプション
   * @returns {boolean} 追加に成功したかどうか
   */
  addShape(selector, options = {}) {
    const element = document.querySelector(selector);
    if (!element) {
      console.warn(`要素が見つかりません: ${selector}`);
      return false;
    }

    // 動的インポートを使用してDistortedShapeをロード
    import('./DistortedShape.js').then(({ DistortedShape }) => {
      const shape = new DistortedShape(element, options);
      if (shape.animation) {
        gsap.ticker.remove(shape.animation);
        shape.animation = null;
      }
      
      this.shapes.set(selector, shape);
      
      // 最初の図形追加時にアニメーション開始
      if (this.shapes.size === 1) {
        this.startAnimation();
      }
    });
    
    return true;
  }

  /**
   * 一括でアニメーション実行（パフォーマンス最適化）
   */
  startAnimation() {
    if (this.isAnimating) return;
    
    this.isAnimating = true;
    this.animationId = gsap.ticker.add(() => {
      // 全ての図形を一度に更新
      this.shapes.forEach(shape => {
        if (shape.updatePath) {
          shape.updatePath();
        }
      });
      
      // パフォーマンス監視
      if (this.performanceMonitor.enabled) {
        this.updatePerformanceMonitor();
      }
    });
  }

  /**
   * パフォーマンス監視の更新
   */
  updatePerformanceMonitor() {
    this.performanceMonitor.frameCount++;
    const currentTime = performance.now();
    
    if (currentTime - this.performanceMonitor.lastTime >= 1000) {
      console.log(`FPS: ${this.performanceMonitor.frameCount}, Active shapes: ${this.shapes.size}`);
      this.performanceMonitor.frameCount = 0;
      this.performanceMonitor.lastTime = currentTime;
    }
  }

  /**
   * パフォーマンス監視の有効/無効切り替え
   * @param {boolean} enabled - 有効にするかどうか
   */
  setPerformanceMonitoring(enabled) {
    this.performanceMonitor.enabled = enabled;
    if (enabled) {
      this.performanceMonitor.frameCount = 0;
      this.performanceMonitor.lastTime = performance.now();
    }
  }

  /**
   * 特定の図形を一時停止
   * @param {string} selector - セレクタ
   */
  pauseShape(selector) {
    const shape = this.shapes.get(selector);
    if (shape) {
      shape.pause();
    }
  }

  /**
   * 特定の図形を再開
   * @param {string} selector - セレクタ
   */
  resumeShape(selector) {
    const shape = this.shapes.get(selector);
    if (shape) {
      shape.resume();
    }
  }

  /**
   * 全ての図形を一時停止
   */
  pauseAll() {
    this.shapes.forEach(shape => shape.pause());
  }

  /**
   * 全ての図形を再開
   */
  resumeAll() {
    this.shapes.forEach(shape => shape.resume());
  }

  /**
   * アニメーション停止
   */
  stopAnimation() {
    if (this.animationId) {
      gsap.ticker.remove(this.animationId);
      this.animationId = null;
      this.isAnimating = false;
    }
  }

  /**
   * 特定の図形を削除
   * @param {string} selector - セレクタ
   */
  removeShape(selector) {
    const shape = this.shapes.get(selector);
    if (shape) {
      shape.destroy();
      this.shapes.delete(selector);
      
      // 全ての図形が削除されたらアニメーション停止
      if (this.shapes.size === 0) {
        this.stopAnimation();
      }
    }
  }

  /**
   * 全ての図形を削除
   */
  destroy() {
    this.shapes.forEach(shape => shape.destroy());
    this.shapes.clear();
    this.stopAnimation();
  }

  /**
   * 現在のパフォーマンス情報を取得
   * @returns {object} パフォーマンス情報
   */
  getPerformanceInfo() {
    const activeCount = Array.from(this.shapes.values()).filter(shape => shape.isActive).length;
    return {
      totalShapes: this.shapes.size,
      activeShapes: activeCount,
      isAnimating: this.isAnimating,
      monitoringEnabled: this.performanceMonitor.enabled
    };
  }
}