/**
 * ハンバーガーメニューの機能を管理するクラス
 */
export class HamburgerMenu {
  constructor() {
    this.hamburger = document.querySelector('.p-hamburger');
    this.globalNav = document.querySelector('.p-global-nav');
    this.overlay = document.querySelector('.p-global-nav__overlay');
    this.navLinks = document.querySelectorAll('.global-nav__link');
    this.body = document.body;
    
    this.isOpen = false;
    
    this.init();
  }
  
  init() {
    if (!this.hamburger || !this.globalNav) {
      console.error('Required elements not found');
      return;
    }
    
    console.log('HamburgerMenu: Found', this.navLinks.length, 'navigation links');
    
    // ハンバーガーボタンのクリックイベント
    this.hamburger.addEventListener('click', () => this.toggle());
    
    // オーバーレイのクリックイベント
    if (this.overlay) {
      this.overlay.addEventListener('click', () => this.close());
    }
    
    // ナビリンクのクリックイベント
    this.navLinks.forEach((link, index) => {
      console.log(`Setting up link ${index}:`, link.getAttribute('href'));
      link.addEventListener('click', (e) => {
        console.log('Navigation link clicked:', e.target.getAttribute('href'));
        this.handleNavClick(e);
      });
    });
    
    // ESCキーでメニューを閉じる
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && this.isOpen) {
        this.close();
      }
    });
    
    // リサイズ時にメニューを閉じる
    window.addEventListener('resize', () => {
      if (window.innerWidth > 768 && this.isOpen) {
        this.close();
      }
    });
  }
  
  toggle() {
    if (this.isOpen) {
      this.close();
    } else {
      this.open();
    }
  }
  
  open() {
    this.isOpen = true;
    this.hamburger.classList.add('is-active');
    this.globalNav.classList.add('is-open');
    this.hamburger.setAttribute('aria-expanded', 'true');
    this.hamburger.setAttribute('aria-label', 'メニューを閉じる');
    this.body.style.overflow = 'hidden';
    
    // フォーカス管理
    this.globalNav.focus();
  }
  
  close() {
    this.isOpen = false;
    this.hamburger.classList.remove('is-active');
    this.globalNav.classList.remove('is-open');
    this.hamburger.setAttribute('aria-expanded', 'false');
    this.hamburger.setAttribute('aria-label', 'メニューを開く');
    this.body.style.overflow = '';
  }
  
  handleNavClick(e) {
    // クリックされた要素から最も近いaタグを探す
    const linkElement = e.target.closest('a');
    
    if (!linkElement) {
      console.warn('No link element found');
      return;
    }

    const href = linkElement.getAttribute('href');
    console.log('Processing navigation click to:', href);
    
    // まずメニューを閉じる
    this.close();
    
    // アンカーリンクの場合のみスムーススクロール
    if (href && href.startsWith('#')) {
      e.preventDefault();
      
      // 少し遅延を入れてメニューが完全に閉じてからスクロール
      setTimeout(() => {
        this.performSmoothScroll(href);
      }, 300);
    }
  }
  
  performSmoothScroll(targetId) {
    console.log('Attempting smooth scroll to:', targetId);
    
    const target = document.querySelector(targetId);
    
    if (!target) {
      console.error('Target element not found:', targetId);
      return;
    }
    
    console.log('Target element found:', target);
    
    // ヘッダーの高さを取得
    const header = document.querySelector('.l-header');
    const headerHeight = header ? header.offsetHeight : 0;
    
    // より正確な要素の位置を取得
    const targetRect = target.getBoundingClientRect();
    const currentScrollY = window.pageYOffset || document.documentElement.scrollTop;
    const targetAbsoluteTop = targetRect.top + currentScrollY;
    
    // ページの高さとウィンドウの高さを取得
    const documentHeight = document.documentElement.scrollHeight;
    const windowHeight = window.innerHeight;
    const maxScrollPosition = Math.max(0, documentHeight - windowHeight);
    
    // スクロール位置を計算（ヘッダー分を差し引く）
    let targetPosition = targetAbsoluteTop - headerHeight - 20; // 20pxのマージン
    
    // 最大スクロール位置を超えないように調整
    targetPosition = Math.min(targetPosition, maxScrollPosition);
    
    // 最小値は0にする
    targetPosition = Math.max(0, targetPosition);
    
    console.log('Detailed scroll calculation:', {
      targetId: targetId,
      targetRect: {
        top: targetRect.top,
        bottom: targetRect.bottom,
        height: targetRect.height
      },
      currentScrollY: currentScrollY,
      targetAbsoluteTop: targetAbsoluteTop,
      headerHeight: headerHeight,
      documentHeight: documentHeight,
      windowHeight: windowHeight,
      maxScrollPosition: maxScrollPosition,
      calculatedPosition: targetAbsoluteTop - headerHeight - 20,
      finalPosition: targetPosition
    });
    
    // スムーススクロール実行
    window.scrollTo({
      top: targetPosition,
      behavior: 'smooth'
    });
    
    console.log('Smooth scroll initiated to position:', targetPosition);
    
    // スクロール完了後にターゲット要素がビューポートに正しく表示されているかチェック
    setTimeout(() => {
      const newRect = target.getBoundingClientRect();
      console.log('After scroll - target position:', {
        top: newRect.top,
        visible: newRect.top >= 0 && newRect.top <= windowHeight
      });
    }, 1000);
  }
}