import Splide from '@splidejs/splide';
import { AutoScroll } from '@splidejs/splide-extension-auto-scroll';

export default function initSlider() {
  const splide = new Splide('.splide.splideExample', {
    updateOnMove: true,
    type: 'loop',
    perPage: 2,
    speed: 600,
    focus: 'center',
    perMove: 1,
    autoplay: false,
    interval: 1000,
    delay: 1000,
    pagination: true,
    width: '100%',
    gap: '10px',
    arrows: false,
    pagination: false,
    autoScroll: {
      pauseOnHover: false, // カーソルが乗ってもスクロールを停止させない
    },
  });

  const splide2 = new Splide('.splide.splideReviews', {
    updateOnMove: true,
    type: 'loop',
    perPage: 1,
    speed: 600,
    focus: 'center',
    perMove: 1,
    autoplay: true,
    interval: 3000,
    delay: 1000,
    pagination: true,
    width: '350px',
    gap: '10px',
    arrows: false,
    pagination: true
  });

  splide.mount({ AutoScroll });
  splide2.mount();
}
