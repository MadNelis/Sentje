@extends('layouts.app')

@section('content')
    @include('partials.messages')
    <div class="container">
        <h2>Dashboard</h2>
        <div class="row">
            <div class="col-md-12">
                {{-- My requests --}}
                <div class="card border-dark mb-3">
                    <div class="card-header bg-dark text-white">
                        <h3 class="d-inline">{{ __('text.my_payment_requests') }}</h3>
                        <a href="/payment_requests/create"
                           class="btn btn-success float-right">{{ __('text.new_payment_request') }}</a>
                    </div>
                    @if($payment_requests != null)
                        <div class="card-body bg-dark">
                            <div class="card">
                                <table class="table table-striped bg-light">
                                    <tr>
                                        <th>{{ __('text.description') }}</th>
                                        <th>{{ __('text.amount') }}</th>
                                        <th>{{ __('text.currency') }}</th>
                                        <th>{{ trans_choice('text.bank_accounts', 1) }}</th>
                                        <th>{{ __('text.paid') }}</th>
                                        <th></th>
                                    </tr>
                                    @foreach($payment_requests as $payment_request)
                                        <tr>
                                            <td>{{$payment_request->description}}</td>
                                            <td>{{ decimalSeparatorConverter($payment_request->amount) }}</td>
                                            <td>{{$payment_request->currency}}</td>
                                            <td>{{$payment_request->bank_account->name}}</td>
                                            <td>{{$payment_request->times_paid}}</td>
                                            <td>
                                                <div class="d-inline-flex float-right">
                                                    <a href="/payment_requests/{{$payment_request->id}}"
                                                       class="btn btn-primary mx-1">{{ __('text.details') }}</a>
                                                    <form
                                                        action="{{ route('payment_requests.destroy', $payment_request->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger"
                                                                onclick="return confirm('{{ __('text.delete_payment_request', ['name' => $payment_request->description]) }}')">
                                                            {{ __('text.delete') }}
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
                {{-- Received requests --}}
                <div class="card px-0 border-dark mb-3">
                    <div class="card-header bg-dark text-white"><h3
                            class="d-inline align-self-center">{{ __('text.received_payment_requests') }}</h3></div>
                    <div class="card-body bg-dark">
                        <div class="card">
                            <table class="table table-striped bg-light">
                                <tr>
                                    <th>{{ __('text.description') }}</th>
                                    <th>{{ __('text.amount') }}</th>
                                    <th>{{ __('text.currency') }}</th>
                                    <th>{{ __('text.paid') }}</th>
                                    <th></th>
                                </tr>
                                @foreach($received_payment_requests as $received_payment_request)
                                    <tr>
                                        <td>{{$received_payment_request->description}}</td>
                                        <td>{{$received_payment_request->amount}}</td>
                                        <td>{{$received_payment_request->currency}}</td>
                                        <td>{{$received_payment_request->paid}}</td>
                                        <td>
                                            <div class="d-inline-flex float-right">
                                                <a href="/payment_requests/{{$received_payment_request->id}}"
                                                   class="btn btn-success mx-1">{{ __('text.pay') }}</a>
                                                <form
                                                    action="{{ action('PaymentRequestController@removeReceived', $received_payment_request->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger"
                                                            onclick="return confirm('{{ __('text.delete_payment_request', ['name' => $received_payment_request->description]) }}')">
                                                        {{ __('text.delete') }}
                                                    </button>
                                                </form>
                                            </div>

                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
                {{-- Groups and Contacts --}}
                <div class="card px-0 border-dark">
                    <div class="card-header bg-dark text-white"><h3
                            class="d-inline">{{ __('text.groups_and_contacts') }}</h3></div>
                    <div class="bg-dark">
                        <div class="row col-12">
                            {{-- Groups --}}
                            <div class="col-6 mt-3">
                                <form method="POST" action="{{ action('ContactController@storeGroup') }}">
                                    <div class="form-group row">
                                        @csrf
                                        <label class="col-sm-3 col-form-label text-white"
                                               for="title">{{ __('text.group_name') }}</label>
                                        <div class="col-sm-5">
                                            <input type="text" class="form-control" name="groupname"
                                                   placeholder="{{ __('text.group_name') }}"/>
                                        </div>
                                        <button type="submit"
                                                class="btn btn-success d-inline">{{ __('text.create') }}</button>
                                    </div>
                                </form>
                                <div class="card px-0">
                                    <table class="table table-striped table-secondary">
                                        <tr>
                                            <th>{{ __('text.group_name') }}</th>
                                            <th></th>
                                        </tr>
                                        @if($groups != null)
                                            @foreach($groups as $group)
                                                <tr>
                                                    <td>{{$group->name}}</td>
                                                    <td>
                                                        <div class="d-inline-flex float-right">
                                                            <a href="/contacts/{{ $group->id }}/edit_group"
                                                               class="btn btn-primary mx-1">{{ __('text.details') }}</a>
                                                            <form
                                                                action="{{ action('ContactController@destroyGroup', $group->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                        onclick="return confirm('{{ __('text.delete_group', ['name' => $group->name]) }}')"
                                                                        class="btn btn-danger">{{ __('text.delete') }}
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </table>
                                </div>
                            </div>
                            {{-- Contacts --}}
                            <div class="col-6 mt-3">
                                <form method="POST" action="{{ action('ContactController@addContact') }}">
                                    <div class="form-group row">
                                        @csrf
                                        <label class="col-sm-3 col-form-label text-white"
                                               for="title">{{ __('text.email') }}</label>
                                        <div class="col-sm-5">
                                            <input type="text" class="form-control" name="email"
                                                   placeholder="{{ __('text.email') }}"/>
                                        </div>
                                        <button type="submit"
                                                class="btn btn-success d-inline">{{ __('text.add') }}</button>
                                    </div>
                                </form>
                                <div class="card px-0 mb-3">
                                    <table class="table table-striped table-secondary">
                                        <tr>
                                            <th>{{ __('text.name') }}</th>
                                            <th></th>
                                        </tr>
                                        @if($contacts != null)
                                            @foreach($contacts as $contact)
                                                <tr>
                                                    <td>{{$contact->name}}</td>
                                                    <td>
                                                        <div class="d-inline-flex float-right">
                                                            <form
                                                                action="{{ action('ContactController@removeContact') }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <input type="hidden" value="{{$contact->id}}"
                                                                       name="id"/>
                                                                <button type="submit"
                                                                        onclick="return confirm('{{ __('text.delete_contact', ['name' => $contact->name]) }}')"
                                                                        class="btn btn-danger">{{ __('text.delete') }}
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
