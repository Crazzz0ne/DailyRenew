@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.dashboard.title'))

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="d-inline-block">Mastermind</h3>
{{--            @can('administrate all masterminds')--}}
                <div class="btn-toolbar float-right" role="toolbar"
                     aria-label="Creat New Mastermind Category">
                    <span class="pt-1 pr-2">Create New Mastermind Category</span>
                    <a href="{{ route('dashboard.masstext.create') }}" class="btn btn-success ml-1"
                       data-toggle="tooltip"
                       title="New Category for Mastermind"><i class="fas fa-plus-circle"></i>
                    </a>
                </div><!--btn-toolbar-->
{{--            @endcan--}}

        </div>
        {{--        TODO: card seems to be missing?--}}
        <div class="card-body">
            <div class="container mt-3">
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Customer Name</th>
                        <th scope="col">Customer Phone</th>
                        <th scope="col">Sending Number</th>
                        <th scope="col">Sent</th>
                        <th scope="col">Sent Date</th>
                    </tr>
                    </thead>
                    <tbody>

                   @foreach($texts as $text)
                           <tr>
                       <td>{{ $text->id }}</td>
                                <td>{{ $text->customer_name }}</td>
                                <td>{{ $text->customer_number }}</td>
                                <td>{{ $text->sending_number }}</td>
                               <td>@if($text->sent)X @endif</td>
                               <td>{{ $text->sent_date }}</td>
                           </tr>
                       @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>


@endsection
