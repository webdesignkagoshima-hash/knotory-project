import Splide from '@splidejs/splide';
import { AutoScroll } from '@splidejs/splide-extension-auto-scroll';

export default function initSlider() {
  // splideExampleが存在する場合のみ初期化
  const splideExampleElement = document.querySelector('.splide.splideExample');
  if (splideExampleElement) {
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
      pagination: false,
      width: '100%',
      gap: '10px',
      arrows: false,
      autoScroll: {
        pauseOnHover: false,
      },
    });

    splide.mount({ AutoScroll });
    console.log('✅ Example slider initialized');
  }

  // splideReviewsが存在する場合のみ初期化
  const splideReviewsElement = document.querySelector('.splide.splideReviews');
  if (splideReviewsElement) {
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
    });

    splide2.mount();
    console.log('✅ Reviews slider initialized');
  }

  // splideReviewsSenderが存在する場合のみ初期化
  const splideReviewsSenderElement = document.querySelector('.splide.splideReviewsSender');
  if (splideReviewsSenderElement) {
    const splideReviewsSender = new Splide('.splide.splideReviewsSender', {
      updateOnMove: true,
      type: 'loop',
      perPage: 1,
      speed: 600,
      focus: 'center',
      perMove: 1,
      autoplay: false,
      interval: 4000,
      pagination: true,
      width: '350px',
      gap: '10px',
      arrows: false,
    });

    splideReviewsSender.mount();
    console.log('✅ Reviews Sender slider initialized');
  }

  // splideReviewsReceiverが存在する場合のみ初期化
  const splideReviewsReceiverElement = document.querySelector('.splide.splideReviewsReceiver');
  if (splideReviewsReceiverElement) {
    const splideReviewsReceiver = new Splide('.splide.splideReviewsReceiver', {
      updateOnMove: true,
      type: 'loop',
      perPage: 1,
      speed: 600,
      focus: 'center',
      perMove: 1,
      autoplay: false,
      interval: 4000,
      pagination: true,
      width: '350px',
      gap: '10px',
      arrows: false,
    });

    splideReviewsReceiver.mount();
    console.log('✅ Reviews Receiver slider initialized');
  }
}
