<?php

namespace App;

use App\Traits\OpenpayTrait;
use Illuminate\Notifications\Notifiable;
use TCG\Voyager\Models\Role;
use Wave\KeyValue;

class User extends \Wave\User
{

    use Notifiable, OpenpayTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'last_name', 'email', 'username', 'phone', 'password', 'verification_code', 'verified', 'trial_ends_at', 'role_id'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'trial_ends_at'
    ];

    public $additional_attributes = ['company', 'rfc'];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getRfcAttribute()
    {
        return $this->keyValue('rfc')->value ?? null;
    }

    public function getCompanyAttribute()
    {
        return $this->keyValue('company')->value ?? null;
    }

    protected function setMorphValue($field, $value)
    {
        if (!in_array($field, $this->additional_attributes)) return;

        $attr = $this->keyValue($field);
        if (isset($attr)) {
            $attr->value = $value;
            $attr->save();
            return;
        }

        KeyValue::create([
            'type' => 'text',
            'keyvalue_id' => $this->id,
            'keyvalue_type' => 'users',
            'key' => $field,
            'value' => $value,
        ]);
    }

    public function setRfcAttribute($value)
    {
        $this->setMorphValue('rfc', $value);
    }

    public function setCompanyAttribute($value)
    {
        $this->setMorphValue('company', $value);
    }

    public function address()
    {
        return $this->hasMany(Address::class);
    }

    public function shipping()
    {
        return $this->hasOne(Address::class)->whereType('shipping');
    }

    public function billing()
    {
        return $this->hasOne(Address::class)->whereType('billing');
    }
    /**
     * The roles that belong to the user.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class,'user_roles');
    }

}
