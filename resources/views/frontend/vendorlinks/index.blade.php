@extends('frontend.layouts.app')

@section('title', app_name() . ' | ' . __('labels.frontend.contact.box_title'))

@section('content')
    <div class="row justify-content-center align-items-center">
        <Vendors permission="{{ json_encode(auth()->user()->roles[0]) }}"></Vendors>
    </div>
@endsection
