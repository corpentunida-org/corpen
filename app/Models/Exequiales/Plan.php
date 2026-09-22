<?php

namespace App\Models\Exequiales;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;
    protected $table = 'planes';
    protected $primaryKey = 'code';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';
}
