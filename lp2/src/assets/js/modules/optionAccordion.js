/**
 * オプション機能アコーディオンの高さを動的に計算
 */
export class OptionAccordion {
  constructor() {
    this.details = document.querySelectorAll('.p-features__optionDetails');
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
      const isSignificantResize = Math.abs(currentWidth - lastWidth) > 10;
      
      if (isSignificantResize && !this.isCalculating) {
        resizeTimer = setTimeout(() => {
          console.log('Significant resize detected, recalculating option accordion heights');
          this.calculateHeightsAccurate();
          lastWidth = currentWidth;
          lastHeight = currentHeight;
        }, 500);
      }
    }, { passive: true });
  }

  calculateHeightsAccurate() {
    if (this.isCalculating) return;
    this.isCalculating = true;

    requestAnimationFrame(() => {
      this.details.forEach(detailsElement => {
        if (detailsElement.tagName.toLowerCase() !== 'details') {
          console.warn('Expected details element, but got:', detailsElement.tagName);
          return;
        }

        const content = detailsElement.querySelector('.p-features__optionContent');
        if (!content) return;

        // 一時的にdetailsを開いて正確な高さを測定
        const wasOpen = detailsElement.hasAttribute('open');
        
        const originalHeight = detailsElement.style.getPropertyValue('--details-content-height');
        detailsElement.style.setProperty('--details-content-height', 'auto');
        
        const originalTransition = detailsElement.style.transition;
        detailsElement.style.transition = 'none';
        
        if (!wasOpen) {
          detailsElement.setAttribute('open', '');
        }
        
        requestAnimationFrame(() => {
          const contentHeight = content.offsetHeight;
          
          console.log(`オプション機能項目の測定結果: ${contentHeight}px`);
          
          if (!wasOpen) {
            detailsElement.removeAttribute('open');
          }

          if (originalHeight) {
            detailsElement.style.setProperty('--details-content-height', originalHeight);
          } else {
            detailsElement.style.removeProperty('--details-content-height');
          }
          
          detailsElement.style.transition = originalTransition;
          
          detailsElement.style.setProperty('--details-content-height', `${contentHeight}px`);
        });
      });

      this.isCalculating = false;
    });
  }

  recalculate() {
    this.calculateHeightsAccurate();
  }
}
