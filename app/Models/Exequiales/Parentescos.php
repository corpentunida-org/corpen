<?php

namespace App\Models\Exequiales;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parentescos extends Model
{
    use HasFactory;
    protected $table = 'parentescos';

    public function beneficiarios()
    {
        return $this->hasMany(ComaeExRelPar::class, 'parentesco', 'codPar');
    }
}
