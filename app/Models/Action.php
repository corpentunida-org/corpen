<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class Action extends Model
{
    use HasFactory;
    protected $table = 'actions';
    protected $primaryKey = 'id'; 
    protected $fillable = [
        'role_id',
        'user_id'
    ];
    /* public function role()
    {
        return $this->belongsTo(Role::class);
    } */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }
}
