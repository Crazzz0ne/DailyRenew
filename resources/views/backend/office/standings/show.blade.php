@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.dashboard.title'))

@section('content')
    <div class="container-fluid mx-0" id="app">
        @if(json_decode($standingsGroupArray) !== null)
            <Standings :standings="{{ $standingsGroupArray }}"></Standings>
        @else
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                            <span>No Standing Please Approve</span>
                        </div>
                        <div class="card-body">
                            <span>Sorry no data has been approved yet</span>

                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
