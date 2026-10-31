<?php

use App\Http\Controllers\Api\ActivityLogController;
use App\Http\Controllers\Api\AnnouncementController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DonationCampaignController;
use App\Http\Controllers\Api\DonationController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\FacilityReportController;
use App\Http\Controllers\Api\FinancialSummaryController;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\MaintenanceTaskController;
use App\Http\Controllers\Api\QurbanController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SocialController;
use App\Http\Controllers\Api\TransactionCategoryController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\TransparencyController;
use App\Http\Controllers\Api\ZakatController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes (Tidak memerlukan login)
|--------------------------------------------------------------------------
*/
// Autentikasi
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Informasi Masjid & Pengumuman
Route::get('/announcements', [AnnouncementController::class, 'index']);
Route::get('/announcements/{id}', [AnnouncementController::class, 'show']);

// Kegiatan
Route::get('/events', [EventController::class, 'index']);
Route::get('/events/{id}', [EventController::class, 'show']);

// Ringkasan Keuangan Publik (hanya yang sudah diverifikasi dan dipublikasikan)
Route::get('/finance/summary', [FinancialSummaryController::class, 'publishedSummary']);

// Program Donasi
Route::get('/donation-campaigns', [DonationCampaignController::class, 'index']);
Route::get('/donation-campaigns/{id}', [DonationCampaignController::class, 'show']);

// Midtrans Webhook (Dipanggil oleh server Midtrans)
Route::post('/donations/webhook', [DonationController::class, 'webhook']);

// Transparansi / Jejak Amanah (Program yang dipublikasikan beserta realisasinya)
Route::get('/transparency', [TransparencyController::class, 'publicPrograms']);
Route::get('/transparency/{id}', [TransparencyController::class, 'show']);

// Inventaris (Publik melihat kondisi fasilitas)
Route::get('/inventories', [InventoryController::class, 'index']);

// Modul Lanjutan Publik
Route::get('/zakat/summary', [ZakatController::class, 'publicSummary']);
Route::get('/qurban', [QurbanController::class, 'publicProgram']);
Route::get('/qurban/{id}', [QurbanController::class, 'show']);
Route::get('/social-programs', [SocialController::class, 'publicPrograms']);

