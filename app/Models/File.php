<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = [
        'name',
        'category',
        'path',
        'image_path',
        'uploader',
        'uploaded_at', // 'uploaded_at' alanını ekledik
    ];

    // Otomatik timestamp alanlarını kullanmak istiyorsanız, bu satırı kaldırın
    // public $timestamps = false;
}
