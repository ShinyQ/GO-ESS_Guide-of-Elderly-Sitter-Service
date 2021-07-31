<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sitter extends Model
{
    protected $fillable = [
        'id_user', 'ktp', 'photo', 'description', 'education',
        'skill', 'is_ready'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'id_user');
    }
}
