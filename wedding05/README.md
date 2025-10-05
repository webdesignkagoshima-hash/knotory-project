# 環境構成
コンパイルなどはViteベースで行っています

+ scss：sassでコーディングするためにcssへコンパイルするため
+ postcss：autoprefixer,メディアクエリの順番を自動で整理してくれる,文字コードを自動で保管してくれる,CSSのプロパティを自動で順番整理してくれる
+ markuplint：マークアップの構文チェック
+ stylelint：CSSの構文チェック
+ eslint：JSの構文チェック

# 前提条件
node v18以上

# コマンド
## 初回実行時
package.jsonからライブラリをローカルにインストールする
```
npm ci
```

huskyのインストールから初期化まで
```
npm run prepare
npx husky set .husky/pre-commit "npx lint-staged"
```

## 開発時のコマンド
npm run dev

## デプロイ時のコマンド
npm run build

# 概要
## meta情報について
src/data/pageData.json　内で管理しております

サンプル
```JSON
  "/index.html": {
    "title": "Main Page",
    "description": "Main Page",
    "url": "https://",
    "ogp": "https://",
    "sitename": "sample"
  }
```

各項目について補足
```JSON
  "パス名を記述": {
    "title": "タイトルを記述",
    "description": "ディスクリプションを記述",
    "url": "ページの本番のURLを記述",
    "ogp": "OGPのURLを記述",
    "sitename": "サイト名を記述"
  }
```

## htmlについて
共通パーツの管理のため「vite-plugin-handlebars」を使っています
共通パーツ（2か所以上で使いまわしたいパーツ）はcomponentsディレクトリに記述して
```
{{> ファイル名}}
```
で引用してご利用下さい

## scssについて
### クラスの命名規則
+ c-Block__element__modifier
コンポーネントクラス
複数の画面で利用する場合に接頭語（c-）を使う
+ p-Block__element__modifier
プロジェクトクラス
単一の画面で利用する場合に接頭語（p-）を使う
+ l-Block__element__modifier
レイアウトクラス
ヘッダー、フッター、アサイド、メインなどのレイアウトで利用する場合に接頭語（l-）を使う
+ u-Block__element__modifier
ユーティリティクラス
PC/SPのみ表示など便利クラスで利用する場合に接頭語（u-）を使う

※Block, Elementでは【スネークケース】snake_case（_で単語をつなぐ）を利用すること
※Tailwindのクラスはこの限りではない