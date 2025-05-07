<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rkb extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'requested_amount',
        'approved_amount',
        'received_amount',
        'used_amount',
        'request_date',
        'approval_date',
        'status',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function temporaryFunds()
    {
        return $this->hasMany(TemporaryFund::class);
    }

    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }

    // Method untuk mendapatkan sisa dana RKB
    public function getRemainingBalance()
    {
        return $this->received_amount - $this->used_amount;
    }

    // Method untuk mendapatkan outstanding uang yang belum diterima dari finance
    public function getOutstandingAmount()
    {
        return $this->approved_amount - $this->received_amount;
    }
}
