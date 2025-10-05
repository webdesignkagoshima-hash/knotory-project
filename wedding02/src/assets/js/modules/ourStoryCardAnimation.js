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
    if (this.isMobile()) {
      this.setupMobileAnimations();
    } else {
      this.setupDesktopAnimations();
    }
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
          // transformを上書きせず、CSSのtranslate(-50%, -50%)を維持
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
          onUpdate: (self) => console.log(`セクション${index}: 進行度 ${(self.progress * 100).toFixed(1)}%`),
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

  // PC用のアニメーション（新規追加）
  setupDesktopAnimations() {
    console.log('PC環境：カードフェードインアニメーション開始');

    // 全てのカードを取得
    const cards = document.querySelectorAll('.c-ourStoryCard');
    
    if (cards.length === 0) {
      console.warn('カードが見つかりません');
      return;
    }

    console.log(`見つかったカード: ${cards.length}枚`);

    // 各カードに個別のScrollTriggerを設定
    cards.forEach((card, index) => {
      // 初期状態を設定（PC用）
      gsap.set(card, {
        opacity: 0,
        x: 30
      });

      // フェードインアニメーション
      gsap.to(card, {
        opacity: 1,
        x: 0,
        duration: 0.8,
        delay: index * 0.3, // カードごとに遅延を設定
        ease: "power2.out",
        scrollTrigger: {
          trigger: card,
          start: "top 80%",
          end: "bottom 20%",
          toggleActions: "play none none reverse",
          onEnter: () => console.log(`PC: カード${index}がフェードイン`),
          onLeave: () => console.log(`PC: カード${index}がフェードアウト`),
        }
      });

      console.log(`PC: カード${index}のアニメーション設定完了`);
    });

    // ScrollTriggerの更新
    ScrollTrigger.refresh();
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