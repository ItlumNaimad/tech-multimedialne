# Kontekst projektowy

### Pracujesz na lokalnym repozytorium projektu wystawionym na stronie: <https://damskopbs.s1.zetohosting.pl/>

### Co to jest

Projekt studencki w którym realizowane są kolejne instrukcje nadsyłane przez prowadzącego ze studiów informatyki stosowanej.
Technologie wykorzystywane:

- System operacyjny folderu roboczego lokalnego repozytium: Windows 10 IoT LTSC 21H2
- Repozytorium lokalne połączone z repozytorium zdalnym: <https://github.com/ItlumNaimad/tech-multimedialne>
- Repozytorium: GitHub
- Hosting: zetohosting
- Język programowania: PHP
- Frontend: Bootstrap
- MySQL PHPMyAdmin

### System tworzenia każdego zadania

- Każde zadanie w oddzielnym folderze (zadanie 4 w folderze z4, zadanie 13 w folderze z13 itp...)
- Czasami zadanie jest podzielone na podzadania np. z12a i z12b
- Każde zadanie ma mieć oddzieloną bazę danych. Zapisuj potrzebne zapytania SQL w pliku database.sql, który będzie zaimportowany do hostingowego PHPMyAdmin
- W folderze do każdego zadania będzie plik .pdf z instrukcją i wymaganiami które trzeba spełnić by wykonać zadanie. Przykłady nazw: "z16 CMS 2023-05-07.pdf", "z12a SCADA 2025-03-17.pdf",  "z13 ToDo 2025-04-06.pdf"

### Twoje zadania

- Wcielić się w rolę Specjalisty WebDevelopera Senior PHP Programisty
- Zaprojektowanie bazy danych i zapytań SQL do jej utworzenia, aby zrealizować zadania z instrukcji w dokumencie pdf.
- Zaprojektowanie i zaprogramowanie wszelkich widoków i logiki backendowych
- Zapisywanie postępów w pracy w pliku Sprawozdanie.md z kontekstem pracy i zapisywać go dla każdego zadania (gdy robimy zadanie 15 to w folderze z15 itp.)
- Sprawozdanie powinno mieć ton mniej profejsonalny, by było widać, że jest pisane jako uczący się student.
- W sprawozdaniu nie zapisywać informacji o autorze projektu (od tego będzie oddzielna tabelka, której w tym projekcie nie ma)

### Standard łączenia z bazą danych (np. db_connect.php)

Staraj się unikać sztywno wpisanego usera `root` czy lokalnego hosta `localhost`, aby można było płynnie testować aplikację na danym hostingu. Stosuj poniższy szablon z podpięciem do plików środowiskowych:

```php
$host = $_ENV['DB_HOST'] ?? '127.0.0.1';
$db   = $_ENV['DB_NAME'] ?? 'damskopb_z16'; // zmodyfikuj przyrostek pod aktualne zadanie, jeśli to potrzebne
$user = $_ENV['DB_USER'] ?? 'damskopb_z16'; // zmodyfikuj przyrostek pod aktualne zadanie, jeśli to potrzebne
$pass = $_ENV['DB_PASS'] ?? '';
$charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';
```
