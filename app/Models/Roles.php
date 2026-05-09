<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    protected $fillable = [
        'name'
    ];
    public function roles()
    {
        return $this->hasOne(Roles::class, 'user_id');
    }
}
