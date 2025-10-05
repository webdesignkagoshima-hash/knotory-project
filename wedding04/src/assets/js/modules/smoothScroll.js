/**
 * スムーズスクロール機能を管理するモジュール
 * 注意: ハンバーガーメニューからのスクロールはHamburgerMenuクラスで処理
 */

/**
 * 一般的なアンカーリンク用のスムーズスクロール機能を初期化
 * 現在は無効化 - ハンバーガーメニューのスクロールとの競合を避けるため
 */
export function initSmoothScroll() {
  console.log('SmoothScroll: Disabled to avoid conflicts with hamburger menu');
  
  // 以下のコードは一時的にコメントアウト
  /*
  const anchorLinks = document.querySelectorAll('a[href^="#"]');
  
  anchorLinks.forEach(link => {
    link.addEventListener('click', function(e) {
      // ハンバーガーメニュー内のリンクは除外（既に処理済み）
      if (this.closest('.p-global-nav')) return;
      
      const href = this.getAttribute('href');
      if (href === '#') return;
      
      e.preventDefault();
      
      const target = document.querySelector(href);
      if (target) {
        const headerHeight = document.querySelector('.l-header')?.offsetHeight || 0;
        const targetPosition = target.offsetTop - headerHeight;
        
        window.scrollTo({
          top: targetPosition,
          behavior: 'smooth'
        });
      }
    });
  });
  */
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