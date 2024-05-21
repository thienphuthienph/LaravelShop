@extends('front.layouts.app')

@section('content')
    @if (Session::get('success'))
        <div class="warning-success warning">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>{{ Session::get('success') }}</strong>
            </div>
    @endif

    <h1 style="text-align:center">Thank you</h1>
    <p style="text-align:center">Your order ID: {{$id}} </p>
@endsection
