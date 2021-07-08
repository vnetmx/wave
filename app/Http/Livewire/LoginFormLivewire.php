<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class LoginFormLivewire extends Component
{
    public $user;
    public $password;

    public function render()
    {
        return view('livewire.login-form-livewire');
    }

    // LOGIN
    public function login()
    {
        $credentials = Validator::make(
            [
                'email' => $this->user,
                'password' => $this->password
            ],
            [
                'email' => ['required', 'email'],
                'password' => ['required'],
            ])->validate();

        if (Auth::attempt($credentials)) {
            request()->session()->regenerate();
            $this->redirect('/');
        }
        $this->resetErrorBag(['username', 'password']);
        // Quickly add a validation message to the error bag.
        $this->addError('login', 'Incorrect Username or Password.');
    }

}
