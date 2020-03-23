<?php

namespace App\Http\Controllers;

use App\BankAccount;
use App\User;
use Facade\IgnitionContracts\Tests\SolutionTest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Mollie\Laravel\Facades\Mollie;
use Illuminate\Support\Facades\Crypt;

class BankAccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user_id = Auth::id();
        $user = User::find($user_id);
        $bank_accounts = $user->bank_accounts;

        $this->checkDonations($bank_accounts);

        foreach ($bank_accounts as $bank_account) {
            $balance = 0;
            $payments = $bank_account->payments->where('paid_at', '!=', null);
            foreach ($payments as $payment) {
                $balance += $payment->amount;
            }
            $balance = number_format((float)$balance, 2);
            $bank_account->balance = decimalSeparatorConverter($balance);
        }

        return view('bank_accounts.index')->with('bank_accounts', $bank_accounts);
    }

    public function show($id)
    {
        $bank_account = BankAccount::find($id);

        $user = Auth::user();

        // Check if bank account is mine
        if (!$user->bank_accounts->contains($bank_account)) {
            return redirect('/dashboard')->with('error', __('text.unauthorized'));
        }

        $balance = 0;
        $payments = $bank_account->payments->where('paid_at', '!=', null)->sortByDesc('paid_at')->paginate(10);
        foreach ($payments as $payment) {
            $balance += $payment->amount;
        }
        $balance = number_format((float)$balance, 2);
        $balance = decimalSeparatorConverter($balance);

        $myfile = fopen("C:\\xampp\\htdocs\\Sentje3\\testfile.json", "w");
        $data = $bank_account->toJson(JSON_PRETTY_PRINT);
        fwrite($myfile, $data);
        fclose($myfile);

        return view('bank_accounts.show')
            ->with('bank_account', $bank_account)
            ->with('payments', $payments)
            ->with('balance', $balance);
    }

    public function downloadBankAccountOverview($id)
    {
        $bank_account = BankAccount::find($id);

        $user = Auth::user();

        // Check if bank account is mine
        if (!$user->bank_accounts->contains($bank_account)) {
            return redirect('/dashboard')->with('error', __('text.unauthorized'));
        }

        $myfile = fopen("Bank_Account_Overview.json", "w");
        $data = $bank_account->toJson(JSON_PRETTY_PRINT);
        fwrite($myfile, $data);
        fclose($myfile);

        return response()->download("Bank_Account_Overview.json")->deleteFileAfterSend();
    }

    public function addBankAccount(Request $request)
    {
        $this->validate($request, [
            'bank_account_name' => 'required',
            'iban' => 'required',
        ]);

        $bank_account_name = $request->input('bank_account_name');
        $iban = $request->input('iban');

        $user = Auth::user();

        $bank_account = new BankAccount();
        $bank_account->name = $bank_account_name;
        $bank_account->iban = Crypt::encryptString($iban);

        $bank_account->user()->associate($user);
        $bank_account->save();

        return redirect('/bank_accounts');
    }

    public function destroy($id)
    {
        $bank_account = BankAccount::find($id);

        $user = Auth::user();

        // Check if bank account is mine
        if (!$user->bank_accounts->contains($bank_account)) {
            return redirect('/dashboard')->with('error', __('text.unauthorized'));
        }

        // Check if there aren't any payment requests for this bank account
        if ($bank_account->payment_requests->count() > 0) {
            return redirect('/bank_accounts')->with('error', __('text.cannot_delete_bank_account_with_payment_requests'));
        }

        // Check if there haven't been any payments to the bank account
        if ($bank_account->payments->where('paid_at', '!=', null)->count() > 0) {
            return redirect('/bank_accounts')->with('error', __('text.cannot_delete_bank_account_with_payments'));
        }

        $bank_account->delete();

        // Check if any bank accounts are left, otherwise turn off donations
        if ($user->bank_accounts()->get()->count() < 1) {
            $user->accepts_donations = false;
            $user->save();
        }

        return redirect('/bank_accounts')->with('success', __('text.bank_account_deleted'));
    }

    private function checkDonations($bank_accounts)
    {
        foreach ($bank_accounts as $bank_account) {
            $payments = $bank_account->payments
                ->where('paid_at', null)
                ->where('payment_id', '!=', null);

            foreach ($payments as $payment) {
                $mollie_payment = Mollie::api()->payments()->get($payment->payment_id);
                if ($mollie_payment->status == 'paid' || $mollie_payment->status == 'authorized') {
                    $mollie_paid_at_date = $mollie_payment->paidAt;

                    // Format the Mollie date to sql datetime
                    $paid_at = str_replace('T', ' ', $mollie_paid_at_date);
                    $paid_at = substr($paid_at, 0, -6);

                    // Update name if null
                    if ($mollie_payment->details != null && $payment->payer_name == null) {
                        $payment->payer_name = $mollie_payment->details->consumerName;
                    }

                    // Update the record in the database
                    $payment->paid_at = $paid_at;
                    $payment->save();
                }
            }
        }
    }
}
