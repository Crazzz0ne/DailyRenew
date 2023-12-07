@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.dashboard.title'))

@section('content')
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <h2>Current Unassigned Appointments</h2>
                    </div>
                    <div class="card-body">
                        <div class="container">
                            <div class="row">
                                @foreach($list as $lead)
                                    <div class="col-md-6 col-sm-12">
                                        <div class="card">
                                            <a href="/dashboard/lead/{{$lead->lead_id}}"
                                               style="text-decoration: none; color: inherit">
                                                <div class="card-header ">
                                                    {{ $lead->lead_id }} {{ $lead->lead->customer->last_name }}
                                                </div>
                                            </a>
                                            <div class="card-body">
                                                <p>{{ \Carbon\Carbon::parse($lead->start_time)->format('j F, Y g:i a') }}

                                                @if($lead->remote)
<br>
                                                <strong>Remote</strong>
                                                @endif
                                                </p>
                                                <a href="/dashboard/lead/{{$lead->lead_id}}/sp2-assign-appointment/{{ $lead->id }}"
                                                   style="text-decoration: none; color: inherit">
                                                    <h2>Assign Rep <i class="fa fa-address-card"></i></h2>
                                                    <p>{{ $lead->lead->office->name }}</p>
                                                </a>
                                            </div>
                                        </div>

                                    </div>
                                @endforeach
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
