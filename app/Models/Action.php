<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class Action extends Model
{
    use HasFactory;

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
