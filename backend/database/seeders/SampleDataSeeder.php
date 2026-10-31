<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Donation;
use App\Models\DonationCampaign;
use App\Models\Event;
use App\Models\FacilityReport;
use App\Models\FinancialSummary;
use App\Models\Inventory;
use App\Models\MaintenanceTask;
use App\Models\ProgramAllocation;
use App\Models\ProgramRealization;
use App\Models\QurbanProgram;
use App\Models\SocialProgram;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Models\User;
use App\Models\ZakatRecord;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin_masjid')->first() ?? User::first();
        $marbot = User::where('role', 'marbot')->first();
        $jamaah = User::where('role', 'jamaah')->first();

        // 1. Announcements
        Announcement::updateOrCreate(
            ['title' => 'Jadwal Sholat & Kajian Rutin Pekanan Masjid BGC'],
            [
                'content' => 'Bismillah, kami sampaikan bahwa kajian rutin ba’da Maghrib setiap hari Sabtu akan diisi oleh Ustadz Dr. Abdullah dengan tema Tafsir Al-Muyassar. Seluruh jamaah diundang hadir.',
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(2),
                'admin_id' => $admin->id,
            ]
        );

        Announcement::updateOrCreate(
            ['title' => 'Gotong Royong Kebersihan Menjelang Ramadhan'],
            [
                'content' => 'Akan diadakan kerja bakti membersihkan karpet dan area wudhu pada hari Ahad pagi pukul 07.30 WIB. Mohon partisipasi jamaah sekalian.',
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(1),
                'admin_id' => $admin->id,
            ]
        );

        Announcement::updateOrCreate(
            ['title' => 'Draft Pengumuman Renovasi Kanopi Area Parkir'],
            [
                'content' => 'Rencana pembangunan kanopi area parkir motor sedang dalam tahap penyusunan RAB.',
                'status' => 'draft',
                'published_at' => null,
                'admin_id' => $admin->id,
            ]
        );

        // 2. Events
        $event1 = Event::updateOrCreate(
            ['title' => 'Kajian Akbar: Meneladani Akhlak Rasulullah'],
            [
                'description' => 'Kajian akbar bersama Ustadz Prof. Dr. Salim A. Fillah di Ruang Utama Masjid BGC. Disediakan snack dan doorprize buku islami.',
                'location' => 'Ruang Sholat Utama Masjid BGC Cisoka',
                'event_date' => Carbon::now()->addDays(5)->format('Y-m-d'),
                'event_time' => '09:00:00',
                'organizer' => 'DKM Masjid Bukit Gading Cisoka',
                'capacity' => 150,
                'is_registration_open' => true,
                'status' => 'planned',
                'admin_id' => $admin->id,
            ]
        );

        Event::updateOrCreate(
            ['title' => 'Pelatihan Pengurusan Jenazah Syar\'i'],
            [
                'description' => 'Pelatihan teori dan praktek fardhu kifayah tata cara memandikan, mengafani, dan mensholatkan jenazah.',
                'location' => 'Aula Serbaguna Masjid BGC',
                'event_date' => Carbon::now()->addDays(12)->format('Y-m-d'),
                'event_time' => '08:30:00',
                'organizer' => 'Biro Sosial & Dakwah BGC',
                'capacity' => 40,
                'is_registration_open' => true,
                'status' => 'planned',
                'admin_id' => $admin->id,
            ]
        );

        // 3. Financial Summary & Transactions
        $summary = FinancialSummary::updateOrCreate(
            ['period_label' => 'Oktober 2026'],
            [
                'period_start' => Carbon::parse('2026-10-01'),
                'period_end' => Carbon::parse('2026-10-31'),
                'opening_balance' => 25000000.00,
                'is_published' => true,
                'published_by' => $admin->id,
                'published_at' => Carbon::now(),
                'notes' => 'Laporan keuangan bulanan periode Oktober 2026 yang telah diverifikasi.',
            ]
        );

        $catInfaq = TransactionCategory::where('name', 'Infaq Kotak Jumat')->first();
        $catListrik = TransactionCategory::where('name', 'Operasional Listrik & Air (PLN/PDAM)')->first();
        $catHonor = TransactionCategory::where('name', 'Honor & Kafalah Marbot')->first();

        if ($catInfaq) {
            Transaction::updateOrCreate(
                ['reference_number' => 'TRX-202610-001'],
                [
                    'type' => 'income',
                    'category_id' => $catInfaq->id,
                    'description' => 'Penerimaan Infaq Jumat Pekan ke-1',
                    'amount' => 4850000.00,
                    'transaction_date' => Carbon::parse('2026-10-04'),
                    'status' => 'verified',
                    'financial_summary_id' => $summary->id,
                    'admin_id' => $admin->id,
                    'created_by' => $admin->id,
                ]
            );
            Transaction::updateOrCreate(
                ['reference_number' => 'TRX-202610-002'],
                [
                    'type' => 'income',
                    'category_id' => $catInfaq->id,
                    'description' => 'Penerimaan Infaq Jumat Pekan ke-2',
                    'amount' => 5200000.00,
                    'transaction_date' => Carbon::parse('2026-10-11'),
                    'status' => 'verified',
                    'financial_summary_id' => $summary->id,
                    'admin_id' => $admin->id,
                    'created_by' => $admin->id,
                ]
            );
        }

        if ($catListrik) {
            Transaction::updateOrCreate(
                ['reference_number' => 'TRX-202610-003'],
                [
                    'type' => 'expense',
                    'category_id' => $catListrik->id,
                    'description' => 'Pembayaran Listrik PLN Masjid BGC Oktober 2026',
                    'amount' => 1850000.00,
                    'transaction_date' => Carbon::parse('2026-10-10'),
                    'status' => 'verified',
                    'financial_summary_id' => $summary->id,
                    'admin_id' => $admin->id,
                    'created_by' => $admin->id,
                ]
            );
        }

        if ($catHonor && $marbot) {
            Transaction::updateOrCreate(
                ['reference_number' => 'TRX-202610-004'],
                [
                    'type' => 'expense',
                    'category_id' => $catHonor->id,
                    'description' => 'Kafalah Marbot Bulan Oktober 2026',
                    'amount' => 2500000.00,
                    'transaction_date' => Carbon::parse('2026-10-15'),
                    'status' => 'verified',
                    'financial_summary_id' => $summary->id,
                    'admin_id' => $admin->id,
                    'created_by' => $admin->id,
                ]
            );
        }

        // 4. Donation Campaign & Jejak Amanah
        $campaign = DonationCampaign::updateOrCreate(
            ['title' => 'Renovasi Tempat Wudhu & Toilet Ramah Difabel'],
            [
                'description' => 'Pengadaan jalur pegangan difabel, peninggian lantai anti slip, dan penggantian 10 kran air hemat wudhu di Masjid BGC.',
                'target_amount' => 20000000.00,
                'collected_amount' => 12500000.00,
                'start_date' => Carbon::parse('2026-10-01'),
                'end_date' => Carbon::parse('2026-11-30'),
                'status' => 'active',
                'admin_id' => $admin->id,
            ]
        );

        // Jejak Amanah: Program Allocation & Realization
        $allocation = ProgramAllocation::updateOrCreate(
            ['title' => 'Tahap 1: Pembelian Keramik Granit Anti-Slip & Pipa'],
            [
                'description' => 'Alokasi dana untuk pembelian material keramik granit anti-slip ukuran 60x60 dan instalasi pemipaan wudhu.',
                'total_allocated' => 8000000.00,
                'campaign_id' => $campaign->id,
                'status' => 'active',
                'is_published' => true,
                'published_by' => $admin->id,
                'published_at' => Carbon::now(),
                'admin_id' => $admin->id,
            ]
        );

        ProgramRealization::updateOrCreate(
            ['description' => 'Pembelian 25 dus keramik granit anti-slip di TB Berkah Jaya'],
            [
                'allocation_id' => $allocation->id,
                'amount_used' => 4500000.00,
                'realization_date' => Carbon::parse('2026-10-18'),
                'notes' => 'Faktur No. TB-88912 disertakan dalam arsip fisik.',
                'admin_id' => $admin->id,
            ]
        );

        // 5. Inventory & Maintenance Tasks
        $invAC = Inventory::updateOrCreate(
            ['name' => 'AC Split Daikin 2 PK Ruang Utama'],
            [
                'category' => 'Elektronik & Pendingin',
                'quantity' => 4,
                'location' => 'Ruang Sholat Utama',
                'condition' => 'needs_repair',
                'last_maintained_at' => Carbon::parse('2026-08-10'),
                'notes' => 'AC no. 2 bagian shaf kiri jamaah terdengar berisik dan kurang dingin.',
                'admin_id' => $admin->id,
            ]
        );

        $invSound = Inventory::updateOrCreate(
            ['name' => 'Mic Wireless Shure Beta 58A'],
            [
                'category' => 'Sound System',
                'quantity' => 2,
                'location' => 'Ruang Mimbar / Sound Controller',
                'condition' => 'good',
                'last_maintained_at' => Carbon::parse('2026-10-01'),
                'notes' => 'Berfungsi normal dengan baterai rechargeable.',
                'admin_id' => $admin->id,
            ]
        );

        if ($marbot) {
            $task = MaintenanceTask::updateOrCreate(
                ['title' => 'Pembersihan Filter & Perbaikan AC No. 2'],
                [
                    'description' => 'Cuci filter AC split no. 2 di ruang utama dan cek tekanan freon bersama teknisi rekanan.',
                    'location' => 'Ruang Sholat Utama Masjid BGC',
                    'priority' => 'high',
                    'assigned_to' => $marbot->id,
                    'assigned_by' => $admin->id,
                    'due_date' => Carbon::now()->addDays(3)->format('Y-m-d'),
                    'status' => 'in_progress',
                    'inventory_id' => $invAC->id,
                    'started_at' => Carbon::now()->subHours(4),
                ]
            );

            FacilityReport::updateOrCreate(
                ['title' => 'Laporan Pipa Kran Wudhu Ikhwan Menetes'],
                [
                    'task_id' => null,
                    'reported_by' => $marbot->id,
                    'description' => 'Kran air nomor 3 pada shaf wudhu laki-laki tidak bisa ditutup rapat sehingga air terus menetes.',
                    'location' => 'Tempat Wudhu Ikhwan Samping Kiri',
                    'condition' => 'needs_repair',
                    'status' => 'open',
                    'notes' => 'Perlu ganti seal karet atau ganti kran 1/2 inch baru.',
                ]
            );
        }

        // 6. Zakat, Qurban, Social Programs
        ZakatRecord::updateOrCreate(
            ['period_label' => 'Ramadhan 1447 H / 2026 M'],
            [
                'type' => 'fitrah',
                'period_year' => 2026,
                'amount' => 15500000.00,
                'payer_name' => 'Kumpulan Jamaah RW 08 Cisoka (310 Jiwa)',
                'notes' => 'Diterima dalam bentuk uang tunai dikonversi dari beras 3.5 liter / jiwa.',
                'admin_id' => $admin->id,
            ]
        );

        QurbanProgram::updateOrCreate(
            ['title' => 'Program Qurban Berkah Masjid BGC 1447 H'],
            [
                'year' => 2026,
                'description' => 'Penerimaan dan penyaluran hewan qurban sapi dan kambing untuk warga Bukit Gading Cisoka dan dhuafa sekitar.',
                'status' => 'open',
                'is_published' => true,
                'admin_id' => $admin->id,
            ]
        );

        SocialProgram::updateOrCreate(
            ['title' => 'Santunan Anak Yatim & Dhuafa Bulanan'],
            [
                'description' => 'Pemberian paket sembako dan uang saku pendidikan bagi 25 anak yatim dan lansia di sekitar Masjid BGC.',
                'budget' => 7500000.00,
                'status' => 'active',
                'is_published' => true,
                'admin_id' => $admin->id,
            ]
        );
    }
}
