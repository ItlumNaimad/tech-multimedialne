<?php
/**
 * Plik: database/login_handler.php
 * Cel: Logika autoryzacji dla ról: admin, coach, pracownik.
 */
session_start();
require_once 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = $_POST['username'] ?? '';
    $password_form = $_POST['password'] ?? '';

    try {
        $user = null;
        $role = '';
        $user_id = 0;

        // 1. Sprawdź tabelę coach (w tym 'admin')
        $stmt = $pdo->prepare("SELECT idc as id, login, haslo FROM coach WHERE login = :login");
        $stmt->execute(['login' => $login]);
        $res = $stmt->fetch();

        if ($res && $res['haslo'] === $password_form) {
            $user = $res;
            $user_id = $res['id'];
            $role = ($login === 'admin') ? 'admin' : 'coach';
        } else {
            // 2. Sprawdź tabelę pracownik
            $stmt = $pdo->prepare("SELECT idp as id, login, haslo FROM pracownik WHERE login = :login");
            $stmt->execute(['login' => $login]);
            $res = $stmt->fetch();

            if ($res && $res['haslo'] === $password_form) {
                $user = $res;
                $user_id = $res['id'];
                $role = 'pracownik';
            }
        }

        if ($user) {
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $login;
            $_SESSION['role'] = $role;

            // Logowanie aktywności
            $stmt_log = $pdo->prepare("INSERT INTO logi_aktywnosci (rola, id_uzytkownika, akcja) VALUES (:role, :id, 'Logowanie')");
            $stmt_log->execute(['role' => $role, 'id' => $user_id]);

            if ($role === 'admin') header("Location: ../pages/panel_admin.php");
            elseif ($role === 'coach') header("Location: ../pages/panel_coach.php");
            else header("Location: ../index.php");
            exit();
        } else {
            die("Błędny login lub hasło.");
        }
    } catch (PDOException $e) {
        die("Błąd systemu: " . $e->getMessage());
    }
}
?>
