<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cfdi extends Model
{
    use HasFactory;

    protected $table = 'cfdis';
    protected $guarded = ['id'];
    protected $casts = [
        'Fecha' => 'date',
        'FechaCancelacion' => 'date',
        'TimbreFiscalDigitalFechaTimbrado' => 'date'
    ];

    /**
     * Voyager Section
     */
    public function getSelectCfdiAttribute()
    {
        return "{$this->Fecha->format('d/m/y')} {$this->Serie}-{$this->Folio} {$this->ReceptorRfc}";
    }
    public $additional_attributes = ['select_cfdi'];
    ////////////////

    public function concepto(): HasMany
    {
        return $this->hasMany(CfdiConcepto::class);
    }

    public function traslados(): HasMany
    {
        return $this->hasMany(CfdiImpuesto::class)->where('Tipo', 'Traslado');
    }

    public function retenciones(): HasMany
    {
        return $this->hasMany(CfdiImpuesto::class)->where('Tipo', 'Retencion');
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(CfdiPago::class);
    }

    public function scopeIngreso($query)
    {
        return $query->where('EmisorRfc', 'ASE200211EE8');
    }

    public function scopeEgreso($query)
    {
        return $query->where('ReceptorRfc', 'ASE200211EE8');
    }

    public function scopeGet($query)
    {
        return $query;
    }
}
