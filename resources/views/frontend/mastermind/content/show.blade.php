@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.dashboard.title'))

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h2>{{$content->name}}</h2>
                    </div>
                    <div class="card-body">
                        @if($content->type == 'youTube')
                            <iframe width=100% style="min-height: 65vh" src="{{ $content->path }}?autoplay=1"
                                    allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen></iframe>
                        @endif
                        @if($content->type == 'pdf')
                            <iframe src="{{Storage::disk('s3')->url($content->path)}}"
                                    width="100%" height="auto" style="min-height: 65vh;"></iframe>
                            <a href="{{Storage::disk('s3')->url($content->path)}}" data-toggle="tooltip"
                               data-placement="top" title="" class="btn btn-primary float-right"
                               data-original-title="View"
                               target="_blank" download="{{Storage::disk('s3')->url($content->path)}}">
                                <i class="fas fa-download"></i>
                            </a>
                        @endif
                        @if($content->type == 'audio')
                            <div id="app">
                                <audio-player file="{{Storage::disk('s3')->url($content->path)}}"></audio-player>
                            </div>
                            <div class="pt-4">
                                <a href="{{Storage::disk('s3')->url($content->path)}}" data-toggle="tooltip"
                                   data-placement="top" title="" class="btn btn-primary" data-original-title="View"
                                   target="_blank">
                                    <i class="fas fa-download"></i>
                                </a>

                            </div>
                        @endif
                        <div class="container">
                            <div class="row">
                                <div class="col-12 px-3 py-3">
                                    <p>{!! $content->description  !!}</p>
                                    <div class="float-left">
                                        <a href="{{ url()->previous() }}"
                                           data-toggle="tooltip" data-placement="top" title="Back"
                                           class="btn btn-info" data-original-title="View">
                                            Back</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
