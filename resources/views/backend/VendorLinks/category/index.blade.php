@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.dashboard.title'))

@section('content')
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <span style="line-height: 2.5">A way to categories your links.</span>
                    @can('administrate all partnerlinks')
                    <div class="btn-toolbar float-right" role="toolbar"
                         aria-label="@lang('labels.general.toolbar_btn_groups')">
                        <a href="{{ route('dashboard.vendorlinks.category.create') }}" class="btn btn-success ml-1"
                           data-toggle="tooltip"
                           title="@lang('labels.general.create_new')"><i class="fas fa-plus-circle"></i></a>
                    </div><!--btn-toolbar-->
                    @endcan
                </div><!--card-header-->
                <div class="card-body">
                    <div class="row justify-content-center pt-2">
                        @foreach($categories as $category)
                            @if ($category->active != 0)
                                <div class="col-xl-3 col-md-6 col-sm-12 py-3">
                                    <div class="card h-100">
                                        <div class="card-header">
                                            <h4 class="h 3 text-capitalize">{{ $category->name }}</h4>
                                        </div>
                                        <div class="card-body">
                                            <p>{{ $category->description  }}</p>
                                            <div class="btn-group float-right" role="group" aria-label="User Actions">
                                                <a href="{{ route('dashboard.vendorlinks.category.show', $category->id) }}"
                                                   data-toggle="tooltip" data-placement="top" title=""
                                                   class="btn btn-info" data-original-title="View"><i
                                                        class="fas fa-eye"></i></a>
                                                @can('administrate all partnerlinks')
                                                    <a href="{{ route('dashboard.vendorlinks.category.edit', $category->id) }}"
                                                       data-toggle="tooltip" data-placement="top" title=""
                                                       class="btn btn-primary" data-original-title="Edit"><i
                                                            class="fas fa-edit"></i></a>
                                                    <form method="POST" onsubmit="return confirmDelete()"
                                                          action="{{ route('dashboard.vendorlinks.category.destroy', $category->id) }}">
                                                        <input type="hidden" name="_method" id="_method" value="DELETE">
                                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                        <button class="btn btn-danger"
                                                                style="border-top-right-radius: 0.25rem;border-bottom-right-radius: 0.25rem;border-top-left-radius: 0px;border-bottom-left-radius: 0px;"
                                                                type="submit">
                                                            <i data-original-title="Delete" class="fas fa-trash"></i>
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
    </div><!--card-body-->
    <script>
        function confirmDelete() {
            var x = confirm("Are you sure you want to delete?");
            return x;
        }
    </script>
@endsection
