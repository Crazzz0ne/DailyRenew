@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.dashboard.title'))

@section('content')
    <a href="/dashboard/training"> <btn class="btn btn-success"> Back</btn></a>
    <div class="card">
        <div class="card-header">

            <h3 class="d-inline-block">Training</h3>
            @can('administrate all trainings')
                <div class="btn-toolbar float-right" role="toolbar">
                    <span class="pt-1 pr-2">Create New Training</span>
                    <a href="{{ route('dashboard.training.content.create') }}" class="btn btn-success ml-1"
                       data-toggle="tooltip"
                       title="New More Content for Training"><i class="fas fa-plus-circle"></i></a>
                </div><!--btn-toolbar-->
            @endcan
        </div>
        @if(count($contents))
            <div class="container mt-3">
                <div class="row">
                    <div class="col-12">
                        <div class="row justify-content-between py-2 my-2">
                            <div class="col">
                    <span>
                        <a href="/dashboard/training/{{ $contents[0]->category_id }}/view?year={{ $lastYear }}">
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
                        <a href="/dashboard/training/{{ $contents[0]->category_id }}/view?year={{ $currentYear +1 }}">
                     <i class="fas fa-arrow-right fa-2x"> </i></a>
                    </span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="row justify-content-center">
                        @foreach($contents as $content)
                            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 py-2">
                                <div class="card h-100 fancy-body-card shadow-lg pt-2">
                                    <div class="card-body pb-0">
                                        <div class="container">
                                            <div class="row">
                                                <div class="col-12">
                                                    <h5> {{ $content->name }} </h5>
                                                    <p>{!!   str_limit($content->description, 200) !!}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="container">
                                            <div class="row">
                                                <div class="col-12">

                                                    <div class="pt-3">
                                                        <div class="float-left">
                                                            @if($content->type == 'youTube' || $content->type == 'video')
                                                                <a href="{{ route('dashboard.training.content.show', $content->id) }}"
                                                                   data-toggle="tooltip" data-placement="top" title=""
                                                                   class="btn btn-reddit" data-original-title="View">
                                                                    <i class="fab fa-youtube fa-2x"></i>
                                                                </a>
                                                            @elseif($content->type == 'pdf')
                                                                <a href="{{ route('dashboard.training.content.show', $content->id) }}"
                                                                   data-toggle="tooltip" data-placement="top" title=""
                                                                   class="btn btn-primary" data-original-title="View">
                                                                    <i class="fas fa-file-pdf fa-2x"></i>
                                                                </a>
                                                            @elseif($content->type == 'audio')
                                                                <a href="{{ route('dashboard.training.content.show', $content->id) }}"
                                                                   data-toggle="tooltip" data-placement="top" title=""
                                                                   class="btn btn-primary" data-original-title="View">
                                                                    <i class="fas fa-file-audio fa-2x"></i>
                                                                </a>
                                                            @endif
                                                        </div>
                                                        @can('administrate all trainings')
                                                            <div class="btn-group float-right" role="group"
                                                                 aria-label="User Actions">
                                                                <a href="{{ route('dashboard.training.content.edit', $content->id) }}"
                                                                   data-toggle="tooltip" data-placement="top" title=""
                                                                   class="btn btn-primary" data-original-title="Edit">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                <form method="POST"
                                                                      action="{{ route('dashboard.training.content.destroy', $content) }}">
                                                                    <input id="_method" name="_method" type="hidden"
                                                                           value="DELETE">
                                                                    {{ csrf_field() }}
                                                                    <button class="btn btn-danger delete-button"
                                                                            type="submit">
                                                                        <i data-original-title="Delete"
                                                                           class="fas fa-trash"></i>
                                                                    </button>
                                                                </form>
                                                            </div>


                                                        @endcan
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="container">
                                            <div class="row mt-4">
                                                <div class="col-12">
                                                    <div class="mx-1 mb-2">
                                                        @forelse($content->tags as $tag)
                                                            <a href="/dashboard/training/content/tag?tag={{$tag->slug}}">#{{$tag->name}}</a>
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
        @else
            <div class="container mt-3 text-center">
                <h1>Time to add some content!</h1>
            </div>
        @endif

    </div>


@endsection
