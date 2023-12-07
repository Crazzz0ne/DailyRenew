#!/bin/bash

echo "Migration Process Starting..."
php artisan migrate:fresh --seed -vv
echo "Running Remote Seeders..."
php artisan generate:PowerCompany
php artisan generate:Inverters
php artisan generate:solarModules
php artisan generate:fp
echo "Seeding Process Finished"
