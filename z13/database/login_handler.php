<?php
/**
 * Plik: database/login_handler.php
 * Cel: Logika autoryzacji użytkownika z historią logowań i ochroną brute-force.
 */
session_start();
require_once 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = $_POST['username'] ?? '';
    $password_form = $_POST['password'] ?? '';

    try {
        // 1. Sprawdź czy użytkownik istnieje
        $stmt = $pdo->prepare("SELECT idp, password FROM pracownik WHERE login = :login");
        $stmt->execute(['login' => $login]);
        $user = $stmt->fetch();

        if ($user) {
            $idp = $user['idp'];

            // 2. Sprawdź blokadę (state > 3 w ciągu ostatniej minuty)
            $stmt_lock = $pdo->prepare("SELECT MAX(state) FROM logowanie 
                                        WHERE idp = :idp AND datetime > (NOW() - INTERVAL 1 MINUTE)");
            $stmt_lock->execute(['idp' => $idp]);
            $last_state = (int)$stmt_lock->fetchColumn();

            if ($last_state > 3) {
                die("Zbyt wiele nieudanych prób. Konto zablokowane na 1 minutę.");
            }

            // 3. Weryfikacja hasła (zwykły tekst)
            if ($password_form === $user['password']) {
                // SUKCES
                $stmt_log = $pdo->prepare("INSERT INTO logowanie (idp, datetime, state) VALUES (:idp, NOW(), 0)");
                $stmt_log->execute(['idp' => $idp]);

                $_SESSION['loggedin'] = true;
                $_SESSION['user_id'] = $idp;
                $_SESSION['username'] = $login;

                header("Location: ../index.php");
                exit();
            } else {
                // PORAŻKA (istniejący user)
                $new_state = ($last_state > 0) ? $last_state + 1 : 1;
                $stmt_log = $pdo->prepare("INSERT INTO logowanie (idp, datetime, state) VALUES (:idp, NOW(), :state)");
                $stmt_log->execute(['idp' => $idp, 'state' => $new_state]);

                die("Błędne hasło.");
            }
        } else {
            // PORAŻKA (użytkownik spoza bazy)
            $stmt_log = $pdo->prepare("INSERT INTO logowanie (idp, datetime, state) VALUES (0, NOW(), -1)");
            $stmt_log->execute();

            die("Błędny login lub hasło.");
        }

    } catch (PDOException $e) {
        die("Błąd systemu: " . $e->getMessage());
    }
}
?>
