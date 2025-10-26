<?php
require_once __DIR__ . '/../pass/auth.php';

// 設定ファイルを明示的に読み込む（_chat_handler.phpより前に読み込む）
require_once __DIR__ . '/config/config.production.php';

// メッセージ送信処理を実行（HTML出力前に処理を完了させる）
require_once __DIR__ . '/../messages/templates/_chat_handler.php';

// 出力バッファリングを開始（ヘッダー送信前の出力を防ぐ）
ob_start();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- noindex -->
  <meta name="robots" content="noindex, nofollow">
  <meta name="format-detection" content="telephone=no">
  
  <!-- favicon -->
  <link rel="icon" type="image/svg+xml" href="./favicon.svg">
  <link rel="icon" href="./favicon.ico" type="image/x-icon">
  <link rel="apple-touch-icon" href="./apple-touch-icon.png">
  
  
  <!-- OGP -->
  <title>Knotory（ノトリー）｜ふたりらしい結婚報告サイト</title>
  <meta name="description" content="プロのデザイナーが手がける、ふたりだけのオリジナル結婚報告Webサイト。QR付きはがきもセットで、大切な人へ丁寧にお届け">
  <meta property="og:title" content="Knotory（ノトリー）｜ふたりらしい結婚報告サイト" />
  <meta property="og:type" content="website" />
  <meta property="og:url" content="" />
  <meta property="og:image" content="" />
  <meta property="og:site_name" content="" />
  <meta property="og:description" content="プロのデザイナーが手がける、ふたりだけのオリジナル結婚報告Webサイト。QR付きはがきもセットで、大切な人へ丁寧にお届け" />
  
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:url" content="">
  <meta name="twitter:title" content="Knotory（ノトリー）｜ふたりらしい結婚報告サイト">
  <meta name="twitter:description" content="プロのデザイナーが手がける、ふたりだけのオリジナル結婚報告Webサイト。QR付きはがきもセットで、大切な人へ丁寧にお届け">
  <meta name="twitter:image:src" content="">
  
  <!-- FV画像のプリロード（WebP優先） -->
  <link rel="preload" as="image" href="./assets/images/figure-fv-sp.webp" type="image/webp" media="(max-width: 767px)">
  <link rel="preload" as="image" href="./assets/images/figure-fv.webp" type="image/webp" media="(min-width: 768px)">
  <!-- WebP非対応ブラウザ用フォールバック -->
  <link rel="preload" as="image" href="./assets/images/figure-fv-sp.png" type="image/png" media="(max-width: 767px)">
  <link rel="preload" as="image" href="./assets/images/figure-fv.png" type="image/png" media="(min-width: 768px)">
  
  <!-- fontfamily -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  

  <!-- css -->
  <script type="module" crossorigin src="./assets/js/index.js"></script>
  <link rel="stylesheet" crossorigin href="./assets/css/index.css">
