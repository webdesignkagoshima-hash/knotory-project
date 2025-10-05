import { HamburgerMenu } from './modules/hamburgerMenu.js';
import { CustomVideoPlayer } from './modules/videoPlayer.js';
import { initSmoothScroll } from './modules/smoothScroll.js';
import { initScrollAnimation } from './modules/scrollAnimation.js';
import { OurStoryCardAnimation } from './modules/ourStoryCardAnimation.js';
import { ClassObserver } from './modules/classObserver.js';
import initSlider from './modules/slider.js';

console.log('Wedding site loaded successfully!');

/**
 * シンプルなアプリケーション初期化
 */
async function initializeApp() {
  console.log('App initialization started');

  try {
    // ハンバーガーメニュー
    new HamburgerMenu();
    console.log('✅ HamburgerMenu initialized');

    // カスタム動画プレーヤー
    new CustomVideoPlayer();
    console.log('✅ VideoPlayer initialized');

    // スムーズスクロール
    initSmoothScroll();
    console.log('✅ SmoothScroll initialized');

    // スライダー
    initSlider();
    console.log('✅ Slider initialized');
    
    // スクロールアニメーション
    initScrollAnimation({
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px',
      once: true
    });
    console.log('✅ ScrollAnimation initialized');
    
    // Our Story Card Animation
    new OurStoryCardAnimation();
    console.log('✅ OurStoryCardAnimation initialized');

    // gratitudeセクションのテキストアニメーション
    const gratitudeElement = document.querySelector('.p-gratitude');
    const gratitudeThankYouElement = document.querySelector('.js-gratitude__thankYou');
    // gratitudeElementのクラスの変更を監視
    if (gratitudeElement && gratitudeThankYouElement) {
      new ClassObserver(gratitudeElement, {
        classNames: ['u-anime'],
        onClassAdded: (element, className) => {
          // 3s待ってから処理
          setTimeout(() => {
            gratitudeThankYouElement.classList.add('is-visible');
          }, 2400);
        }
      });
      console.log('✅ ClassObserver initialized for .p-gratitude');
    }
  } catch (error) {
    console.error('App initialization failed:', error);
  }
}

// DOM読み込み完了後にアプリケーションを初期化
document.addEventListener('DOMContentLoaded', initializeApp);