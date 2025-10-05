/**
 * ScrollToTopButton
 * ページの上部に戻るボタンの制御を行う
 */
export class ScrollToTopButton {
  constructor() {
    this.button = document.getElementById('scrollToTop');
    this.isVisible = false;
    this.lastScrollTop = 0;
    
    this.init();
  }

  init() {
    if (!this.button) return;

    // ボタンクリック時の処理
    this.button.addEventListener('click', this.scrollToTop.bind(this));
    
    // スクロール監視
    window.addEventListener('scroll', this.handleScroll.bind(this));
    
    // 初期状態で非表示
    this.hideButton();
  }

  handleScroll() {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    const windowHeight = window.innerHeight;
    const documentHeight = document.documentElement.scrollHeight;
    const footer = document.querySelector('footer');
    
    // FVセクションを超えたら表示
    const fvHeight = document.querySelector('.p-fv')?.offsetHeight || windowHeight;
    
    if (scrollTop > fvHeight) {
      // フッターとの重複チェック
      if (footer) {
        const footerTop = footer.offsetTop;
        const scrollBottom = scrollTop + windowHeight;
        
        // フッターに被る場合は非表示
        if (scrollBottom >= footerTop - 20) {
          this.hideButton();
        } else {
          this.showButton();
        }
      } else {
        this.showButton();
      }
    } else {
      this.hideButton();
    }
    
    this.lastScrollTop = scrollTop;
  }

  showButton() {
    if (!this.isVisible) {
      this.button.classList.remove('is-hidden');
      this.button.classList.add('is-visible');
      this.isVisible = true;
    }
  }

  hideButton() {
    if (this.isVisible || this.isVisible === undefined) {
      this.button.classList.remove('is-visible');
      this.button.classList.add('is-hidden');
      this.isVisible = false;
    }
  }

  scrollToTop() {
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  }
}