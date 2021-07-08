<?php


namespace App\Services;


use App\User;
use Carbon\Carbon;
use Openpay;
use Wave\OpenpayUser;

class OpenpayService
{
    /**
     * @var mixed
     */
    private $gw;
    private $merchant_id;
    private $public_key;
    private $private_key;

    /**
     * Crea el objeto pasando las llaves asignadas en el portal
     * de Openpay para realizar las transacciones con el API.
     *
     * @param string $merchant
     * @param string $public
     * @param string $private
     */
    public function __construct($merchant, $public, $private)
    {
        $this->merchant_id = $merchant;
        $this->public_key = $public;
        $this->private_key = $private;
        Openpay::setId($merchant);
        Openpay::setApiKey($private);
        Openpay::setProductionMode(config('openpay.production'));
        $this->gw = Openpay::getInstance();
    }

    /**
     * Obtiene un customer del API de openpay a través
     * del openpay_id
     *
     * @param User $user
     * @return mixed
     */
    public function getCustomer(User $user)
    {
        return $this->gw->customers->get($user->openpay->openpay_id);
    }

    /**
     * Elimina un customer
     *
     * @param User $user
     * @return mixed
     */
    public function deleteCustomer(User $user)
    {
        if($this->exists($user)) {
            return $this->getCustomer($user)->delete();
        }

        return null;
    }

    public function listCustomers(Carbon $from = null, Carbon $to = null, $offset = 0, $limit = 5)
    {
        if(is_null($from)) $from = Carbon::now()->firstOfMonth();
        if(is_null($to)) $to = Carbon::now()->lastOfMonth();

        return $this->gw->customers->getList([
            'creation[gte]' => $from->toDateString(),
            'creation[lte]' => $to->toDateString(),
            'offset' => $offset,
            'limit' => $limit
        ]);
    }

    public function updateAddress(User $user)
    {
        $customer = $this->getCustomer($user);
        $customer->address->line1 = $user->address->line1;
        $customer->address->line2 = $user->address->line2;
        $customer->address->line3 = $user->address->line3;
        $customer->address->state = $user->address->state;
        $customer->address->city = $user->address->city;
        $customer->address->postal_code = $user->address->postal_code;
        $customer->address->country_code = $user->address->country_code;
        return $customer->save();
    }

    public function updateCustomer(User $user)
    {
        $this->updateProfile($user);
        $this->updateAddress($user);
        return $this->getCustomer($user);
    }

    public function updateProfile(User $user)
    {
        $customer = $this->getCustomer($user);
        $customer->name = $user->name;
        $customer->last_name = $user->last_name;
        $customer->email = $user->email;
        $customer->phone_number = $user->phone_number;
        return $customer->save();
    }

    public function createCustomer(User $user)
    {
        if($this->exists($user)) return $this->getCustomer($user);

        $customerData = [
            'name' => $user->name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'phone_number' => $user->phone,
        ];
        $address = [];
        if($user->address) {
            $address = [
                'address' => [
                    'line1' => $user->address->line1,
                    'line2' => $user->address->line2,
                    'line3' => $user->address->line3,
                    'postal_code' => $user->address->postal_code,
                    'state' => $user->address->state,
                    'city' => $user->address->city,
                    'country_code' => $user->address->country_code
                ]
            ];
        }
        $customer = $this->gw->customers->add($customerData + $address);

        OpenpayUser::create([
           'user_id' => $user->id,
           'openpay_id' => $customer->id
        ]);

        return $customer;
    }

    public function exists(User $user)
    {
        if($user->openpay)
        {
            return true;
        }

        return false;
    }

}
