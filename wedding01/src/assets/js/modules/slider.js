import Splide from '@splidejs/splide';

export default function initSlider() {
  const splide = new Splide('.splide', {
    updateOnMove: true,
    type: 'loop',
    perPage: 1,
    speed: 600,
    perMove: 1,
    autoplay: true,
    interval: 3000,
    delay: 1000,
    gap: '24px',
    pagination: true,
    paginationType: 'bullets',
    paginationClickable: true,
    
    width: '1108px',
    arrows: false
  });

  splide.mount();
}