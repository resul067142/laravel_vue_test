<?php

// app/Models/Puanlafoto.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Puanlafoto extends Model
{
    use HasFactory;

    protected $table = 'puanlafoto';

    protected $fillable = ['ad', 'dosya_yolu', 'guzellik', 'yetenek', 'kisisel', 'ortalama_puan'];
}
