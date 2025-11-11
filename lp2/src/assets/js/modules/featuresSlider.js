import Splide from '@splidejs/splide';
import { AutoScroll } from '@splidejs/splide-extension-auto-scroll';

/**
 * Features セクションのスライダーを初期化
 */
export default function initFeaturesSlider() {
  // 基本的な掲載内容は横スクロールで対応（Splide不要）

  // 選べるデザインのスライダー（流れるスライダー - AutoScroll使用）
  const designSlider = document.querySelector('.splide.splideDesign');
  if (designSlider) {
    const splideDesign = new Splide('.splide.splideDesign', {
      type: 'loop',
      drag: 'free',
      perPage: 2,
      perMove: 1,
      gap: '1px',
      arrows: false,
      pagination: false,
      autoScroll: {
        speed: 1,
        pauseOnHover: false,
        pauseOnFocus: false,
      },
    });

    splideDesign.mount({ AutoScroll });
    console.log('✅ Design slider initialized');
  } else {
    console.warn('⚠️ Design slider element not found');
  }
}
