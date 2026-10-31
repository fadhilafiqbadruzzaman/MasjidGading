#!/bin/bash
cd /home/fadhilbzm/perkuliahan/MasjidGading/backend
php artisan make:filament-resource User --generate --no-interaction
php artisan make:filament-resource Event --generate --no-interaction
php artisan make:filament-resource TransactionCategory --generate --no-interaction
php artisan make:filament-resource Transaction --generate --no-interaction
php artisan make:filament-resource FinancialSummary --generate --no-interaction
php artisan make:filament-resource DonationCampaign --generate --no-interaction
php artisan make:filament-resource ProgramAllocation --generate --no-interaction
php artisan make:filament-resource Inventory --generate --no-interaction
php artisan make:filament-resource MaintenanceTask --generate --no-interaction
php artisan make:filament-resource FacilityReport --generate --no-interaction
php artisan make:filament-resource ZakatRecord --generate --no-interaction
php artisan make:filament-resource QurbanProgram --generate --no-interaction
php artisan make:filament-resource SocialProgram --generate --no-interaction
php artisan make:filament-resource ActivityLog --generate --no-interaction
echo "ALL FILAMENT RESOURCES GENERATED"
