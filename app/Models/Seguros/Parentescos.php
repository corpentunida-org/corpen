<?php

namespace App\Models\Seguros;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parentescos extends Model
{
    use HasFactory;
    protected $table = 'parentescos';

    
    public function beneficiario()
    {
        return $this->hasMany(SegBeneficiario::class, 'parentesco', 'code');
    }
}