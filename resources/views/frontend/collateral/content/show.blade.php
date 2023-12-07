@extends('frontend.layouts.app')

@section('title', app_name() . ' | ' . __('Collateral') )

@section('content')
    {{--{{ dd($content->url) }}--}}
    <div class="container">
        <div class="row">
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

                        <div>
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
@endsection
