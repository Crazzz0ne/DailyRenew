@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.dashboard.title'))

@section('content')
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h2 class="d-inline-block">{{ $logged_in_user->name }} here's what's going on.</h2>
                    @can('administrate all announcements')
                        <div class="btn-toolbar float-right" role="toolbar"
                             aria-label="@lang('labels.general.toolbar_btn_groups')">
                            <a href="{{ route('dashboard.announcement.create') }}" class="btn btn-third ml-1"
                               data-toggle="tooltip"
                               title="@lang('labels.general.create_new')"><i class="fas fa-plus-circle"></i></a>
                        </div>
                    @endcan
                </div>
                <div class="card-body">
                    <div class="row pt-2">
                        @foreach($announcements as $announcement)
                            @if ($announcement->sticky)
                                <div class="col-lg-6 col-sm-12 my-2">
                                    <div class="card h-100 shadow-lg">
                                        @switch($announcement->color)
                                            @case('normal')
                                                <div class="card-header pt-3 pb-1">
                                            @break
                                            @case('green')
                                                <div class="card-header pt-3 pb-1 select-green text-white">
                                            @break
                                            @case('yellow')
                                                 <div class="card-header pt-3 pb-1 select-yellow">
                                            @break
                                            @case('red')
                                                <div class="card-header pt-3 pb-1 select-red text-white">
                                            @break
                                            @default
                                                    <div class="card-header pt-3 pb-1 select-red text-white">
                                            @break
                                            @endswitch
                                            <div class="container">
                                                <div class="row">
                                                    @if($announcement->seen)
                                                        <div class="col-10">
                                                            <h3>{{ substr($announcement->subject, 0, 15) }}</h3>
                                                        </div>
                                                        <div class="col-2 pt-2">
                                                            <div class="badge badge-danger mb-3"
                                                                 style="font-size: large">
                                                                <a class="text-white"
                                                                   href="/dashboard/announcement/{{ $announcement->id }}">
                                                                    New</a>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="col-12">
                                                            <h3>{{ substr($announcement->subject, 0, 39) }}</h3>
                                                        </div>

                                                    @endif
                                                    <div class="col-12">
                                                        <p>{{ date('d-M', strtotime($announcement->created_at)) }}</p>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body ">
                                            <div class="mb-0">
                                                {{   Str::limit($announcement->body, 250, '...') }}

                                            </div>
                                            <div class="btn-group float-right pt-2" role="group"
                                                 aria-label="User Actions">
                                                <a href="{{ route('dashboard.announcement.show', $announcement->id) }}"
                                                   data-toggle="tooltip" data-placement="top"
                                                   title=""
                                                   class="btn btn-info"
                                                   data-original-title="View"><i
                                                        class="fas fa-eye"></i></a>
                                                @can('administrate all announcements')
                                                    <a href="{{ route('dashboard.announcement.edit', $announcement->id) }}"
                                                       data-toggle="tooltip" data-placement="top"
                                                       title=""
                                                       class="btn btn-primary"
                                                       data-original-title="Edit"><i
                                                            class="fas fa-edit"></i></a>
                                                    <form method="POST"
                                                          action="{{ route('dashboard.announcement.destroy', $announcement) }}">
                                                        <input type="hidden" name="_method"
                                                               id="_method"
                                                               value="DELETE">
                                                        <input type="hidden" name="_token"
                                                               value="{{ csrf_token() }}">
                                                        <button class="btn btn-danger delete-button"
                                                                type="submit">
                                                            <i data-original-title="Delete"
                                                               class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @endforeach
                            </div>
                            <hr class="horline">
                            <div class="row pt-2">
                                @foreach($announcements as $announcement)
                                    @if (!$announcement->sticky)
                                        <div class="col-lg-6 my-2">
                                            <div class="card h-100 shadow-lg">
                                                @switch($announcement->color)
                                                    @case('normal')
                                                    <div class="card-header pt-3 pb-1">
                                                        @break
                                                        @case('green')
                                                        <div
                                                            class="card-header pt-3 pb-1 select-green text-white">
                                                            @break
                                                            @case('yellow')
                                                            <div
                                                                class="card-header pt-3 pb-1 select-yellow">
                                                                @break
                                                                @case('red')
                                                                <div
                                                                    class="card-header pt-3 pb-1 select-red text-white">
                                                                    @break
                                                                    @default
                                                                    @endswitch
                                                                    <div class="container">
                                                                        <div class="row">
                                                                            @if($announcement->seen)
                                                                                <div class="col-10">
                                                                                    <h3>{{ substr($announcement->subject, 0, 15 ) }}</h3>
                                                                                </div>
                                                                                <div
                                                                                    class="col-2 pt-2">
                                                                                    <div
                                                                                        class="badge badge-danger mb-3"
                                                                                        style="font-size: large">
                                                                                        <a class="text-white"
                                                                                           href="/admin/announcement/{{ $announcement->id }}">
                                                                                            New</a>
                                                                                    </div>
                                                                                </div>
                                                                            @else
                                                                                <div class="col-12">
                                                                                    <h3>{{ substr($announcement->subject, 0, 39) }}</h3>
                                                                                </div>

                                                                            @endif
                                                                            <div class="col-12">
                                                                                <p>{{ date('d-M', strtotime($announcement->created_at)) }}</p>

                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="card-body ">
                                                                    <div class="mb-0">
                                                                        {{   Str::limit($announcement->body, 250, '...') }}
                                                                    </div>
                                                                    <div
                                                                        class="btn-group float-right pt-2"
                                                                        role="group"
                                                                        aria-label="User Actions">
                                                                        <a href="{{ route('dashboard.announcement.show', $announcement->id) }}"
                                                                           data-toggle="tooltip"
                                                                           data-placement="top"
                                                                           title=""
                                                                           class="btn btn-info"
                                                                           data-original-title="View"><i
                                                                                class="fas fa-eye"></i></a>
                                                                        @can('administrate all announcements')
                                                                            <a href="{{ route('dashboard.announcement.edit', $announcement->id) }}"
                                                                               data-toggle="tooltip"
                                                                               data-placement="top"
                                                                               title=""
                                                                               class="btn btn-primary"
                                                                               data-original-title="Edit"><i
                                                                                    class="fas fa-edit"></i></a>
                                                                            <form method="POST"
                                                                                  action="{{ route('dashboard.announcement.destroy', $announcement) }}">
                                                                                <input type="hidden"
                                                                                       name="_method"
                                                                                       id="_method"
                                                                                       value="DELETE">
                                                                                <input type="hidden"
                                                                                       name="_token"
                                                                                       value="{{ csrf_token() }}">
                                                                                <button
                                                                                    class="btn btn-danger delete-button"
                                                                                    type="submit">
                                                                                    <i data-original-title="Delete"
                                                                                       class="fas fa-trash"></i>
                                                                                </button>
                                                                            </form>
                                                                        @endcan
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @endif
                                                        @endforeach
                                                    </div>
                                            </div>
                                        </div>
                            </div>
                        </div>
                </div>
            </div>
@endsection
