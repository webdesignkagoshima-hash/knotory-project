export default function initFvSlider() {
  const slider = document.querySelector('.p-fv__slider');
  if (!slider) return;

  const slides = slider.querySelectorAll('.p-fv__sliderSlide');
  if (slides.length === 0) return;

  let currentIndex = 0;
  const totalSlides = slides.length;

  // 初期設定：最初のスライド以外を非表示にする
  slides.forEach((slide, index) => {
    slide.style.opacity = index === 0 ? '1' : '0';
    slide.style.transition = 'opacity 0.5s ease-in-out';
    // 既存のCSSスタイルを保持し、位置関連のスタイルは上書きしない
  });

  // スライド切り替え関数
  function showSlide(index) {
    // 現在のスライドをフェードアウト
    slides[currentIndex].style.opacity = '0';
    
    // 次のスライドをフェードイン
    setTimeout(() => {
      slides[index].style.opacity = '1';
      currentIndex = index;
    }, 250); // フェードアウトの半分の時間後にフェードイン開始
  }

  // 次のスライドに移動
  function nextSlide() {
    const nextIndex = (currentIndex + 1) % totalSlides;
    showSlide(nextIndex);
  }

  // 3秒ごとにスライドを切り替え
  const intervalId = setInterval(nextSlide, 3000);

  // ページが非表示になったときにインターバルをクリア（パフォーマンス向上）
  document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
      clearInterval(intervalId);
    } else {
      // ページが再び表示されたときにインターバルを再開
      setInterval(nextSlide, 3000);
    }
  });

  return {
    destroy: () => clearInterval(intervalId)
  };
}