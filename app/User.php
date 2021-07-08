<?php

namespace App;

use App\Traits\OpenpayTrait;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use \Storage;

class User extends \Wave\User
{

    use Notifiable, OpenpayTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'last_name', 'email', 'username', 'phone', 'password', 'verification_code', 'verified', 'trial_ends_at'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'trial_ends_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function address()
    {
        return $this->hasOne(Address::class);
    }

}
