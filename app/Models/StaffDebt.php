<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffDebt extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'transaction_id',
        'given_amount',
        'used_amount',
        'returned_amount',
        'purchase_details',
        'status',
        'type',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }

    // Method untuk mendapatkan piutang saat ini
    public function getCurrentDebt()
    {
        return $this->given_amount - $this->used_amount - $this->returned_amount;
    }

    // Method untuk mengupdate status
    public function updateStatus()
    {
        $currentDebt = $this->getCurrentDebt();

        if ($currentDebt <= 0) {
            $this->status = 'settled';
        } elseif ($this->returned_amount > 0 || $this->used_amount > 0) {
            $this->status = 'partial';
        } else {
            $this->status = 'outstanding';
        }

        $this->save();
    }
}
