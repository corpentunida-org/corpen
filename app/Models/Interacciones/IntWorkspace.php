<?php

namespace App\Models\Interacciones;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Archivo\GdoArea; // Ajusta según el nombre real de tu modelo de área

class IntWorkspace extends Model
{
    protected $table = 'int_workspaces';

    protected $fillable = [
        'name',
        'description',
        'status',
        'area_id'
    ];

    public function conversations(): HasMany
    {
        return $this->hasMany(IntConversation::class, 'workspace_id');
    }

    public function area()
    {
        return $this->belongsTo(GdoArea::class, 'area_id');
    }
}