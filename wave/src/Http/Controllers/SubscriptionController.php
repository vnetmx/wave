<?php

namespace Wave\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Payments\PaymentInterface;
use Carbon\Carbon;
use Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Wave\Plan;
use Wave\User;
use Wave\PaddleSubscription;
use TCG\Voyager\Models\Role;

class SubscriptionController extends Controller
{
    private $paymentService;

    public function __construct(PaymentInterface $paymentService){
        $this->paymentService = $paymentService;
    }

    public function webhook(Request $request){
        return $this->paymentService->webhook($request);
    }

    public function cancel(Request $request){
        return $this->paymentService->cancel($request);
    }

    public function checkout(Request $request){
        return $this->paymentService->checkout($request);
    }

    public function invoices(User $user){
        return $this->paymentService->invoices($user);
    }

    public function switchPlans(Request $request) {
        return $this->paymentService->switchPlans($request);
    }

}
