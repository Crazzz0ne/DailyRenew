@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.dashboard.title'))

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col col-sm-8 align-self-center">
                <div class="card shadow-lg">
                    @switch($announcement->color)
                        @case('normal')
                        <div class="card-header">
                            @break
                            @case('green')
                            <div class="card-header select-green text-white">
                                @break
                                @case('yellow')
                                <div class="card-header select-yellow">
                                    @break
                                    @case('red')
                                    <div class="card-header select-red text-white">
                                        @break
                                        @default
                                        @endswitch

                                        <h3 class="announcements-header">{{ $announcement->subject }}</h3>
                                        <span
                                            class="float-right">{{ date('d-M', strtotime($announcement->created_at)) }}</span>
                                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                {!!   $announcement->trixRichText[0]->content !!}
{{--                                <p> {!!   @trix($announcement, 'content') !!}</p>--}}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                {{--                            <label for="sticky"><b>Stick to Top</b></label>--}}
                                {{--                            <input type="checkbox" name="sticky" id="sticky"--}}
                                {{--                                   value="1" {{ $announcement->sticky == 1 ? 'checked' : '' }}>--}}
                                <div class="float-right">
                                    <a href="{{ url()->previous() }}" data-toggle="tooltip"
                                       data-placement="top"
                                       title="Back" class="btn btn-info" data-original-title="View">
                                        Back</a>
                                    @can('administrate all announcements')
                                        <a href="{{ route('dashboard.announcement.edit', $announcement->id) }}"
                                           data-toggle="tooltip" data-placement="top"
                                           title="Edit" class="btn btn-primary" data-original-title="Edit">
                                            Edit</a>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>




@endsection
