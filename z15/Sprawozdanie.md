# Sprawozdanie z zadania 15 - CRM (System Obsługi Klienta)

## Wstęp
No i udało się dowieźć ten projekt! Tym razem zadanie polegało na stworzeniu uproszczonego systemu CRM. Wybrałem branżę **WHS (Web Hosting Services)**, bo wydawała mi się najciekawsza do oprogramowania (problemy z SSH, domenami itp.). Całość oparta o PHP, PDO i Bootstapa, żeby to jakoś wyglądało na dzisiejsze standardy.

## Co zostało zrobione?
1. **Baza danych**: Zgodnie z podpowiedzią z PDFa, połączyłem tabelki klientów i pracowników w jedną `uzytkownicy`, dodając kolumnę `typ`. To znacznie uprościło logowanie. Dodałem też tabelkę na logi, zgłoszenia i wiadomości.
2. **System logowania i rejestracji**: Działa dla wszystkich typów użytkowników. Przy okazji logowania zbieram dane o IP, przeglądarce i systemie operacyjnym (zgodnie z instrukcją).
3. **Komunikator**: Zamiast prostego "pytanie-odpowiedź", zrobiłem to w formie wątku (czatu). Klient może dopytać o szczegóły, a pracownik odpowiedzieć.
4. **Panel Pracownika**: Pracownicy muszą najpierw wybrać swoją "specjalizację" (zagadnienie), żeby zobaczyć zgłoszenia, na których się znają.
5. **Panel Admina (statystyki)**: To była najciekawsza część. Obliczanie tempa pracy w stosunku do średniej zespołu i przypisywanie ikon (ślimak, żółw, człowiek, puma). Do tego średnie oceny gwiazdkowe od klientów.
6. **Obsługa .env**: Zastosowałem plik `.env` do konfiguracji bazy danych, żeby nie musieć za każdym razem grzebać w kodzie przy wrzucaniu na serwer.

## Wyzwania
Najwięcej czasu zeszło mi na logice wyliczania ikon tempa pracy dla admina. Musiałem wyciągnąć średnią ze wszystkich pracowników i porównać każdego z nich z tą średnią. Fajnie wyszło z tymi emoji! 🐌🐢👤🐆

## Podsumowanie
System CRM jest gotowy do testów przez prowadzącego. Wszystkie loginy i hasła są zgodne z tymi z ramki w PDFie (klient1/pass1, pracownik1/pass1, admin/admin itd.).

---
*Student Informatyki Stosowanej*
