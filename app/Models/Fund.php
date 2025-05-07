<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fund extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
        'initial_balance',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function sourceTemporaryFunds()
    {
        return $this->hasMany(TemporaryFund::class, 'source_fund_id');
    }

    public function targetTemporaryFunds()
    {
        return $this->hasMany(TemporaryFund::class, 'target_fund_id');
    }

    // Method untuk mendapatkan balance saat ini
    public function getCurrentBalance()
    {
        $income = $this->transactions()->where('type', 'pemasukan')->sum('amount');
        $expense = $this->transactions()->where('type', 'pengeluaran')->sum('amount');

        return $this->initial_balance + $income - $expense;
    }
}
