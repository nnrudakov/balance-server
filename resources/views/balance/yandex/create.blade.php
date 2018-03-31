@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Add &laquo;Яндекс.Деньги&raquo;</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('ya.add') }}">
                        @csrf

                        <div class="row justify-content-center">
                            <button type="submit" class="btn btn-primary">
                                {{ __('Connect') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
