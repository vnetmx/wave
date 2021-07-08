<?php

namespace App\Http\Controllers\Auth;

use App\Services\UserService;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;

class RegisterController extends \Wave\Http\Controllers\Auth\RegisterController
{
    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    public function create(array $data) : User
    {
        return app(UserService::class)->create($data);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $data = $request->all();

        $this->validator($data)->validate();

        $user = $this->create($data);


        if(! $user->verified){
            // send email verification
            return redirect()->route('login')->with(['message' => __('Thanks for signing up! Please check your email to verify your account.'), 'message_type' => 'success']);

        } else {
            $this->guard()->login($user);

            return $this->registered($request, $user)
                ?: redirect($this->redirectPath())->with(['message' => __('Thanks for signing up!'), 'message_type' => 'success']);
        }

    }
}
