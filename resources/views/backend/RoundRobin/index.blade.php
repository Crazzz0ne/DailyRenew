@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.dashboard.title'))

@section('content')
    <Round-Robin-App></Round-Robin-App>
@endsection
