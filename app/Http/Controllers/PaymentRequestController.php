<?php

namespace App\Http\Controllers;

use App\Currency;
use App\FavoritesGroup;
use App\Http\Requests\SharePaymentRequest;
use App\Http\Requests\StorePayPlanRequest;
use App\Payment;
use App\PaymentRequest;
use App\PayPlan;
use App\ScheduledPayment;
use App\User;
use App\Http\Requests\StorePaymentRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mollie\Laravel\Facades\Mollie;
use Symfony\Component\Console\Input\Input;

class PaymentRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['show', 'pay', 'donate']);
    }

    public function create()
    {
        $user_id = Auth::id();
        $user = User::find($user_id);
        $bank_accounts = $user->bank_accounts;
        $currencies = Currency::all();

        return view('payment_requests.create')
            ->with('bank_accounts', $bank_accounts)
            ->with('currencies', $currencies);
    }

    public function store(StorePaymentRequest $request)
    {
        $request->validated();

        $payment_request = new PaymentRequest();
        $payment_request->description = $request->input('description');
        $payment_request->amount = $request->input('amount');
        $payment_request->currency = $request->input('currency');
        $payment_request->bank_account_id = $request->input('bank_account');
        $payment_request->save();

        $payment_request_id = $payment_request->id;

        return redirect('/payment_requests/' . $payment_request_id);
    }

    public function show($id)
    {
        $payment_request = PaymentRequest::find($id);
        $payments = $payment_request->payments->whereNotNull('paid_at');

        return view('payment_requests.show')->with('payment_request', $payment_request)
            ->with('payments', $payments);
    }

    public function share($id)
    {
        $user_id = Auth::id();
        $user = User::find($user_id);
        $payment_request = PaymentRequest::find($id);

        if (!$user->payment_requests->contains($payment_request)) {
            return redirect('/dashboard')->with('error', __('text.not_your_payment_request'));
        }

        $groups = $user->groups;
        $contacts = $user->contacts;

        return view('payment_requests.share')
            ->with('payment_request', $payment_request)
            ->with('groups', $groups)
            ->with('contacts', $contacts);
    }

    public function shareRequest(SharePaymentRequest $request)
    {
        $request->validated();

        $payment_request_id = $request->input('payment_request_id');
        $payment_request = PaymentRequest::find($payment_request_id);

        // contact_ids will function as the full list of users to send the request too
        $contact_ids = $request->input('contacts');
        $group_ids = $request->input('groups');

        // Get the groups and merge them
        if ($group_ids != null) {
            $groups = FavoritesGroup::find($group_ids[0])->get();
            for ($i = 1; $i < count($group_ids); $i++) {
                $groups->merge(FavoritesGroup::find($group_ids[$i])->get());
            }

            // In case no contacts have been selected
            if ($contact_ids == null) {
                $contact_ids = array();
            }

            // Take all the member id's and add them to the contact_ids
            foreach ($groups as $group) {
                foreach ($group->members as $member) {
                    array_push($contact_ids, $member->id);
                }
            }
        }

        if ($contact_ids == null) {
            return redirect('/payment_requests/' . $payment_request_id . '/share')
                ->with('error', __('text.nobody_in_group_error'));
        }

        // Take out the ones that are already in there (in a group and a contact)
        $contact_ids = array_unique($contact_ids);

        // Send the request out
        foreach ($contact_ids as $contact_id) {
            $user = User::find($contact_id);
            if (!$user->received_payment_requests->contains($payment_request)) {
                $user->received_payment_requests()->attach($payment_request_id);
            }
        }

        return redirect('/dashboard')->with('success', __('text.payment_request_sent'));
    }

    public function destroy($id)
    {
        $payment_request = PaymentRequest::find($id);

        $user_id = Auth::id();
        $user = User::find($user_id);
        $bank_accounts = $user->bank_accounts;

        // Check if payment request is mine
        if (!$bank_accounts->contains($payment_request->bank_account)) {
            return redirect('/dashboard')->with('error', __('text.unauthorized'));
        }

        // Check if payment request isn't paid
        if ($payment_request->payments->first() != null) {
            return redirect('/dashboard')->with('error', __('text.cannot_delete_paid_request'));
        }

        $payment_request->delete();
        return redirect('/dashboard')->with('success', __('text.payment_request_deleted'));
    }

    public function removeReceived($id)
    {
        $user_id = Auth::id();
        $user = User::find($user_id);

        $user->received_payment_requests()->detach($id);

        return back();
    }

    public function pay(Request $request, $id)
    {
        $this->validate($request, [
            'image' => 'image|nullable|max:1999',
        ]);

        // Get the paymentrequest this payment is for
        $paymentrequest = PaymentRequest::find($id);

        // Handle image
        if ($request->hasFile('image')) {
            $filenameWithExt = $request->file('image')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('image')->getClientOriginalExtension();
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;
            $path = $request->file('image')->storeAs('public/images', $fileNameToStore);
        }

        // Convert currency if it's USD or GBP
        $amount = convertCurrency($paymentrequest->amount, $paymentrequest->currency);

        // Create Mollie payment
        $molliepayment = Mollie::api()->payments()->create([
            'amount' => [
                'currency' => 'EUR',
                'value' => $amount,
            ],
            'description' => $paymentrequest->description,
            'redirectUrl' => route('dashboard'),
        ]);
        $molliepayment = Mollie::api()->payments()->get($molliepayment->id);

        // Create 'own' payment
        $payment = new Payment();
        $payment->description = $molliepayment->description;
        $payment->amount = $molliepayment->amount->value;
        $payment->currency = $molliepayment->amount->currency;
        $payment->payment_id = $molliepayment->id;
        $payment->bank_account_id = $paymentrequest->bank_account_id;
        $payment->note = $request->input('note');
        if (isset($fileNameToStore)) {
            $payment->image_path = $fileNameToStore;
        }

        // Set name if user is logged in
        if (Auth::check()) {
            $payment->user_id = Auth::id();
            $payment->payer_name = Auth::user()->name;
        }

        $payment->payment_request_id = $id;
        $payment->save();

        // Redirect customer to Mollie checkout page
        return redirect($molliepayment->getCheckoutUrl(), 303);
    }

    public function donate(Request $request)
    {
        $this->validate($request, [
            'amount' => 'required|digits_between:0.01,10000.00',
            'note' => 'max:255',
        ]);

        $user_id = $request->input('user_id');
        $user = User::find($user_id);

        $amount = $request->input('amount');
        $currency = $request->input('currency');
        $amount = convertCurrency($amount, $currency);
        $note = $request->input('note');

        // Create Mollie payment
        $molliepayment = Mollie::api()->payments()->create([
            'amount' => [
                'currency' => 'EUR',
                'value' => $amount,
            ],
            'description' => 'Donation',
            'redirectUrl' => route('dashboard'),
        ]);
        $molliepayment = Mollie::api()->payments()->get($molliepayment->id);

        // Create 'own' payment
        $payment = new Payment();
        $payment->description = $molliepayment->description;
        $payment->amount = $molliepayment->amount->value;
        $payment->currency = $molliepayment->amount->currency;
        $payment->payment_id = $molliepayment->id;
        $payment->bank_account_id = $user->bank_accounts->first()->id;
        $payment->note = $note;
        $payment->save();

        return redirect($molliepayment->getCheckoutUrl(), 303);
    }

    public function payPlan($id, $nrOfDates = 1)
    {
        $payment_request = PaymentRequest::find($id);
        $hasMandate = false;

        // Check if user has valid mandate (to determine whether an initial payment is necessary)
        $user_id = Auth::id();
        $user = User::find($user_id);
        if ($user->mollie_id) {
            $customer = Mollie::api()->customers()->get($user->mollie_id);
            $mandates = Mollie::api()->mandates()->listFor($customer);
            foreach ($mandates as $mandate) {
                if ($mandate->status == 'valid') {
                    $hasMandate = true;
                    break;
                }
            }
        }

        $nrOfDates = $nrOfDates > 9 ? 10 : $nrOfDates;
        $nrOfDates = $nrOfDates < 1 ? 1 : $nrOfDates;

        return view('payment_requests.pay_plan')
            ->with('payment_request', $payment_request)
            ->with('nrOfDates', $nrOfDates)
            ->with('hasMandate', $hasMandate);
    }

    public function payPlanConfirm(StorePayPlanRequest $request, $id)
    {
        $request->validated();

        $hasMandate = $request->input('hasMandate');

        // Get the paymentrequest this payment is for
        $paymentrequest = PaymentRequest::find($id);

        $dates = $request->input('dates');

        $user_id = Auth::id();
        // Create pay plan
        $pay_plan = new PayPlan();
        $pay_plan->nr_of_payments = count($dates) + !$hasMandate;
        $pay_plan->payment_request_id = $paymentrequest->id;
        $pay_plan->user_id = $user_id;
        $pay_plan->save();

        $redirect = redirect('/dashboard');
        // The initial payment if user doesn't have a mandate
        if (!$hasMandate) {
            $redirect = $this->payPlanFirstPayment($paymentrequest, $pay_plan);
        }

        // Create scheduled payments for each date
        foreach ($dates as $date) {
            $scheduled_payment = new ScheduledPayment();
            $scheduled_payment->date = $date;
            $scheduled_payment->pay_plan_id = $pay_plan->id;
            $scheduled_payment->save();
        }

        return $redirect;
    }

    private function payPlanFirstPayment($payment_request, $pay_plan)
    {
        $initial_payment = new ScheduledPayment();
        $initial_payment->date = now();
        $initial_payment->pay_plan_id = $pay_plan->id;
        $initial_payment->save();

        $user_id = Auth::id();
        $user = User::find($user_id);
        $customer = Mollie::api()->customers()->create([
            'name' => $user->name,
            'email' => $user->email,
        ]);
        $user->mollie_id = $customer->id;
        $user->save();

        $value = $payment_request->amount / $pay_plan->nr_of_payments;
        $value = convertCurrency($value, $payment_request->currency);

        $mollie_payment = Mollie::api()->payments()->create([
            'amount' => [
                'currency' => 'EUR',
                'value' => $value,
            ],
            'customerId' => $customer->id,
            'sequenceType' => 'first',
            'description' => $payment_request->description . ' | Pay Plan Initial',
            'redirectUrl' => route('dashboard'),
        ]);

        // Make 'own' payment record
        $payment = new Payment();
        $payment->description = $mollie_payment->description;
        $payment->amount = $mollie_payment->amount->value;
        $payment->currency = $mollie_payment->amount->currency;
        $payment->type = 2;
        $payment->payment_id = $mollie_payment->id;
        $payment->bank_account_id = $payment_request->bank_account_id;
        $payment->user_id = $user_id;
        $payment->payment_request_id = $payment_request->id;
        $payment->save();

        // Link payment to initial payment
        $initial_payment->payment()->associate($payment->id);
        $initial_payment->save();

        return redirect($mollie_payment->getCheckoutUrl(), 303);
    }

    // FOR TESTING
    public function forcePay($id)
    {
        // Loop over all scheduled payments and debit the ones of today
        $scheduled_payments = ScheduledPayment::where([
            ['date', Carbon::today()],
            ['payment_id', null]
        ])->get();

        foreach ($scheduled_payments as $scheduled_payment) {
            $pay_plan = $scheduled_payment->pay_plan;
            $payment_request = $pay_plan->payment_request;

            // Convert currency if it's USD or GBP
            $amount = convertCurrency($payment_request->amount, $payment_request->currency);
            // Divide by nr of payments that need to be done in total
            $amount = number_format((float)($amount / $pay_plan->nr_of_payments), 2);
            $amount = str_replace(',', '', $amount);

            error_log($amount);

            $user = $pay_plan->user;
            $customer = Mollie::api()->customers()->get($user->mollie_id);
            $mollie_payment = Mollie::api()->payments()->create([
                'amount' => [
                    'currency' => 'EUR',
                    'value' => $amount,
                ],
                'customerId' => $customer->id,
                'sequenceType' => 'recurring',
                'description' => $payment_request->description . ' | Direct Charge',
            ]);

            $payment = new Payment();
            $payment->description = $mollie_payment->description;
            $payment->amount = $mollie_payment->amount->value;
            $payment->currency = $mollie_payment->amount->currency;
            $payment->type = 2;
            $payment->payment_id = $mollie_payment->id;
            $payment->bank_account_id = $payment_request->bank_account_id;
            $payment->user_id = $user->id;
            $payment->payment_request_id = $payment_request->id;
            $payment->save();

            $scheduled_payment->payment()->associate($payment->id);
            $scheduled_payment->save();
        }
    }
}
