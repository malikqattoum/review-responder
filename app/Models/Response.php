<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Response extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_id',
        'body',
        'tone',
    ];

    public function review()
    {
        return $this->belongsTo(Review::class);
    }
}
