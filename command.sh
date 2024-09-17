
php artisan passport:install
php artisan passport:client --personal
php artisan queue:table
php artisan migrate
php artisan notifications:table
php artisan queue:failed-table
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
