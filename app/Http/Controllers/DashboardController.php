<?php

namespace App\Http\Controllers;

use App\Currency;
use App\FavoritesGroup;
use App\Payment;
use App\PaymentRequest;
use App\PayPlan;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use Mollie\Laravel\Facades\Mollie;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['donation_page']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $this->updatePaymentRequestsPaid();
        $this->updateReceivedPaymentRequestsPaid();

        $user = Auth::user();

        // Get my payment requests
        $payment_requests = $user->payment_requests->sortByDesc('created_at');

        // Get the amount of times a request is paid
        foreach ($payment_requests as $payment_request) {
            // Set the amount of times paid
            $payment_request->times_paid = Payment::where('payment_request_id', $payment_request->id)
                    ->whereNotNull('paid_at')
                    ->count() . 'x';
            // Delete uncompleted payments
            $payments = Payment::where('payment_request_id', $payment_request->id)
                ->whereNull('paid_at')->get();
            foreach ($payments as $payment) {
                $mollie_payment = Mollie::api()->payments()->get($payment->payment_id);
                $status = $mollie_payment->status;
                error_log($status);
                if ($status == 'expired' || $status == 'canceled' || 'failed') {
                    $payment->delete();
                }
            }
        }

        // Get received payment requests
        $received_payment_requests = $user->received_payment_requests->sortBy('created_at');
        // Get whether I've paid the request / A pay plan is in progress
        $received_payment_requests = $this->checkReceivedPaymentRequestsStatus($received_payment_requests);

        $groups = $user->groups;
        $contacts = $user->contacts;

        return view('dashboard.index')
            ->with('payment_requests', $payment_requests)
            ->with('received_payment_requests', $received_payment_requests)
            ->with('groups', $groups)
            ->with('contacts', $contacts);
    }

    public function user()
    {
        $user_id = Auth::id();
        $user = User::find($user_id);

        return view('dashboard.profile')
            ->with('user', $user);
    }

    public function donationSwitch()
    {
        $user = Auth::user();

        $view = view('dashboard.profile')
            ->with('user', $user);

        if (!$user->bank_accounts->count() > 0) {
            return $view;
        }

        $user->accepts_donations = !$user->accepts_donations;
        $user->save();

        return $view;
    }

    public function donationPage($id)
    {
        if (Auth::id() == $id) {
            return redirect('/dashboard');
        }
        $user = User::find($id);

        if (!$user->accepts_donations) {
            return redirect('/dashboard')->with('error', __('text.user_not_accepts_donations'));
        }

        $currencies = Currency::all();

        return view('dashboard.donation')
            ->with('user', $user)
            ->with('currencies', $currencies);
    }

    public function checkReceivedPaymentRequestsStatus($received_payment_requests)
    {
        $user = Auth::user();
        foreach ($received_payment_requests as $received_payment_request) {
            // Check for pay plans
            if ($user->pay_plans->where('payment_request_id', $received_payment_request->id)->first()) {
                $pay_plan = $user->pay_plans->where('payment_request_id', $received_payment_request->id)->first();
                // Check if payment was done
                $payment = $pay_plan->payments
                    ->where('payment_request_id', $received_payment_request->id)
                    ->where('payment_id', '!=', null)
                    ->first();
                $mollie_payment = Mollie::api()->payments()->get($payment->payment_id);
                // If the initial payment was not completed, the pay plan gets deleted
                if ($mollie_payment->status != 'paid') {
                    // Delete pay plan
                    $scheduled_payments = $pay_plan->scheduled_payments;
                    foreach ($scheduled_payments as $scheduled_payment) {
                        $scheduled_payment->delete();
                    }
                    $payments = $pay_plan->payments;
                    foreach ($payments as $payment) {
                        $payment->delete();
                    }
                    $pay_plan->delete();
                    $received_payment_request->paid = __('text.no');
                } else {
                    $payments_done = $pay_plan->payments->count();
                    if ($pay_plan->nr_of_payments == $payments_done) {
                        $received_payment_request->paid = __('text.pay_plan_completed');
                    } else {
                        $received_payment_request->paid = __('text.pay_plan_in_progress') . ' ' . $payments_done . '/' . $pay_plan->nr_of_payments;
                    }
                }
            } else {
                $received_payment_request->paid = $received_payment_request->payments
                    ->where('paid_at', '!=', null)
                    ->where('type', 'full')
                    ->first() != null ? __('text.yes') : __('text.no');
            }
        }

        return $received_payment_requests;
    }

    public function updatePaymentRequestsPaid()
    {
        $user_id = Auth::id();
        $user = User::find($user_id);
        $payment_requests = $user->payment_requests;

        // Get the amount of times a request is paid
        foreach ($payment_requests as $payment_request) {
            // Check if the unpaid requests have been paid in Mollie
            $unpaid_payments = Payment::where('payment_request_id', $payment_request->id)
                ->whereNull('paid_at')->get();
            foreach ($unpaid_payments as $unpaid_payment) {
                $mollie_payment = Mollie::api()->payments()->get($unpaid_payment->payment_id);
                if ($mollie_payment->status == 'paid' || $mollie_payment->status == 'authorized') {
                    $mollie_paid_at_date = $mollie_payment->paidAt;

                    // Format the Mollie date to sql datetime
                    $paid_at = str_replace('T', ' ', $mollie_paid_at_date);
                    $paid_at = substr($paid_at, 0, -6);

                    // Update name if null
                    if ($mollie_payment->details != null && $unpaid_payment->payer_name == null) {
                        $unpaid_payment->payer_name = $mollie_payment->details->consumerName;
                    }

                    // Update the record in the database
                    $unpaid_payment->paid_at = $paid_at;
                    $unpaid_payment->save();
                }
            }
        }
    }

    public function updateReceivedPaymentRequestsPaid()
    {
        $user_id = Auth::id();
        $user = User::find($user_id);
        $payment_requests = $user->received_payment_requests;

        // Get the amount of times a request is paid
        foreach ($payment_requests as $payment_request) {
            // Check if the unpaid requests have been paid in Mollie
            $unpaid_payments = Payment::where('payment_request_id', $payment_request->id)
                ->whereNull('paid_at')->get();
            foreach ($unpaid_payments as $unpaid_payment) {
                $mollie_payment = Mollie::api()->payments()->get($unpaid_payment->payment_id);
                if ($mollie_payment->status == 'paid' || $mollie_payment->status == 'authorized') {
                    $mollie_paid_at_date = $mollie_payment->paidAt;

                    // Format the Mollie date to sql datetime
                    $paid_at = str_replace('T', ' ', $mollie_paid_at_date);
                    $paid_at = substr($paid_at, 0, -6);

                    // Update name if null
                    if ($mollie_payment->details != null && $unpaid_payment->payer_name == null) {
                        $unpaid_payment->payer_name = $mollie_payment->details->consumerName;
                    }

                    // Update the record in the database
                    $unpaid_payment->paid_at = $paid_at;
                    $unpaid_payment->save();
                }
            }
        }
    }
}
