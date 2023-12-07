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
                            <iframe width=100% style="min-height: 65vh" src="{{ $link }}?autoplay=1"
                                    allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen></iframe>
                        @endif
                        @if($content->type == 'pdf')
                            <iframe src="{{ $link }}"
                                    width="100%" height="auto" style="min-height: 65vh;"></iframe>
                            <a href="{{$link}}" data-toggle="tooltip"
                               data-placement="top" title="" class="btn btn-primary float-right"
                               data-original-title="View"
                               target="_blank" download="{{ $link }}">
                                <i class="fas fa-download"></i>
                            </a>
                        @endif
                        @if($content->type == 'audio')
                            <div id="app">
                                <audio-player file="{{ $link }}"></audio-player>
                            </div>
                            <div class="pt-4">
                                <a href="{{ $link }}" data-toggle="tooltip"
                                   data-placement="top" title="" class="btn btn-primary" data-original-title="View"
                                   target="_blank">
                                    <i class="fas fa-download"></i>
                                </a>

                            </div>
                        @endif
                            @if ($content->type == 'video')
                                <div>
                                    <video width="100%" height="auto" autoplay controls>
                                        <source src="{{  $link }}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>

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
<script>
    import AudioPlayer from "../../../../js/backend/components/AudioPlayer";
    export default {
        components: {AudioPlayer}
    }
</script>