/*
|--------------------------------------------------------------------------
| Authenticated User Routes (Jamaah, Admin, Marbot)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    // Auth & Profil
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);

    // Kegiatan Jamaah
    Route::post('/events/{id}/register', [EventController::class, 'register']);
    Route::post('/events/{id}/cancel', [EventController::class, 'cancelRegistration']);
    Route::get('/my-events', [EventController::class, 'myRegistrations']);

    // Donasi Online (Wajib Login sesuai konfirmasi)
    Route::post('/donations/initiate', [DonationController::class, 'initiate']);
    Route::get('/donations/my-donations', [DonationController::class, 'myDonations']);
    Route::get('/donations/status/{orderId}', [DonationController::class, 'showStatus']);

    // Bantuan Sosial Permohonan
    Route::post('/social-programs/{id}/apply', [SocialController::class, 'apply']);
    Route::get('/my-social-applications', [SocialController::class, 'myApplications']);

    /*
    |--------------------------------------------------------------------------
    | Marbot Operational Routes (Marbot & Admin)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:marbot,admin_masjid,super_admin')->prefix('marbot')->group(function () {
        // BR-12: Marbot hanya melihat tugas miliknya
        Route::get('/tasks', [MaintenanceTaskController::class, 'myTasks']);
        Route::put('/tasks/{id}/status', [MaintenanceTaskController::class, 'updateStatus']);

        // Pelaporan kerusakan / fasilitas
        Route::post('/facility-reports', [FacilityReportController::class, 'store']);
        Route::get('/facility-reports', [FacilityReportController::class, 'myReports']);
    });

    /*
    |--------------------------------------------------------------------------
    | Admin Masjid Routes (Admin Masjid & Super Admin)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin_masjid,super_admin')->prefix('admin')->group(function () {
        // BR-02: Admin Masjid membuat akun Marbot
        Route::post('/marbot', [AuthController::class, 'createMarbot']);

        // Pengumuman
        Route::post('/announcements', [AnnouncementController::class, 'store']);
        Route::put('/announcements/{id}', [AnnouncementController::class, 'update']);
        Route::delete('/announcements/{id}', [AnnouncementController::class, 'destroy']);

        // Kegiatan
        Route::post('/events', [EventController::class, 'store']);
        Route::put('/events/{id}', [EventController::class, 'update']);
        Route::delete('/events/{id}', [EventController::class, 'destroy']);
        Route::get('/events/{id}/participants', [EventController::class, 'participants']);

        // Kategori Transaksi
        Route::apiResource('transaction-categories', TransactionCategoryController::class);

        // Transaksi Keuangan
        Route::get('/transactions', [TransactionController::class, 'index']);
        Route::post('/transactions', [TransactionController::class, 'store']);
        Route::get('/transactions/{id}', [TransactionController::class, 'show']);
        Route::put('/transactions/{id}', [TransactionController::class, 'update']);
        Route::post('/transactions/{id}/verify', [TransactionController::class, 'verify']);
        Route::post('/transactions/{id}/cancel', [TransactionController::class, 'cancel']);

        // Ringkasan Keuangan
        Route::get('/financial-summaries', [FinancialSummaryController::class, 'index']);
        Route::post('/financial-summaries', [FinancialSummaryController::class, 'store']);
        Route::get('/financial-summaries/{id}', [FinancialSummaryController::class, 'show']);
        Route::post('/financial-summaries/{id}/publish', [FinancialSummaryController::class, 'publish']);
        Route::post('/financial-summaries/{id}/unpublish', [FinancialSummaryController::class, 'unpublish']);

        // Laporan Keuangan (PDF & Excel terpisah, memuat identitas penyusun)
        Route::get('/reports/summary', [ReportController::class, 'summary']);
        Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf']);
        Route::get('/reports/export-excel', [ReportController::class, 'exportExcel']);

        // Kampanye Donasi
        Route::post('/donation-campaigns', [DonationCampaignController::class, 'store']);
        Route::put('/donation-campaigns/{id}', [DonationCampaignController::class, 'update']);
        Route::delete('/donation-campaigns/{id}', [DonationCampaignController::class, 'destroy']);

        // Jejak Amanah (Alokasi & Realisasi)
        Route::post('/program-allocations', [TransparencyController::class, 'storeAllocation']);
        Route::post('/program-allocations/{id}/realizations', [TransparencyController::class, 'storeRealization']);

        // Inventaris
        Route::post('/inventories', [InventoryController::class, 'store']);
        Route::put('/inventories/{id}', [InventoryController::class, 'update']);
        Route::delete('/inventories/{id}', [InventoryController::class, 'destroy']);

        // Pemeliharaan Fasilitas
        Route::get('/tasks', [MaintenanceTaskController::class, 'allTasks']);
        Route::post('/tasks', [MaintenanceTaskController::class, 'store']);
        Route::delete('/tasks/{id}', [MaintenanceTaskController::class, 'destroy']);
        Route::get('/facility-reports', [FacilityReportController::class, 'allReports']);
        Route::post('/facility-reports/{id}/acknowledge', [FacilityReportController::class, 'acknowledge']);
        Route::post('/facility-reports/{id}/resolve', [FacilityReportController::class, 'resolve']);

        // Zakat
        Route::get('/zakat/records', [ZakatController::class, 'records']);
        Route::post('/zakat/records', [ZakatController::class, 'storeRecord']);
        Route::get('/zakat/distributions', [ZakatController::class, 'distributions']);
        Route::post('/zakat/distributions', [ZakatController::class, 'storeDistribution']);

        // Qurban
        Route::post('/qurban/programs', [QurbanController::class, 'storeProgram']);
        Route::get('/qurban/programs/{id}/participants', [QurbanController::class, 'participants']);
        Route::post('/qurban/programs/{id}/participants', [QurbanController::class, 'storeParticipant']);
        Route::get('/qurban/programs/{id}/distributions', [QurbanController::class, 'distributions']);
        Route::post('/qurban/programs/{id}/distributions', [QurbanController::class, 'storeDistribution']);

        // Bantuan Sosial (Data pribadi dibatasi kepada Admin)
        Route::get('/social-programs/{id}/applications', [SocialController::class, 'applications']);
        Route::get('/social-applications', [SocialController::class, 'allApplications']);
        Route::post('/social-applications/{id}/review', [SocialController::class, 'reviewApplication']);
        Route::post('/social-programs/{id}/distributions', [SocialController::class, 'storeDistribution']);

        // Audit & Log Aktivitas
        Route::get('/activity-logs', [ActivityLogController::class, 'index']);
    });

    /*
    |--------------------------------------------------------------------------
    | Super Admin Routes Only
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:super_admin')->prefix('super-admin')->group(function () {
        // BR-02: Hanya Super Admin yang berwenang menambah Admin Masjid
        Route::post('/admin-masjid', [AuthController::class, 'createAdmin']);
    });
});
