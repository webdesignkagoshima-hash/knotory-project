/**
 * SVG関連のユーティリティ関数群
 */
export class SVGUtils {
  /**
   * パスデータを解析してコマンド配列に変換
   * @param {string} pathData - SVGパスデータ
   * @returns {Array} パスコマンドの配列
   */
  static parsePathData(pathData) {
    const commands = [];
    const regex = /([MLHVCSQTAZ])([^MLHVCSQTAZ]*)/gi;
    let match;
    
    while ((match = regex.exec(pathData)) !== null) {
      const command = match[1].toUpperCase();
      const params = match[2].trim().split(/[\s,]+/).filter(p => p !== '').map(Number);
      commands.push({ command, params });
    }
    
    this.ensureClosedPath(commands);
    return commands;
  }

  /**
   * パスが閉じられていることを確認し、必要に応じてZコマンドを追加
   * @param {Array} commands - パスコマンドの配列
   */
  static ensureClosedPath(commands) {
    const lastCommand = commands[commands.length - 1];
    if (lastCommand && lastCommand.command !== 'Z') {
      commands.push({ command: 'Z', params: [] });
    }
  }

  /**
   * SVGの表示領域を歪みの値に応じて調整
   * @param {SVGPathElement} pathElement - パス要素
   * @param {number} amplitude - 歪みの振幅
   * @param {string} animationType - アニメーションタイプ ('noise' または 'morphing')
   */
  static adjustViewBox(pathElement, amplitude, animationType = 'noise') {
    const svg = pathElement.closest('svg');
    if (!svg) return;
    
    const viewBox = svg.getAttribute('viewBox');
    if (!viewBox) return;
    
    const [x, y, width, height] = viewBox.split(' ').map(Number);
    
    // アニメーションタイプに応じてpadding計算を調整
    let padding;
    if (animationType === 'morphing') {
      // モーフィングの場合：より控えめなpadding（元の形状を維持するため）
      padding = amplitude * 1.2;
    } else {
      // ノイズアニメーションの場合：従来通りのpadding
      padding = amplitude * 3;
    }
    
    // SVG viewBoxを拡張
    svg.setAttribute('viewBox', `${x - padding} ${y - padding} ${width + padding * 2} ${height + padding * 2}`);
    
    // マスク、image、filter要素も調整
    this.adjustMaskBounds(pathElement, x, y, width, height, padding);
    this.adjustImageElements(svg, x, y, width, height, padding);
    this.adjustFilterElements(svg, x, y, width, height, padding);
  }

  /**
   * マスクの範囲を調整
   * @param {SVGPathElement} pathElement - パス要素
   * @param {number} x - 元のx座標
   * @param {number} y - 元のy座標
   * @param {number} width - 元の幅
   * @param {number} height - 元の高さ
   * @param {number} padding - 追加する余白
   */
  static adjustMaskBounds(pathElement, x, y, width, height, padding) {
    const mask = pathElement.closest('mask');
    if (mask) {
      mask.setAttribute('x', x - padding);
      mask.setAttribute('y', y - padding);
      mask.setAttribute('width', width + padding * 2);
      mask.setAttribute('height', height + padding * 2);
    }
  }

  /**
   * image要素の位置とサイズを調整
   * @param {SVGElement} svg - SVG要素
   * @param {number} x - 元のx座標
   * @param {number} y - 元のy座標
   * @param {number} width - 元の幅
   * @param {number} height - 元の高さ
   * @param {number} padding - 追加する余白
   */
  static adjustImageElements(svg, x, y, width, height, padding) {
    const images = svg.querySelectorAll('image');
    images.forEach(image => {
      const currentX = parseFloat(image.getAttribute('x') || 0);
      const currentY = parseFloat(image.getAttribute('y') || 0);
      const currentWidth = parseFloat(image.getAttribute('width') || width);
      const currentHeight = parseFloat(image.getAttribute('height') || height);
      
      image.setAttribute('x', currentX - padding);
      image.setAttribute('y', currentY - padding);
      image.setAttribute('width', currentWidth + padding * 2);
      image.setAttribute('height', currentHeight + padding * 2);
    });
  }

  /**
   * filter要素の位置とサイズを調整
   * @param {SVGElement} svg - SVG要素
   * @param {number} x - 元のx座標
   * @param {number} y - 元のy座標
   * @param {number} width - 元の幅
   * @param {number} height - 元の高さ
   * @param {number} padding - 追加する余白
   */
  static adjustFilterElements(svg, x, y, width, height, padding) {
    const filters = svg.querySelectorAll('filter');
    filters.forEach(filter => {
      const currentX = parseFloat(filter.getAttribute('x') || 0);
      const currentY = parseFloat(filter.getAttribute('y') || 0);
      const currentWidth = parseFloat(filter.getAttribute('width') || width);
      const currentHeight = parseFloat(filter.getAttribute('height') || height);
      
      filter.setAttribute('x', currentX - padding);
      filter.setAttribute('y', currentY - padding);
      filter.setAttribute('width', currentWidth + padding * 2);
      filter.setAttribute('height', currentHeight + padding * 2);
    });
  }
}