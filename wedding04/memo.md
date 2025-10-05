・タイトル、テキストのウエイトが太いのでデザインくらいに細く修正
※数値通りの設定になっているので、要確認

・PCFVが1画面に入り切れていないので調整（100vh）
→OK

・SP私たちについて部分で、テキストが左揃えになっているので中央揃えに修正。また、女性の写真と文字の順番が逆になっているためこちらも修正
OK

・「ふたりの物語」箇所、PCは中央揃えに。（画面いっぱいにならなくてもOK）PCの「→」は右端だけにし、右スクロールできるように。
→OK

また、Birthdayなどのテキストはカーニングを広げる。
→OK

Just Marriedの画像のサイズがPCだと1画面に収まらないので調整してほしいです。
→OK

・メニューの中のデザインが違うので調整。（Figmaの左側のメニューでファイルの切り替えがあります。そちらにデザインがあります）
→OK

```main.js
import { HamburgerMenu } from './modules/hamburgerMenu.js';
import { CustomVideoPlayer } from './modules/videoPlayer.js';
import { initSmoothScroll } from './modules/smoothScroll.js';
import { initScrollAnimation } from './modules/scrollAnimation.js';
import { OurStoryCardAnimation } from './modules/ourStoryCardAnimation.js';
import { ClassObserver } from './modules/classObserver.js';
import initSlider from './modules/slider.js';
import { FVImageAnimation } from './modules/fvImageAnimation.js';
import initSVGAnimations from './svg-animation/init.js';

console.log('Wedding site loaded successfully!');

/**
 * シンプルなアプリケーション初期化
 */
async function initializeApp() {
  console.log('App initialization started');

  try {
    // 最小限の初期化のみ実行
    new HamburgerMenu();
    console.log('✅ HamburgerMenu initialized');

    // ファーストビューに必要な軽い処理のみ
    initSmoothScroll();
    console.log('✅ SmoothScroll initialized');

    // FVイメージアニメーションのみ0.5秒遅延
    setTimeout(() => {
      new FVImageAnimation();
      console.log('✅ FVImageAnimation initialized (0.5s delayed)');
    }, 500);

    // スクロールアニメーション
    initScrollAnimation({
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px',
      once: true
    });
    console.log('✅ ScrollAnimation initialized (deferred)');

    new CustomVideoPlayer();
    console.log('✅ VideoPlayer initialized (deferred)');

    initSlider();
    console.log('✅ Slider initialized (deferred)');
    
    new OurStoryCardAnimation();
    console.log('✅ OurStoryCardAnimation initialized (deferred)');

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
          }, 2000);
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

```