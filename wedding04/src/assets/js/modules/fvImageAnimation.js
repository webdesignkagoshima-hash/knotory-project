import { gsap } from 'gsap';

export class FVImageAnimation {
  constructor() {
    this.isVisible = false;
    this.timeline = null;
    
    this.initElements();
    this.setupIntersectionObserver();
  }

  initElements() {
    // PC版とSP版のpath要素を取得
    this.pcPath = document.querySelector('.p-fv__image.u-onlyPc path');
    this.spPath = document.querySelector('.p-fv__image.u-onlySp path');
    
    // PC版のvalues配列（8秒サイクル）
    this.pcValues = [
      "M26.9325 88.6353C85.4771 27.3465 152.635 49.7581 213.543 49.7581C366.5 49.7581 518 -62.1976 874.457 49.7581C1001.15 89.5501 1081.84 5.82044 1171.3 101.899C1220.69 154.955 1171.3 310.815 1171.3 373.583C1171.3 449.508 1233.96 612.31 1159.4 665.391C1121.81 692.159 1082.99 706.283 976.91 718.447C883.053 729.209 829.152 802.604 629.301 802.604C410.674 802.604 370.882 748.634 260.196 738.571C176.768 730.987 121.636 739.292 52.5458 691.919C-48.217 622.829 26.9325 495.757 26.9325 373.583C26.9325 234.997 -31.7755 150.095 26.9325 88.6353Z",
      "M50.9985 100.531C123.996 57.4592 275.591 113.795 336.5 113.795C489.457 113.795 441.19 -92.79 859 51.5C1000.98 100.531 1105.91 17.716 1195.36 113.795C1244.76 166.851 1255.72 229 1195.36 385.478C1113 599 1254.16 549.575 1183.47 677.286C1084 857 1055 750.467 1000.98 730.342C775.5 730.342 853.218 814.5 653.367 814.5C434.74 814.5 536 677.286 284.262 750.467C203.82 773.852 145.702 751.188 76.6118 703.815C-24.151 634.724 50.9985 507.653 50.9985 385.478C50.9985 246.892 -62.5 167.5 50.9985 100.531Z",
      "M26.9325 88.6353C85.4771 27.3465 152.635 49.7581 213.543 49.7581C366.5 49.7581 518 -62.1976 874.457 49.7581C1001.15 89.5501 1081.84 5.82044 1171.3 101.899C1220.69 154.955 1171.3 310.815 1171.3 373.583C1171.3 449.508 1233.96 612.31 1159.4 665.391C1121.81 692.159 1082.99 706.283 976.91 718.447C883.053 729.209 829.152 802.604 629.301 802.604C410.674 802.604 370.882 748.634 260.196 738.571C176.768 730.987 121.636 739.292 52.5458 691.919C-48.217 622.829 26.9325 495.757 26.9325 373.583C26.9325 234.997 -31.7755 150.095 26.9325 88.6353Z"
    ];
    
    // SP版のvalues配列（6秒サイクル）
    this.spValues = [
      "M763.076 4.7243C509.036 41.5051 442.536 23.76 278.559 4.72429C138.592 -11.524 -14.8341 108.666 12.318 300.896C39.4701 493.126 80.0102 1238.49 12.3178 1524.79C-58.183 1822.97 193.212 1828.71 278.559 1835.77C417.72 1847.29 471.277 1773.77 736.192 1835.77C1001.11 1897.77 1068.86 1666.1 1029.77 1524.79C920.169 1128.53 998.186 554.376 1029.77 356.928C1042.22 279.132 1089.82 -42.5827 763.076 4.7243Z",
      "M765.076 15.7243C570.147 15.7243 575 120 280.559 15.7243C63.7072 -61.0733 -4.45336 179 14.3179 311.896C33.0892 444.792 109.317 1134 14.3177 1535.79C-56.1831 1833.97 151.5 1887.5 280.559 1846.77C413.722 1804.75 591.384 1812.41 738.192 1846.77C885 1881.13 1096.5 1846.77 1031.77 1535.79C913.571 967.892 1000.19 565.376 1031.77 367.928C1044.22 290.132 1031.77 15.7243 765.076 15.7243Z",
      "M763.076 4.7243C509.036 41.5051 442.536 23.76 278.559 4.72429C138.592 -11.524 -14.8341 108.666 12.318 300.896C39.4701 493.126 80.0102 1238.49 12.3178 1524.79C-58.183 1822.97 193.212 1828.71 278.559 1835.77C417.72 1847.29 471.277 1773.77 736.192 1835.77C1001.11 1897.77 1068.86 1666.1 1029.77 1524.79C920.169 1128.53 998.186 554.376 1029.77 356.928C1042.22 279.132 1089.82 -42.5827 763.076 4.7243Z"
    ];
  }

  setupIntersectionObserver() {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          this.start();
        } else {
          this.stop();
        }
      });
    }, { 
      threshold: 0.1,
      rootMargin: '50px 0px'
    });

    const fvContainer = document.querySelector('.p-fv__imageContainer');
    if (fvContainer) {
      observer.observe(fvContainer);
    }
  }

  start() {
    if (this.isVisible) return;
    this.isVisible = true;
    this.createAnimations();
  }

  stop() {
    this.isVisible = false;
    if (this.timeline) {
      this.timeline.kill();
      this.timeline = null;
    }
  }

  createAnimations() {
    // PC版のアニメーション（8秒サイクル）
    if (this.pcPath) {
      this.createPathAnimation(this.pcPath, this.pcValues, 8);
    }

    // SP版のアニメーション（6秒サイクル）
    if (this.spPath) {
      this.createPathAnimation(this.spPath, this.spValues, 6);
    }
  }

  createPathAnimation(pathElement, values, duration) {
    if (!pathElement || !values || values.length === 0) return;

    // GSAPタイムラインを作成
    const tl = gsap.timeline({ 
      repeat: -1, 
      ease: "power2.inOut"
    });

    // 各キーフレーム間のアニメーションを作成
    for (let i = 0; i < values.length - 1; i++) {
      const currentPath = values[i];
      const nextPath = values[i + 1];
      const segmentDuration = duration / (values.length - 1);

      // GSAPのattrプラグインを使用してd属性を滑らかに変更
      tl.to(pathElement, {
        duration: segmentDuration,
        attr: { d: nextPath },
        ease: "power2.inOut"
      });
    }

    // 最後のフレームから最初のフレームに戻る
    const lastPath = values[values.length - 1];
    const firstPath = values[0];
    const finalSegmentDuration = duration / (values.length - 1);
    
    tl.to(pathElement, {
      duration: finalSegmentDuration,
      attr: { d: firstPath },
      ease: "power2.inOut"
    });

    // タイムラインを保存（停止時に使用）
    if (!this.timeline) {
      this.timeline = gsap.timeline();
    }
    this.timeline.add(tl, 0);
  }

  destroy() {
    this.stop();
  }
}