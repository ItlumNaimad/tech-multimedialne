# Sprawozdanie - Zadanie 16: System Zarządzania Treścią (CMS)

**Data rozpoczęcia:** 5 kwietnia 2026

## Opis zadania
Wykonałem ćwiczenie 16 do wykonanai aplikacji z CMS o firmie, gdzie można edytować opisy z poziomu administratora logując się bezpośrednio na stronie. Najważniejsze funkcjonalności to:
- Edycja treści za pomocą edytora WYSIWYG (CKEditor).
- Możliwość konfiguracji logo (również jako animowany SVG).
- Przechowywanie historii rozmów i logowań.
- Integracja z Google Maps i Google Translate.

Do aplikacji zaaplikowałem Czata AI z API Geminiego z Google Studio AI. Pozyskanie klucza API dzieki Google Studio AI jest wyjątkowo proste. Czat wykorzystuje zaktualizowane dane firmy prosto z bazy CMS, by w naturalny sposób pomagać klientom na stronie.

## Dziennik prac
### 2026-04-05
- Analiza instrukcji PDF.
- Projektowanie bazy danych: tabele `cms`, `admins`, `chatbot`, `login_history`.
- Przygotowanie struktury plików projektu.
- Utworzenie pliku `database.sql` i `db_connect.php`.
- Konfiguracja środowiska (plik `.env`).
- Implementacja `index.php` z systemem szablonowym.
- Integracja edytora wizualnego CKEditor dla administratora.
- Dodanie funkcjonalności przesyłania logo i automatycznego tłumaczenia (Google Translate).
- Implementacja historii rozmów chatbota.

### 2026-04-13
- Podmiana prostej logiki chatbota na inteligentnego asystenta współpracującego z instrukcjami systemowymi wysyłanymi do modelu Google Gemini.
- Ustawienie adresu z Politechniką w database.sql.

## Wykorzystane technologie
- PHP (PDO)
- MySQL
- Bootstrap 5 (Frontend)
- CKEditor (Edytor wizualny)
- SVG + CSS (Animowane logo)
- API Google Gemini
