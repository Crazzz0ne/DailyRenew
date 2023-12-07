@extends('frontend.layouts.app')

@section('title', app_name() . ' | ' . __('Announcement') )


@section('content')
    <div class="container">
        @if($content->type == 'youTube')
            <div class="row justify-content-center">
                <div class="col-md-8 col-sm-12 h-100">
                    {{--        TODO: set the iframe to be responsive--}}
                    <iframe width=100% style="min-height: 480px" src="{{ $content->path }}?autoplay=1"
                            allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen></iframe>
                    <div><h2>{{$content->name}}</h2>
                        <div><p>{!! $content->description  !!}</p></div>
                    </div>
                </div>
                <div class="float-left">
                    <a href="{{ url()->previous() }}"
                       data-toggle="tooltip" data-placement="top" title="Back"
                       class="btn btn-info" data-original-title="View">
                        Back</a>
                </div>
            </div>
        @else
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <h2>{{ $content->name }} </h2>
                    </div>
                    <div class="card-body">
                        <div class="text-center">
                            <a href="{{Storage::disk('s3')->url($content->path)}}"
                               data-toggle="tooltip" data-placement="top" title=""
                               class="btn btn-primary" data-original-title="View" target="_blank"><i
                                    class="fas fa-file-pdf fa-6x"></i></a>

                        </div>
                        <div class="py-5">
                            {!! $content->description !!}}
                        </div>


                    </div>
                    <div class="float-left p-3">
                        <a href="{{ url()->previous() }}"
                           data-toggle="tooltip" data-placement="top" title="Back"
                           class="btn btn-info" data-original-title="View">
                            Back</a>
                    </div>
                </div>

            </div>

        @endif

    </div>


@endsection
