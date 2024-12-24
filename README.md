# Aplikacja Symfony

## Opis
To jest aplikacja oparta na frameworku Symfony. Aplikacja jest gotowa do uruchomienia lokalnie przy pomocy narzędzi Symfony.

## Wymagania systemowe
- PHP 8.1 lub wyższy
- Composer
- Symfony CLI
- Bazodanowy system zarządzania (np. MySQL, PostgreSQL) - konfigurowalny w pliku `.env`

## Instalacja

1. **Sklonuj repozytorium:**
   ```bash
   git clone https://github.com/robert611/web24_zadanie.git
   ``` 

2. **Stwórz bazę danych:**
   ```bash
   php bin/console doctrine:database:create
   ```

3. **Wykonaj migracje:**
   ```bash
   php bin/console doctrine:migrations:migrate
   ```
   
4. **Uruchom aplikację:**
   ```bash
   symfony server:start
   ```
   
5. **Wejdź w zakładkę:**
   ```code
   /api/doc
   ```
