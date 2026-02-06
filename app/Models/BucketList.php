<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BucketList extends Model
{
    use HasFactory;

    protected $fillable = [
        'destination',
        'description',
        'priority',
        'added_at'
    ];

    protected $casts = [
        'added_at' => 'datetime',
    ];

    protected $table = 'bucket_lists';
}
