<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Staff extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'position',
        'email',
        'phone',
        'bank_account',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function debts()
    {
        return $this->hasMany(StaffDebt::class);
    }

    // Method untuk mendapatkan total piutang staff
    public function getTotalDebt()
    {
        return $this->debts()->whereIn('status', ['outstanding', 'partial'])->sum(DB::raw('given_amount - used_amount - returned_amount'));
    }
}
