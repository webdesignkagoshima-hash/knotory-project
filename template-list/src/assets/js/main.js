import initSlider from './modules/slider.js';

async function initializeApp() {
  try {
    // スライダー
    initSlider();
    console.log('✅ Slider initialized');
  } catch (error) {
    console.error('Error initializing app:', error);
  }
}

// DOM読み込み完了後にアプリケーションを初期化
document.addEventListener('DOMContentLoaded', initializeApp);