import Splide from '@splidejs/splide';
import { AutoScroll } from '@splidejs/splide-extension-auto-scroll';

export default function initSlider() {
  const splideElement = document.querySelector('.splide');
  const splideReverseElement = document.querySelector('.splide.reverseSplide');

  const isPCDevice = () => window.innerWidth > 768;

  if(splideElement) {
    try{
      const splide = new Splide('.splide.autoSplide', {
        updateOnMove: true,
        type: 'loop',
        speed: 600,
        perMove: 1,
        autoplay: false,
        interval: 3000,
        delay: 1000,
        pagination: false,    
        width: '100%',
        fixedWidth: '294px',
        fixedHeight: '387px',
        arrows: false,
        mediaQuery: 'min',
        breakpoints: {
          768: {
            fixedWidth: '295.5px',
            fixedHeight: '294px',
          }
        }
      });
  
      splide.mount({ AutoScroll });
    } catch (error) {
      console.error('Error initializing splide:', error);
    }
  }

  if(splideReverseElement) {
    try {
      const splideReverse = new Splide('.splide.reverseSplide', {
        updateOnMove: true,
        type: 'loop',
        speed: 600,
        perMove: 1,
        autoplay: false,
        interval: 3000,
        delay: 1000,
        pagination: false,    
        width: '100%',
        fixedWidth: '294px',
        fixedHeight: '387px',
        arrows: false,
        direction: 'rtl',
        mediaQuery: 'min',
        breakpoints: {
          768: {
            fixedWidth: '295.5px',
            fixedHeight: '294px',
          }
        }
      });
    
      splideReverse.mount({ AutoScroll });
    } catch (error) {
      console.error('Error initializing splideReverse:', error);
    }
  }

  if(isPCDevice()) {
    // 全てのOur Storyスライダー要素を取得
    const ourStoryContentSlideElements = document.querySelectorAll('.js-ourStoryContentSlide');
    
    ourStoryContentSlideElements.forEach((element, index) => {
      try {
        // スライド数をチェック
        const slides = element.querySelectorAll('.splide__slide');
        const slideCount = slides.length;
        const slideWidth = 480; // 1スライドの幅（px）
        const totalContentWidth = slideCount * slideWidth;
        
        // ビューポート幅を取得
        const viewportWidth = window.innerWidth;
        
        // スライダーが必要かどうかを判定
        const needsSlider = totalContentWidth > viewportWidth;
        
        const ourStoryContentSlide = new Splide(element, {
          updateOnMove: true,
          type: 'slide',
          speed: 600,
          perPage: 3,
          perMove: 1,
          autoplay: false,
          interval: 3000,
          delay: 1000,
          pagination: false,    
          width: needsSlider ? '100%' : `${totalContentWidth}px`, // スライダーが不要な場合はコンテンツ幅に
          fixedWidth: '480px',
          arrows: needsSlider, // スライダーが不要な場合は矢印も非表示
          arrowPath: 'M29.81 20.4999C30.0861 20.2238 30.0861 19.7762 29.81 19.5001L25.3113 15.0015C25.0353 14.7254 24.5877 14.7254 24.3116 15.0015C24.0356 15.2775 24.0356 15.7251 24.3116 16.0012L28.3104 20L24.3116 23.9988C24.0356 24.2749 24.0356 24.7225 24.3116 24.9985C24.5877 25.2746 25.0353 25.2746 25.3113 24.9985L29.81 20.4999ZM10.6895 20L10.6895 20.7069L29.3101 20.7069L29.3101 20L29.3101 19.2931L10.6895 19.2931L10.6895 20Z',
          classes: {
            arrows: 'splide__arrows',
            arrow: 'splide__arrow',
            prev: 'splide__arrow--prev',
            next: 'splide__arrow--next'
          }
        });
      
        ourStoryContentSlide.mount();
        
        // スライダーが不要な場合はスライダーコンテナを中央配置
        if (!needsSlider) {
          // スライダーコンテナ自体を中央配置
          element.style.marginLeft = 'auto';
          element.style.marginRight = 'auto';
        }
      } catch (error) {
        console.error(`Error initializing ourStoryContentSlide ${index}:`, error);
      }
    });
  }
}