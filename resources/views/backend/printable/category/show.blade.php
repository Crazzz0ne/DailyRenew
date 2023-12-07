@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.dashboard.title'))

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="d-inline-block">Printable</h3>
            @can('administrate all announcements')
                <div class="btn-toolbar float-right" role="toolbar"
                     aria-label="Printable">
                    <a href="{{ route('dashboard.printable.content.create') }}" class="btn btn-success ml-1"
                       data-toggle="tooltip"
                       title="New More Content for Training"><i class="fas fa-plus-circle"></i></a>
                </div><!--btn-toolbar-->
            @endcan
        </div>
        <div class="container mt-3">
            <div class="container">
                <div class="row justify-content-center">
                    @foreach($contents as $content)
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 py-2">
                            <div class="card h-100 pt-4">
                                <div class="card-body">
                                    <h5> {{ $content->name }} </h5>
                                    <p>{{ $content->description }}</p>

                                    <div class="pt-3">
                                        <div class="float-left">
                                            <a href="{{ route('dashboard.printable.content.show', $content->id) }}"
                                               data-toggle="tooltip" data-placement="top" title=""
                                               class="btn btn-primary" data-original-title="View"><i
                                                    class="fas fa-file-pdf fa-2x"></i></a>
                                        </div>
                                        @can('administrate all printables')
                                            <div class="btn-group float-right" role="group" aria-label="User Actions">
                                                <a href="{{ route('dashboard.printable.content.edit', $content->id) }}"
                                                   data-toggle="tooltip" data-placement="top" title=""
                                                   class="btn btn-primary" data-original-title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="{{ route('dashboard.printable.content.destroy', $content) }}"
                                                   data-method="delete"
                                                   data-token="{{csrf_token()}}" data-confirm="Are you sure?">
                                                    <button class="btn btn-danger delete-button"
                                                            type="submit">
                                                        <i data-original-title="Delete"
                                                           class="fas fa-trash"></i>
                                                    </button>
                                                </a>
                                                {{--                                                <form method="POST"--}}
                                                {{--                                                      action="{{ route('dashboard.printable.content.destroy', $content) }}">--}}
                                                {{--                                                    <input id="_method" name="_method" type="hidden" value="DELETE">--}}
                                                {{--                                                    {{ csrf_field() }}--}}
                                                {{--                                                    <button class="btn btn-danger delete-button" type="submit">--}}
                                                {{--                                                        <i data-original-title="Delete" class="fas fa-trash"></i>--}}
                                                {{--                                                    </button>--}}
                                                {{--                                                </form>--}}
                                            </div>
                                        @endcan
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
