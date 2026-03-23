<?php
/**
 * Plik: database/logout_handler.php
 * Cel: Wylogowanie użytkownika i zniszczenie sesji.
 */
session_start();
session_unset();
session_destroy();

// Powrót do strony głównej / logowania
header("Location: ../index.php");
exit();
?>
