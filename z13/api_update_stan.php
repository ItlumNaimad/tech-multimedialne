<?php
/**
 * Plik: api_update_stan.php
 * Cel: Asynchroniczna aktualizacja stanu realizacji podzadania.
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
    $stan = $_POST['stan'] ?? null;
    $user_id = $_SESSION['user_id'];

    if ($idpz !== null && $stan !== null) {
        try {
            // Weryfikacja czy użytkownik jest PM-em zadania, do którego należy to podzadanie
            $stmt_check = $pdo->prepare("SELECT z.idp FROM podzadanie pz 
                                          JOIN zadanie z ON pz.idz = z.idz 
                                          WHERE pz.idpz = :idpz");
            $stmt_check->execute(['idpz' => $idpz]);
            $owner = $stmt_check->fetch();

            if ($owner && $owner['idp'] == $user_id) {
                // Aktualizacja
                $stmt_update = $pdo->prepare("UPDATE podzadanie SET stan = :stan WHERE idpz = :idpz");
                $stmt_update->execute(['stan' => $stan, 'idpz' => $idpz]);
                
                echo json_encode(['status' => 'success', 'message' => 'Zaktualizowano stan.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Brak uprawnień do edycji tego podzadania.']);
            }
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Błąd bazy danych: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Brak wymaganych parametrów.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Nieprawidłowa metoda żądania.']);
}
?>
