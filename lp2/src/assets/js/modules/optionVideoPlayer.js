/**
 * オプション機能の動画プレーヤーを管理するクラス
 */
export class OptionVideoPlayer {
  constructor() {
    this.videoWrapper = document.querySelector('.p-features__optionVideoWrapper');
    this.video = document.querySelector('.p-features__optionVideoWrapper video');
    this.playButton = document.querySelector('.p-features__optionPlayButton');
    this.isPlaying = false;
    this.isFullscreen = false;
    
    this.init();
  }
  
  init() {
    if (!this.video || !this.playButton) return;
    
    // 初期状態設定
    this.video.removeAttribute('controls');
    this.showCustomButton();
    
    // 全画面表示の状態変化を監視
    document.addEventListener('fullscreenchange', () => this.handleFullscreenChange());
    document.addEventListener('webkitfullscreenchange', () => this.handleFullscreenChange());
    document.addEventListener('mozfullscreenchange', () => this.handleFullscreenChange());
    document.addEventListener('MSFullscreenChange', () => this.handleFullscreenChange());
    
    // カスタム再生ボタンのクリックイベント
    this.playButton.addEventListener('click', (e) => {
      e.stopPropagation();
      e.preventDefault();
      
      if (this.isFullscreen) {
        console.log('Custom button click ignored - fullscreen mode');
        return;
      }
      
      this.playVideo();
    });
    
    // 動画エリアのクリックイベント（一時停止用）
    this.video.addEventListener('click', (e) => {
      if (this.isFullscreen) {
        console.log('Video click ignored - fullscreen mode');
        return;
      }
      
      e.preventDefault();
      e.stopPropagation();
      
      if (this.isPlaying) {
        this.pauseVideo();
      }
    });
    
    // 動画の状態変化を監視
    this.video.addEventListener('play', () => {
      console.log('Option video started playing');
      this.isPlaying = true;
      this.videoWrapper.classList.add('is-playing');
      
      if (!this.isFullscreen) {
        this.hideCustomButton();
        this.showControls();
      }
    });
    
    this.video.addEventListener('pause', () => {
      console.log('Option video paused');
      this.isPlaying = false;
      this.videoWrapper.classList.remove('is-playing');
      
      if (!this.isFullscreen) {
        this.showCustomButton();
        this.hideControls();
      }
    });
    
    this.video.addEventListener('ended', () => {
      console.log('Option video ended');
      this.isPlaying = false;
      this.videoWrapper.classList.remove('is-playing');
      
      if (!this.isFullscreen) {
        this.showCustomButton();
        this.hideControls();
      }
    });
    
    // マウスホバー時のコントロール表示
    this.videoWrapper.addEventListener('mouseenter', () => {
      if (this.isPlaying && !this.isFullscreen) {
        this.showControls();
      }
    });
    
    this.videoWrapper.addEventListener('mouseleave', () => {
      if (this.isPlaying && !this.isFullscreen) {
        setTimeout(() => {
          if (!this.videoWrapper.matches(':hover')) {
            this.hideControls();
          }
        }, 2000);
      }
    });
  }
  
  handleFullscreenChange() {
    this.isFullscreen = !!(
      document.fullscreenElement ||
      document.webkitFullscreenElement ||
      document.mozFullScreenElement ||
      document.msFullscreenElement
    );
    
    console.log('Fullscreen state changed:', this.isFullscreen);
    
    if (this.isFullscreen) {
      console.log('Entered fullscreen - disabling custom controls');
      this.hideCustomButton();
      this.video.setAttribute('controls', 'controls');
    } else {
      console.log('Exited fullscreen - enabling custom controls');
      
      if (this.isPlaying) {
        this.hideCustomButton();
        this.showControls();
      } else {
        this.showCustomButton();
        this.hideControls();
      }
    }
  }
  
  playVideo() {
    console.log('Playing option video via custom button');
    this.video.play().catch(err => {
      console.error('Failed to play video:', err);
    });
  }
  
  pauseVideo() {
    console.log('Pausing option video via video click');
    this.video.pause();
  }
  
  showCustomButton() {
    if (this.isFullscreen) return;
    this.playButton.classList.remove('is-hidden');
  }
  
  hideCustomButton() {
    this.playButton.classList.add('is-hidden');
  }
  
  showControls() {
    this.video.setAttribute('controls', 'controls');
  }
  
  hideControls() {
    if (this.isFullscreen) return;
    this.video.removeAttribute('controls');
  }
}
