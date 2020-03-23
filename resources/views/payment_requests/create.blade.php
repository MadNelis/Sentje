@extends('layouts.app')

@section('content')
    <div class="container">
        <a href="/dashboard" class="btn btn-outline-secondary mb-3">{{ __('text.back') }}</a>
        <h2>{{ __('text.new_payment_request') }}</h2>
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card px-0 mb-3">
                    <div class="card-body">
                        <form method="POST" action="{{ route('payment_requests.store') }}">
                            <div class="form-group row">
                                @csrf
                                <label class="col-sm-2 col-form-label" for="title">{{ __('text.description') }}</label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control @error('description') is-invalid @enderror"
                                           name="description"
                                           placeholder="{{ __('text.description') }}"/>
                                    @error('description')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
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
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label"
                                       for="bank_account">{{ trans_choice('text.bank_accounts', 1) }}</label>
                                <div class="col-sm-3">
                                    <select class="form-control" name="bank_account">
                                        @foreach($bank_accounts as $bank_account)
                                            <option value="{{ $bank_account->id }}">{{$bank_account->name}}</option>
                                        @endforeach
                                    </select>
                                    @error('bank_account')
                                        <div class="alert alert-danger">
                                            <p>{{ $message }} {{ __('text.make_bank_account') }}</p>
                                            <a class="btn btn-primary" href="/bank_accounts">{{ __('text.add_bank_account') }}</a>
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">{{ __('text.next') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
