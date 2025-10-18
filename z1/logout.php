<?php
// Zawsze zaczynamy od session_start()
session_start();

// Wyczyszczenie wszystkich zmiennych sesyjnych
session_unset();

// Usunięcie (zniszczenie) całej sesji
session_destroy();

// Przekierowanie z powrotem do formularza logowania
header('Location: index3.php');
exit();
?>