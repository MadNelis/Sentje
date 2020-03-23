@extends('layouts.app')

@section('content')
    <div class="container">
        <a href="/dashboard" class="btn btn-outline-secondary mb-3">{{ __('text.back') }}</a>
        <div class="card bg-dark text-white">
            <div class="card-body">
                <div>
                    <h1 class="card-title d-inline">{{ __('text.payment_request') }}</h1>
                    @Auth
                        @if(Auth::user()->payment_requests->contains($payment_request))
                            {{-- Share --}}
                            <a class="btn btn-lg btn-primary float-right"
                               href="/payment_requests/{{ $payment_request->id }}/share">{{ __('text.share_payment_request') }}</a>
                        @endif
                    @endauth
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
                {{-- List of payers --}}
                @auth()
                    @if(Auth::user()->payment_requests->contains($payment_request))
                        <div class="card-text">
                            <h4>{{ __('text.paid_by') }}</h4>
                            @if($payments !=  null)
                                <ul class="list-group">
                                    @foreach($payments as $payment)
                                        <li class="list-group-item list-group-item-dark">
                                            <p>{{ $payment->payer_name }}</p>
                                            <div class="d-flex flex-row">
                                                @if($payment->image_path != null)
                                                    <div class="col-3">
                                                        <img class="img-fluid"
                                                             src="/storage/images/{{$payment->image_path}}"
                                                             style="max-height: 250px">
                                                    </div>
                                                @endif
                                                @if($payment->note != null)
                                                    <div class="col-6">
                                                        <div class="card bg-secondary text-white">
                                                            <div class="card-body">
                                                                <p>{{ __('text.note') }}</p>
                                                                <p>{{ $payment->note }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @endif
                @endauth
                {{-- Payment form with note and image upload --}}
                @if(!Auth::check() || !Auth::user()->payment_requests->contains($payment_request))
                    <form class="d-inline" method="POST" action="{{ action('PaymentRequestController@pay', $payment_request->id) }}"
                          enctype="multipart/form-data">
                        <div class="form-group row">
                            @csrf
                            <label class="col-sm-2 col-form-label" for="note">{{ __('text.note') }}</label>
                            <div class="col-sm-5">
                                <textarea class="form-control" name="note" placeholder="{{ __('text.note') }}"
                                          maxlength="255"></textarea>
                            </div>
                        </div>
                        @auth()
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label" for="image">{{ __('text.image') }}</label>
                                <div class="col-sm-5">
                                    <input type="file" accept="image/*" class="form-control-file" name="image">
                                </div>
                            </div>
                        @endauth
                        <button type="submit" class="btn btn-primary">{{ __('text.to_payment') }}</button>
                    </form>
                    <a class="btn btn-primary float-right" href="/payment_requests/{{ $payment_request->id }}/pay_plan/1">{{ __('text.pay_in_parts') }}</a>
                @endif
            </div>
        </div>
    </div>
@endsection
