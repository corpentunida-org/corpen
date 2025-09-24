<?php

namespace App\Models\Soportes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScpSubTipo extends Model
{
    use HasFactory;

    protected $table = 'scp_sub_tipos'; // 3. ¿SE LLAMA ASÍ EXACTAMENTE TU TABLA EN LA DB?

    protected $fillable = [
        'scp_tipo_id', // 4. ¿SE LLAMA ASÍ EXACTAMENTE TU COLUMNA EN LA DB?
        'nombre',
        'descripcion',
    ];

    public function tipo()
    {
        return $this->belongsTo(ScpTipo::class, 'scp_tipo_id'); // 5. CONFIRMA AQUÍ TAMBIÉN
    }
}