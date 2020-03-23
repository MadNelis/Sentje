@extends('layouts.app')

@section('content')
    <div class="container">
        <a href="/dashboard" class="btn btn-outline-secondary mb-3">{{ __('text.back') }}</a>

        <div class="card">
            <div class="card-body">
                <div class="card-title">
                    <h2>{{ $user->name }}'s {{ __('text.donation_page') }}</h2>
                </div>
                <hr/>
                <div class="card-text">
                    <form method="POST" action="{{ action('PaymentRequestController@donate') }}">
                        <div class="form-group row">
                            @csrf
                            <label class="col-sm-2 col-form-label" for="note">{{ __('text.note') }}</label>
                            <div class="col-sm-5">
                                <textarea class="form-control" name="note" placeholder="{{ __('text.note') }}"
                                          maxlength="255"></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label" for="amount">{{ __('text.amount') }}</label>
                            <div class="col-sm-2">
                                <input type="number" step="any" max="10000"
                                       class="form-control @error('amount') is-invalid @enderror" name="amount"
                                       placeholder="{{ __('text.amount') }}"/>
                                @error('amount')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label" for="currency">{{ __('text.currency') }}</label>
                            <div class="col-sm-2">
                                <select class="form-control" name="currency">
                                    @foreach($currencies as $currency)
                                        <option value="{{ $currency->currency }}">{{ $currency->currency }}</option>
                                    @endforeach

                                </select>
                            </div>
                        </div>
                        <input name="user_id" type="hidden" value="{{ $user->id }}" />
                        <button type="submit" class="btn btn-success">{{ __('text.pay') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
