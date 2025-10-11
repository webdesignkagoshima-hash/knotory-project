import Splide from '@splidejs/splide';

export default function initSlider() {
  const splide = new Splide('.splide', {
    updateOnMove: true,
    type: 'loop',
    speed: 1000,
    focus: 'center',
    perMove: 1,
    autoplay: true,
    interval: 4500,
    delay: 1000,
    width: '100%',
    fixedWidth: 'calc(1115 / 1920 * 100svw)',
    gap: 'calc(150 / 1920 * 100svw)',
    pagination: false,
    arrows: true,
    breakpoints: {
      768: {
        fixedWidth: 'calc(264 / 390 * 100svw)',
        gap: 'calc(12 / 390 * 100svw)',
      },
    },
  });

  splide.mount();
}
