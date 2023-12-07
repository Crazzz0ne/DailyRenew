@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.dashboard.title'))

@section('content')
    <div class="row" id="app">
        <Vendors permission="{{ json_encode(auth()->user()->roles[0]) }}"></Vendors>
    </div>
@endsection
