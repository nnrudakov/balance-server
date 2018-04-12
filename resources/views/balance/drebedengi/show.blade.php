@php
/** @var array $balances */
/*
    [
        [sum] => 13500
        [currency_id] => 186209
        [place_id] => 2163856
        [date] => 2018-03-28
        [is_for_duty] => f
        [is_credit_card] => f
        [description] =>
        [place_name] => Наличные
        [currency_name] => EUR
        [currency_default] => f
        [parent_id] => -1
        [sort] => 2163856
        [formatted] => 135.23 $
    ]
 */
@endphp

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">&laquo;Дребеденьги&raquo;</div>

                <div class="card-body">
                    <div class="row justify-content-end">
                        <form action="{{ route('dd.update') }}" method="post">
                            @csrf
                            <button type="submit" class="btn btn-primary">Update</button>
                        </form>
                    </div>
                    <div class="row mt-3">
                        <table class="table">
                            <tbody>
                            @foreach ($balances as $balance)
                                <tr>
                                    <td>{{ $balance['place_name'] }}</td>
                                    <td>{{ $balance['formatted'] }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
