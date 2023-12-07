@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.dashboard.title'))

@section('content')
    <div class="card">
        <div class="card-header">
            {{--            ToDO:Need to make @can --}}
            <h3 class="d-inline-block">Training</h3>
            @can('administrate all trainings')
                <div class="btn-toolbar float-right" role="toolbar"
                     aria-label="Creat New Training Category">
                    <span class="pt-1 pr-2">Create New Training Category</span>
                    <a href="{{ route('dashboard.training.category.create') }}" class="btn btn-success ml-1"
                       data-toggle="tooltip"
                       title="New Category for Training"><i class="fas fa-plus-circle"></i>
                    </a>

                </div><!--btn-toolbar-->
            @endcan
        </div>
        {{--        TODO: card seems to be missing?--}}
        <div class="card-body" id="parent-card">
            <div class="container mt-3">
                <div class="row justify-content-center">
                    @foreach($categories as $category)
                        <div class="col-xl-3 col-md-6 col-sm-12 py-2">
                            <div class="card h-100 fancy-body-card shadow-lg">
                                <div class="card-body pb-0">
                                    <h5> {{ $category->name }} </h5>
                                    <p>{{ $category->description }}</p>
                                    <div class="btn-group float-right" role="group">
                                        <a class="btn btn-info" data-original-title="View" data-placement="top"
                                           data-toggle="tooltip"
                                           href="{{ route('dashboard.training.show', $category->id) }}"
                                           title="View Category">
                                            <i class="fas fa-eye">
                                            </i>
                                        </a>
                                        @can('administrate all trainings')
                                            <a class="btn btn-primary" data-original-title="Edit" data-placement="top"
                                               data-toggle="tooltip"
                                               href="{{ route('dashboard.training.category.edit', $category->id) }}"
                                               title="Edit Category">
                                                <i class="fas fa-edit">
                                                </i>
                                            </a>

                                            <form action="{{ route('dashboard.training.category.destroy', $category) }}"
                                                  method="POST">
                                                <input id="_method" name="_method" type="hidden" value="DELETE">
                                                {{ csrf_field() }}
                                                <button class="btn btn-danger delete-button" type="submit">
                                                    <i class="fas fa-trash" data-original-title="Delete">
                                                    </i>
                                                </button>
                                            </form>
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
