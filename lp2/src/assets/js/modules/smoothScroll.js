/**
 * スムーズスクロール機能を管理するモジュール
 * 注意: ハンバーガーメニューからのスクロールはHamburgerMenuクラスで処理
 */

/**
 * 一般的なアンカーリンク用のスムーズスクロール機能を初期化
 */
export function initSmoothScroll() {
  console.log('SmoothScroll: Disabled to avoid conflicts with hamburger menu');
  // ページ内リンクを取得
  const anchorLinks = document.querySelectorAll('a[href^="#"]');

  anchorLinks.forEach(link => {
    console.log('😄SmoothScroll: Setting up link', link);
    
    link.addEventListener('click', (event) => {
      const targetId = link.getAttribute('href');
      if (targetId.length > 1) { // '#'のみの場合は無視
        event.preventDefault();
        smoothScrollTo(targetId);
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