<?php


namespace App\Payments;


use Illuminate\Http\Request;
use Wave\Transaction;
use Wave\User;

abstract class PaymentInterface
{
    protected static $view='theme::payments.default';
    protected $_with = [];

    public static function instance()
    {
        return new static();
    }
    public function render()
    {
        return view(static::$view)->with($this->_with)->render();
    }
    public function with(array $vars)
    {
        $this->_with = array_merge($this->_with, $vars);
    }

    // Interface para SubscriptionController
    abstract public function webhook(Request $request);
    abstract public function cancel(Request $request);
    abstract public function checkout(Request $request);
    abstract public function invoices(User $user);
    abstract public function switchPlans(Request $request);

    public function createTransaction($data)
    {
        return Transaction::create($data);
    }
}
