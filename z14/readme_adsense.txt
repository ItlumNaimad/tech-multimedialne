# Google AdSense w systemie E-learningowym (Strony za logowaniem)

Problem:
Robot Google (Mediapartners-Google) nie może zaindeksować treści lekcji, ponieważ są one chronione sesją PHP (wymagają logowania). Może to skutkować brakiem wyświetlania reklam lub odrzuceniem witryny.

Rozwiązanie (Dostęp dla robota):
Aby umożliwić wyświetlanie reklam na stronach chronionych hasłem, należy:
1. Zalogować się do panelu Google AdSense.
2. Przejść do sekcji: Ustawienia -> Dostęp i autoryzacja -> Dostęp dla robotów.
3. Kliknąć "Dodaj dane logowania".
4. Podać:
   - Adres URL strony logowania: (np. http://twoja-domena.pl/z14/pages/logowanie.php)
   - Metoda: POST
   - Nazwy parametrów formularza: (w naszym przypadku 'username' oraz 'password')
   - Dane logowania: (stworzyć dedykowane konto testowe dla Google w bazie danych)
5. Dzięki temu robot Google automatycznie zaloguje się do systemu, pobierze treść lekcji i dopasuje do niej kontekstowe reklamy.

Uwagi techniczne:
- Należy upewnić się, że plik robots.txt nie blokuje dostępu do folderu z14/.
- Nie należy ukrywać reklam przed robotem – Google musi widzieć to samo, co widzi użytkownik.
