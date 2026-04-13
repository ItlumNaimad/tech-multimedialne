# Sprawozdanie - Zadanie 16: System Zarządzania Treścią (CMS)

**Data rozpoczęcia:** 5 kwietnia 2026

## Opis zadania
Zadanie polega na stworzeniu autorskiego systemu CMS, który pozwala na zarządzanie wieloma portalami firmowymi z jednego poziomu bazy danych. Kluczowe funkcjonalności to:
- Edycja treści za pomocą edytora WYSIWYG (CKEditor).
- Możliwość konfiguracji logo (również jako animowany SVG).
- Implementacja inteligentnego Chatbota reagującego na słowa kluczowe.
- Przechowywanie historii rozmów i logowań.
- Integracja z Google Maps i Google Translate.

## Dziennik prac
### 2026-04-05
- Analiza instrukcji PDF.
- Projektowanie bazy danych: tabele `cms`, `admins`, `chatbot`, `login_history`.
- Przygotowanie struktury plików projektu.
- Utworzenie pliku `database.sql` i `db_connect.php`.
- Konfiguracja środowiska (plik `.env`).
- Implementacja `index.php` z systemem szablonowym.
- Integracja edytora wizualnego CKEditor dla administratora.
- Stworzenie inteligentnego Chatbota z animowanym avatarem SVG.
- Dodanie funkcjonalności przesyłania logo i automatycznego tłumaczenia (Google Translate).
- Implementacja historii rozmów chatbota.

## Wykorzystane technologie
- PHP (PDO)
- MySQL
- Bootstrap 5 (Frontend)
- CKEditor (Edytor wizualny)
- SVG + CSS (Animowane logo)