</head>
<body>
  <div class="c-opening">
    <figure>
      <img src="./assets/images/logo.png" alt="Logo" class="c-opening__logo js-opening__logo" />
    </figure>
    <p class="c-opening__text">Webで届ける、あたらしい結婚のカタチ</p>
  </div>  <header class="l-header">
    <div class="l-header__container">
      <!-- ハンバーガーメニューボタン -->
      <button class="p-hamburger" type="button" aria-label="メニューを開く" aria-expanded="false" aria-controls="global-nav">
        <span class="p-hamburger__line"></span>
        <span class="p-hamburger__line"></span>
        <span class="p-hamburger__line"></span>
      </button>
  
      <!-- グローバルナビゲーション -->
      <nav class="p-global-nav" id="global-nav" role="navigation" aria-label="メインメニュー">
        <div class="p-global-nav__overlay"></div>
        <div class="p-global-nav__content">
          <div class="p-global-nav__menu">
            <hgroup class="p-global-nav__item">
              <div class="p-global-nav__itemContainer">
                <a href="#announcement" class="global-nav__link" id="nav-announcement">
                  <h2 class="p-global-nav__title">
                    ふたりからのご報告
                  </h2>
                  <p class="p-global-nav__subTitle">announcement</p>
                </a>
              </div>
            </hgroup>
            <hgroup class="p-global-nav__item">
              <div class="p-global-nav__itemContainer">
                <a href="#about-us" class="global-nav__link" id="nav-about">
                  <h2 class="p-global-nav__title">
                    私たちについて
                  </h2>
                  <p class="p-global-nav__subTitle">about us</p>
                </a>
              </div>
            </hgroup>
            <hgroup class="p-global-nav__item">
              <div class="p-global-nav__itemContainer">
                <a href="#our-story" class="global-nav__link" id="nav-story">
                  <h2 class="p-global-nav__title">
                    ふたりの物語
                  </h2>
                  <p class="p-global-nav__subTitle">our story</p>
                </a>
              </div>
            </hgroup>
            <hgroup class="p-global-nav__item">
              <div class="p-global-nav__itemContainer">
                <a href="#qa-with-us" class="global-nav__link" id="nav-qa">
                  <h2 class="p-global-nav__title">
                    ふたりに聞いてみました
                  </h2>
                  <p class="p-global-nav__subTitle">q&amp;a with us</p>
                </a>
              </div>
            </hgroup>
            <hgroup class="p-global-nav__item">
              <div class="p-global-nav__itemContainer">
                <a href="#memories" class="global-nav__link" id="nav-memories">
                  <h2 class="p-global-nav__title">
                    思い出のアルバム
                  </h2>
                  <p class="p-global-nav__subTitle">memories</p>
                </a>
              </div>
            </hgroup>
            <hgroup class="p-global-nav__item">
              <div class="p-global-nav__itemContainer">
                <a href="#gratitude" class="global-nav__link" id="nav-gratitude">
                  <h2 class="p-global-nav__title">
                    心からのメッセージ
                  </h2>
                  <p class="p-global-nav__subTitle">gratitude</p>
                </a>
              </div>
            </hgroup>
          </div>
        </div>
      </nav>
    </div>
  </header>
  <main class="l-main">
    <!-- サイトタイトルと合わせて、表示なしのh1要素を入れる -->
    <h1 class="u-visuallyHidden">Knotory</h1>
    <!-- 
    # 更新マニュアル
    
    このファイルは、結婚式のウェブサイトのファーストビューセクションを定義しています。
    このセクションでは、メインキャッチコピーと新郎新婦の名前を表示しています。
    
    ## 更新対象
    ・メインキャッチコピー
    ・新郎新婦の名前（英語表記）
    
    ## 画像のパス
    背景画像として使用されます
    ・デスクトップ用背景画像: `/assets/images/figure-fv.png`
    ・デスクトップ用背景画像（Webp）: `/assets/images/figure-fv.webp`
    ・スマートフォン用背景画像: `/assets/images/figure-fv-sp.png`
    ・スマートフォン用背景画像（Webp）: `/assets/images/figure-fv-sp.webp`
    
    ※推奨サイズ：デスクトップ 3840px x 2160px、スマートフォン 750px x 1624px
    
    ## 注意事項
    ・画像は、WebP形式とPNG形式の両方を用意してください。
    　※WebP形式とは、Googleが開発した画像フォーマットで、圧縮率が高く、画質を保ちながらファイルサイズを小さくできます。
    　※WebP形式の画像は、対応しているブラウザでのみ表示されます。
    ・画像は、デスクトップとスマートフォンで異なるサイズを使用します。
    
    -->
    <div class="p-fv">
      <div class="p-fv__imageContainer js-fadeIn">
        <picture>
          <source srcset="./assets/images/figure-fv.webp" type="image/webp" media="(min-width: 769px)">
          <source srcset="./assets/images/figure-fv.png" type="image/png" media="(min-width: 769px)">
          <source srcset="./assets/images/figure-fv-sp.webp" type="image/webp" media="(max-width: 768px)">
          <img src="./assets/images/figure-fv-sp.webp" alt="" class="p-fv__image js-fv__image">
        </picture>
      </div>
    
      <div class="p-fv__container js-anime">
        <!-- メインキャッチコピーを入力してください -->
        <p class="p-fv__copy">We Tied the Knot</p>
        <!-- 新郎新婦の名前（英語表記）を入力してください -->
        <p class="p-fv__byline">By Naoto&Yuna</p>
      </div>
      <div class="p-fv__scroll js-fadeIn">
        <p class="p-fv__scrollText">scroll</p>
      </div>
    </div>
    <!-- 
    # 更新マニュアル
    
    このファイルは、結婚式のウェブサイトの「ふたりからのご報告」セクションを定義しています。
    このセクションでは、結婚報告のメッセージと新郎新婦の名前を表示しています。
    
    ## 更新対象
    ・報告テキスト（メッセージ本文）
    ・新郎新婦の名前（英語表記）
    ・報告日付
    
    ## 注意事項
    ・改行は<br>タグを使用してください
    -->
    <section class="p-announcement">
      <div class="p-announcement__container">
        <hgroup class="c-heading2 js-fadeIn">
          <h2 class="c-heading2__mainText" id="announcement">ふたりからのご報告</h2>
          <p class="c-heading2__subText">Announcement</p>
        </hgroup>
        <div class="p-announcement__content js-fadeIn">
          <!-- 報告テキストを入力してください（改行は<br>を記入してください） -->
          <p>
            これまで私たちをあたたかく見守り、<br class="u-onlySp">支えてくださった皆さまへ。<br>
            <br>
            心よりの感謝を込めて、ささやかではありますが<br>
            ご報告をさせていただきます。<br>
            このたび、私たちは結婚いたしました。<br>
            <br>
            日々の中で交わす小さな笑顔や、<br class="u-onlySp">ふたりで過ごす何気ない時間が<br>
            これからの人生をよりあたたかく、<br>
            やさしいものにしてくれるように感じています。<br>
            <br>
            まだまだ未熟なふたりではありますが、<br class="u-onlySp">これからの毎日を支え合い、励まし合いながら<br>
            ひとつひとつの節目を大切に歩んでまいります。<br>
            <br>
            今後とも変わらぬご縁を<br class="u-onlySp">いただけますと幸いです。<br>
            どうぞよろしくお願い申し上げます。<br>
            <br>
            <!-- 報告日付を入力してください -->
            令和7年8月<br>
            <!-- 新郎新婦の名前（英語表記）を入力してください -->
            <span class="u-fontFamilyEn">Naoto ＆ Yuna</span>
          </p>
      </div>
    </section>
    <!-- 
    # 更新マニュアル
    
    このファイルは、結婚式のウェブサイトの「私たちについて」セクションを定義しています。
    このセクションでは、新郎新婦のプロフィールを紹介しています。
    
    ## 更新対象
    ・新郎新婦の名前
    ・新郎新婦の名前（英語表記）
    ・新郎新婦のプロフィール（生年月日、出身地、血液型、職業など）
    
    ## 画像のパス
    同じファイル名で差し替えて使用してください
    ・新郎の画像: `/assets/images/figure-groom.png`
    ・新婦の画像: `/assets/images/figure-bride.png`
    
    ※推奨サイズ：440px x 460px
    -->
    <section class="p-aboutUs">
      <div class="p-aboutUs__container">
        <hgroup class="c-heading2 js-fadeIn">
          <h2 class="c-heading2__mainText" id="about-us">私たちについて</h2>
          <p class="c-heading2__subText">About us</p>
        </hgroup>
        <div class="p-aboutUs__profileWrapper">
          <article class="c-profile js-anime">
            <figure class="c-profile__image">
              <img src="./assets/images/figure-groom.png" alt="">
            </figure>
            <!-- 名前を入力してください -->
            <hgroup class="c-profile__heading">
              <h1 class="c-profile__name">吉岡 直人</h1>
              <p class="c-profile__nameEn">Naoto Yoshioka</p>
            </hgroup>
            <div class="c-profile__content">
              <!-- プロフィールを入力してください -->
              <p>
                1995年9月25日生まれ<br>
                石川県出身 O型 会社員(エンジニア)<br>
                <br>
                歴史ある町並みや神社巡りが好きで<br>
                ひとり旅で訪れた京都で彼女と出会いました<br>
                最近は和楽器の音色にも興味があります。
              </p>
            </div>
          </article>
          <article class="c-profile js-anime">
            <figure class="c-profile__image">
              <img src="./assets/images/figure-bride.png" alt="">
            </figure>
            <hgroup class="c-profile__heading">
              <h1 class="c-profile__name">花村 結那</h1>
              <p class="c-profile__nameEn">Yuna Hanamura</p>
            </hgroup>
            <div class="c-profile__content">
              <p>
                1998年2月10日生まれ<br>
                東京都出身　A型 会社員(広報職)<br>
                <br>
                和の趣を感じるものが好きで<br>
                休日は着物で美術館を巡ったり<br>
                季節の花を楽しむ時間が癒しです
              </p>
            </div>
          </article>
        </div>
      </div>
    
    </section>
    <!-- 
    # 更新マニュアル
    
    このファイルは、結婚式のウェブサイトの「ふたりの物語」セクションを定義しています。
    このセクションでは、新郎新婦の生い立ちから出会い、結婚までのストーリーを時系列で表示しています。
    
    ## 更新対象
    ・各年代の年号
    ・各ストーリーのテキスト内容
    ・ストーリーの画像
    
    ## 画像のパス
    ストーリーごとに画像を設定できます
    ・ストーリー画像: `/assets/images/our-story-001.png` ～ `/assets/images/our-story-006.png`
    
    ※推奨サイズ：ストーリー画像 500px x 400px
    -->
    <section class="p-ourStory">
      <hgroup class="c-heading2 js-fadeIn">
        <h2 class="c-heading2__mainText" id="our-story">ふたりの物語</h2>
        <p class="c-heading2__subText">Our Story</p>
      </hgroup>
      <div class="p-ourStory__container">
        <div class="p-ourStory__content">
          <div class="p-ourStory__contentItemWrapper">
            <div class="c-ourStoryCard__wrapper">
              <article class="c-ourStoryCard">
                <div class="c-ourStoryCard__container">
                  <!-- 年号を入力してください -->
                  <h1 class="c-ourStoryCard__year">1995</h1>
                  <figure class="c-ourStoryCard__image">
                    <!-- 画像のパスを入力してください -->
                    <img src="./assets/images/our-story-001.png" alt="">
                  </figure>
                  <div class="c-ourStoryCard__textWrapper">
                    <!-- テキストを入力してください -->
                    <p class="c-ourStoryCard__text">
                      1995年9月25日誕生<br>石川県生まれ O型<br><br>吉岡家の次男として<br class=u-onlySp>のびのびと成長する。
                    </p>
                  </div>
                </div>
              </article>
            </div>
    
            <div class="c-ourStoryCard__wrapper">
              <article class="c-ourStoryCard">
                <div class="c-ourStoryCard__container">
                  <!-- 年号を入力してください -->
                  <h1 class="c-ourStoryCard__year">1998</h1>
                  <figure class="c-ourStoryCard__image">
                    <!-- 画像のパスを入力してください -->
                    <img src="./assets/images/our-story-002.png" alt="">
                  </figure>
                  <div class="c-ourStoryCard__textWrapper">
                    <!-- テキストを入力してください -->
                    <p class="c-ourStoryCard__text">
                      1998年2月10日誕生<br>東京都生まれ O型<br><br>花村家の長女として、<br>やさしく朗らかに育ちました。
                    </p>
                  </div>
                </div>
              </article>
            </div>
    
            <div class="c-ourStoryCard__wrapper">
              <article class="c-ourStoryCard">
                <div class="c-ourStoryCard__container">
                  <!-- 年号を入力してください -->
                  <h1 class="c-ourStoryCard__year">2004</h1>
                  <figure class="c-ourStoryCard__image">
                    <!-- 画像のパスを入力してください -->
                    <img src="./assets/images/our-story-003.png" alt="">
                  </figure>
                  <div class="c-ourStoryCard__textWrapper">
                    <!-- テキストを入力してください -->
                    <p class="c-ourStoryCard__text">
                      毎日外で元気いっぱいに遊び<br>運動会では俊足を<br>活かして活躍し、<br>いつも全力で走っていました。
                    </p>
                  </div>
                </div>
              </article>
            </div>
      
            <div class="c-ourStoryCard__wrapper">
              <article class="c-ourStoryCard">
                <div class="c-ourStoryCard__container">
                  <!-- 年号を入力してください -->
                  <h1 class="c-ourStoryCard__year">2009</h1>
                  <figure class="c-ourStoryCard__image">
                    <!-- 画像のパスを入力してください -->
                    <img src="./assets/images/our-story-004.png" alt="">
                  </figure>
                  <div class="c-ourStoryCard__textWrapper">
                    <!-- テキストを入力してください -->
                    <p class="c-ourStoryCard__text">
                      本を読むことや勉強も大好きで、<br>静かに机に向かうのが得意な<br>子どもでした。<br>テストはいつも満点でした。
                    </p>
                  </div>
                </div>
              </article>
            </div>
      
            <div class="c-ourStoryCard__wrapper">
              <article class="c-ourStoryCard">
                <div class="c-ourStoryCard__container">
                  <!-- 年号を入力してください -->
                  <h1 class="c-ourStoryCard__year">2011</h1>
                  <figure class="c-ourStoryCard__image">
                    <!-- 画像のパスを入力してください -->
                    <img src="./assets/images/our-story-005.png" alt="">
                  </figure>
                  <div class="c-ourStoryCard__textWrapper">
                    <!-- テキストを入力してください -->
                    <p class="c-ourStoryCard__text">
                      中学では野球部に所属し、<br>仲間と汗を流しながら<br>夢中で白球を追いかける毎日を<br>過ごしました。
                    </p>
                  </div>
                </div>
              </article>
            </div>
      
            <div class="c-ourStoryCard__wrapper">
              <article class="c-ourStoryCard">
                <div class="c-ourStoryCard__container">
                  <!-- 年号を入力してください -->
                  <h1 class="c-ourStoryCard__year">2015</h1>
                  <figure class="c-ourStoryCard__image">
                    <!-- 画像のパスを入力してください -->
                    <img src="./assets/images/our-story-006.png" alt="">
                  </figure>
                  <div class="c-ourStoryCard__textWrapper">
                    <!-- テキストを入力してください -->
                    <p class="c-ourStoryCard__text">
                      茶道部に所属し、<br>着物や和の文化に親しむ日々。<br>心落ち着く豊かな高校生活を<br>過ごしました。
                    </p>
                  </div>
                </div>
              </article>
            </div>
      
            <div class="c-ourStoryCard__wrapper">
              <article class="c-ourStoryCard">
                <div class="c-ourStoryCard__container">
                  <!-- 年号を入力してください -->
                  <h1 class="c-ourStoryCard__year">2017</h1>
                  <figure class="c-ourStoryCard__image">
                    <!-- 画像のパスを入力してください -->
                    <img src="./assets/images/our-story-007.png" alt="">
                  </figure>
                  <div class="c-ourStoryCard__textWrapper">
                    <!-- テキストを入力してください -->
                    <p class="c-ourStoryCard__text">
                      大学のサークルで出会い、<br>自然と会話を重ねるうちに距離が<br>縮まっていきました。ほぼ毎日<br>あっていました。
                    </p>
                  </div>
                </div>
              </article>
            </div>
      
            <div class="c-ourStoryCard__wrapper">
              <article class="c-ourStoryCard">
                <div class="c-ourStoryCard__container">
                  <!-- 年号を入力してください -->
                  <h1 class="c-ourStoryCard__year">2024</h1>
                  <figure class="c-ourStoryCard__image">
                    <!-- 画像のパスを入力してください -->
                    <img src="./assets/images/our-story-008.png" alt="">
                  </figure>
                  <div class="c-ourStoryCard__textWrapper">
                    <!-- テキストを入力してください -->
                    <p class="c-ourStoryCard__text">
                      付き合った記念日に、「これから<br>もそばで笑っていてほしい」と<br>プロポーズ。これから2人で<br>支え合って歩んでいきます。
                    </p>
                  </div>
                </div>
              </article>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- 
    # 更新マニュアル
    
    このファイルは、結婚式のウェブサイトの「ふたりに聞いてみました」セクションを定義しています。
    このセクションでは、新郎新婦への質問と回答をQ&A形式で表示しています。
    
    ## 更新対象
    ・質問内容
    ・新郎の回答内容
    ・新婦の回答内容
    
    ## 画像のパス
    新郎新婦のアバター画像
    ・新郎アバター: `/assets/images/avatar-groom.png`
    ・新婦アバター: `/assets/images/avatar-bride.png`
    
    ## Q&A追加・削除方法
    Q&Aの追加・削除は div.p-qaWithUs__item 要素を追加・削除してください。
    各質問には新郎と新婦それぞれの回答を設定できます。
    
    ※推奨サイズ：アバター画像 100px x 100px
    -->
    <section class="p-qaWithUs">
      <div class="p-qaWithUs__container">
        <hgroup class="c-heading2 js-fadeIn">
          <h2 class="c-heading2__mainText" id="qa-with-us">ふたりに聞いてみました</h2>
          <p class="c-heading2__subText">Q&amp;A with us</p>
        </hgroup>
        <div class="p-qaWithUs__content">
          <dl class="p-qaWithUs__list">
            <div class="p-qaWithUs__item js-fadeDown">
              <!-- 質問内容を入力してください -->
              <dt class="p-qaWithUs__question">
                お互いの呼び名は？
              </dt>
              <dd class="p-qaWithUs__answers">
                <div class="p-qaWithUs__answer p-qaWithUs__answer__groom">
                  <div class="p-qaWithUs__answerHeader">
                    <img src="./assets/images/avatar-groom.png" alt="新郎のアバター" class="p-qaWithUs__avatar">
                  </div>
                  <div class="p-qaWithUs__answerBox">
                    <!-- 新郎の回答を入力してください -->
                    <p class="p-qaWithUs__answerText">
                      ゆうな
                    </p>
                  </div>
                </div>
                <div class="p-qaWithUs__answer p-qaWithUs__answer__bride">
                  <div class="p-qaWithUs__answerHeader">
                    <img src="./assets/images/avatar-bride.png" alt="新婦のアバター" class="p-qaWithUs__avatar">
                  </div>
                  <div class="p-qaWithUs__answerBox">
                    <!-- 新婦の回答を入力してください -->
                    <p class="p-qaWithUs__answerText">
                      なお
                    </p>
                  </div>
                </div>
              </dd>
            </div>
    
            <div class="p-qaWithUs__item js-fadeDown">
              <!-- 質問内容を入力してください -->
              <dt class="p-qaWithUs__question">
                お互いの第一印象は？
              </dt>
              <dd class="p-qaWithUs__answers">
                <div class="p-qaWithUs__answer p-qaWithUs__answer__groom">
                  <div class="p-qaWithUs__answerHeader">
                    <img src="./assets/images/avatar-groom.png" alt="新郎のアバター" class="p-qaWithUs__avatar">
                  </div>
                  <div class="p-qaWithUs__answerBox">
                    <!-- 新郎の回答を入力してください -->
                    <p class="p-qaWithUs__answerText">
                      着物が似合う人
                    </p>
                  </div>
                </div>
                <div class="p-qaWithUs__answer p-qaWithUs__answer__bride">
                  <div class="p-qaWithUs__answerHeader">
                    <img src="./assets/images/avatar-bride.png" alt="新婦のアバター" class="p-qaWithUs__avatar">
                  </div>
                  <div class="p-qaWithUs__answerBox">
                    <!-- 新婦の回答を入力してください -->
                    <p class="p-qaWithUs__answerText">
                      まじめそうな人
                    </p>
                  </div>
                </div>
              </dd>
            </div>
    
            <div class="p-qaWithUs__item js-fadeDown">
              <!-- 質問内容を入力してください -->
              <dt class="p-qaWithUs__question">
                初デートの場所は？
              </dt>
              <dd class="p-qaWithUs__answers">
                <div class="p-qaWithUs__answer p-qaWithUs__answer__groom">
                  <div class="p-qaWithUs__answerHeader">
                    <img src="./assets/images/avatar-groom.png" alt="新郎のアバター" class="p-qaWithUs__avatar">
                  </div>
                  <div class="p-qaWithUs__answerBox">
                    <!-- 新郎の回答を入力してください -->
                    <p class="p-qaWithUs__answerText">
                      大学帰りの映画
                    </p>
                  </div>
                </div>
                <div class="p-qaWithUs__answer p-qaWithUs__answer__bride">
                  <div class="p-qaWithUs__answerHeader">
                    <img src="./assets/images/avatar-bride.png" alt="新婦のアバター" class="p-qaWithUs__avatar">
                  </div>
                  <div class="p-qaWithUs__answerBox">
                    <!-- 新婦の回答を入力してください -->
                    <p class="p-qaWithUs__answerText">
                      恋愛映画みたよね
                    </p>
                  </div>
                </div>
              </dd>
            </div>
    
            <div class="p-qaWithUs__item js-fadeDown">
              <!-- 質問内容を入力してください -->
              <dt class="p-qaWithUs__question">
                相手の好きなところは？
              </dt>
              <dd class="p-qaWithUs__answers">
                <div class="p-qaWithUs__answer p-qaWithUs__answer__groom">
                  <div class="p-qaWithUs__answerHeader">
                    <img src="./assets/images/avatar-groom.png" alt="新郎のアバター" class="p-qaWithUs__avatar">
                  </div>
                  <div class="p-qaWithUs__answerBox">
                    <!-- 新郎の回答を入力してください -->
                    <p class="p-qaWithUs__answerText">
                      どんな時もポジティブなとこ
                    </p>
                  </div>
                </div>
                <div class="p-qaWithUs__answer p-qaWithUs__answer__bride">
                  <div class="p-qaWithUs__answerHeader">
                    <img src="./assets/images/avatar-bride.png" alt="新婦のアバター" class="p-qaWithUs__avatar">
                  </div>
                  <div class="p-qaWithUs__answerBox">
                    <!-- 新婦の回答を入力してください -->
                    <p class="p-qaWithUs__answerText">
                      真面目だけどちょっと抜けてるとこ
                    </p>
                  </div>
                </div>
              </dd>
            </div>
    
            <div class="p-qaWithUs__item js-fadeDown">
              <!-- 質問内容を入力してください -->
              <dt class="p-qaWithUs__question">
                相手に驚いたことは？
              </dt>
              <dd class="p-qaWithUs__answers">
                <div class="p-qaWithUs__answer p-qaWithUs__answer__groom">
                  <div class="p-qaWithUs__answerHeader">
                    <img src="./assets/images/avatar-groom.png" alt="新郎のアバター" class="p-qaWithUs__avatar">
                  </div>
                  <div class="p-qaWithUs__answerBox">
                    <!-- 新郎の回答を入力してください -->
                    <p class="p-qaWithUs__answerText">
                      地図なしでスイスイ歩ける方向感覚
                    </p>
                  </div>
                </div>
                <div class="p-qaWithUs__answer p-qaWithUs__answer__bride">
                  <div class="p-qaWithUs__answerHeader">
                    <img src="./assets/images/avatar-bride.png" alt="新婦のアバター" class="p-qaWithUs__avatar">
                  </div>
                  <div class="p-qaWithUs__answerBox">
                    <!-- 新婦の回答を入力してください -->
                    <p class="p-qaWithUs__answerText">
                      料理が得意なとこ
                    </p>
                  </div>
                </div>
              </dd>
            </div>
    
            <div class="p-qaWithUs__item js-fadeDown">
              <!-- 質問内容を入力してください -->
              <dt class="p-qaWithUs__question">
                休日はどう過ごしてる？
              </dt>
              <dd class="p-qaWithUs__answers">
                <div class="p-qaWithUs__answer p-qaWithUs__answer__groom">
                  <div class="p-qaWithUs__answerHeader">
                    <img src="./assets/images/avatar-groom.png" alt="新郎のアバター" class="p-qaWithUs__avatar">
                  </div>
                  <div class="p-qaWithUs__answerBox">
                    <!-- 新郎の回答を入力してください -->
                    <p class="p-qaWithUs__answerText">
                      カフェでのんびり
                    </p>
                  </div>
                </div>
                <div class="p-qaWithUs__answer p-qaWithUs__answer__bride">
                  <div class="p-qaWithUs__answerHeader">
                    <img src="./assets/images/avatar-bride.png" alt="新婦のアバター" class="p-qaWithUs__avatar">
                  </div>
                  <div class="p-qaWithUs__answerBox">
                    <!-- 新婦の回答を入力してください -->
                    <p class="p-qaWithUs__answerText">
                      映画を見まくる
                    </p>
                  </div>
                </div>
              </dd>
            </div>
    
            <div class="p-qaWithUs__item js-fadeDown">
              <!-- 質問内容を入力してください -->
              <dt class="p-qaWithUs__question">
                どんな家庭を築きたい？
              </dt>
              <dd class="p-qaWithUs__answers">
                <div class="p-qaWithUs__answer p-qaWithUs__answer__groom">
                  <div class="p-qaWithUs__answerHeader">
                    <img src="./assets/images/avatar-groom.png" alt="新郎のアバター" class="p-qaWithUs__avatar">
                  </div>
                  <div class="p-qaWithUs__answerBox">
                    <!-- 新郎の回答を入力してください -->
                    <p class="p-qaWithUs__answerText">
                      安心して帰りたくなる場所
                    </p>
                  </div>
                </div>
                <div class="p-qaWithUs__answer p-qaWithUs__answer__bride">
                  <div class="p-qaWithUs__answerHeader">
                    <img src="./assets/images/avatar-bride.png" alt="新婦のアバター" class="p-qaWithUs__avatar">
                  </div>
                  <div class="p-qaWithUs__answerBox">
                    <!-- 新婦の回答を入力してください -->
                    <p class="p-qaWithUs__answerText">
                      笑顔が絶えない家族
                    </p>
                  </div>
                </div>
              </dd>
            </div>
    
            <div class="p-qaWithUs__item js-fadeDown">
              <!-- 質問内容を入力してください -->
              <dt class="p-qaWithUs__question">
                最後にひとこと！
              </dt>
              <dd class="p-qaWithUs__answers">
                <div class="p-qaWithUs__answer p-qaWithUs__answer__groom">
                  <div class="p-qaWithUs__answerHeader">
                    <img src="./assets/images/avatar-groom.png" alt="新郎のアバター" class="p-qaWithUs__avatar">
                  </div>
                  <div class="p-qaWithUs__answerBox">
                    <!-- 新郎の回答を入力してください -->
                    <p class="p-qaWithUs__answerText">
                      どんな時もずっと支えるね
                    </p>
                  </div>
                </div>
                <div class="p-qaWithUs__answer p-qaWithUs__answer__bride">
                  <div class="p-qaWithUs__answerHeader">
                    <img src="./assets/images/avatar-bride.png" alt="新婦のアバター" class="p-qaWithUs__avatar">
                  </div>
                  <div class="p-qaWithUs__answerBox">
                    <!-- 新婦の回答を入力してください -->
                    <p class="p-qaWithUs__answerText">
                      これからもよろしくね
                    </p>
                  </div>
                </div>
              </dd>
            </div>
          </dl>
        </div>
      </div>
    
    </section>
    <!-- 
    # 更新マニュアル
    
    このファイルは、結婚式のウェブサイトの「思い出のアルバム」セクションを定義しています。
    このセクションでは、写真のスライダーと動画を表示しています。
    
    ## 更新対象
    ・画像３枚
    ・写真スライドの画像とキャプション
    ・動画ファイル
    ・動画のキャプション
    ・動画のサムネイル
    
    ## 画像のパス
    固定の画像３枚
    ・メイン画像: `/assets/images/figure-memories-main.png` SP 300 × 400 / PC 415 × 400
    ・左側画像: `/assets/images/figure-memories-left-side.png` SP 221 × 141 / PC 251 × 206
    ・右側画像: `/assets/images/figure-memories-right-side.png` SP 118 × 118 / PC 250 × 250
    
    スライダー用の画像ファイル
    ・思い出画像: `/assets/images/figure-memories-001.png` など
    （複数枚使用する場合は連番で管理：figure-memories-002.png, figure-memories-003.png など）
    
    ## 動画のパス
    ・思い出動画: `/assets/videos/memories.mp4`
    
    ## 動画のサムネイル
    ・サムネイル画像: `/assets/images/thumbnail-memories.jpg`
    
    ## スライド追加・削除方法
    スライドの追加・削除は li.splide__slide 要素を追加・削除してください。
    各スライドには画像とキャプションを設定できます。
    
    ※推奨サイズ：スライド画像 800px x 600px、動画 1920px x 1080px
    -->
    <section class="p-memories">
      <div class="p-memories__container">
        <hgroup class="c-heading2 js-fadeIn">
          <h2 class="c-heading2__mainText" id="memories">思い出のアルバム</h2>
          <p class="c-heading2__subText">Memories</p>
        </hgroup>
        <div class="p-memories__content">
          <!-- 画像３枚 -->
          <div class="p-memories__images js-anime">
            <figure class="p-memories__imageFigure p-memories__imageFigure__main js-anime">
              <!-- 思い出画像を入力してください -->
              <img src="./assets/images/figure-memories-main.png" alt="" class="p-memories__image" width="300" height="400">
            </figure>
            <figure class="p-memories__imageFigure p-memories__imageFigure__leftSide js-anime">
              <img src="./assets/images/figure-memories-left-side.png" alt="" class="p-memories__image" width="221" height="141">
            </figure>
            <figure class="p-memories__imageFigure p-memories__imageFigure__rightSide js-anime">
              <img src="./assets/images/figure-memories-right-side.png" alt="" class="p-memories__image" width="118" height="118">
            </figure>
          </div>
    
          <!-- splide.jsを使ったカルーセルスライダーで画像＋画像のキャプションのスライドを生成 -->
          <div class="p-memories__slider splide js-simpleFadeIn">
            <div class="splide__track">
              <ul class="splide__list">
                <li class="splide__slide">
                  <figure class="p-memories__slideFigure">
                    <!-- 思い出画像を入力してください -->
                    <img src="./assets/images/figure-memories-001.png" alt="" class="p-memories__slideImage">
                    <figcaption class="p-memories__slideCaption">
                      <!-- 画像のキャプションを入力してください -->
                      友人の結婚式場にて 1
                    </figcaption>
                  </figure>
                </li>
                <li class="splide__slide">
                  <figure class="p-memories__slideFigure">
                    <!-- 思い出画像を入力してください -->
                    <img src="./assets/images/figure-memories-001.png" alt="" class="p-memories__slideImage">
                    <figcaption class="p-memories__slideCaption">
                      <!-- 画像のキャプションを入力してください -->
                      友人の結婚式場にて 2
                    </figcaption>
                  </figure>
                </li>
                <li class="splide__slide">
                  <figure class="p-memories__slideFigure">
                    <!-- 思い出画像を入力してください -->
                    <img src="./assets/images/figure-memories-001.png" alt="" class="p-memories__slideImage">
                    <figcaption class="p-memories__slideCaption">
                      <!-- 画像のキャプションを入力してください -->
                      友人の結婚式場にて 3
                    </figcaption>
                  </figure>
                </li>
                <li class="splide__slide">
                  <figure class="p-memories__slideFigure">
                    <!-- 思い出画像を入力してください -->
                    <img src="./assets/images/figure-memories-001.png" alt="" class="p-memories__slideImage">
                    <figcaption class="p-memories__slideCaption">
                      <!-- 画像のキャプションを入力してください -->
                      友人の結婚式場にて 4
                    </figcaption>
                  </figure>
                </li>
                <li class="splide__slide">
                  <figure class="p-memories__slideFigure">
                    <!-- 思い出画像を入力してください -->
                    <img src="./assets/images/figure-memories-001.png" alt="" class="p-memories__slideImage">
                    <figcaption class="p-memories__slideCaption">
                      <!-- 画像のキャプションを入力してください -->
                      友人の結婚式場にて 5
                    </figcaption>
                  </figure>
                </li>
              </ul>
            </div>
          </div>
    
          <!-- videoタグでMP4データを埋め込み -->
          <div class="p-memories__video js-simpleFadeIn">
            <div class="p-memories__videoWrapper">
              <!-- 動画ファイル/サムネイルを入力してください -->
              <video
                controls
                muted
                playsinline
                preload
                onclick="this.play();"
                poster="./assets/images/thumbnail-memories.jpg"
              >
                <source src="./assets/mp4/memories.mp4" type="video/mp4">
                お使いのブラウザは動画タグに対応していません。
              </video>
              <button class="p-memories__playButton" aria-label="動画を再生">
                <svg width="60" height="60" viewBox="0 0 60 60" fill="none">
                  <circle cx="30" cy="30" r="30" fill="rgba(255, 255, 255, 0.9)"/>
                  <path d="M25 20L40 30L25 40V20Z" fill="#AF9B4F"/>
                </svg>
              </button>
            </div>
            <!-- 動画のキャプションを入力してください -->
            <p class="p-memories__videoCaption">
              想い出のムービー
            </p>
          </div>
        </div>
      </div>
    </section>
    <!-- 
    # 更新マニュアル
    
    このファイルは、結婚式のウェブサイトの「心からのメッセージ」セクションを定義しています。
    このセクションでは、ゲストへの感謝のメッセージを表示しています。
    
    ## 更新対象
    ・感謝のメッセージ本文
    
    ## 注意事項
    ・改行は<br>タグを使用してください
    -->
    <section class="p-gratitude js-anime js-gratitude">
      <div class="p-gratitude__container">
        <hgroup class="c-heading2 js-fadeIn">
          <h2 class="c-heading2__mainText" id="gratitude">心からのメッセージ</h2>
          <p class="c-heading2__subText">Gratitude</p>
        </hgroup>
        <div class="p-gratitude__content">
          <!-- 感謝のメッセージを入力してください（改行は<br>を記入してください） -->
          <p class="p-gratitude__message">
            本日はお忙しい中、<br>
            私たちの門出にお立ち会いいただき、<br>
            誠にありがとうございます。<br>
            これまでの人生の中で、<br>
            数えきれないほどの支えや励ましをいただき、<br>
            今日という日を迎えることができました。<br>
            <br>
            どんな時も寄り添い、導いてくださったご家族、<br>
            笑い合い、背中を押してくれた友人、<br>
            ともに時間を過ごしてきたすべての方へ<br>
            心より感謝申し上げます。<br>
            <br>
            これからは夫婦として、<br>
            皆さまへの感謝を忘れず、<br>
            日々を大切に歩んでまいります。<br>
            今後とも温かく見守って<br>
            いただけましたら幸いです。
          </p>
          <p class="p-gratitude__thankYou js-gratitude__thankYou">
            <span class="p-gratitude__thankYouChar" style="--delay: 50ms">T</span><span class="p-gratitude__thankYouChar" style="--delay: 100ms">h</span><span class="p-gratitude__thankYouChar" style="--delay: 150ms">a</span><span class="p-gratitude__thankYouChar" style="--delay: 200ms">n</span><span class="p-gratitude__thankYouChar" style="--delay: 250ms">k</span><span class="p-gratitude__thankYouChar" style="--delay: 300ms">&nbsp;</span><span class="p-gratitude__thankYouChar" style="--delay: 350ms">y</span><span class="p-gratitude__thankYouChar" style="--delay: 400ms">o</span><span class="p-gratitude__thankYouChar" style="--delay: 450ms">u</span><span class="p-gratitude__thankYouChar" style="--delay: 500ms">！</span>
          </p>
        </div>
      </div>
    </section>
    <!-- メッセージ送信フォームの埋め込み -->
    <?php include_once '../messages/templates/_chat.php'; ?>
  </main>
  <footer class="l-footer">
    <div class="l-footer__container js-fadeIn">
      <!-- logo -->
      <div class="l-footer__logo">
        <p class="l-footer__copy">Webで届ける、あたらしい結婚のカタチ</p>
        <figure class="l-footer__logoImage">
          <img src="./assets/images/logo.png" alt="Knotory ノトリ">
        </figure>
      </div>
      <p class="l-footer__text">
        こちらのページは、Knotory が<br class="u-onlySp">作成しています。<br>
        結婚のご報告を「あなたらしく」<br>
        丁寧に報告されたい方にぴったりのサービスです。
      </p>
      <div class="l-footer__btnWrapper">
        <a href="#" class="c-btn">
          Knotoryをもっと詳しく
        </a>
      </div>
      <div class="c-btnLine">
        <div class="c-btnLine__linkContainer">
          <div class="c-btnLine__linkInner">
            <figure class="c-btnLine__linkIcon">
              <img src="data:image/png;base64,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" alt="LINE">
            </figure>
            <p class="c-btnLine__text">
              LINE公式アカウント<br />
              で無料相談できます♪
            </p>
          </div>
          <div class="c-btnLine__linkBtnWrapper">
            <a href="#" class="c-btn c-btn__line">無料で相談してみる</a>
          </div>
        </div>
      </div>
      <p class="l-footer__text">&copy; Knotory</p>
    </div>
  
  </footer>  
</body>
</html>