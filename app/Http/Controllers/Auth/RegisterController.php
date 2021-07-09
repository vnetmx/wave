<?php

namespace App\Http\Controllers\Auth;

use App\Services\UserService;
use App\User;
use Illuminate\Http\Request;

class RegisterController extends \Wave\Http\Controllers\Auth\RegisterController
{
    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
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
        $data = $request->except(['role_id']);

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
