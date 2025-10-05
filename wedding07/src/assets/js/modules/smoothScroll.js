/**
 * スムーズスクロール機能を管理するモジュール
 * 注意: ハンバーガーメニューからのスクロールはHamburgerMenuクラスで処理
 */

/**
 * 一般的なアンカーリンク用のスムーズスクロール機能を初期化
 * PC版デスクトップナビゲーションとその他のアンカーリンクに対応
 */
export function initSmoothScroll() {
  console.log('SmoothScroll: Initializing smooth scroll for desktop navigation and other anchor links');
  
  const anchorLinks = document.querySelectorAll('a[href^="#"]');
  
  anchorLinks.forEach(link => {
    link.addEventListener('click', function(e) {
      // ハンバーガーメニュー内のリンクは除外（HamburgerMenuクラスで処理済み）
      if (this.closest('.p-global-nav')) return;
      
      const href = this.getAttribute('href');
      if (href === '#') return;
      
      e.preventDefault();
      
      const target = document.querySelector(href);
      if (target) {
        const headerHeight = document.querySelector('.l-header')?.offsetHeight || 0;
        const targetRect = target.getBoundingClientRect();
        const targetPosition = window.pageYOffset + targetRect.top - headerHeight - 40; // 20pxのマージンを追加
        
        // 最小値は0にする
        const finalPosition = Math.max(0, targetPosition);
        
        window.scrollTo({
          top: finalPosition,
          behavior: 'smooth'
        });
        
        console.log('SmoothScroll: Scrolling to', href, 'at position', finalPosition, 'with header height', headerHeight, 'and target offset', target.offsetTop, 'target', target);
      }
    });
  });
}

/**
 * 指定された要素へのスムーズスクロールを実行
 * @param {string} targetSelector - スクロール先の要素のセレクタ
 * @param {number} offset - オフセット値（デフォルト: ヘッダーの高さ）
 */
export function smoothScrollTo(targetSelector, offset = null) {
  const target = document.querySelector(targetSelector);
  if (!target) return;
  
  const headerHeight = offset !== null ? offset : (document.querySelector('.l-header')?.offsetHeight || 0);
  const targetPosition = target.offsetTop - headerHeight;
  
  window.scrollTo({
    top: targetPosition,
    behavior: 'smooth'
  });
}