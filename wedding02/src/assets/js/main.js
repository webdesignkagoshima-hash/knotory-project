import { HamburgerMenu } from './modules/hamburgerMenu.js';
import { CustomVideoPlayer } from './modules/videoPlayer.js';
import { initSmoothScroll } from './modules/smoothScroll.js';
import { initScrollAnimation } from './modules/scrollAnimation.js';
import { ClassObserver } from './modules/classObserver.js';
import { SvgAnimation } from './modules/svgAnimation.js';
import { OurStoryCardAnimation } from './modules/ourStoryCardAnimation.js';
import { ScrollToTopButton } from './modules/scrollToTopButton.js';
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

    // Our Story Card Animation
    new OurStoryCardAnimation();
    console.log('✅ OurStoryCardAnimation initialized');

    // SVGアニメーション初期化
    function initSvgAnimation() {
      const svgElement = document.querySelector('.c-fvTextOverlay__svg');
      
      if (svgElement) {
        // Intersection Observerでスクロールトリガー
        const observer = new IntersectionObserver((entries) => {
          entries.forEach(entry => {
            if (entry.isIntersecting && entry.intersectionRatio >= 0.3) {
              // SVGアニメーション開始
              new SvgAnimation({
                onComplete: () => {
                  console.log('SVG animation completed');
                }
              }).start();
              
              // 一度だけ実行
              observer.disconnect();
            }
          });
        }, {
          threshold: 0.3
        });
        
        observer.observe(svgElement);
      }
    }

    // SVGアニメーション初期化
    initSvgAnimation();
    console.log('✅ SVG Animation initialized');

    // スクロールアニメーション
    initScrollAnimation({
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px',
      once: true
    });
    console.log('✅ ScrollAnimation initialized');

    // ScrollToTopButton
    new ScrollToTopButton();
    console.log('✅ ScrollToTopButton initialized');

    // gratitudeセクションのテキストアニメーション
    const gratitudeElement = document.querySelector('.p-gratitude');
    const gratitudeThankYouElement = document.querySelector('.js-gratitude__thankYou');
    // gratitudeElementのクラスの変更を監視
    if (gratitudeElement && gratitudeThankYouElement) {
      new ClassObserver(gratitudeElement, {
        classNames: ['u-anime'],
        onClassAdded: () => {
          // 3s待ってから処理
          setTimeout(() => {
            gratitudeThankYouElement.classList.add('is-visible');
          }, 3000);
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