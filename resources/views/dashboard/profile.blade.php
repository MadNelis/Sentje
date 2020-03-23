@extends('layouts.app')

@section('content')
    <div class="container">
        <a href="/dashboard" class="btn btn-outline-secondary mb-3">{{ __('text.back') }}</a>
        <div class="card">
            <div class="card-body">
                <div class="card-title">
                    <h2>{{ __('text.profile') }}</h2>
                </div>
                <hr/>
                <div class="card-text">
                    @if($user->accepts_donations)
                        <h4>{{ __('text.accepting_donations') }}</h4>
                        <a class="btn btn-danger"
                           href="{{ action('DashboardController@donationSwitch') }}">{{ __('text.turn_off') }}</a>
                    @else
                        <h4>{{ __('text.not_accepting_donations') }}</h4>
                        @if($user->bank_accounts->count() > 0)
                            <a class="btn btn-success"
                               href="{{ action('DashboardController@donationSwitch') }}">{{ __('text.turn_on') }}</a>
                        @else
                            <h5>{{ __('text.add_bank_account_for_donations') }}</h5>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
