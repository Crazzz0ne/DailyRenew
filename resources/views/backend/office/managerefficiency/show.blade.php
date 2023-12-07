@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.dashboard.title'))

@section('content')

    <div class="container">
        <table class="table table-striped" style="background-color: ghostwhite;border-radius: 15px;">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Office Name</th>
                <th scope="col">Name</th>
                <th scope="col">Employees</th>
                <th scope="col">Mangers</th>
                <th scope="col">PartTime</th>
                <th scope="col">Truth</th>
            </tr>
            </thead>
            <tbody>
            @foreach($managerEfficiency as $efficiency)
                <tr>
                    <th scope="row">{{ $efficiency->office_id }}</th>
                    <td>{{ $efficiency->office->name }}</td>
                    <td>{{ $efficiency->user->full_name }}</td>
                    <td>{{ $efficiency->canvassaers_openers_closers_avg }}</td>
                    <td>{{ $efficiency->manager_avg }}</td>
                    <td>{{ $efficiency->other }}</td>
                    <td>@if($efficiency->truth) yes @else no @endif </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div class="float-left pt-3">
            <a href="{{ url()->previous() }}"
               data-toggle="tooltip" data-placement="top" title="Back"
               class="btn btn-info" data-original-title="View">
                Back</a>
        </div>

    </div>

@endsection
