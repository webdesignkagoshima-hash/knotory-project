import gsap from 'gsap';
import { createNoise2D } from 'simplex-noise';
import { SVGUtils } from './SVGUtils.js';

const noise2D = createNoise2D();

/**
 * ノイズアニメーション専用クラス
 */
export class NoiseAnimation {
  constructor(pathElement, options = {}) {
    this.pathElement = pathElement;
    this.options = {
      frequency: 0.001,
      amplitude: 10,
      speed: 0.01,
      targetFPS: 60,
      ...options,
    };
    
    this.originalPath = pathElement.getAttribute('d');
    this.cachedCommands = SVGUtils.parsePathData(this.originalPath);
    this.pathStringCache = '';
    this.lastUpdateTime = 0;
    this.frameInterval = 1000 / this.options.targetFPS;
    this.time = 0;
    this.isActive = true;
    this.animation = null;
    
    this.init();
  }

  init() {
    SVGUtils.adjustViewBox(this.pathElement, this.options.amplitude, 'noise');
    this.startAnimation();
  }

  startAnimation() {
    this.animation = gsap.ticker.add(() => {
      this.updatePath();
    });
  }

  /**
   * 座標にノイズを適用して新しい座標を返す
   * @param {number} x - x座標
   * @param {number} y - y座標
   * @param {number} timeOffset - 時間オフセット
   * @returns {object} ノイズを適用した座標 {x, y}
   */
  applyNoiseToPoint(x, y, timeOffset = 0) {
    const seedX = x * this.options.frequency + this.time + timeOffset;
    const seedY = y * this.options.frequency + this.time + timeOffset + 100;
    
    return {
      x: x + noise2D(seedX, 0) * this.options.amplitude,
      y: y + noise2D(seedY, 0) * this.options.amplitude
    };
  }

  /**
   * パス更新処理
   */
  updatePath() {
    if (!this.isActive) return;
    
    const now = performance.now();
    if (now - this.lastUpdateTime < this.frameInterval) {
      return;
    }
    
    this.lastUpdateTime = now;
    this.time += this.options.speed;
    
    const distortedPath = this.generateDistortedPath();
    if (distortedPath !== this.pathStringCache) {
      this.pathElement.setAttribute('d', distortedPath);
      this.pathStringCache = distortedPath;
    }
  }

  /**
   * パスコマンドを変形する
   * @param {object} cmd - パスコマンド
   * @param {number} index - コマンドのインデックス
   * @param {object} firstPointNoise - 最初の点のノイズ値
   * @returns {object} 変形されたパスコマンド
   */
  distortCommand(cmd, index, firstPointNoise) {
    const newCmd = { ...cmd };
    
    switch (cmd.command) {
      case 'M':
        return this.distortMoveCommand(newCmd, firstPointNoise);
      case 'C':
        return this.distortCurveCommand(newCmd, index, firstPointNoise);
      case 'Z':
        return newCmd;
      default:
        return this.distortGenericCommand(newCmd, index);
    }
  }

  distortMoveCommand(cmd, firstPointNoise) {
    if (cmd.params.length >= 2) {
      const distorted = this.applyNoiseToPoint(cmd.params[0], cmd.params[1]);
      firstPointNoise.x = distorted.x - cmd.params[0];
      firstPointNoise.y = distorted.y - cmd.params[1];
      cmd.params = [distorted.x, distorted.y, ...cmd.params.slice(2)];
    }
    return cmd;
  }

  distortCurveCommand(cmd, index, firstPointNoise) {
    if (cmd.params.length >= 6) {
      const newParams = [];
      for (let i = 0; i < cmd.params.length; i += 2) {
        if (i + 1 < cmd.params.length) {
          const isLastPoint = this.isLastPoint(index, i, cmd.params.length);
          
          if (isLastPoint && firstPointNoise.x !== null) {
            newParams.push(
              this.cachedCommands[0].params[0] + firstPointNoise.x,
              this.cachedCommands[0].params[1] + firstPointNoise.y
            );
          } else {
            const distorted = this.applyNoiseToPoint(cmd.params[i], cmd.params[i + 1], index * 0.1);
            newParams.push(distorted.x, distorted.y);
          }
        }
      }
      cmd.params = newParams;
    }
    return cmd;
  }

  distortGenericCommand(cmd, index) {
    if (cmd.params.length >= 2) {
      for (let i = 0; i < cmd.params.length; i += 2) {
        if (i + 1 < cmd.params.length) {
          const distorted = this.applyNoiseToPoint(cmd.params[i], cmd.params[i + 1], index * 0.1);
          cmd.params[i] = distorted.x;
          cmd.params[i + 1] = distorted.y;
        }
      }
    }
    return cmd;
  }

  isLastPoint(commandIndex, paramIndex, paramLength) {
    return (commandIndex === this.cachedCommands.length - 2) && (paramIndex === paramLength - 2);
  }

  generateDistortedPath() {
    const firstPointNoise = { x: null, y: null };
    
    const distortedCommands = this.cachedCommands.map((cmd, index) => 
      this.distortCommand(cmd, index, firstPointNoise)
    );
    
    return distortedCommands.map(cmd => {
      if (cmd.command === 'Z') return 'Z';
      const params = cmd.params.map(p => p.toFixed(2)).join(' ');
      return `${cmd.command}${params}`;
    }).join(' ');
  }

  pause() {
    this.isActive = false;
  }

  resume() {
    this.isActive = true;
  }

  destroy() {
    if (this.animation) {
      gsap.ticker.remove(this.animation);
      this.animation = null;
    }
    this.pathElement.setAttribute('d', this.originalPath);
    this.isActive = false;
  }
}