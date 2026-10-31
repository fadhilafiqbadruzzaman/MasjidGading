<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialSummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'period_label',
        'period_start',
        'period_end',
        'opening_balance',
        'is_published',
        'published_by',
        'published_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end' => 'date',
            'opening_balance' => 'decimal:2',
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function publisher()
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // Business rule: Calculate total income, expense, and closing balance
    // Saldo = saldo_awal + pemasukan - pengeluaran (BR-05)
    public function calculateReport(string $type = 'all'): array
    {
        $query = Transaction::whereBetween('transaction_date', [$this->period_start, $this->period_end])
            ->where('status', 'verified');

        $totalIncome = (clone $query)->where('type', 'income')->sum('amount');
        $totalExpense = (clone $query)->where('type', 'expense')->sum('amount');
        $closingBalance = (float)$this->opening_balance + (float)$totalIncome - (float)$totalExpense;

        if ($type === 'income') {
            $transactions = (clone $query)->where('type', 'income')->with('category')->orderBy('transaction_date')->get();
        } elseif ($type === 'expense') {
            $transactions = (clone $query)->where('type', 'expense')->with('category')->orderBy('transaction_date')->get();
        } else {
            $transactions = $query->with('category')->orderBy('transaction_date')->get();
        }

        return [
            'period_label' => $this->period_label,
            'period_start' => $this->period_start->format('Y-m-d'),
            'period_end' => $this->period_end->format('Y-m-d'),
            'opening_balance' => (float)$this->opening_balance,
            'total_income' => (float)$totalIncome,
            'total_expense' => (float)$totalExpense,
            'closing_balance' => $closingBalance,
            'type' => $type,
            'transactions' => $transactions,
        ];
    }
}
