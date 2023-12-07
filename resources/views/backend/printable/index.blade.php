@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.dashboard.title'))

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="d-inline-block">Printable</h3>
            @can('administrate all announcements')
            <div class="btn-toolbar float-right" role="toolbar"
                 aria-label="Moar Training">
                <a href="{{ route('dashboard.printable.category.create') }}" class="btn btn-success ml-1"
                   data-toggle="tooltip"
                   title="New Category for Training"><i class="fas fa-plus-circle"></i></a>
            </div><!--btn-toolbar-->
            @endcan
        </div>
        <div class="card-body">
            <div class="container mt-3">
                <div class="row justify-content-center">
                    @foreach($categories as $category)
                        <div class="col-xl-3 col-md-6 col-sm-12 py-2">
                            <div class="card h-100 fancy-body-card shadow-lg">
                                <div class="card-body">
                                    <h5> {{ $category->name }} </h5>
                                    <p>{{ $category->description }}</p>
                                    <div class="btn-group float-right" role="group">
                                        <a class="btn btn-info" data-original-title="View" data-placement="top"
                                           data-toggle="tooltip"
                                           href="{{ route('dashboard.printable.show', $category->id) }}"
                                           title="View Category">
                                            <i class="fas fa-eye">
                                            </i>
                                        </a>
                                        @can('administrate all announcements')
                                            <a class="btn btn-primary" data-original-title="Edit" data-placement="top"
                                               data-toggle="tooltip"
                                               href="{{ route('dashboard.printable.category.edit', $category->id) }}"
                                               title="Edit Category">
                                                <i class="fas fa-edit">
                                                </i>
                                            </a>

                                            {{--                                            <form--}}
                                            {{--                                                action="{{ route('dashboard.printable.category.destroy', $category) }}"--}}
                                            {{--                                                method="DELETE">--}}
                                            {{--                                                <input id="_method" name="_method" type="hidden" value="DELETE">--}}
                                            {{--                                                {{ csrf_field() }}--}}
                                            {{--                                                <button class="btn btn-danger delete-button" type="submit">--}}
                                            {{--                                                    <i class="fas fa-trash" data-original-title="Delete">--}}
                                            {{--                                                    </i>--}}
                                            {{--                                                </button>--}}
                                            {{--                                            </form>--}}
                                            {{--                                        TODO it does a 404, it should not so commented out--}}
                                            <a href="{{ route('dashboard.printable.category.destroy', $category) }}"
                                               data-method="delete"
                                               data-token="{{csrf_token()}}"
                                               data-confirm="Are you sure?">
                                                <button class="btn btn-danger delete-button"
                                                        type="submit">
                                                    <i data-original-title="Delete"
                                                       class="fas fa-trash"></i>
                                                </button>
                                            </a>
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
