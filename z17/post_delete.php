<?php
require_once 'db_connect.php';
require_once 'auth.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isLoggedIn()) {
    $post_id = $_POST['post_id'] ?? null;
    $thread_id = $_POST['thread_id'] ?? null;
    
    if ($post_id) {
        $stmt = $pdo->prepare("SELECT created_by FROM posts WHERE id = ?");
        $stmt->execute([$post_id]);
        $post = $stmt->fetch();
        
        if ($post && ($post['created_by'] == getCurrentUserId() || isAdmin())) {
            $del = $pdo->prepare("DELETE FROM posts WHERE id = ?");
            $del->execute([$post_id]);
        }
    }
    
    if ($thread_id) {
        header("Location: thread.php?id=" . $thread_id);
    } else {
        header("Location: index.php");
    }
    exit;
}
header("Location: index.php");
exit;
?>
