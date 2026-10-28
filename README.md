# Laravel Web Application (v1.0)

Base web application built with the Laravel 11 framework.

## Features
- **Settings:** Timezone `Asia/Irkutsk`, Locale `ru`, Debug mode enabled.
- **Blade Templating:** Base layout (`layouts/app.blade.php`), header and footer partials, page templates.
- **Form & Validation:** POST form with server-side validation and error messages.
- **File Storage:** Each submitted record is saved into a uniquely named JSON file (`storage/app/feedback/record_<timestamp>_<uniqid>.json`).
- **Table View:** Displays all saved JSON records in a structured HTML table.

## Running the Project

1. Install dependencies:
   ```bash
   composer install
   ```

2. Configure environment:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. Run automated tests:
   ```bash
   php artisan test
   ```

4. Start development server:
   ```bash
   php artisan serve
   ```
