<?php
// Zawsze zaczynamy od session_start(), aby móc manipulować sesją
session_start();

// Wyczyszczenie wszystkich zmiennych sesyjnych
session_unset();

// Usunięcie (zniszczenie) całej sesji
session_destroy();

// Przekierowanie z powrotem do strony głównej z2
header('Location: index.php?page=home');
exit();
?>