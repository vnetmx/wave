<?php

namespace App\Http\Livewire;

use App\Payments\PaymentInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Wave\Plan;

class OpenpayPaymentLivewire extends Component
{
    public $text;
    public $signup = false;
    public $name;

    // PAYMENT
    protected $listeners = [
        'token',
        'setProduct',
        'refreshComponent' => '$refresh',
        'isSignup'
    ];
    protected $payment;

    // PRODUCT
    public $productId;
    public $productType;

    public function mount()
    {
        if (auth()->check()) {
            $this->name = auth()->user()->name;
        }
    }

    // LOGIN
    /*
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
*/
    // PAYMENT
    public function charge()
    {
        $this->dispatchBrowserEvent('payment-event', ['newName' => 'hola']);
    }

    public function token($response, PaymentInterface $payment)
    {
        if (is_array($response) && isset($response['status']) && $response['status'] != 200) {
            $this->addError($response['data']['category'], $response['data']['description']);
            return;
        }
        $user = auth()->user();
        $customer = [
            'name' => $user->name,
            'last_name' => $user->last_name,
            'phone_number' => $user->phone,
            'email' => $user->email,
        ];

        $product = $this->productType::find($this->productId);

        $chargeRequest = [
            'method' => 'card',
            'source_id' => $response['data']['id'],
            'amount' => $product->price,
            'description' => $product->name . '::' . $product->features,
            'order_id' => strtoupper(uniqid(auth()->id())),
            'device_session_id' => 'kR1MiQhz2otdIuUlQkbEyitIqVMiI16f', //TODO implement on production.
            'customer' => $customer
        ];

        $response = $payment->singleCharge($chargeRequest);

        dd($response);
    }

    public function isSignup($bool)
    {
        $this->signup = $bool;
        $this->emit('$refresh');
    }
    // Product
    public function setProduct($productId, $model = 'plan')
    {
        $this->productId = $productId;
        $this->productType = config('openpay.products.' . strtolower($model), null);
    }

    public function render()
    {
        $this->update = false;
        $product = new Plan();
        if ($this->isProductSelected()) {
            $product = $this->productType::find($this->productId);
        }
        return view('livewire.openpay-payment-livewire')->with(['product' => $product]);
    }

    public function isProductSelected(): bool
    {
        return !is_null($this->productId) && !is_null($this->productType);
    }
}
