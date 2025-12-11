<?php
session_start();
require_once '../database/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    // Checkbox przesyła 'on' jeśli zaznaczony, w przeciwnym razie nic. Zamieniamy na 1 lub 0.
    $public = isset($_POST['public']) ? 1 : 0;
    $idu = $_SESSION['user_id'];

    try {
        $sql = "INSERT INTO playlistname (idu, name, public) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idu, $name, $public]);

        header('Location: ../index.php?page=my_playlists&msg=created');
    } catch (PDOException $e) {
        die("Błąd tworzenia playlisty: " . $e->getMessage());
    }
}
?>