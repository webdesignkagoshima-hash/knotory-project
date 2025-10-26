<?php
/**
 * プロジェクト01 管理画面エントリーポイント
 * 共通管理画面を読み込みます
 */

// プロジェクトIDを定義
define('PROJECT_ID', '01');

// 設定ファイルを読み込む
require_once __DIR__ . '/../config/config.production.php';

// 共通関数を読み込む
require_once __DIR__ . '/../../messages/functions.php';

// セキュリティ初期化
initSecurity();

// 共通管理画面を読み込む
require_once __DIR__ . '/../../messages/admin.php';