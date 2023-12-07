@extends('frontend.layouts.app')

@section('title', app_name() . ' | ' . __('Announcement') )

@section('content')
    <div class="row justify-content-center">
        <div class="col col-sm-8 align-self-center">
            <div class="card">
                @switch($announcement->color)
                    @case('normal')
                    <div class="card-header">
                        <span class="announcements-header">{{ $announcement->subject }}</span>
                        <span class="float-right">
                                                    {{ date('d-M', strtotime($announcement->created_at)) }}</span>
                    </div>
                    @break
                    @case('green')
                    <div class="card-header select-green">
                        <span class="announcements-header">{{ $announcement->subject }}</span>
                        <span class="float-right">
                                                    {{ date('d-M', strtotime($announcement->created_at)) }}</span>
                    </div>
                    @break
                    @case('yellow')
                    <div class="card-header select-yellow">
                        <span class="announcements-header">{{ $announcement->subject }}</span>
                        <span class="float-right">
                                                    {{ date('d-M', strtotime($announcement->created_at)) }}</span>
                    </div>
                    @break
                    @case('red')
                    <div class="card-header select-red">
                        <span class="announcements-header">{{ $announcement->subject }}</span>
                        <span class="float-right">
                                                    {{ date('d-M', strtotime($announcement->created_at)) }}</span>
                    </div>
                    @break
                    @default
                @endswitch

                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <label for="body"><b>Body</b></label>
                            <p>{!! $announcement->body !!}</p>
                            <div class="float-right">
                                <a href="{{ url()->previous() }}"
                                   data-toggle="tooltip" data-placement="top" title="Back"
                                   class="btn btn-info" data-original-title="View">
                                    Back</a>
                            </div>
                        </div><!--col-->
                    </div><!--row-->
                </div><!--card-body-->
            </div><!--card-->
        </div>
    </div><!--row-->
@endsection
