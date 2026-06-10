<?php
/**
 * z19/auth.php
 * Funkcje autoryzacyjne dla portalu firmowego.
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
    // W z19 tabela users ma tylko role 'user' i 'admin'
    return isAdmin();
}

function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

function getCurrentUsername() {
    return $_SESSION['username'] ?? null;
}
?>
