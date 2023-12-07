@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.dashboard.title'))

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="d-inline-block">Training</h3>
            @if( auth()->user()->roles[0]->name == 'administrator' || auth()->user()->roles[0]->name == 'executive')
                <div class="btn-toolbar float-right" role="toolbar">
                    <span class="pt-1 pr-2">Create New Training</span>
                    <a href="{{ route('dashboard.training.content.create') }}" class="btn btn-success ml-1"
                       data-toggle="tooltip"
                       title="New More Content for Training"><i class="fas fa-plus-circle"></i></a>
                </div><!--btn-toolbar-->
            @endif
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
                                                <p>{!!  $content->description !!}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-12">
                                                @can('administrate all trainings')
                                                    <div class="pt-3">
                                                        <div class="float-left">
                                                            @if($content->type == 'youTube')
                                                                <a href="{{ route('dashboard.training.content.show', $content->id) }}"
                                                                   data-toggle="tooltip" data-placement="top" title=""
                                                                   class="btn btn-reddit" data-original-title="View"><i
                                                                        class="fab fa-youtube fa-2x"></i></a>
                                                            @else
                                                                <a href="{{ route('dashboard.training.content.show', $content->id) }}"
                                                                   data-toggle="tooltip" data-placement="top" title=""
                                                                   class="btn btn-primary" data-original-title="View"><i
                                                                        class="fas fa-file-pdf fa-2x"></i></a>
                                                            @endif
                                                        </div>
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
                                                    </div>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                    <div class="container">
                                        <div class="row mt-4">
                                            <div class="col-12">
                                                <div class="mx-1 mb-2">
                                                    @forelse($content->tags as $stuff)
                                                        <a href="/dashboard/training/content/tag?tag={{$stuff->slug}}">#{{$stuff->name}}</a>
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
