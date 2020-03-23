@extends('layouts.app')

@section('content')
    @include('partials.array_messages')
    <div class="container">
        <a href="/payment_requests/{{ $payment_request->id }}" class="btn btn-outline-secondary mb-3">{{ __('text.back') }}</a>
        <h2>{{ __('text.pay_plan') }} {{ __('text.for') }} {{ $payment_request->description }}</h2>
        <h2>{{ __('text.amount') }} {{ $payment_request->amount }} {{ $payment_request->currency }}</h2>
        <div class="row">
            <div class="card col-12">
                <div class="card-body">
                    <form method="POST"
                          action="{{ action('PaymentRequestController@payPlanConfirm', $payment_request->id) }}">
                        @csrf
                        <div class="d-flex row mb-3">
                            <div class="col-8">
                                <h3>{{ __('text.pick_dates') }}</h3>
                                @if(!$hasMandate)
                                    <h5>{{ __('text.first_payment_needs_to_be_done_now') }}</h5>
                                @endif
                            </div>
                            <h3 class="float-right">{{ number_format((float)($payment_request->amount / ($nrOfDates + !$hasMandate)), 2) }} {{ $payment_request->currency }}
                                {{ __('text.each_payment') }}</h3>
                        </div>
                        @for($i = 0; $i < $nrOfDates; $i++)
                            <input class="p-1 my-1" name="dates[]" type="date">
                        @endfor
                        <input name="hasMandate" type="hidden" value="{{ $hasMandate }}">
                        @if(!$hasMandate)
                            <button type="submit"
                                    class="btn btn-primary float-right m-1">{{ __('text.to_payment') }}</button>
                        @else
                            <button type="submit"
                                    class="btn btn-primary float-right m-1">{{ __('text.schedule_payments') }}</button>
                        @endif


                    </form>
                    <a class="btn btn-primary float-left m-1"
                       href="/payment_requests/{{ $payment_request->id }}/pay_plan/{{ $nrOfDates-1 }}">Remove
                        datepicker</a>
                    <a class="btn btn-primary float-left m-1"
                       href="/payment_requests/{{ $payment_request->id }}/pay_plan/{{ $nrOfDates+1 }}">Add
                        datepicker</a>
                    <a class="btn btn-primary float-left m-1"
                       href="/payment_requests/{{ $payment_request->id }}/force_pay">TEST FORCED PAYMENT</a>
                </div>
            </div>
        </div>
    </div>
@endsection
