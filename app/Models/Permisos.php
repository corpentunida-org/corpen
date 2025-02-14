<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Permisos extends Model
{
    protected $table = 'permissions';
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'guard_name',
        'role_id'
    ];
    
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

}