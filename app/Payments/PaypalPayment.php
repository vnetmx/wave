<?php


namespace App\Payments;


use Illuminate\Http\Request;
use Wave\User;

class PaypalPayment extends PaymentInterface
{
    protected static $view='theme::payments.paypal';
    public function render()
    {
        // TODO: Implement render() method.
    }

    public function webhook(Request $request)
    {
        // TODO: Implement webhook() method.
    }

    public function cancel(Request $request)
    {
        // TODO: Implement cancel() method.
    }

    public function checkout(Request $request)
    {
        // TODO: Implement checkout() method.
    }

    public function invoices(User $user)
    {
        // TODO: Implement invoices() method.
    }

    public function switchPlans(Request $request)
    {
        // TODO: Implement switchPlans() method.
    }
}
