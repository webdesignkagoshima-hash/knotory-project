import Splide from '@splidejs/splide';

export default function initSlider() {
  const splide = new Splide('.splide', {
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
    width: '1108px',
    fixedHeight: '600px',
    arrows: false,
    arrowPath: 'M25.4736 18.5068H7.56445V20.5068H25.4736V22.4688L31.2012 19.6348L25.4736 16.7998V18.5068Z',
    breakpoints: {
      768: {
        width: '100%',
        fixedHeight: '64svw',
      },
    },
  });

  splide.mount();
}
