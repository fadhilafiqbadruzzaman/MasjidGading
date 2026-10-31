<?php

namespace App\Services;

use App\Models\FinancialSummary;
use App\Models\Transaction;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;

class ReportService
{
    /**
     * BR-04 & BR-05:
     * - Hanya transaksi 'verified' yang masuk ke perhitungan laporan.
     * - Saldo akhir = saldo awal + total pemasukan - total pengeluaran.
     */
    public function getReportData(string $dateFrom, string $dateTo, string $type = 'all'): array
    {
        // Cari saldo awal dari ringkasan keuangan sebelumnya
        $summary = FinancialSummary::where('period_start', '<=', $dateFrom)
            ->where('period_end', '>=', $dateFrom)
            ->first();

        $openingBalance = $summary ? (float)$summary->opening_balance : 0.00;

        // Query transaksi terverifikasi
        $baseQuery = Transaction::whereBetween('transaction_date', [$dateFrom, $dateTo])
            ->where('status', 'verified');

        $totalIncome = (float)(clone $baseQuery)->where('type', 'income')->sum('amount');
        $totalExpense = (float)(clone $baseQuery)->where('type', 'expense')->sum('amount');
        $closingBalance = $openingBalance + $totalIncome - $totalExpense;

        $trxQuery = clone $baseQuery;
        if ($type === 'income') {
            $trxQuery->where('type', 'income');
        } elseif ($type === 'expense') {
            $trxQuery->where('type', 'expense');
        }

        $transactions = $trxQuery->with('category')
            ->orderBy('transaction_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        return [
            'masjid_name' => 'Masjid Bukit Gading Cisoka (Masjid BGC)',
            'address' => 'Perumahan Bukit Gading Cisoka, Kec. Cisoka, Tangerang, Banten',
            'period_from' => $dateFrom,
            'period_to' => $dateTo,
            'type' => $type,
            'opening_balance' => $openingBalance,
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'closing_balance' => $closingBalance,
            'transactions' => $transactions,
        ];
    }

    /**
     * Ekspor Laporan PDF dengan Identitas Penyusun.
     */
    public function generatePdf(array $data, User $exporter): \Barryvdh\DomPDF\PDF
    {
        $html = view('reports.financial_pdf', array_merge($data, [
            'exporter_name' => $exporter->name,
            'exporter_email' => $exporter->email,
            'exporter_role' => $exporter->role === 'super_admin' ? 'Super Admin' : 'Admin Masjid',
            'generated_at' => Carbon::now()->translatedFormat('d F Y H:i:s'),
        ]))->render();

        return Pdf::loadHTML($html)->setPaper('a4', 'portrait');
    }

    /**
     * Ekspor Laporan Excel (.xlsx) dengan Identitas Penyusun & 2 Sheets.
     */
    public function generateExcel(array $data, User $exporter, string $filePath): void
    {
        $writer = new Writer();
        $writer->openToFile($filePath);

        // Sheet 1: Ringkasan
        $sheet1 = $writer->getCurrentSheet();
        $sheet1->setName('Ringkasan Keuangan');

        $writer->addRow(Row::fromValues(['LAPORAN KEUANGAN MASJID BUKIT GADING CISOKA']));
        $writer->addRow(Row::fromValues(['Perumahan Bukit Gading Cisoka, Tangerang, Banten']));
        $writer->addRow(Row::fromValues([]));
        $writer->addRow(Row::fromValues(['Identitas Penyusun:', $exporter->name . ' (' . $exporter->email . ')']));
        $writer->addRow(Row::fromValues(['Jabatan:', $exporter->role === 'super_admin' ? 'Super Admin' : 'Admin Masjid']));
        $writer->addRow(Row::fromValues(['Tanggal Cetak:', Carbon::now()->translatedFormat('d F Y H:i:s')]));
        $writer->addRow(Row::fromValues(['Periode Laporan:', $data['period_from'] . ' s/d ' . $data['period_to']]));
        $writer->addRow(Row::fromValues(['Filter Jenis Transaksi:', strtoupper($data['type'])]));
        $writer->addRow(Row::fromValues([]));
        $writer->addRow(Row::fromValues(['RINGKASAN SALDO']));
        $writer->addRow(Row::fromValues(['Saldo Awal', number_format($data['opening_balance'], 2, ',', '.')]));
        $writer->addRow(Row::fromValues(['Total Pemasukan (+)', number_format($data['total_income'], 2, ',', '.')]));
        $writer->addRow(Row::fromValues(['Total Pengeluaran (-)', number_format($data['total_expense'], 2, ',', '.')]));
        $writer->addRow(Row::fromValues(['Saldo Akhir', number_format($data['closing_balance'], 2, ',', '.')]));

        // Sheet 2: Rincian Transaksi
        $sheet2 = $writer->addNewSheetAndMakeItCurrent();
        $sheet2->setName('Rincian Transaksi');

        $writer->addRow(Row::fromValues(['No', 'Tanggal', 'No. Referensi', 'Kategori', 'Keterangan', 'Pemasukan (Rp)', 'Pengeluaran (Rp)', 'Status']));

        $no = 1;
        foreach ($data['transactions'] as $trx) {
            $income = $trx->type === 'income' ? (float)$trx->amount : 0;
            $expense = $trx->type === 'expense' ? (float)$trx->amount : 0;

            $writer->addRow(Row::fromValues([
                $no++,
                $trx->transaction_date->format('Y-m-d'),
                $trx->reference_number ?? '-',
                $trx->category ? $trx->category->name : '-',
                $trx->description,
                $income > 0 ? number_format($income, 2, ',', '.') : '-',
                $expense > 0 ? number_format($expense, 2, ',', '.') : '-',
                $trx->status === 'verified' ? 'Terverifikasi' : 'Pending',
            ]));
        }

        $writer->close();
    }
}
