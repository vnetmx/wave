<?php

namespace App\Http\Livewire;

use App\Services\UserService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\User;

class RegisterFormLivewire extends Component
{
    public $name, $last_name, $email, $phone, $password, $password_confirmation, $username;

    protected function rules()
    {
        if (setting('auth.username_in_registration') == 'yes') {
            return [
                'name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'username' => 'required|string|max:20|unique:users',
                'password' => 'required|string|min:6|confirmed',
                'phone' => 'required|min:10|string',

            ];
        }

        return [
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'required|min:10|string',

        ];
    }

    public function render()
    {
        return view('livewire.register-form-livewire');
    }

    public function register()
    {
        $validatedData = $this->validate();

        $user = app(UserService::class)->create($validatedData);

        if(! $user->verified){
            // send email verification
            return redirect()->route('login')->with(['message' => __('Thanks for signing up! Please check your email to verify your account.'), 'message_type' => 'success']);
        } else {
            auth()->login($user);
            $this->emitUp('isSignup', false);
        }
    }
}
