<?php


namespace App\Services;


use App\Exceptions\OpenpayCustomerNotFound;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Openpay;
use OpenpayApiRequestError;
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

    private $address_rules = [
        'postal_code' => 'required|string|min:1|max:12',
        'city' => 'required',
        'line1' => 'required',
        'state' => 'required',
    ];

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
        return $this->getIfExists($user);
    }

    /**
     * Elimina un customer
     *
     * @param User $user
     * @return mixed
     */
    public function deleteCustomer(User $user)
    {
        try {
            return $this->getIfExists($user)->delete();
        } catch (OpenpayCustomerNotFound $e) {
            Log::info("User " . $user->email . " not found on Openpaypal <-> DB");
        }
        return null;
    }

    public function listCustomers(Carbon $from = null, Carbon $to = null, $offset = 0, $limit = 5)
    {
        if (is_null($from)) $from = Carbon::now()->firstOfMonth();
        if (is_null($to)) $to = Carbon::now()->lastOfMonth();

        return $this->gw->customers->getList([
            'creation[gte]' => $from->toDateString(),
            'creation[lte]' => $to->toDateString(),
            'offset' => $offset,
            'limit' => $limit
        ]);
    }

    public function updateAddress(User $user, array $data)
    {
        // Debido a que no hay forma de actualizar la dirección  directamente al cliente
        // lo que haremos será borrar el cliente y volverlo a crear pero con la dirección actualizada.
        if ($data['type'] != 'billing') return false;

        /**
         * address.postal_code la longitud tiene que estar entre 1 y 12, address.postal_code is required, address.city
         * is required, address.line1 is required, address.state is required
         *
         * If fails the validation let dont to anything.
         */
        if(Validator::make($data,$this->address_rules)->fails()) return false;
        $this->deleteCustomer($user);
        $user->openpay()->delete();
        return $this->createCustomer($user);

    }

    public function updateCustomer(User $user)
    {
        $this->updateProfile($user);
        $this->updateAddress($user, $user->billing->attributesToArray());
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
        // Verificamos si el usuario tiene un OpenpayID (no debería),
        // en caso de que lo tenga verificamos si existe en Openpay
        // si existe entonces lo regresamos y nos brincamos el proceso
        // de creación del usuario en openpay.
        try {
            return $this->getIfExists($user);
        } catch (OpenpayCustomerNotFound $e) {
            Log::notice('Se encontro una relación Openpay ID <-> Usuario Local pero sin existir en el API, se procede a eliminar.');
        }

        $customerData = [
            'name' => $user->name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'phone_number' => $user->phone,
        ];
        $address = [];
        if ($user->billing) {
            $address = [
                'address' => [
                    'line1' => $user->billing->line1,
                    'line2' => $user->billing->line2,
                    'line3' => $user->billing->line3,
                    'postal_code' => $user->billing->postal_code,
                    'state' => $user->billing->state,
                    'city' => $user->billing->city,
                    'country_code' => $user->billing->country_code
                ]
            ];
        }
        $customer = $this->gw->customers->add($customerData + $address);

        OpenpayUser::updateOrCreate(
            [
                'user_id' => $user->id
            ],
            [
                'user_id' => $user->id,
                'openpay_id' => $customer->id
            ]);

        return $customer;
    }

    /**
     * @param User $user
     * @return mixed
     * @throws OpenpayCustomerNotFound
     */
    public function getIfExists(User $user)
    {
        // Si tenemos una relacion de usuario <-> openpay_id en la DB
        if ($user->openpay) {
            // Intentamos obtener el usuario de openpay
            try {
                return $this->gw->customers->get($user->openpay->openpay_id);
            } catch (OpenpayApiRequestError $e) {
                // Si no hay un customer en openpay con el openpay_id regresamos null y
                // editamos la base de datos para borrar esa relación.
                $user->openpay()->delete();
            }
        }

        throw new OpenpayCustomerNotFound("Openpay Customer Not Found");
    }

}
