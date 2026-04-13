<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user'])) {
    http_response_code(403);
    exit(json_encode(['error' => 'Unauthorized']));
}

$user_id = $_SESSION['user']['id'];
$typ = $_SESSION['user']['typ'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $zgl_id = $_POST['zgl_id'] ?? null;
    $tresc = $_POST['tresc'] ?? '';

    if ($zgl_id && $tresc) {
        if ($typ === 'pracownik') {
            $stmt = $pdo->prepare("UPDATE zgloszenia SET id_pracownika = ? WHERE id = ? AND id_pracownika IS NULL");
            $stmt->execute([$user_id, $zgl_id]);
        }

        $stmt = $pdo->prepare("INSERT INTO wiadomosci (id_zgloszenia, id_autora, tresc) VALUES (?, ?, ?)");
        $stmt->execute([$zgl_id, $user_id, $tresc]);
        echo json_encode(['success' => true]);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $zgl_id = $_GET['zgl_id'] ?? null;
    $last_id = $_GET['last_id'] ?? 0;

    if ($zgl_id) {
        $stmt = $pdo->prepare("SELECT w.*, u.nazwisko, u.typ FROM wiadomosci w JOIN uzytkownicy u ON w.id_autora = u.id WHERE w.id_zgloszenia = ? AND w.id > ? ORDER BY w.data_godzina ASC");
        $stmt->execute([$zgl_id, $last_id]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $pdo->prepare("SELECT status FROM zgloszenia WHERE id = ?");
        $stmt->execute([$zgl_id]);
        $status = $stmt->fetchColumn();

        echo json_encode(['messages' => $messages, 'status' => $status]);
        exit;
    }
}
