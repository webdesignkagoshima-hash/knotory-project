/**
 * 料金プランの管理費詳細の表示/非表示を切り替える
 */
export class PlanDetailToggle {
  constructor() {
    this.init();
  }

  init() {
    this.bindEvents();
  }

  bindEvents() {
    // 管理費詳細ボタンのクリックイベントを設定
    const toggleButtons = document.querySelectorAll('.js-plan__managementButton');
    
    toggleButtons.forEach((button) => {
      button.addEventListener('click', (e) => {
        this.handleToggle(e.currentTarget);
      });
    });
  }

  handleToggle(button) {
    // 対応する詳細エリアを取得
    const detailElement = button.parentElement.querySelector('.js-plan__managementDetail');
    
    if (!detailElement) {
      console.warn('管理費詳細エリアが見つかりません');
      return;
    }

    // 現在の状態を取得
    const isExpanded = detailElement.getAttribute('aria-expanded') === 'true';

    if (isExpanded) {
      // 詳細を非表示にする
      button.textContent = '詳細を見る';
      button.setAttribute('aria-expanded', 'false');
      detailElement.setAttribute('aria-hidden', 'true');
      detailElement.setAttribute('aria-expanded', 'false');
    } else {
      // 詳細を表示する
      button.textContent = '詳細を閉じる';
      button.setAttribute('aria-expanded', 'true');
      detailElement.setAttribute('aria-hidden', 'false');
      detailElement.setAttribute('aria-expanded', 'true');
    }
  }
}