@extends('frontend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.dashboard.title'))

@section('content')
    {{--    {{ dd($contents) }}--}}
    <div class="card">
        <div class="card-header">
            <h3 class="d-inline-block">Mastermind</h3>
        </div>
        <div class="container mt-3">
            <div class="row">
                <div class="col-12">
                    <div class="row justify-content-between py-2 my-2">
                        <div class="col">
                    <span>
                        <a href="/mastermind/1/view?year={{ $lastYear }}">
                    <i class="fas fa-arrow-left fa-2x"> </i>
                        </a>
                    </span>
                        </div>
                        <div class="col text-center">
                            <h3><span><strong>{{ $currentYear }} </strong></span></h3>
                        </div>
                        <div class="col">
                            @if(now()->year != $currentYear)
                                <span class="float-right">
                        <a href="/mastermind/1/view?year={{ $currentYear +1 }}">
                     <i class="fas fa-arrow-right fa-2x"> </i></a>
                    </span>

                            @endif
                        </div>
                    </div>
                </div>
            </div>
            {{--            Todo: I can not get it to check multiple permissions. , 'approve mastermind' --}}
            <div class="container">

                <div class="row justify-content-center">
                    @can('administrate all masterminds')
                        @foreach($contents as $content)
                            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 py-2">
                                <div class="card h-100 pt-4">
                                    <div class="card-body pb-0">
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

                                                    <div class="pt-3">
                                                        <div class="float-left">
                                                            @if($content->type == 'youTube')
                                                                <a href="{{ route('frontend.mastermind.content.show', $content->id) }}"
                                                                   data-toggle="tooltip" data-placement="top"
                                                                   title=""
                                                                   class="btn btn-reddit"
                                                                   data-original-title="View">
                                                                    <i class="fab fa-youtube fa-2x"></i>
                                                                </a>
                                                            @endif
                                                            @if($content->type == 'pdf')
                                                                <a href="{{ route('frontend.mastermind.content.show', $content->id) }}"
                                                                   data-toggle="tooltip" data-placement="top"
                                                                   title=""
                                                                   class="btn btn-primary"
                                                                   data-original-title="View">
                                                                    <i class="fas fa-file-pdf fa-2x"></i>
                                                                </a>
                                                            @endif
                                                            @if($content->type == 'audio')
                                                                <a href="{{ route('frontend.mastermind.content.show', $content->id) }}"
                                                                   data-toggle="tooltip" data-placement="top"
                                                                   title=""
                                                                   class="btn btn-primary"
                                                                   data-original-title="View">
                                                                    <i class="fas fa-file-audio fa-2x"></i>
                                                                </a>
                                                            @endif
                                                            @if ($content->type == 'none')
                                                                <a href="{{ route('frontend.mastermind.content.show', $content->id) }}"
                                                                   data-toggle="tooltip" data-placement="top"
                                                                   title=""
                                                                   class="btn btn-primary"
                                                                   data-original-title="View">
                                                                    <i class="far fa-lightbulb fa-2x"></i>

                                                                </a>

                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row mt-4">
                                                    <div class="col-12">
                                                        <div class="mx-1 mb-2">
                                                            @forelse($content->tags as $tag)
                                                                <a href="/mastermind/content/tag?tag={{$tag->slug}}">#{{$tag->name}}</a>
                                                            @empty
                                                            @endforelse
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endcan
                </div>

            </div>
        </div>
    </div>


@endsection
