<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class Action extends Model
{
    use HasFactory;
    protected $table = 'actions';
    protected $fillable = [
        'user_email',
        'role_id',
    ];
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
