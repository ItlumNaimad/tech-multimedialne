<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['typ'] != 'pracownik') {
    http_response_code(403);
    exit(json_encode(['error' => 'Unauthorized']));
}

$user_id = $_SESSION['user']['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $zgl_id = $data['zgl_id'] ?? null;
    $status = $data['status'] ?? null;

    if ($zgl_id && $status) {
        $stmt = $pdo->prepare("UPDATE zgloszenia SET status = ?, id_pracownika = IFNULL(id_pracownika, ?) WHERE id = ?");
        $stmt->execute([$status, $user_id, $zgl_id]);
        echo json_encode(['success' => true]);
        exit;
    }
}
