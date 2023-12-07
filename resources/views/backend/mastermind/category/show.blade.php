@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.dashboard.title'))

@section('content')
    {{--    {{ dd($contents) }}--}}
    <?php $c = 0 ?>
    <div class="card">
        <div class="card-header">
            <h3 class="d-inline-block">Mastermind</h3>
            <div class="btn-toolbar float-right" role="toolbar">
                <span class="pt-1 pr-2">Create New Grand Idea</span>
                <a href="{{ route('dashboard.mastermind.content.create') }}" class="btn btn-success ml-1"
                   data-toggle="tooltip"
                   title="A new Mastermind Category"><i class="fas fa-plus-circle"></i></a>
            </div><!--btn-toolbar-->
        </div>

        @if(count($contents))
        <div class="container mt-3 pb-5">
            <div class="row">
                <div class="col-12">
                    <div class="row justify-content-between py-2 my-2">
                        <div class="col">
                    <span>
                        <a href="/dashboard/mastermind/{{ $contents[0]->category_id }}/view?year={{ $lastYear }}">
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
                        <a href="/dashboard/mastermind/{{ $contents[0]->category_id }}/view?year={{ $currentYear +1 }}">
                     <i class="fas fa-arrow-right fa-2x"> </i></a>
                    </span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="container">

                <div class="row justify-content-center">
                    @can('administrate all masterminds')
                        @foreach($contents as $content)
                            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 py-2">
                                <div class="card h-100 fancy-body-card shadow-lg pt-2">
                                    <div class="card-body pb-0">
                                        <div class="container">
                                            <div class="row">
                                                <div class="col-12">
                                                    @if($content->approved)
                                                        <div>
                                                            <h4>{{ $content->name }}</h4>
                                                        </div>
                                                        <div>
                                                            <span class="badge badge-success mb-3"
                                                                  style="font-size: large">
                                                                <a class="text-white"
                                                                   href="/dashboard/mastermind/content/{{ $content->id }}/edit">
                                                                     Approved </a>
                                                            </span>
                                                        </div>
                                                    @else
                                                        <div>
                                                            <h4>{{ $content->name }}</h4>
                                                        </div>
                                                        <div>
                                                            <div class="badge badge-danger mb-3"
                                                                 style="font-size: large">
                                                                <a class="text-white"
                                                                   href="/dashboard/mastermind/content/{{ $content->id }}/edit">
                                                                    Not Approved</a>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    {{--                                                    <h5> {{ $content->name }} </h5>--}}

                                                    <p>{{ Str::limit($content->description, 150, '...') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="container">
                                            <div class="row">
                                                <div class="col-12">

                                                    <div class="pt-3">
                                                        <div class="float-left">
                                                            @if($content->type == 'youTube')
                                                                <a href="{{ route('dashboard.mastermind.content.show', $content->id) }}"
                                                                   data-toggle="tooltip" data-placement="top"
                                                                   title=""
                                                                   class="btn btn-reddit"
                                                                   data-original-title="View">
                                                                    <i class="fab fa-youtube fa-2x"></i>
                                                                </a>
                                                            @endif
                                                            @if($content->type == 'pdf')
                                                                <a href="{{ route('dashboard.mastermind.content.show', $content->id) }}"
                                                                   data-toggle="tooltip" data-placement="top"
                                                                   title=""
                                                                   class="btn btn-primary"
                                                                   data-original-title="View">
                                                                    <i class="fas fa-file-pdf fa-2x"></i>
                                                                </a>
                                                            @endif
                                                            @if($content->type == 'audio')
                                                                <a href="{{ route('dashboard.mastermind.content.show', $content->id) }}"
                                                                   data-toggle="tooltip" data-placement="top"
                                                                   title=""
                                                                   class="btn btn-primary"
                                                                   data-original-title="View">
                                                                    <i class="fas fa-file-audio fa-2x"></i>
                                                                </a>
                                                            @endif
                                                            @if ($content->type == 'none')
                                                                <a href="{{ route('dashboard.mastermind.content.show', $content->id) }}"
                                                                   data-toggle="tooltip" data-placement="top"
                                                                   title=""
                                                                   class="btn btn-primary"
                                                                   data-original-title="View">
                                                                    <i class="far fa-lightbulb fa-2x"></i>

                                                                </a>

                                                            @endif
                                                        </div>
                                                        @can('administrate all masterminds')
                                                            <div class="btn-group float-right" role="group"
                                                                 aria-label="User Actions">
                                                                <a href="{{ route('dashboard.mastermind.content.edit', $content->id) }}"
                                                                   data-toggle="tooltip" data-placement="top" title=""
                                                                   class="btn btn-primary" data-original-title="Edit">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                @if(Auth::user()->hasRole('executive'))
                                                                <form method="POST"
                                                                      action="{{ route('dashboard.mastermind.content.destroy', $content) }}">
                                                                    <input id="_method" name="_method" type="hidden"
                                                                           value="DELETE">
                                                                    {{ csrf_field() }}


                                                                    <button class="btn btn-danger delete-button"
                                                                            type="submit">
                                                                        <i data-original-title="Delete"
                                                                           class="fas fa-trash"></i>
                                                                    </button>
                                                                </form>
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
                                                            <a href="/dashboard/mastermind/tag?tag={{$tag->slug}}">#{{$tag->name}}</a>
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
                        <?php $c = 1 ?>
                    @endcan

                    @if(!$c)
                        @foreach($contents as $content)
                            @if($content->approved)
                                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 py-2">
                                    <div class="card h-100 fancy-body-card shadow-lg pt-2">
                                        <div class="card-body pb-0">
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-12">

                                                        <div>
                                                            <h4>{{ $content->name }}</h4>
                                                        </div>
                                                        {{--                                                    <h5> {{ $content->name }} </h5>--}}

                                                        <p>{{ Str::limit($content->description, 150, '...') }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="pt-3">
                                                            <div class="float-left">
                                                                @if($content->type == 'youTube')
                                                                    <a href="{{ route('dashboard.mastermind.content.show', $content->id) }}"
                                                                       data-toggle="tooltip" data-placement="top"
                                                                       title=""
                                                                       class="btn btn-reddit"
                                                                       data-original-title="View">
                                                                        <i class="fab fa-youtube fa-2x"></i>
                                                                    </a>
                                                                @endif
                                                                @if($content->type == 'pdf')
                                                                    <a href="{{ route('dashboard.mastermind.content.show', $content->id) }}"
                                                                       data-toggle="tooltip" data-placement="top"
                                                                       title=""
                                                                       class="btn btn-primary"
                                                                       data-original-title="View">
                                                                        <i class="fas fa-file-pdf fa-2x"></i>
                                                                    </a>
                                                                @endif
                                                                @if($content->type == 'audio')
                                                                    <a href="{{ route('dashboard.mastermind.content.show', $content->id) }}"
                                                                       data-toggle="tooltip" data-placement="top"
                                                                       title=""
                                                                       class="btn btn-primary"
                                                                       data-original-title="View">
                                                                        <i class="fas fa-file-audio fa-2x"></i>
                                                                    </a>
                                                                @endif
                                                                @if ($content->type == 'none')
                                                                    <a href="{{ route('dashboard.mastermind.content.show', $content->id) }}"
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
                                            </div>
                                            <div class="container">
                                                <div class="row mt-4">
                                                    <div class="col-12">
                                                        <div class="mx-1 mb-2">
                                                            @forelse($content->tags as $tag)
                                                                <a href="/dashboard/mastermind/tag?tag={{$tag->slug}}">#{{$tag->name}}</a>
                                                            @empty
                                                            @endforelse
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach

                    @endif
                </div>

            </div>
        </div>
            @else
            <div class="container mt-3 text-center">
                <h1> Time to Create some new Ideas! </h1>
            </div>
        @endif
    </div>


@endsection
