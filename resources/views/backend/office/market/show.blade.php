@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.dashboard.title'))

@section('content')
    <div class="row justify-content-center">
        <div class="col col-sm-8 align-self-center">
            <div class="card">
                <div class="card-header">
                    <h3 class="pt-2">{{ $office->name }}</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-3 col ">
                            <span><b>Address:</b></span>
                        </div>
                        <div class=" col-sm-4 ccol-md-6">
                            <span>{{ $office->address }}</span><br>
                            <span>{{ $office->city }}, {{ $office->state }} {{ $office->zipCode }}</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3  col ">
                            <span><b>Phone:</b></span>
                        </div>
                        <div class="col-sm-4 col-md-6">
                            <span><a href="tel:{{ $office->phone_number }}">{{ $office->phone_number }}</a></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3 col-md-3 ">
                            <span><b>Email:</b></span>
                        </div>
                        <div class="col-sm-4 col-md-6">
                            <span><a href="mailto:{{ $office->email }}">{{ $office->email }}</a></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if (!isset(auth()->user()->office))
        @if($office->id == auth()->user()->office[0] || auth()->user()->roles[0]->name == 'executive')
            <div class="card">
                <div class="card-header">
                    Roster
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($officeUser[0]->user as $user)
                            <div class="col-md-3 col-sm-12">
                                <div class="card">
                                    <div
                                        class="card-header text-body text-capitalize">
                                        {{ $user->first_name }} {{ $user->last_name }}
                                    </div>
                                    <div class="card-body">
                                        <p> {{ ($user->phone_number) }} </p>
                                        <br>
                                        <a class="btn btn-third btn-xlg mt-2 text-white"
                                           href="tel:{{ $user->phone_number }}">
                                            Click to Call <i class="fas fa-phone-square-alt ml-2"></i>
                                        </a>
                                        <a class="btn btn-success btn-xlg mt-2 text-white"
                                           href="sms:{{ $user->phone_number }}">
                                            Click to text <i class="fas fa-sms ml-2"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    @endif

@endsection
