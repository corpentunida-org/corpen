<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Role extends Model
{
    protected $table = 'roles';
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'actions', 'role_id', 'user_email');
    }
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }
}