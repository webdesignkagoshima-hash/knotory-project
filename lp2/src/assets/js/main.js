import { initScrollAnimation } from './modules/scrollAnimation.js';
import initSlider from './modules/slider.js';
import initFvSlider from './modules/fvSlider.js';
import initFeaturesSlider from './modules/featuresSlider.js';
import { initSmoothScroll } from './modules/smoothScroll.js';
import { ExampleCardStackAnimation } from './modules/exampleAnimation.js';
import { PlanDetailToggle } from './modules/planDetailToggle.js';
import ContactForm from './modules/contactForm.js';
import FloatingCTA from './modules/floatingCTA.js';
import { FloatingScrollUI } from './modules/floatingScrollUI.js';
import { FaqAccordion } from './modules/faqAccordion.js';
import { OptionAccordion } from './modules/optionAccordion.js';
import { RecaptchaBadgeController } from './modules/recaptchaBadgeController.js';
import { OptionVideoPlayer } from './modules/optionVideoPlayer.js';

console.log('Wedding site loaded successfully!');

/**
 * シンプルなアプリケーション初期化
 */
async function initializeApp() {
  // SP時のみ
  // <meta name="viewport" content="width=device-width, initial-scale=1.0">
  // のwidthを390とする処理
  if (window.innerWidth <= 767) {
    const viewportMeta = document.querySelector('meta[name="viewport"]');
    if (viewportMeta) {
      viewportMeta.setAttribute('content', 'width=390');
      console.log('Viewport width set to 390 for mobile devices');
    } else {
      console.warn('Viewport meta tag not found');
    }
  }

  // resizeイベントでwidthを戻す
  window.addEventListener('resize', () => {
    const viewportMeta = document.querySelector('meta[name="viewport"]'); 
    if (viewportMeta) {
      if (window.innerWidth > 767) {
        viewportMeta.setAttribute('content', 'width=device-width, initial-scale=1.0');
        console.log('Viewport width reset to device-width for larger screens');
      } else {
        viewportMeta.setAttribute('content', 'width=390');
        console.log('Viewport width set to 390 for mobile devices');
      }
    }
  });
  
  console.log('App initialization started');

  try {
    // スライダー
    initSlider();
    console.log('✅ Slider initialized');

    // FVスライダー
    initFvSlider();
    console.log('✅ FV Slider initialized');

    // Featuresスライダー
    initFeaturesSlider();
    console.log('✅ Features Slider initialized');

    // スムーススクロール
    initSmoothScroll();
    console.log('✅ SmoothScroll initialized');

    // スクロールアニメーション
    initScrollAnimation({
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px',
      once: true
    });
    console.log('✅ ScrollAnimation initialized');

    // Example Card Stack Animation (SP環境のみ)
    new ExampleCardStackAnimation();
    console.log('✅ Example Card Stack Animation initialized');

    // 料金プランの管理費詳細切り替え
    new PlanDetailToggle();
    console.log('✅ Plan Detail Toggle initialized');

    // お問い合わせフォーム
    const contactForm = new ContactForm();
    console.log('✅ Contact Form initialized');

    // フローティングCTAボタン
    new FloatingCTA();
    console.log('✅ Floating CTA initialized');

    // フローティングスクロールUI（exampleセクション専用）
    new FloatingScrollUI();
    console.log('✅ Floating Scroll UI initialized');

    // FAQアコーディオンの高さ計算
    new FaqAccordion();
    console.log('✅ FAQ Accordion initialized');

    // オプション機能アコーディオンの高さ計算
    new OptionAccordion();
    console.log('✅ Option Accordion initialized');

    // reCAPTCHAバッジコントローラー
    new RecaptchaBadgeController();
    console.log('✅ reCAPTCHA Badge Controller initialized');

    // オプション動画プレーヤー
    new OptionVideoPlayer();
    console.log('✅ Option Video Player initialized');
  } catch (error) {
    console.error('App initialization failed:', error);
  }
}

// DOM読み込み完了後にアプリケーションを初期化
document.addEventListener('DOMContentLoaded', initializeApp);