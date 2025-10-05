/**
 * Our Story Card Animation Module
 * GSAPのScrollTriggerを使用してカードを順番に表示するアニメーション（SPのみ）
 */
import { gsap } from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

export class OurStoryCardAnimation {
  constructor() {
    this.currentState = null; // 'mobile' or 'desktop'
    this.resizeTimeout = null;
    this.backgroundImages = []; // 背景画像要素を格納
    this.currentBackgroundIndex = 0; // 現在の背景画像インデックス
    this.init();
    this.setupResizeHandler();
  }

  init() {
    // GSAPとScrollTriggerがロードされているかチェック
    if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') {
      console.warn('GSAP or ScrollTrigger is not loaded');
      return;
    }

    // ScrollTriggerプラグインを登録
    gsap.registerPlugin(ScrollTrigger);

    // 背景画像要素を取得
    this.initBackgroundImages();

    this.setupCardAnimations();
  }

  // PC/SP判定
  isMobile() {
    return window.innerWidth < 768; // 768px未満をSPとする
  }

  // リサイズハンドラーの設定
  setupResizeHandler() {
    const handleResize = () => {
      // デバウンス処理
      if (this.resizeTimeout) {
        clearTimeout(this.resizeTimeout);
      }
      
      this.resizeTimeout = setTimeout(() => {
        const newState = this.isMobile() ? 'mobile' : 'desktop';
        
        // 状態が変更された場合のみ処理
        if (this.currentState !== newState) {
          console.log(`状態変更: ${this.currentState} → ${newState}`);
          
          // 既存のScrollTriggerを破棄
          this.destroy();
          
          // 要素のスタイルをリセット
          this.resetElementStyles();
          
          // 新しい状態に応じてアニメーションを再設定
          this.setupCardAnimations();
          
          this.currentState = newState;
        }
      }, 100);
    };

    window.addEventListener('resize', handleResize);
    
    // 初期状態を設定
    this.currentState = this.isMobile() ? 'mobile' : 'desktop';
  }

  // 要素のスタイルをリセット
  resetElementStyles() {
    const contentItems = document.querySelectorAll('.p-ourStory__contentItem');
    
    contentItems.forEach((item) => {
      // 動的に設定した高さをリセット
      item.style.height = '';
      
      const cards = item.querySelectorAll('.c-ourStoryCard');
      cards.forEach((card) => {
        // GSAPで設定したスタイルをクリア
        gsap.set(card, { clearProps: "all" });
      });
    });
  }

  setupCardAnimations() {
    // PCとSPで共通のアニメーション
    this.setupMobileAnimations();
  }

  // SP用のアニメーション（既存）
  setupMobileAnimations() {
    console.log('SP環境：カードアニメーション開始');

    // 各p-ourStory__contentItemに対して個別にScrollTriggerを作成
    const contentItems = document.querySelectorAll('.p-ourStory__contentItem');
    
    if (contentItems.length === 0) {
      console.warn('p-ourStory__contentItemが見つかりません');
      return;
    }

    console.log(`見つかったcontentItem: ${contentItems.length}個`);

    contentItems.forEach((item, index) => {
      const cards = item.querySelectorAll('.c-ourStoryCard');
      
      if (cards.length === 0) {
        console.warn(`セクション${index}: カードが見つかりません`);
        return;
      }

      console.log(`セクション${index}: ${cards.length}枚のカードを処理`);

      // カード枚数に応じてセクションの高さを動的に調整
      const cardCount = cards.length;
      const sectionHeight = Math.max(200, cardCount * 80 + 100); // より多くの高さを確保
      item.style.height = `${sectionHeight}vh`;
      
      console.log(`セクション${index}の高さを${sectionHeight}vhに設定`);

      // 初期状態でカードを非表示に設定
      cards.forEach((card, cardIndex) => {
        gsap.set(card, {
          opacity: 0,
          scale: 0.8,
          // pinされた状態でも中央配置を維持するため、xPercentとyPercentを使用
          xPercent: -50,
          yPercent: -50,
          transformOrigin: "center center"
        });
        console.log(`Card ${cardIndex}を初期状態に設定`);
      });

      // タイムラインを作成
      const tl = gsap.timeline({
        scrollTrigger: {
          trigger: item,
          start: "top top",
          end: "bottom top",
          scrub: true,
          pin: true,
          anticipatePin: 1,
          invalidateOnRefresh: true,
          onEnter: () => console.log(`セクション${index}: ScrollTrigger開始`),
          onLeave: () => console.log(`セクション${index}: ScrollTrigger終了`),
          onUpdate: (self) => {
            console.log(`セクション${index}: 進行度 ${(self.progress * 100).toFixed(1)}%`);
            this.updateBackgroundBasedOnProgress(self.progress, cards.length);
          },
        }
      });

      // 各カードを順番に表示（一枚ずつ）
      cards.forEach((card, cardIndex) => {
        console.log(`カード${cardIndex}のアニメーションを設定`);
        
        tl.to(card, {
          opacity: 1,
          scale: 1,
          duration: 1,
          ease: "power2.out",
        })
        .to(card, {
          opacity: 0,
          scale: 0.8,
          duration: 0.8,
          ease: "power2.in",
        }, "+=0.5");
      });

      console.log(`セクション${index}のScrollTrigger設定完了`);
    });

    // ScrollTriggerの更新
    ScrollTrigger.refresh();
  }

  // 背景画像要素を初期化
  initBackgroundImages() {
    this.backgroundImages = document.querySelectorAll('.p-ourStory__backgroundImage');
    
    if (this.backgroundImages.length === 0) {
      console.warn('背景画像要素が見つかりません');
      return;
    }

    console.log(`背景画像要素を${this.backgroundImages.length}個発見`);
    
    // 最初の背景画像をアクティブに設定
    this.setActiveBackground(0);
  }

  // 背景画像をアクティブに設定
  setActiveBackground(index) {
    // 有効なインデックス範囲をチェック
    if (index < 0 || index >= this.backgroundImages.length) {
      return;
    }

    // 現在のインデックスと同じ場合は何もしない
    if (this.currentBackgroundIndex === index) {
      return;
    }

    console.log(`背景画像を${this.currentBackgroundIndex}から${index}に変更`);

    // 全ての背景画像を非アクティブに
    this.backgroundImages.forEach(bg => {
      bg.classList.remove('is-active');
    });

    // 指定された背景画像をアクティブに
    this.backgroundImages[index].classList.add('is-active');
    this.currentBackgroundIndex = index;
  }

  // カードの進行度に基づいて背景画像を更新
  updateBackgroundBasedOnProgress(progress, totalCards) {
    // 5枚の背景画像に対してカード数に応じて分割
    const segmentSize = 1 / 5; // 5等分
    const backgroundIndex = Math.min(4, Math.floor(progress / segmentSize));
    
    this.setActiveBackground(backgroundIndex);
  }

  // 破棄メソッド
  destroy() {
    ScrollTrigger.getAll().forEach(trigger => {
      // SP用のScrollTrigger（contentItem）とPC用のScrollTrigger（card）両方を破棄
      if (trigger.trigger && 
          (trigger.trigger.classList.contains('p-ourStory__contentItem') || 
           trigger.trigger.classList.contains('c-ourStoryCard'))) {
        trigger.kill();
      }
    });
  }
}