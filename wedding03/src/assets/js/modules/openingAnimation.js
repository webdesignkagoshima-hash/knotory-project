export function initOpeningAnimation() {
  const openingElement = document.querySelector('.c-opening');
  const logoElement = document.querySelector('.js-opening__logo');
  const textElement = document.querySelector('.c-opening__text');

  // スクロールを無効化
  document.body.classList.add('no-scroll');

  if (logoElement && textElement) {
    // ロゴのフェードインアニメーション
    logoElement.style.filter = 'blur(10px)';
    logoElement.style.transition = 'filter 2s ease, opacity 2s ease';
    logoElement.style.opacity = '1';

    // テキストのフェードインアニメーション
    textElement.style.filter = 'blur(10px)';
    textElement.style.opacity = '1';
    textElement.style.transition = 'filter 2s ease, opacity 2s ease';
  
    setTimeout(() => {
      logoElement.style.filter = 'blur(0)';
      textElement.style.filter = 'blur(0)';
      openingElement.style.opacity = '1';
    }, 0);

    setTimeout(() => {
      logoElement.style.opacity = '0';
      logoElement.style.transition = 'opacity 1s ease';
      textElement.style.opacity = '0';
      textElement.style.transition = 'opacity 1s ease';
    }, 2000); // 2秒後にフェードアウト

    setTimeout(() => {
      openingElement.remove();
      // スクロールを有効化
      document.body.classList.remove('no-scroll');
    }, 3000); // 3秒後にオープニング要素をDOMから削除
  }
}