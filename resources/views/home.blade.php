@php
/** @var array $accounts */
@endphp
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($accounts)
                        <ul class="list-group">
                        @foreach ($accounts as $title => $balances)
                            <li class="list-group-item list-group-item-info">{{ $title }}
                                <ul>
                                    @foreach ($balances as $balance)
                                        <li></li>
                                    @endforeach
                                </ul>
                            </li>
                        @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
