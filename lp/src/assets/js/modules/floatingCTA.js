/**
 * Floating CTA Button Controller
 * ファーストビューのCTAが見えなくなったら表示し、フッターに入る前に非表示にする
 */

class FloatingCTA {
  constructor() {
    this.floatingCTA = document.querySelector('.js-floating-cta');
    this.fvCTA = document.querySelector('.p-fv__ctaArea');
    this.footer = document.querySelector('footer');
    this.contactSection = document.querySelector('.p-contact');
    this.exampleSection = document.querySelector('.p-example');
    this.planSection = document.querySelector('.p-plan');
    
    if (!this.floatingCTA || !this.fvCTA || !this.footer) return;
    
    this.init();
  }

  init() {
    // 初期状態は非表示
    this.floatingCTA.style.display = 'none';
    
    // スクロールイベントの監視
    this.handleScroll = this.handleScroll.bind(this);
    window.addEventListener('scroll', this.handleScroll);
    
    // リサイズイベントの監視（レスポンシブ対応）
    window.addEventListener('resize', this.handleScroll);
  }

  handleScroll() {
    const fvCTABottom = this.fvCTA.getBoundingClientRect().bottom;
    const footerTop = this.footer.getBoundingClientRect().top;
    const windowHeight = window.innerHeight;
    
    // ファーストビューのCTAが完全に画面から消えた場合は表示
    const shouldShow = fvCTABottom < 0;
    
    // フッターが画面に入ってきた場合は非表示
    const shouldHideByFooter = footerTop < windowHeight;
    
    // contactセクションが画面に入ってきた場合は非表示
    let shouldHideByContact = false;
    if (this.contactSection) {
      const contactTop = this.contactSection.getBoundingClientRect().top;
      shouldHideByContact = contactTop < windowHeight;
    }
    
    // exampleセクション内では非表示、planセクション以降では再表示
    let shouldHideByExample = false;
    if (this.exampleSection && this.planSection) {
      const exampleTop = this.exampleSection.getBoundingClientRect().top;
      const exampleBottom = this.exampleSection.getBoundingClientRect().bottom;
      const planTop = this.planSection.getBoundingClientRect().top;
      
      // exampleセクションが画面に入ってきて、まだplanセクションに到達していない場合は非表示
      shouldHideByExample = exampleTop < windowHeight && planTop >= 0;
    }
    
    if (shouldShow && !shouldHideByFooter && !shouldHideByContact && !shouldHideByExample) {
      this.showFloatingCTA();
    } else {
      this.hideFloatingCTA();
    }
  }

  showFloatingCTA() {
    if (this.floatingCTA.style.display === 'none') {
      this.floatingCTA.style.display = 'block';
      // アニメーション用のクラスを追加
      requestAnimationFrame(() => {
        this.floatingCTA.classList.add('is-visible');
      });
    }
  }

  hideFloatingCTA() {
    if (this.floatingCTA.style.display !== 'none') {
      this.floatingCTA.classList.remove('is-visible');
      // アニメーション完了後に非表示
      setTimeout(() => {
        if (!this.floatingCTA.classList.contains('is-visible')) {
          this.floatingCTA.style.display = 'none';
        }
      }, 300);
    }
  }

  destroy() {
    window.removeEventListener('scroll', this.handleScroll);
    window.removeEventListener('resize', this.handleScroll);
  }
}

export default FloatingCTA;