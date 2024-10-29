# TODO app

---
## en

##  Basic info:

- 🚧 Vue 3 with Inertia.
- 🎨 Tailwind CSS.
- 🌐 Laravel Breeze: Authentication: Login, registration, and password reset.
- 🔒 Public & Private Sharing: Share your Todos publicly with a link or privately with selected users.
- 🔔 Notifications: Get notified when a Todo is completed, deleted, or shared.
- ⚡ Quick Create: Quickly create Todos with a streamlined form.
- 🔍 Easy Filtering: Filter Todos by name, description, category, and shared status using an intuitive input.

## Tech Stack
- Laravel 11, Vue 3
- PHP 8.2, Node 20.10
- sqlite
- Tailwind Css

## Development
```bash
cp .env.example .env # - copy environment file
composer install # - install BE dependencies
npm install # - install FE dependencies
php artisan key:generate # - generate application key
```
Change database section in .env with your db connection data. (default sqlite)

```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```
```bash
php artisan migrate # - creating tables in the database
php artisan serve # - start development server on http://localhost:8000
npm run dev # - start development server on http://localhost:5173
```

## Other:
### Documentation:
[Laravel 11 documentation](https://laravel.com/docs/11.x)

---

## sk

##  Základné info:
- 🚧 Vue 3 s Inertia.
- 🎨 Tailwind CSS.
- 🌐 Laravel s Breeze: Autentifikácia: Funkcia prihlásenia, registrácie a obnovenia hesla.
- 🔒 Verejné a súkromné zdieľanie: Zdieľajte svoje úlohy verejne pomocou odkazu alebo súkromne s vybranými používateľmi.
- 🔔 Upozornenia: Získajte upozornenia, keď je úloha dokončená, vymazaná alebo zdieľaná.
- ⚡ Rýchle vytváranie: Rýchlo vytvorte úlohy pomocou zjednodušeného formulára.
- 🔍 Jednoduché filtrovanie: Filtrujte úlohy podľa názvu, popisu, kategórie a zdieľaného stavu pomocou intuitívneho vstupu.

## Použité technológie
- Laravel 11, Vue 3
- PHP 8.2, Node 20.10
- sqlite
- Tailwind Css
- PHP STAN, Pine (PHP CS)

## Vývoj
```bash
cp .env.example .env # - prekopírovanie súboru s nastaveniami projektu
composer install # - inštalácia BE balíkov
npm install # - inštalácia FE balíkov
php artisan key:generate # - vygenerovanie aplikačného kľúča
```
 
```bash
# CHECK BE
./vendor/bin/pint
./vendor/bin/phpstan analyse

# CHECK FE
npx eslint . 

# FIX
npx eslint . --fix
```
Upravte v .env informácie potrebné na pripojenie do databázy

```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```
```bash
php artisan migrate # - vytvorenie tabuliek v databáze
php artisan serve # - spustenie servera na http://localhost:8000
npm run dev # - spustenie FE na http://localhost:5173
```

## Ostatné:
### Dokumentácia:
[Laravel 11 dokumentácia](https://laravel.com/docs/11.x)
