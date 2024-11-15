<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamilyMember extends Model
{
    use HasFactory;

    // Toplu atamaya izin verilen alanlar
    protected $fillable = [
        'name',       // İsim
        'age',        // Yaş
        'relation',   // İlişki
        'photo_path'  // Fotoğraf yolu
    ];
}
