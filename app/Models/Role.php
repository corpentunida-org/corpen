<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Spatie\Permission\Traits\HasRoles;

class Role extends Model
{
    protected $table = 'roles';
    use HasRoles;
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'guard_name'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'actions', 'role_id', 'user_email');
    }
    public function permissions()
    {
        return $this->belongsToMany(Permisos::class, 'role_has_permissions', 'role_id', 'permission_id');
    }
}