<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SatWSRequestStatus extends Model
{
    use HasFactory;

    protected $table = 'sat_ws_request_statuses';

    protected $guarded = ['id'];

    protected $casts = [
      'from' => 'datetime',
      'to' => 'datetime'
    ];

    public function scopeDownloaded($query)
    {
        return $query->whereStatus('Downloaded');
    }

    public function scopeNotImported($query)
    {
        return $query->whereImported(0);
    }

    public function scopeMetadata($query)
    {
        return $query->where('requestType', 'Metadata');
    }

    public function scopeCfdi($query)
    {
        return $query->where('requestType', 'CFDI');
    }
}
