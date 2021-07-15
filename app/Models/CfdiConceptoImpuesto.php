<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CfdiConceptoImpuesto extends Model
{
    use HasFactory;
    protected $guarded = ['id'];


    public function cfdi()
    {
        $this->belongsTo(Cfdi::class);
    }
    public function ceoncepto()
    {
        $this->belongsTo(CfdiConcepto::class);
    }

}
