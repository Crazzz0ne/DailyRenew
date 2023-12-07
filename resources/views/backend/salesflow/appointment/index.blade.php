@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.dashboard.title'))
@include('includes.partials.javascript')
@section('content')
    <h3 class="page-title">Appointments</h3>
    @can('appointment_create')
        <p>
            {{ route('admin.appointments.create') }}
            <a href="#"
               class="btn btn-success">Create</a>

        </p>
    @endcan

    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.css'/>

    <div id='calendar'></div>

    <br/>

    <div class="panel panel-default">
        <div class="panel-heading">
            List
        </div>

        <div class="panel-body table-responsive">
            {{--            @can('appointment_delete')--}}
            dt-select
            <table
                class="table table-bordered table-striped {{ count($appointments) > 0 ? 'datatable' : '' }} dt-select">
                <thead>
                <tr>
                    {{--                    @can('appointment_delete')--}}
                    <th style="text-align:center;"><input type="checkbox" id="select-all"/></th>
                    {{--                    @endcan--}}

                    <th>Client</th>
                    <th>Last Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Employee</th>
                    <th>Last Name</th>
                    <th>Start Time</th>
                    <th>Finish time</th>
                    <th>Comments</th>
                    <th>&nbsp;</th>
                </tr>
                </thead>

                <tbody>
                {{--                {{ dd($appointment->lead) }}--}}
                @if (count($appointments) > 0)
                    @foreach ($appointments as $appointment)
                        <tr data-entry-id="{{ $appointment->id }}">
                            {{--                            @can('appointment_delete')--}}
                            <td></td>
                            {{--                            @endcan--}}

                            <td>{{ $appointment->lead->customer->first_name or '' }}</td>
                            <td>{{ isset($appointment->lead->customer) ? $appointment->lead->customer->last_name : '' }}</td>
                            <td>{{ isset($appointment->customer) ? $appointment->customer->phone : '' }}</td>
                            <td>{{ isset($appointment->customer) ? $appointment->customer->email : '' }}</td>
                            <td>{{ $appointment->user->first_name or '' }}</td>
                            <td>{{ isset($appointment->user) ? $appointment->user->last_name : '' }}</td>
                            <td>{{ $appointment->start_time }}</td>
                            <td>{{ $appointment->finish_time }}</td>
                            <td>{!! $appointment->subject !!}</td>
                            <td>
                                @can('appointment_view')
                                    <a href="{{ route('admin.appointments.show',[$appointment->id]) }}"
                                       class="btn btn-xs btn-primary">View</a>
                                @endcan
                                @can('appointment_edit')
                                    <a href="{{ route('admin.appointments.edit',[$appointment->id]) }}"
                                       class="btn btn-xs btn-info">Edit</a>
                                @endcan
                                @can('appointment_delete')
                                    {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('Are you Sure?');",
                                        'route' => ['dashboard.salesFlow.appointments.destroy', $appointment->id])) !!}
                                    {!! Form::submit('Delete', array('class' => 'btn btn-xs btn-danger')) !!}
                                    {!! Form::close() !!}
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="9">No entries in table</td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('javascript')
    <script>
        @can('appointment_delete')
            window.route_mass_crud_entries_destroy = '{{ route('admin.appointments.mass_destroy') }}';
        @endcan

    </script>

    <script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/moment.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.js'></script>
    <script>
        $(document).ready(function () {
            // page is now ready, initialize the calendar...
            $('#calendar').fullCalendar({
                // put your options and callbacks here
                defaultView: 'agendaWeek',
                events: [
                        @foreach($appointments as $appointment)
                    {
                        title: '{{ $appointment->lead->customer->first_name . ' ' . $appointment->lead->customer->last_name }}',
                        start: '{{ $appointment->start_time }}',
                        @if ($appointment->finish_time)
                        end: '{{ $appointment->finish_time }}',
                        @endif
                        url: '{{ route('dashboard.salesFlow.appointment.edit', $appointment->id) }}'
                    },
                    @endforeach
                ]
            })
        });
    </script>
@endsection
