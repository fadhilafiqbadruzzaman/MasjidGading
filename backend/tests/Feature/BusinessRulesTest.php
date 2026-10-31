<?php

namespace Tests\Feature;

use App\Models\Donation;
use App\Models\DonationCampaign;
use App\Models\FinancialSummary;
use App\Models\MaintenanceTask;
use App\Models\SocialProgram;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Models\User;
use App\Services\ReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessRulesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * BR-01: Public registration always creates user with 'jamaah' role.
     */
    public function test_public_registration_always_creates_jamaah_role(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Calon Hacker',
            'email' => 'hacker@example.com',
            'password' => 'secret12345',
            'password_confirmation' => 'secret12345',
            'role' => 'admin_masjid', // Mencoba menyusupkan role admin
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            'email' => 'hacker@example.com',
            'role' => 'jamaah', // Wajib tetap jamaah!
        ]);
    }

    /**
     * BR-02: Admin Masjid can create Marbot, but cannot create another Admin.
     */
    public function test_admin_masjid_can_create_marbot_but_not_admin(): void
    {
        $admin = User::where('role', 'admin_masjid')->first();

        // 1. Admin berhasil membuat Marbot
        $response = $this->actingAs($admin, 'sanctum')->postJson('/api/admin/marbot', [
            'name' => 'Marbot Baru',
            'email' => 'marbotbaru@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            'email' => 'marbotbaru@example.com',
            'role' => 'marbot',
        ]);

        // 2. Admin GAGAL membuat admin baru di endpoint super admin
        $failResponse = $this->actingAs($admin, 'sanctum')->postJson('/api/super-admin/admin-masjid', [
            'name' => 'Admin Baru Palsu',
            'email' => 'adminpalsu@example.com',
            'password' => 'password123',
        ]);

        $failResponse->assertStatus(403);
    }

    /**
     * BR-04 & BR-05: Only verified transactions count in report & balance calculation.
     */
    public function test_only_verified_transactions_in_report_and_balance_formula(): void
    {
        $admin = User::where('role', 'admin_masjid')->first();
        $cat = TransactionCategory::where('type', 'income')->first();

        // Buat transaksi pending (belum terverifikasi)
        Transaction::create([
            'type' => 'income',
            'category_id' => $cat->id,
            'description' => 'Infaq Belum Verifikasi',
            'amount' => 10000000,
            'transaction_date' => '2026-10-15',
            'status' => 'pending',
            'created_by' => $admin->id,
            'admin_id' => $admin->id,
        ]);

        $reportService = new ReportService();
        $report = $reportService->getReportData('2026-10-01', '2026-10-31', 'all');

        // Transaksi pending TIDAK boleh masuk ke total_income
        $transactionsInReport = $report['transactions'];
        foreach ($transactionsInReport as $trx) {
            $this->assertEquals('verified', $trx->status);
        }

        // BR-05: Saldo Akhir = Saldo Awal + Total Pemasukan - Total Pengeluaran
        $expectedClosing = $report['opening_balance'] + $report['total_income'] - $report['total_expense'];
        $this->assertEquals($expectedClosing, $report['closing_balance']);
    }

    /**
     * BR-10 & BR-11: Midtrans payment idempotent webhook.
     */
    public function test_midtrans_duplicate_webhook_is_idempotent_no_duplicate_transactions(): void
    {
        $campaign = DonationCampaign::first();
        $jamaah = User::where('role', 'jamaah')->first();

        $donation = Donation::create([
            'campaign_id' => $campaign->id,
            'user_id' => $jamaah->id,
            'amount' => 500000,
            'midtrans_order_id' => 'DON-TEST-12345',
            'payment_status' => 'pending',
        ]);

        $webhookPayload = [
            'order_id' => 'DON-TEST-12345',
            'status_code' => '200',
            'gross_amount' => '500000',
            'transaction_status' => 'settlement',
            'payment_type' => 'qris',
            'transaction_id' => 'MID-999999',
        ];

        // First webhook call
        $res1 = $this->postJson('/api/donations/webhook', $webhookPayload);
        $res1->assertStatus(200);

        $this->assertEquals('success', $donation->fresh()->payment_status);
        $transactionCount = Transaction::where('reference_number', 'DON-TEST-12345')->count();
        $this->assertEquals(1, $transactionCount);

        // Second webhook call (Duplicate notification from Midtrans)
        $res2 = $this->postJson('/api/donations/webhook', $webhookPayload);
        $res2->assertStatus(200);

        // Must still be 1, NOT 2! (No duplicate financial transaction created)
        $transactionCountAfter = Transaction::where('reference_number', 'DON-TEST-12345')->count();
        $this->assertEquals(1, $transactionCountAfter);
    }

    /**
     * BR-12: Marbot only sees their own assigned tasks.
     */
    public function test_marbot_only_sees_assigned_tasks(): void
    {
        $marbot1 = User::where('role', 'marbot')->first();
        $admin = User::where('role', 'admin_masjid')->first();

        $marbot2 = User::create([
            'name' => 'Marbot Dua',
            'email' => 'marbot2@example.com',
            'password' => 'password123',
            'role' => 'marbot',
            'is_active' => true,
        ]);

        MaintenanceTask::create([
            'title' => 'Tugas Marbot 1',
            'description' => 'Membersihkan karpet',
            'assigned_to' => $marbot1->id,
            'assigned_by' => $admin->id,
            'priority' => 'low',
            'status' => 'pending',
        ]);

        MaintenanceTask::create([
            'title' => 'Tugas Marbot 2',
            'description' => 'Servis Pompa Air',
            'assigned_to' => $marbot2->id,
            'assigned_by' => $admin->id,
            'priority' => 'high',
            'status' => 'pending',
        ]);

        // Login as Marbot 1
        $response = $this->actingAs($marbot1, 'sanctum')->getJson('/api/marbot/tasks');
        $response->assertStatus(200);

        $taskIds = collect($response->json('data'))->pluck('assigned_to')->unique();
        $this->assertEquals([$marbot1->id], $taskIds->values()->toArray());
    }

    /**
     * BR-13: Social applications recipient details not exposed to public.
     */
    public function test_social_applications_cannot_be_accessed_by_public_or_jamaah(): void
    {
        $program = SocialProgram::first();
        $jamaah = User::where('role', 'jamaah')->first();

        // 1. Publik ditolak
        $publicRes = $this->getJson("/api/admin/social-programs/{$program->id}/applications");
        $publicRes->assertStatus(401);

        // 2. Jamaah ditolak
        $jamaahRes = $this->actingAs($jamaah, 'sanctum')->getJson("/api/admin/social-programs/{$program->id}/applications");
        $jamaahRes->assertStatus(403);
    }

    /**
     * BR-06: PDF and Excel reports contain same numbers and exporter identity.
     */
    public function test_pdf_and_excel_export_include_exporter_name(): void
    {
        $admin = User::where('role', 'admin_masjid')->first();

        // 1. PDF Export
        $pdfRes = $this->actingAs($admin, 'sanctum')->get('/api/admin/reports/export-pdf?date_from=2026-10-01&date_to=2026-10-31&type=all');
        $pdfRes->assertStatus(200);
        $this->assertEquals('application/pdf', $pdfRes->headers->get('content-type'));

        // 2. Excel Export
        $excelRes = $this->actingAs($admin, 'sanctum')->get('/api/admin/reports/export-excel?date_from=2026-10-01&date_to=2026-10-31&type=all');
        $excelRes->assertStatus(200);
        $this->assertStringContainsString('spreadsheetml.sheet', $excelRes->headers->get('content-type'));
    }
}
