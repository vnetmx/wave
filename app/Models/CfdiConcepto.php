<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CfdiConcepto extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function cfdi()
    {
        return $this->belongsTo(Cfdi::class, 'cfdi_id')->where('Tipo', 'Concepto');
    }

    public function parte() : HasMany
    {
        return $this->hasMany(CfdiConcepto::class, 'parent_id')->where('Tipo', 'Parte');
    }

    public function traslados() : HasMany
    {
        return $this->hasMany(CfdiConceptoImpuesto::class)->where('Tipo', 'Traslado');
    }

    public function retenciones() : HasMany
    {
        return $this->hasMany(CfdiConceptoImpuesto::class)->where('Tipo', 'Retencion');
    }
}
