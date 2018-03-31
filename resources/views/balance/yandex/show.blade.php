@php
/** @var stdClass $balance */
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
                                <td>{{ $balance->formatted }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
