<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    // HasProfilePhoto;
    use Notifiable;
    //use TwoFactorAuthenticatable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'nid',
        'fecha_nacimiento',
        'type',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'actions', 'user_id', 'role_id');
    }

    public function actions()
    {
        return $this->hasMany(Action::class, 'user_id', 'id');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];
    // En el modelo User (User.php)

    public function permissions()
    {
        return $this->belongsToMany(Permisos::class, 'model_has_permissions', 'model_id', 'permission_id');
    }

    public function hasPermission($permiso)
    {
        return $this->permissions()->where('name', $permiso)->exists();
    }

/*

    protected $appends = [
        'profile_photo_url',

    ];


    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    } */

}
