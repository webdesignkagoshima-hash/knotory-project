/**
 * FAQアコーディオンの高さを動的に計算（改善版）
 */
export class FaqAccordion {
  constructor() {
    this.details = document.querySelectorAll('.p-faq__details');
    this.isCalculating = false;
    this.init();
  }

  init() {
    if (this.details.length === 0) return;
    
    // DOMが完全に読み込まれてから計算
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', () => {
        this.calculateHeightsAccurate();
      });
    } else {
      this.calculateHeightsAccurate();
    }
    
    // リサイズ時の処理を改善（iOS対応）
    let resizeTimer;
    let lastWidth = window.innerWidth;
    let lastHeight = window.innerHeight;
    
    window.addEventListener('resize', () => {
      clearTimeout(resizeTimer);
      
      const currentWidth = window.innerWidth;
      const currentHeight = window.innerHeight;
      
      // 横幅の変化のみを検知（縦幅の変化は無視）
      // iOS でスクロール時のブラウザUI変化による縦幅変化を除外
      const isSignificantResize = Math.abs(currentWidth - lastWidth) > 10;
      
      if (isSignificantResize && !this.isCalculating) {
        resizeTimer = setTimeout(() => {
          console.log('Significant resize detected, recalculating FAQ heights');
          this.calculateHeightsAccurate();
          lastWidth = currentWidth;
          lastHeight = currentHeight;
        }, 500); // デバウンス時間を長めに設定
      }
    }, { passive: true }); // passive オプションでパフォーマンス向上
  }

  calculateHeightsAccurate() {
    if (this.isCalculating) return;
    this.isCalculating = true;

    requestAnimationFrame(() => {
      this.details.forEach(detailsElement => {
        // 正しいdetails要素であることを確認
        if (detailsElement.tagName.toLowerCase() !== 'details') {
          console.warn('Expected details element, but got:', detailsElement.tagName);
          return;
        }

        const answer = detailsElement.querySelector('.p-faq__answer');
        if (!answer) return;

        // 一時的にdetailsを開いて正確な高さを測定
        const wasOpen = detailsElement.hasAttribute('open');
        
        // CSS変数を一時的にautoに設定して自然な高さを取得
        const originalHeight = detailsElement.style.getPropertyValue('--details-content-height');
        detailsElement.style.setProperty('--details-content-height', 'auto');
        
        // アニメーションを一時的に無効化
        const originalTransition = detailsElement.style.transition;
        detailsElement.style.transition = 'none';
        
        // 開いて測定
        if (!wasOpen) {
          detailsElement.setAttribute('open', '');
        }
        
        // 次のフレームで測定（レンダリング完了を待つ）
        requestAnimationFrame(() => {
          // answer要素の実際の高さ（パディング込み）を取得
          const answerHeight = answer.offsetHeight;
          
          console.log(`FAQ項目の測定結果: ${answerHeight}px`);
          
          // 元の状態に戻す
          if (!wasOpen) {
            detailsElement.removeAttribute('open');
          }

          // 元のCSS変数の値に戻す
          if (originalHeight) {
            detailsElement.style.setProperty('--details-content-height', originalHeight);
          } else {
            detailsElement.style.removeProperty('--details-content-height');
          }
          
          // アニメーションを復元
          detailsElement.style.transition = originalTransition;
          
          // CSS変数として設定
          detailsElement.style.setProperty('--details-content-height', `${answerHeight}px`);
        });
      });

      this.isCalculating = false;
    });
  }

  // 手動で高さを再計算（外部から呼び出し可能）
  recalculate() {
    this.calculateHeightsAccurate();
  }
}