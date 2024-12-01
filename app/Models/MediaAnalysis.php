<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaAnalysis extends Model
{
    use HasFactory;

    protected $fillable = ['file_paths', 'faces'];

    protected $casts = [
        'faces' => 'array',
    ];
}
