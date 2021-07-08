<?php


namespace App\Traits;


use Wave\OpenpayUser;

trait OpenpayTrait
{
    public function openpay()
    {
        return $this->hasOne(OpenpayUser::class);
    }
}
