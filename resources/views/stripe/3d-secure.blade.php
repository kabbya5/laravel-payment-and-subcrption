@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    <p class="text-red-500"> {{isset($status) ' ' . $status .' ' . $client_secret}} </p>
                    <p> You need to follow some steps with your bank to complete this payment. Let's go !</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection