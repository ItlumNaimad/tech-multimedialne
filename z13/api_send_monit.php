<?php
/**
 * Plik: api_send_monit.php
 * Cel: Asynchroniczne wysyłanie monitu do wykonawcy podzadania.
 */
session_start();
require_once 'database/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['loggedin'])) {
    echo json_encode(['status' => 'error', 'message' => 'Brak autoryzacji.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idpz = $_POST['idpz'] ?? null;
    $idp_to = $_POST['idp_to'] ?? null;
    $message = $_POST['message'] ?? 'PM prosi o aktualizację statusu!';
    $user_id = $_SESSION['user_id'];

    if ($idpz && $idp_to) {
        try {
            // Weryfikacja czy wysyłający jest PM-em zadania
            $stmt_check = $pdo->prepare("SELECT z.idp FROM podzadanie pz 
                                          JOIN zadanie z ON pz.idz = z.idz 
                                          WHERE pz.idpz = :idpz");
            $stmt_check->execute(['idpz' => $idpz]);
            $owner = $stmt_check->fetch();

            if ($owner && $owner['idp'] == $user_id) {
                $stmt_insert = $pdo->prepare("INSERT INTO monit (idp_to, idpz, message) VALUES (:to, :idpz, :msg)");
                $stmt_insert->execute([
                    'to' => $idp_to,
                    'idpz' => $idpz,
                    'msg' => $message
                ]);
                
                echo json_encode(['status' => 'success', 'message' => 'Monit został wysłany.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Brak uprawnień do wysyłania monitu dla tego zadania.']);
            }
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Błąd bazy danych: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Brak wymaganych danych.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Nieprawidłowa metoda.']);
}
?>
