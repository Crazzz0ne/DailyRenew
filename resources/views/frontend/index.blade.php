@extends('frontend.layouts.app')

@section('title', app_name() . ' | ' . __('navs.general.home'))

@section('content')
    <div class="container">
        @include('includes.partials.messages')
        <div class="row text-center">
            <div class="col-12">
                <h1>Welcome To {{env('APP_NAME')}} Dashboard</h1>

                <a href="{{route('frontend.auth.login')}}"><button class="btn btn-primary">Login</button></a>


            </div>
        </div>
    </div>
@endsection
