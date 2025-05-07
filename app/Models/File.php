<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'fileable_id',
        'fileable_type',
        'filename',
        'original_filename',
        'file_path',
        'file_type',
        'mime_type',
        'file_size',
    ];

    public function fileable()
    {
        return $this->morphTo();
    }
}
