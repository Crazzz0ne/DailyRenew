@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.dashboard.title'))

@section('content')
    <div class="row" id="app">
        <Partner-Links-App></Partner-Links-App>
    </div>
@endsection
