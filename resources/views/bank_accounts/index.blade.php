@extends('layouts.app')

@section('content')
    @include('partials.messages')
    <div class="container">
        <a href="/dashboard" class="btn btn-outline-secondary mb-3">{{ __('text.back') }}</a>
        <div class="row">
            <div class="col-md-12">
                {{-- My bank accounts --}}
                <div class="card border-dark mb-3">
                    <div class="card-header bg-dark text-white pr-2">
                        <h3 class="d-inline">{{ trans_choice('text.bank_accounts', 2) }}</h3>
                        <form method="POST"
                              action="{{ action('BankAccountController@addBankAccount') }}"
                              class="d-inline">
                            <div class="d-inline-flex float-right">
                                @csrf
                                <div class="col-sm-4 px-0">
                                    <input type="text" class="form-control" name="bank_account_name"
                                           placeholder="{{ __('text.name') }}" />
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" name="iban"
                                           placeholder="IBAN" />
                                </div>
                                <button type="submit"
                                        class="btn btn-success">{{ __('text.add_bank_account') }}</button>
                            </div>
                        </form>
                    </div>
                    <ul class="list-group">
                        @if($bank_accounts != null)
                            @foreach($bank_accounts as $bank_account)
                                <li class="list-group-item d-flex flex-row">
                                    <div class="d-flex flex-row col-10">
                                        <h5 class="col-3">{{ $bank_account->name }}</h5>
                                        <h5 class="col-4">{{ $bank_account->getIban() }}</h5>
                                        <h5> €{{ $bank_account->balance }}</h5>
                                    </div>
                                    <div class="d-inline-flex float-right">
                                        <a href="/bank_accounts/{{$bank_account->id}}"
                                           class="btn btn-primary mx-1">{{ __('text.details') }}</a>
                                        <form
                                            action="{{ action('BankAccountController@destroy', $bank_account->id) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger"
                                                    onclick="return confirm('{{ __('text.delete_bank_account', ['name' => $bank_account->name]) }}')">
                                                {{ __('text.delete') }}
                                            </button>
                                        </form>
                                    </div>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
