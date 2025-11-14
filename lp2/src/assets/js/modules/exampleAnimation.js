/**
 * Example Card Stack Animation Module
 * GSAPのScrollTriggerを使用してカードを順番に重ねるアニメーション
 */
import { gsap } from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

export class ExampleCardStackAnimation {
  constructor() {
    this.scrollTriggers = [];
    this.init();
  }

  init() {
    // GSAPとScrollTriggerがロードされているかチェック
    if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') {
      console.warn('GSAP or ScrollTrigger is not loaded');
      return;
    }

    // ScrollTriggerプラグインを登録
    gsap.registerPlugin(ScrollTrigger);
    console.log('GSAP ScrollTrigger registered');

    this.setupCardAnimations();
  }

  setupCardAnimations() {
    const exampleSection = document.querySelector('.p-example');
    const exampleItems = document.querySelectorAll('.p-example__item');
    
    if (!exampleSection || exampleItems.length === 0) {
      console.warn('p-example要素またはp-example__item要素が見つかりません');
      return;
    }

    console.log(`見つかったカード: ${exampleItems.length}枚`);

    // 2枚目以降のカードの初期位置を画面下に設定
    exampleItems.forEach((item, index) => {
      if (index > 0) {
        gsap.set(item, {
          y: '100vh'
        });
      }
    });

    // 各カードのスクロールアニメーション設定
    exampleItems.forEach((item, index) => {
      if (index === 0) return; // 最初のカードはアニメーション対象外

      // アニメーション開始位置を調整（より遅いタイミングで開始）
      const startPosition = `${(index - 1) * window.innerHeight + window.innerHeight * 0.5}px top`;
      const endPosition = `${index * window.innerHeight + window.innerHeight * 0.5}px top`;

      const trigger = ScrollTrigger.create({
        trigger: exampleSection,
        start: startPosition,
        end: endPosition,
        scrub: 1,
        animation: gsap.to(item, {
          y: 0,
          ease: "none",
          duration: 1
        }),
        onUpdate: (self) => {
          console.log(`カード${index + 1}: ${(self.progress * 100).toFixed(0)}%`);
        }
      });

      this.scrollTriggers.push(trigger);
    });

    console.log(`${this.scrollTriggers.length}個のScrollTriggerを作成しました`);
    ScrollTrigger.refresh();
  }

  // 破棄メソッド
  destroy() {
    console.log('ScrollTriggerを破棄します');
    this.scrollTriggers.forEach(trigger => {
      trigger.kill();
    });
    this.scrollTriggers = [];
  }
}