<?php
/**
 * 共通関数（リファクタリング後）
 * 新しいクラス構造を使用した互換性維持用の関数
 */

// オートローダーの読み込み
require_once __DIR__ . '/autoload.php';

// 後方互換性のための関数ラッパー

function initSecurity() {
    Security::init();
}

function generateCSRFToken(): string {
    return Security::generateCSRFToken();
}

function validateCSRFToken(string $token): bool {
    return Security::validateCSRFToken($token);
}

function validateSession(): bool {
    return Security::validateSession();
}

function validateAdminSession(): bool {
    return Security::validateAdminSession();
}

function writeSecurityLog(string $message, string $level = 'INFO') {
    Logger::write($message, $level);
}

function saveMessage(string $sessionId, string $userIp, string $userName, string $message): bool {
    return Message::save($sessionId, $userIp, $userName, $message);
}

function getUserMessages(string $sessionId): array {
    return Message::getUserMessages($sessionId);
}

function getAllMessages(): array {
    return Message::getAllMessages();
}

function getDatabase(): ?PDO {
    return Database::getInstance();
}

function loadConfig(): bool {
    return Config::load();
}

function checkIPRestriction(): bool {
    return Security::checkIPRestriction();
}

function setSecurityHeaders() {
    Security::setHeaders();
}

function isAdmin(): bool {
    return Security::isAdmin();
}

// 新しい関数（クラスメソッドのラッパー）

function loginAdmin(string $password): array {
    return Auth::adminLogin($password);
}

function logoutUser() {
    Auth::logout();
}

function logoutAdmin() {
    Auth::adminLogout();
}

function getLoginAttemptInfo(): array {
    return Auth::getLoginAttemptInfo();
}

function validateMessage(string $userName, string $message): array {
    return Message::validate($userName, $message);
}

// ページネーション関連の関数
function getAllMessagesWithPagination(int $page = 1, int $perPage = 10): array {
    return Message::getAllMessagesWithPagination($page, $perPage);
}

function getTotalMessageCount(): int {
    return Message::getTotalCount();
}

function getPaginationInfo(int $totalItems, int $currentPage, int $perPage): array {
    $totalPages = ceil($totalItems / $perPage);
    
    return [
        'current_page' => $currentPage,
        'per_page' => $perPage,
        'total_items' => $totalItems,
        'total_pages' => $totalPages,
        'has_prev' => $currentPage > 1,
        'has_next' => $currentPage < $totalPages,
        'prev_page' => $currentPage > 1 ? $currentPage - 1 : null,
        'next_page' => $currentPage < $totalPages ? $currentPage + 1 : null,
        'start_item' => ($currentPage - 1) * $perPage + 1,
        'end_item' => min($currentPage * $perPage, $totalItems)
    ];
}
?>