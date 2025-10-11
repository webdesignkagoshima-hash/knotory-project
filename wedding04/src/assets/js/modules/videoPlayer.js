/**
 * カスタム動画プレーヤーの機能を管理するクラス
 */
export class CustomVideoPlayer {
  constructor() {
    this.videoWrapper = document.querySelector('.p-memories__videoWrapper');
    this.video = document.querySelector('.p-memories__videoWrapper video');
    this.playButton = document.querySelector('.p-memories__playButton');
    this.isPlaying = false;
    this.isFullscreen = false; // 全画面表示状態のフラグ
    
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
      
      // 全画面表示中はカスタムボタンの処理を無効化
      if (this.isFullscreen) {
        console.log('Custom button click ignored - fullscreen mode');
        return;
      }
      
      this.playVideo();
    });
    
    // 動画エリアのクリックイベント（一時停止用）
    this.video.addEventListener('click', (e) => {
      // 全画面表示中はカスタム処理をスキップ
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
      console.log('Video started playing');
      this.isPlaying = true;
      
      // videoWrapperにis-playingクラスを追加
      this.videoWrapper.classList.add('is-playing');
      
      // 全画面表示中でなければカスタムUI制御
      if (!this.isFullscreen) {
        this.hideCustomButton();
        this.showControls();
      }
    });
    
    this.video.addEventListener('pause', () => {
      console.log('Video paused');
      this.isPlaying = false;
      
      // videoWrapperからis-playingクラスを削除
      this.videoWrapper.classList.remove('is-playing');
      
      // 全画面表示中でなければカスタムUI制御
      if (!this.isFullscreen) {
        this.showCustomButton();
        this.hideControls();
      }
    });
    
    this.video.addEventListener('ended', () => {
      console.log('Video ended');
      this.isPlaying = false;
      
      // videoWrapperからis-playingクラスを削除
      this.videoWrapper.classList.remove('is-playing');
      
      // 全画面表示中でなければカスタムUI制御
      if (!this.isFullscreen) {
        this.showCustomButton();
        this.hideControls();
      }
    });
    
    // マウスホバー時のコントロール表示（全画面表示中は無効）
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
    // 全画面表示状態を判定
    this.isFullscreen = !!(
      document.fullscreenElement ||
      document.webkitFullscreenElement ||
      document.mozFullScreenElement ||
      document.msFullscreenElement
    );
    
    console.log('Fullscreen state changed:', this.isFullscreen);
    
    if (this.isFullscreen) {
      // 全画面表示開始時
      console.log('Entered fullscreen - disabling custom controls');
      this.hideCustomButton();
      this.video.setAttribute('controls', 'controls'); // 標準コントロールを強制表示
    } else {
      // 全画面表示終了時
      console.log('Exited fullscreen - enabling custom controls');
      
      // 動画の再生状態に応じてUIを復元
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
    console.log('Playing video via custom button');
    this.video.play().catch(err => {
      console.error('Failed to play video:', err);
    });
  }
  
  pauseVideo() {
    console.log('Pausing video via video click');
    this.video.pause();
  }
  
  showCustomButton() {
    // 全画面表示中はカスタムボタンを表示しない
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
    // 全画面表示中はコントロールを隠さない
    if (this.isFullscreen) return;
    
    this.video.removeAttribute('controls');
  }
}