# Sprawozdanie z realizacji projektu: System E-learningowy
**Autor:** Student
**Przedmiot:** Technologie Multimedialne
**Data:** 23 marca 2026

## 1. Cel projektu
Celem zadania było zaprojektowanie i zaimplementowanie autorskiego portalu e-learningowego wspomagającego proces szkolenia pracowników w korporacji. Aplikacja miała wspierać trzy role użytkowników (Administrator, Coach, Pracownik) oraz umożliwiać zarządzanie lekcjami multimedialnymi i weryfikację wiedzy poprzez testy online.

## 2. Architektura systemu i bazy danych
Projekt oparto na architekturze klient-serwer z wykorzystaniem technologii PHP (backend) oraz MySQL (baza danych). Połączenie z bazą zrealizowano przy pomocy interfejsu **PDO**, co zapewnia bezpieczeństwo (Prepared Statements) oraz elastyczność.

Struktura bazy danych obejmuje:
- **coach / pracownik**: tabele przechowujące dane uwierzytelniające.
- **lekcje**: treść dydaktyczna wraz z obsługą plików multimedialnych (grafika, wideo, audio).
- **test / pytania**: struktura pozwalająca na tworzenie testów wielokrotnego wyboru.
- **wyniki / logi_aktywnosci**: mechanizmy monitorowania postępów i audytu działań użytkowników.

## 3. Przebieg prac implementacyjnych
Proces tworzenia aplikacji podzielono na kilka etapów:

### Etap 1: System autoryzacji
Zaimplementowano zaawansowany `login_handler.php`, który dynamicznie rozpoznaje rolę użytkownika i przekierowuje go do odpowiedniego panelu. Zgodnie z wytycznymi, dla celów testowych zastosowano jawne hasła, co ułatwiło debugowanie uprawnień.

### Etap 2: Panel Coacha i edycja treści
W panelu szkoleniowca zintegrowano edytor **CKEditor**, co pozwoliło na tworzenie bogatych wizualnie lekcji (formatowanie tekstu, tabele, linki). Dodano moduł uploadu plików, który automatycznie rozpoznaje format (zdjęcie, dźwięk, film) i odpowiednio renderuje go w widoku kursanta.

### Etap 3: Silnik testowy
Największym wyzwaniem było stworzenie mechanizmu sprawdzania testów wielokrotnego wyboru. Wykorzystano tablice w PHP do przesyłania zaznaczonych checkboxów. Punktacja jest rygorystyczna: 1 punkt przyznawany jest tylko za idealne dopasowanie zaznaczeń do wzorca w bazie danych. Dodatkowo wdrożono stoper w JavaScript, który wymusza zakończenie testu po upływie zadanego czasu.

### Etap 4: Generowanie raportów PDF
Zintegrowano bibliotekę **FPDF**. Skrypt po zakończeniu testu generuje imienny raport z wynikami, kolorując odpowiedzi poprawne na zielono, a błędne na czerwono. Raporty są archiwizowane na serwerze i dostępne dla pracownika w sekcji "Podsumowanie".

## 4. Wykorzystane technologie
- **Backend:** PHP 8.x (PDO)
- **Frontend:** HTML5, CSS3, Bootstrap 5 (Responsive Web Design)
- **Baza danych:** MariaDB / MySQL
- **Biblioteki zewnętrzne:** CKEditor 4, FPDF 1.8x
- **Skrypty:** JavaScript (Timer, integracja z DOM)

## 5. Wnioski i refleksje
1. **Złożoność uprawnień:** Zarządzanie sesjami i rolami wymaga dużej dyscypliny w kodzie, aby uniknąć nieautoryzowanego dostępu do paneli administracyjnych.
2. **Interakcja z mediami:** Obsługa różnych formatów multimedialnych (MP4, MP3) wymaga uwzględnienia parametrów takich jak `autoplay` czy `controls` w celu zapewnienia dobrego UX (User Experience).
3. **Automatyzacja:** Integracja generowania plików PDF bezpośrednio po akcji użytkownika znacząco podnosi profesjonalizm aplikacji i ułatwia archiwizację wyników.
4. **Bezpieczeństwo:** Praca z PDO i bindowanie parametrów to obecnie standard, który skutecznie chroni przed atakami typu SQL Injection, co jest kluczowe w systemach przechowujących wyniki egzaminów.

## 6. Podsumowanie
Aplikacja w pełni realizuje założenia instrukcji laboratoryjnej. Jest funkcjonalna, posiada responsywny interfejs i pozwala na kompletny obieg dokumentacji szkoleniowej – od stworzenia lekcji przez Coacha, aż po otrzymanie certyfikatu PDF przez Pracownika.
