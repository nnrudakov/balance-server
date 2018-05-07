@php
/** @var stdClass $balance */
/** @var \stdClass[] $transactions */
/** @var \Carbon\Carbon $sync_date */
@endphp

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">&laquo;Яндекс.Деньги&raquo;</div>

                <div class="card-body">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td>{{ $balance->account }}</td>
                                <td class="text-success">{{ $balance->formatted }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <h3 class="mt-5">The last synchronization date</h3>
                    <div>
                        @if ($sync_date)
                            {{ $sync_date->diffForHumans() }} ({{ $sync_date->format('d.m.Y H:i') }})
                        @else
                            No synchronization yet.
                        @endif</div>
                    <h3 class="mt-5">The last transactions</h3>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>When</th>
                                <th>Where</th>
                                <th>How much</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($transactions->operations as $transaction)
                            <tr>
                                <td>{{ Carbon\Carbon::parse($transaction->datetime, new \DateTimeZone('Europe/Moscow'))->addRealHour(3)->format('d.m.Y H:i') }}</td>
                                <td>{{ $transaction->title }}</td>
                                <td class="@if ($transaction->direction === App\Balance\AccountYandex::TYPE_OUT) text-info @else text-success @endif">@if ($transaction->direction === 'out') &minus;&nbsp;@else &plus;&nbsp;@endif{{ $transaction->formatted }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
