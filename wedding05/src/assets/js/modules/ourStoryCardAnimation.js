/**
 * Our Story Card Animation Module
 * GSAPのScrollTriggerを使用してカードを順番に表示するアニメーション（SPのみ）
 */
import { gsap } from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

export class OurStoryCardAnimation {
  constructor() {
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

    this.setupCardAnimations();
  }

  setupCardAnimations() {
    this.setupAnimations();
  }

  // アニメーションのセットアップ
  setupAnimations() {
    console.log('SP環境：カードアニメーション開始');

    // 各p-ourStory__contentItemに対して個別にScrollTriggerを作成
    const contentItem = document.querySelector('.p-ourStory__contentItemWrapper');
    
    if (!contentItem) {
      console.warn('p-ourStory__contentItemWrapperが見つかりません');
      return;
    }

    const cards = contentItem.querySelectorAll('.c-ourStoryCard__wrapper');
    
    if (cards.length === 0) {
      console.warn(`カードが見つかりません`);
      return;
    }

    console.log(`${cards.length}枚のカードを処理`);

    // カード枚数に応じてセクションの高さを動的に調整
    const cardCount = cards.length;
    const sectionHeight = Math.max(200, cardCount * 80 + 100); // より多くの高さを確保
    contentItem.style.height = `${sectionHeight}vh`;
    
    console.log(`高さを${sectionHeight}vhに設定`);

    // 初期状態でカードを非表示に設定
    cards.forEach((card, cardIndex) => {
      if (cardIndex === 0) {
        // 最初のカードは表示状態にする
        gsap.set(card, {
          y: 0,
          opacity: 1
        });
      } else { 
        gsap.set(card, {
          y: '100svh',
          transformOrigin: "center center"
        });
      }
      console.log(`Card ${cardIndex}を表示状態に設定`);
    });

    // タイムラインを作成
    const tl = gsap.timeline({
      scrollTrigger: {
        trigger: contentItem,
        start: "top top",
        end: "bottom top",
        scrub: true,
        pin: true,
        anticipatePin: 1,
        invalidateOnRefresh: true,
        onEnter: () => console.log(`ScrollTrigger開始`),
        onLeave: () => console.log(`ScrollTrigger終了`),
        onUpdate: (self) => console.log(`進行度 ${(self.progress * 100).toFixed(1)}%`),
      }
    });

    // 各カードを順番に表示（一枚ずつ）
    cards.forEach((card, cardIndex) => {
      console.log(`カード${cardIndex}のアニメーションを設定`);
      
      tl.to(card, {
        y: 0,
        duration: 1,
        ease: "power2.out",
      })
    });

    console.log(`ScrollTrigger設定完了`);

    // ScrollTriggerの更新
    ScrollTrigger.refresh();
  }

  // 破棄メソッド
  destroy() {
    ScrollTrigger.getAll().forEach(trigger => {
      // SP用のScrollTrigger（contentItem）とPC用のScrollTrigger（card）両方を破棄
      if (trigger.trigger && 
          (trigger.trigger.classList.contains('p-ourStory__contentItemWrapper') || 
           trigger.trigger.classList.contains('c-ourStoryCard__wrapper'))) {
        trigger.kill();
      }
    });
  }
}