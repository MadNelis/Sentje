@extends('layouts.app')

@section('content')
    <div class="container">
        <a href="/bank_accounts" class="btn btn-outline-secondary mb-3">{{ __('text.back') }}</a>
        <a href="/bank_accounts/{{ $bank_account->id }}/download" class="btn btn-primary mb-3 float-right">{{ __('text.download_overview') }}</a>
        <div class="row">
            <div class="col-md-12">
                <div class="card border-dark mb-3">
                    {{-- Name and Balance --}}
                    <div class="card-header bg-dark text-white">
                        <h3 class="d-inline">{{ $bank_account->name }}</h3>
                        <h3 class="d-inline float-right">{{ __('text.balance') . ': € ' . $balance }}</h3>
                    </div>
                    {{-- Payments --}}
                    <div>
                        @foreach($payments as $payment)
                            <div class="card bg-light m-3">
                                <div class="card-body row">
                                    <div class="d-flex flex-column col-7">
                                        <table class="table">
                                            <tr>
                                                <td class="m-1">{{ __('text.payer') }}</td>
                                                <td class="m-1">{{ $payment->payer_name }}</td>
                                            </tr>
                                            <tr>
                                                <td class="m-1">{{ __('text.description') }}</td>
                                                <td class="m-1">{{ $payment->description }}</td>
                                            </tr>
                                            <tr>
                                                <td class="m-1">{{ __('text.amount') }}</td>
                                                <td class="m-1">{{ decimalSeparatorConverter($payment->amount) }} {{ $payment->currency }}</td>
                                            </tr>
                                            <tr>
                                                <td class="m-1">{{ __('text.paid_at') }}</td>
                                                <td class="m-1">{{ dateConverter($payment->paid_at) }}</td>
                                            </tr>
                                            <tr>
                                                <td class="m-1">{{ __('text.note') }}</td>
                                                <td class="m-1 col-6">{{ $payment->note }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    {{ $payments->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
