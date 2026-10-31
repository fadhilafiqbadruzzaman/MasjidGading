<?php

namespace Database\Seeders;

use App\Models\TransactionCategory;
use Illuminate\Database\Seeder;

class TransactionCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // Incomes
            ['name' => 'Infaq Kotak Jumat', 'type' => 'income', 'description' => 'Pemasukan dari kotak infaq sholat Jumat'],
            ['name' => 'Sedekah Subuh', 'type' => 'income', 'description' => 'Pemasukan dari program sedekah subuh'],
            ['name' => 'Donasi Online Midtrans', 'type' => 'income', 'description' => 'Pemasukan donasi daring melalui Midtrans'],
            ['name' => 'Infaq Pembangunan & Renovasi', 'type' => 'income', 'description' => 'Pemasukan khusus pemugaran dan perbaikan fasilitas'],
            ['name' => 'Penerimaan Zakat Fitrah', 'type' => 'income', 'description' => 'Penerimaan zakat fitrah'],
            ['name' => 'Penerimaan Zakat Maal', 'type' => 'income', 'description' => 'Penerimaan zakat harta/maal'],

            // Expenses
            ['name' => 'Operasional Listrik & Air (PLN/PDAM)', 'type' => 'expense', 'description' => 'Pembayaran tagihan utilitas bulanan'],
            ['name' => 'Honor & Kafalah Marbot', 'type' => 'expense', 'description' => 'Tunjangan bulanan marbot dan kebersihan'],
            ['name' => 'Pemeliharaan & Kebersihan Fasilitas', 'type' => 'expense', 'description' => 'Pembelian alat kebersihan dan servis AC/sound'],
            ['name' => 'Kegiatan Dakwah & Kajian Rutin', 'type' => 'expense', 'description' => 'Konsumsi jamaah dan honor asatidz pemateri'],
            ['name' => 'Penyaluran Santunan Bantuan Sosial', 'type' => 'expense', 'description' => 'Bantuan untuk dhuafa dan yatim sekitar masjid'],
            ['name' => 'Distribusi Zakat', 'type' => 'expense', 'description' => 'Penyaluran zakat kepada 8 asnaf'],
        ];

        foreach ($categories as $cat) {
            TransactionCategory::updateOrCreate(['name' => $cat['name']], $cat);
        }
    }
}
