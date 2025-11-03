<?php
// Plik obsługujący usuwanie hosta
session_start();
require_once '../src/database.php';

// Sprawdzamy sesję i czy ID zostało podane
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && isset($_GET['id'])) {

    $id_to_delete = $_GET['id'];
    $user_id = $_SESSION['user_id'];
    $username = $_SESSION['username'];

    try {
        if ($username === 'admin') {
            // Admin może usuwać wszystko
            $sql = "DELETE FROM domeny WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['id' => $id_to_delete]);
        } else {
            // Zwykły user usuwa tylko swoje
            $sql = "DELETE FROM domeny WHERE id = :id AND user_id = :user_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['id' => $id_to_delete, 'user_id' => $user_id]);
        }

        // Wracamy do strony monitora
        header("Location: index.php?page=home");
        exit();

    } catch (PDOException $e) {
        die("Błąd bazy danych: " . $e->getMessage());
    }
} else {
    die("Nieautoryzowany dostęp.");
}
?>