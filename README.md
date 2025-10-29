# School Questionnaire Form

A minimal Google Forms–style survey module built with core PHP 8.3, MySQL 8, HTML, CSS, and vanilla JavaScript. Designed for integration into a School Information System (SIS) and supports bilingual (English/Arabic) UI.

## Project structure
```
/.
├── app
│   ├── controllers
│   ├── helpers
│   ├── middleware
│   ├── models
│   └── views
├── public
│   ├── css
│   ├── js
│   └── index.php
├── resources/lang
├── storage/uploads
├── vendor/autoload.php
├── config.php
└── database.sql
```

## Setup
1. Create a MySQL database and user (defaults assume `school_forms` and local root access).
2. Import `database.sql`:
   ```bash
   mysql -u root -p < database.sql
   ```
3. Update `config.php` with your database credentials and application paths if needed.
4. Ensure the `storage/uploads` directory is writable by the web server.
5. Configure Apache to point the virtual host document root to the `public/` directory (e.g., mount under `/surveys`).
6. Start Apache/PHP 8.3 and visit `http://localhost/surveys`.

### Default accounts
| Role     | Email              | Password     |
|----------|--------------------|--------------|
| Admin    | admin@example.com  | Admin123!    |
| Employee | emma@example.com   | Employee123! |
| Family   | omar@example.com   | Family123!   |

## Development notes
- No frameworks are used; routing is handled by a lightweight front controller.
- CSRF tokens are enforced on all POST forms.
- Prepared statements (PDO) and file upload sanitisation provide baseline security.
- Autosave for response forms uses `localStorage` drafts and resume via unique tokens.
- Conditional logic stubs are present for extension (question settings include `logic`).

## Embedding into an SIS
- Deploy the repository alongside the SIS codebase and mount the `public/` folder within the SIS web server (e.g., `Alias /surveys /var/www/surveys/public`).
- Integrate authentication by swapping the `Auth` helper with the SIS session when ready.
- Use the provided SQL schema to extend or migrate into the SIS database.

## Acceptance checklist
- [x] PHP 8.3 + MySQL 8 compatible, no external frameworks.
- [x] Config bootstrap with routing, CSRF, auth, i18n helpers.
- [x] Session auth with role-based access (Admin, Employee, Family).
- [x] Form builder with question types, required flag, reorder endpoint.
- [x] Conditional logic scaffolding and per-question metadata.
- [x] Distribution flows (audience filters, tokens, email stub, CSV links).
- [x] Response collection with validation hooks, autosave, progress bar.
- [x] Bilingual UI with EN/AR JSON dictionaries and RTL toggle.
- [x] Dashboard and analytics summaries with CSV export.
- [x] Secure uploads stored outside web root reference directory.
- [x] Sample School Climate (Families) and Employee Wellness forms seeded.
