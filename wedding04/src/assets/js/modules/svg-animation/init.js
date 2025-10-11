import { DistortedShapeManager } from './DistortedShapeManager.js';

/**
 * SVGアニメーション関連の初期化処理
 */
export function initSVGAnimations() {
  // 複数のSVGアニメーションを効率的に管理
  const shapeManager = new DistortedShapeManager();
  
  // パフォーマンス監視を有効にする（開発時のみ）
  shapeManager.setPerformanceMonitoring(true);

  // 共通の設定オプション
  const defaultShapeOptions = {
    animationType: 'noise',
    frequency: 0.001,
    amplitude: 10,
    speed: 0.005,
    targetFPS: 60
  };

  // js-distorted-circle要素のselectorリスト
  const shapeIds = [
    '#js-distorted-circle',
    '#js-distorted-circle-2',
    '#js-distorted-circle-3',
    '#js-distorted-circle-4',
    '#js-distorted-circle-5',
    '#js-distorted-circle-6'
  ];

  // 各shapeを効率的に追加
  const successfullyAddedShapes = [];
  shapeIds.forEach((id, index) => {
    const selector = `${id}`;
    const isAdded = shapeManager.addShape(selector, defaultShapeOptions);
    
    if (isAdded) {
      successfullyAddedShapes.push(id);
      console.log(`DistortedShape (${id}) successfully added`);
    } else {
      console.warn(`Failed to add DistortedShape (${id})`);
    }
  });

  console.log(`Successfully added ${successfullyAddedShapes.length}/${shapeIds.length} shapes`);

  // パフォーマンス情報をコンソールで確認
  console.log('Initial Performance Info:', shapeManager.getPerformanceInfo());

  // Intersection Observer で画面外では停止（パフォーマンス向上）
  // SVG要素自体を監視対象にする
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      // SVG内のパス要素を探してセレクタを構築
      const pathElement = entry.target.querySelector('[id^="js-distorted-"]');
      if (pathElement) {
        const selector = `#${pathElement.id}`;
        console.log(`Intersection change: ${selector}, visible: ${entry.isIntersecting}`);
        
        if (entry.isIntersecting) {
          shapeManager.resumeShape(selector);
        } else {
          shapeManager.pauseShape(selector);
        }
        
        // 状態変化後のパフォーマンス情報を表示
        console.log('Performance Info after visibility change:', shapeManager.getPerformanceInfo());
      }
    });
  }, {
    // より敏感に検出するため、少しでも見えたらtrueにする
    threshold: 0.1
  });

  // SVG要素を監視対象に追加
  const svgElements = document.querySelectorAll('svg');
  svgElements.forEach(svg => {
    observer.observe(svg);
  });

  // ページ離脱時にクリーンアップ
  window.addEventListener('beforeunload', () => {
    shapeManager.destroy();
  });

  // デバッグ用：手動でテストできるようにグローバルに公開
  window.shapeManager = shapeManager;
  console.log('Shape manager available as window.shapeManager for debugging');

  return shapeManager;
}