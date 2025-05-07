<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemporaryFund extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_fund_id',
        'target_fund_id',
        'rkb_id',
        'amount',
        'loan_date',
        'settlement_date',
        'status',
        'description',
    ];

    public function sourceFund()
    {
        return $this->belongsTo(Fund::class, 'source_fund_id');
    }

    public function targetFund()
    {
        return $this->belongsTo(Fund::class, 'target_fund_id');
    }

    public function rkb()
    {
        return $this->belongsTo(Rkb::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }
}
