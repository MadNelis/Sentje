@extends('layouts.app')

@section('head')
    <link href="{{ asset('css/share.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div class="container">
        <a href="/dashboard" class="btn btn-outline-secondary mb-3">{{ __('text.back') }}</a>
        <div class="card bg-dark text-white">
            <div class="card-body">
                <div>
                    <h1 class="card-title d-inline">{{ __('text.payment_request') }}</h1>
                </div>
                <hr/>
                <table class="table table-borderless col-5 text-white">
                    <tr>
                        <td class="h3"><strong>{{ __('text.description') }}</strong></td>
                        <td class="h3">{{ $payment_request->description }}</td>
                    </tr>
                    <tr>
                        <td class="h3"><strong>{{ __('text.amount') }}</strong></td>
                        <td class="h3">{{ decimalSeparatorConverter($payment_request->amount) }} {{ $payment_request->currency }}</td>
                    </tr>
                </table>
                <input class="col-8 ml-3 px-0 h5" type="text" value="{{ str_replace('share', '', url()->current()) }}"
                       readonly/>
                <hr/>
                <div class="card-text">
                    <div class="bg-dark">
                        <form action="{{ action('PaymentRequestController@shareRequest') }}" method="POST">
                            @csrf
                            <div class="row col-12">
                                {{-- Groups --}}
                                <div class="col-6 mt-3">
                                    <h4>{{ __('text.groups') }}</h4>
                                    <div class="card px-0">
                                        <table class="table table-striped table-secondary">
                                            <tr>
                                                <th>{{ __('text.name') }}</th>
                                                <th></th>
                                            </tr>
                                            @if($groups != null)
                                                @foreach($groups as $group)
                                                    <tr>
                                                        <td>{{ $group->name }}</td>
                                                        <td><input name="groups[]" type="checkbox"
                                                                   class="big-checkbox"
                                                                   value="{{ $group->id }}"/></td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </table>
                                    </div>
                                </div>
                                {{-- Contacts --}}
                                <div class="col-6 mt-3">
                                    <h4>{{ __('text.contacts') }}</h4>
                                    <div class="card px-0 mb-3">
                                        <table class="table table-striped table-secondary">
                                            <tr>
                                                <th>{{ __('text.name') }}</th>
                                                <th></th>
                                            </tr>
                                            @if($contacts != null)
                                                @foreach($contacts as $contact)
                                                    <tr>
                                                        <td>{{ $contact->name }}</td>
                                                        <td>
                                                            <input name="contacts[]" type="checkbox"
                                                                   class="big-checkbox"
                                                                   value="{{ $contact->id }}"/>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" value="{{ $payment_request->id }}" name="payment_request_id"/>
                            <button type="submit"
                                    class="btn btn-lg btn-primary float-right">{{ __('text.send') }}</button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
