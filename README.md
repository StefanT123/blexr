# Test application for Blexr

### Framework:
- [Laravel](https://github.com/laravel/laravel)

### Dependencies:
- [Laravel passport](https://github.com/laravel/passport)

This application provides only the REST API for the [frontend application](https://github.com/StefanT123/blexr-frontend).

Custom code can be found in these files and folders:
- `app/Http/Controllers`
- `app/Http/Middleware/Admin.php`
- `app/Http/Requests`
- `app/Http/Resources`
- `app/Listeners/`
- `app/Mail`
- `app/Models`
- `app/Notifications`
- `app/Policies`
- `app/Utilities`
- `database/factories`
- `database/migrations/2020_10_22_102355_create_default_tables.php`
- `database/migrations/2020_10_22_102401_create_foreign_keys.php`
- `resources/views/emails/login-details.blade.php`
- `routes/api.php`
- `tests/Feature`

Steps that need to be done in order to use this application:
- `composer require laravel/passport`
- set the environment variables in the `.env` file as shown in the `.env.example`
  - APP_URL={url_for_this_app}
  - APP_FRONTEND_URL={url_of_the_frontend_app}
  - DB_CONNECTION=
  - DB_HOST=
  - DB_PORT=
  - DB_DATABASE=
  - DB_USERNAME=
  - DB_PASSWORD=
  - MAIL_MAILER={log_or_mailer_that_you_will_use}
- `php artisan migrate --seed`
- `php artisan passport:install` this command will generate password client and secret, copy and paste them in the `.env` file
  - PASSWORD_CLIENT_ID=
  - PASSWORD_CLIENT_SECRET=
- run the tests - `vendor/bin/phpunit` **NOTE** tests are using in memory database, so they don't pollute our database
- admin credentials:
  - email: `admin@admin.com`
  - password: `password`
