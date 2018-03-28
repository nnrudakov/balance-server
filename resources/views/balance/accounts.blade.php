@php
/** @var App\Balance\Account[] $accounts */
@endphp

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Accounts') }}</div>

                <div class="card-body">
                    <div class="row justify-content-end">
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{ __('Add Account') }}
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" href="#">Альфа-Банк</a>
                                <a class="dropdown-item" href="{{ route('dd.add') }}">Дребеденьги</a>
                                <a class="dropdown-item" href="#">Яндекс.Деньги</a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <nav class="nav flex-column">
                            @foreach ($accounts as $account)
                                <a class="nav-link" href="/{{ $account->name }}/show">{{ $account->title }}</a>
                            @endforeach
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
