@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.dashboard.title'))

@section('content')
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <span style="line-height: 2.5">A place to list all of the companies you work with.</span>
                    @can('administrate all partnerlinks')
                    <div class="btn-toolbar float-right" role="toolbar"
                         aria-label="@lang('labels.general.toolbar_btn_groups')">
                        <span>Create A New Partner </span>

                        <a href="{{ route('dashboard.vendorlinks.vendor.create') }}" class="btn btn-success ml-1"
                           data-toggle="tooltip"
                           title="@lang('labels.general.create_new')"><i class="fas fa-plus-circle"></i></a>
                    </div><!--btn-toolbar-->
                    @endcan
                </div><!--card-header-->
                <div class="card-body">
                    <div class="row pt-2">
                        {{--                        {{ dd($vendors) }}--}}
                        @foreach($vendors as $vendor)
                            <div class="col-xl-3 col-md-6 col-sm-12 py-3">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <span class="h2"> {{ $vendor->company_name }} </span>
                                    </div>
                                    <div class="card-body">
                                        <div class="container">
                                            <div class="row justify-content-center mb-3">
                                                <div class="col mx-auto text-center">
                                                    <div class="vendor-img"
                                                         {{--                                                         https://cuic-dashboard.s3-us-west-1.amazonaws.com/--}}
                                                         style='background-image: url("{{ Storage::disk('s3')->url($vendor->picture) }}")'></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="btn-group float-right" role="group" aria-label="User Actions">
                                            <a href="{{ route('dashboard.vendorlinks.vendor.show', $vendor->id) }}"
                                               class="btn btn-info" data-original-title="View"
                                               data-placement="top" data-toggle="tooltip" title="View">
                                                <i class="fas fa-eye">
                                                </i>
                                            </a>

                                            @can('administrate all partnerlinks')
                                                <a href="{{ route('dashboard.vendorlinks.vendor.edit', $vendor->id) }}"
                                                   data-toggle="tooltip" data-placement="top" title=""
                                                   class="btn btn-primary" data-original-title="Edit"><i
                                                        class="fas fa-edit"></i></a>
                                                <form method="POST" onsubmit="confirmDelete()"
                                                      action="{{ route('dashboard.vendorlinks.vendor.destroy', $vendor->id) }}">
                                                    <input type="hidden" name="_method" id="_method" value="DELETE">
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    <button
                                                        class="btn btn-danger"
                                                        style="border-top-right-radius: 0.25rem;
                                                                   border-bottom-right-radius: 0.25rem;
                                                                    border-top-left-radius: 0px;
                                                                    border-bottom-left-radius: 0px;"
                                                        type="submit">
                                                        <i data-original-title="Delete" class="fas fa-trash"></i>
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
    </div><!--card-body-->
    <script>
        function confirmDelete() {
            var x = confirm("Are you sure you want to delete?");
            return x;
        }
    </script>
@endsection
