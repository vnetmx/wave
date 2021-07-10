<?php


namespace App\Services;


use App\Address;
use App\Events\AddressUpdated;
use App\Events\UserUpdated;
use App\User;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use TCG\Voyager\Models\Role;
use Wave\Notifications\VerifyEmail;

/**
 * @property bool|null verify_email
 * @property string|null defaultRole
 * @property integer|null trialDays
 */
class UserService
{

    protected $settings;

    public function __construct(array $settings, $default_role, $trial_days)
    {
        $this->settings = $settings;
        $this->settings['defaultRole'] = $default_role;
        $this->settings['trialDays'] = $trial_days;
    }

    public function __get($property)
    {
        return $this->settings[$property] ?? NULL;
    }

    /**
     * @param $id
     * @return mixed
     * @throws ModelNotFoundException
     */
    public function find($id)
    {
        return User::findOrFail($id);
    }

    public function verifiable()
    {
        return (bool)$this->verify_email;
    }

    public function update(User $user, array $data)
    {
        collect($data)->each(/**
         * @throws Exception
         */ function ($item, $key) use (&$user) {
            if (!is_array($item) && $key != "id") {
                if ($key == "password") {
                    $user->{$key} = Hash::make($item);
                    return;
                }
                $user->{$key} = $item;

                return;
            }

            if (in_array($key, ['shipping', 'billing'])) {
                $this->updateAddress($item, $user);
            }
        });

        $user->save();

        event(new UserUpdated($user, $data));

    }

    /**
     * @throws Exception
     */
    public function create(array $data): User
    {

        if (!isset($data['role_id'])) {
            try {
                $role = Role::where('name', '=', $this->defaultRole)->first();
            } catch (ModelNotFoundException $e) {
                throw new Exception("El Rol solicitado para la creación del usuario no existe.");
            }
        }

        if (isset($data['username']) && !empty($data['username'])) {
            $username = $data['username'];
        } else {
            $username = $this->getUniqueUsernameFromEmail($data['email']);
        }

        $user = User::create([
            'name' => $data['name'],
            'last_name' => $data['last_name'] ?? '',
            'email' => $data['email'],
            'username' => $username,
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'] ?? '',
            'role_id' => $data['role_id'] ?? $role->id,
            'verification_code' => $this->verifiable() ? Str::random(30) : NULL,
            'verified' => !$this->verifiable(),
            'trial_ends_at' => intval($this->trialDays) > 0 ? now()->addDays($this->trialDays) : null
        ]);

        // Tenemos en el morph, lo del rfc y company, así que debemos guardarlo al crear el usuario si existe en el $data.
        foreach (['rfc', 'company'] as $field) {
            if (isset($data[$field])) $user->{$field} = $data[$field];
        }
        $user->save();


        if ($this->verifiable()) {
            $user->notify(new VerifyEmail($user));
        }

        event(new Registered($user));

        return $user;
    }

    public function getUniqueUsernameFromEmail($email): string
    {
        $user = Str::of($email)->before('@')
            ->trim()
            ->replace('-', '_')
            ->replaceMatches('/[^A-Za-z0-9_]++/', '') // Lo que no sea alfanumerico o guion bajo, quitarlo.
            ->rtrim('_')->lower(); // no queremos que termine en _

        return $user . uniqid();
    }

    /**
     * @throws Exception
     */
    public function updateAddress($data, User $user)
    {
        try {
            $user->address()->save(Address::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'type' => $data['type'],
                ],
                [
                    'user_id' => $user->id,
                    'line1' => $data['line1'] ?? '',
                    'line2' => $data['line2'] ?? '',
                    'line3' => $data['line3'] ?? '',
                    'postal_code' => $data['postal_code'] ?? '',
                    'state' => $data['state'] ?? '',
                    'city' => $data['city'] ?? '',
                    'country_code' => $data['country_code'] ?? 'MX',
                    'type' => $data['type'],
                ])
            );
        } catch (ModelNotFoundException $e) {
            throw new Exception($e->getMessage());
        }

        event(new AddressUpdated($user, $data));

        return $user;
    }

}
