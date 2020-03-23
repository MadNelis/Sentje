<?php

namespace App\Console\Commands;

use App\Payment;
use App\ScheduledPayment;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\BankAccount;
use Illuminate\Support\Facades\Auth;
use Mollie\Laravel\Facades\Mollie;

class ScheduledPaymentsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'handle:scheduledpayments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
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
