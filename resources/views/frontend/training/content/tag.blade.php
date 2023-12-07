@extends('frontend.layouts.app')

@section('title', app_name() . ' | Training' )

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="d-inline-block">Training</h3>

        </div>
        <div class="container mt-3">

            <div class="container">
                <div class="row justify-content-center">
                    @foreach($contents as $content)
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 py-2">
                            <div class="card h-100 pt-4">
                                <div class="card-body">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-12">
                                                <h5> {{ $content->name }} </h5>
                                                <p>{{ $content->description }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-12">
                                                @if( auth()->user()->roles[0]->name == 'administrator' || auth()->user()->roles[0]->name == 'executive')
                                                    <div class="pt-3">
                                                        <div class="float-left">
                                                            @if($content->type == 'youTube')
                                                                <a href="{{ route('admin.training.content.show', $content->id) }}"
                                                                   data-toggle="tooltip" data-placement="top" title=""
                                                                   class="btn btn-reddit" data-original-title="View"><i
                                                                        class="fab fa-youtube fa-2x"></i></a>
                                                            @else
                                                                <a href="{{ route('admin.training.content.show', $content->id) }}"
                                                                   data-toggle="tooltip" data-placement="top" title=""
                                                                   class="btn btn-primary" data-original-title="View"><i
                                                                        class="fas fa-file-pdf fa-2x"></i></a>
                                                            @endif
                                                        </div>

                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="container">
                                        <div class="row mt-4">
                                            <div class="col-12">
                                                <div class="mx-1 mb-2">
                                                    @forelse($content->tags as $tag)
                                                        <a href="/training/content/tag?tag={{$tag->slug}}">#{{$tag->name}}</a>
                                                    @empty
                                                    @endforelse
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>


@endsection
