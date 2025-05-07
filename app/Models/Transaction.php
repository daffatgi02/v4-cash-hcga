<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'fund_id',
        'category_id',
        'rkb_id',
        'staff_id',
        'type',
        'amount',
        'transaction_date',
        'description',
        'recipient_name',
        'recipient_type',
        'is_settlement',
        'temporary_fund_id',
    ];

    public function fund()
    {
        return $this->belongsTo(Fund::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function rkb()
    {
        return $this->belongsTo(Rkb::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function temporaryFund()
    {
        return $this->belongsTo(TemporaryFund::class);
    }

    public function staffDebt()
    {
        return $this->hasOne(StaffDebt::class);
    }

    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }
}
