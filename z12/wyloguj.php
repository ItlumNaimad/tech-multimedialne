<?php
/**
 * Plik: wyloguj.php
 * Cel: Zakończenie sesji użytkownika.
 * Funkcjonalność: Usuwa dane sesyjne i wylogowuje z aplikacji.
 * Wykorzystane biblioteki: Brak.
 * Sposób działania: Wywołuje session_unset() oraz session_destroy(), a następnie przekierowuje do strony głównej (index.php), która ze względu na brak sesji wyświetli formularz logowania.
 */
// Zawsze zaczynamy od session_start(), aby móc manipulować sesją
session_start();

// Wyczyszczenie wszystkich zmiennych sesyjnych
session_unset();

// Usunięcie (zniszczenie) całej sesji
session_destroy();

// Przekierowanie z powrotem do strony głównej (router przekieruje do logowania)
header('Location: index.php?page=home');
exit();
?>