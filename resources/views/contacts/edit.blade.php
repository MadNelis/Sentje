@extends('layouts.app')

@section('content')
    @include('partials.messages')
    <div class="container">
        <a href="/dashboard" class="btn btn-outline-secondary mb-3">{{ __('text.back') }}</a>
        <div class="card bg-dark text-white">
            <div class="card-header"><h3>{{ $group->name }}</h3></div>
            <div class="card-body bg-dark">
                <form method="POST" action="{{ action('ContactController@addUserToGroup') }}">
                    <div class="form-group row">
                        @csrf
                        <label class="col-sm-2 col-form-label text-white" for="title">{{ __('text.email') }}</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" name="email" placeholder="Emailadres"/>
                        </div>
                        <input type="hidden" value="{{ $group->id }}" name="id"/>
                        <button type="submit" class="btn btn-success d-inline">{{ __('text.add') }}</button>
                    </div>
                </form>
                <div class="card col-7 px-0">
                    <table class="table table-striped table-secondary">
                        <tr>
                            <th>{{ __('text.name') }}</th>
                            <th>{{ __('text.email') }}</th>
                            <th></th>
                        </tr>
                        @if($group->members != null)
                            @foreach($group->members as $member)
                                <tr>
                                    <td>{{ $member->name }}</td>
                                    <td>{{ $member->email }}</td>
                                    <td>
                                        <form
                                            action="{{ action('ContactController@removeUserFromGroup') }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" value="{{ $member->id }}" name="user_id"/>
                                            <input type="hidden" value="{{ $group->id }}" name="group_id"/>
                                            <button type="submit" class="btn btn-danger float-right">{{ __('text.delete') }}</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection
