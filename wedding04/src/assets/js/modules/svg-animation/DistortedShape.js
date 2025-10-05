import { NoiseAnimation } from './NoiseAnimation.js';

/**
 * SVGパス要素をノイズで歪ませてアニメーションさせるクラス
 * マスクパス、通常のパス要素どちらにも対応
 */
export class DistortedShape {
  constructor(pathElement, options = {}) {
    this.pathElement = pathElement;
    this.options = {
      frequency: 0.001,
      amplitude: 10,
      speed: 0.01,
      targetFPS: 60,
      ...options,
    };
    
    this.originalPath = pathElement.getAttribute('d');
    this.isActive = true;
    this.animationInstance = null;
    
    this.init();
  }

  /**
   * アニメーションの初期化
   */
  init() {
    this.animationInstance = new NoiseAnimation(this.pathElement, this.options);
  }

  /**
   * アニメーション一時停止
   */
  pause() {
    this.isActive = false;
    if (this.animationInstance) {
      this.animationInstance.pause();
    }
  }

  /**
   * アニメーション再開
   */
  resume() {
    this.isActive = true;
    if (this.animationInstance) {
      this.animationInstance.resume();
    }
  }

  /**
   * パス更新処理（ノイズアニメーション用）
   */
  updatePath() {
    if (this.animationInstance && this.animationInstance.updatePath) {
      this.animationInstance.updatePath();
    }
  }

  /**
   * アニメーションを停止して元の状態に戻す
   */
  destroy() {
    if (this.animationInstance) {
      this.animationInstance.destroy();
      this.animationInstance = null;
    }
    this.pathElement.setAttribute('d', this.originalPath);
    this.isActive = false;
  }
}