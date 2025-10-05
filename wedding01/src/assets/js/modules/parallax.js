/**
 * Parallax effect module for background fixed images
 */
export class Parallax {
  constructor(selector = '.p-ourStory__container', options = {}) {
    this.container = document.querySelector(selector);
    this.options = {
      cssVariableName: '--our-story-bg-attachment-fixed',
      ...options
    };
    
    if (!this.container) {
      console.warn(`Parallax: Container not found with selector "${selector}"`);
      return;
    }

    this.viewportHeight = window.innerHeight;
    this.init();
  }

  init() {
    this.bindEvents();
    console.log('✅ Parallax initialized');
  }

  bindEvents() {
    window.addEventListener('scroll', () => this.updateParallaxPosition());
    window.addEventListener('resize', () => this.handleResize());
  }

  updateParallaxPosition() {
    // ビューポートの高さを更新
    this.viewportHeight = window.innerHeight;
    // コンテナの位置を取得
    const rect = this.container.getBoundingClientRect();
    const { top: rectTop, bottom: rectBottom } = rect;
    const { viewportHeight } = this;
    
    let bgPosition;

    if (rectTop <= viewportHeight && rectTop > 0) {
      // コンテナの上端がビューポート内にある場合      
      bgPosition = `${rectTop}px`;
    } else if (rectTop <= 0 && rectBottom >= viewportHeight) {
      // コンテナ全体がビューポート内にある場合
      bgPosition = '0px';
    } else if (rectBottom < viewportHeight && rectBottom >= 0) {
      // コンテナの下端がビューポート内にある場合
      bgPosition = `${rectBottom - viewportHeight}px`;
    } else {
      bgPosition = `${viewportHeight}px`;
    }

    document.documentElement.style.setProperty(
      this.options.cssVariableName, 
      bgPosition
    );
  }

  handleResize() {
    this.viewportHeight = window.innerHeight;
    this.updateParallaxPosition();
  }

  destroy() {
    window.removeEventListener('scroll', this.updateParallaxPosition);
    window.removeEventListener('resize', this.handleResize);
  }
}

export default Parallax;