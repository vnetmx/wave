<?php


namespace App\Services;


use App\Address;
use App\Events\AddressUpdated;
use App\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Wave\Notifications\VerifyEmail;
use function PHPUnit\Framework\isInstanceOf;

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

    public function verifiable()
    {
        return (bool)$this->verify_email;
    }

    public function create(array $data) : User
    {

        $role = \TCG\Voyager\Models\Role::where('name', '=', $this->defaultRole)->first();

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
            'role_id' => $role->id,
            'verification_code' => $this->verifiable() ? Str::random(30) : NULL,
            'verified' => ! $this->verifiable(),
            'trial_ends_at' => intval($this->trialDays) > 0 ? now()->addDays($this->trialDays) : null
        ]);

        if ($this->verifiable()) {
            $user->notify(new VerifyEmail($user));
        }

        event(new Registered($user));

        return $user;
    }

    public function getUniqueUsernameFromEmail($email) : string
    {
        $user = Str::of($email)->before('@')
                               ->trim()
                               ->replace('-', '_')
                               ->replaceMatches('/[^A-Za-z0-9_]++/', '') // Lo que no sea alfanumerico o guion bajo, quitarlo.
                               ->rtrim('_')->lower(); // no queremos que termine en _

        return $user . uniqid();
    }

    public function updateAddress($data, User $user)
    {
        $user->address()->associate(Address::updateOrCreate(
            [
            'user_id' => $user->id
            ],
            [
                'user_id' => $user->id,
                'line1' => $data['line1'],
                'line2' => $data['line2'],
                'line3' => $data['line3'],
                'postal_code' => $data['postal_code'],
                'state' => $data['state'],
                'city' => $data['city'],
                'country_code' => $data['country_code'] ?? 'MX',
            ])
        );

        $user->save();

        event(new AddressUpdated($user));

        return $user;
    }

}
