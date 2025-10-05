import Splide from '@splidejs/splide';

export default function initSlider() {
  const splide = new Splide('.splide', {
    updateOnMove: true,
    type: 'loop',
    perPage: 2,
    speed: 600,
    focus: 'center',
    perMove: 1,
    autoplay: true,
    interval: 3000,
    delay: 1000,
    pagination: true,
    width: '100%',
    height: 'min(calc(480 / 1050 * 100svw), 480px)',
    gap: '-120px',
    arrows: false,
    arrowPath: 'M25.4736 18.5068H7.56445V20.5068H25.4736V22.4688L31.2012 19.6348L25.4736 16.7998V18.5068Z',
    breakpoints: {
      768: {
        fixedWidth: '100%',
        height: 'calc(240 / 390 * 100svw + 57px)',
        gap: '0'
      },
    },
  });

  splide.mount();
}
