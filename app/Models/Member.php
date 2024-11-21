<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $fillable = [
        'name',
        'role',
        'details',
        'birth_date',
        'occupation',
        'contact',
        'social_media',
        'image',
        'parent_id',
        'hobbies',
    ];

    protected $casts = [
        'hobbies' => 'array',
    ];

    public function parent()
    {
        return $this->belongsTo(Member::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Member::class, 'parent_id');
    }
}
