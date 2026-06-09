<?php
/**
 * z18/auth.php
 */
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getUserRole() {
    return $_SESSION['user_role'] ?? 'guest';
}

function isAdmin() {
    return getUserRole() === 'admin';
}

function isModerator() {
    return getUserRole() === 'moderator' || isAdmin();
}

function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

function getCurrentUsername() {
    return $_SESSION['username'] ?? null;
}

function isBanned() {
    return isset($_SESSION['is_banned']) && $_SESSION['is_banned'] == 1;
}

function updateSessionUser($pdo, $userId) {
    if (!$userId) return;
    $stmt = $pdo->prepare("SELECT role, is_banned FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    if ($user) {
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['is_banned'] = $user['is_banned'];
    }
}
?>
