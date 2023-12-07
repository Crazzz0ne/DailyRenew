{{--{{dd($markets)}}--}}
@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.dashboard.title'))

@section('content')
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-5">
                            <h4 class="card-title mb-0">
                                Office Management
                                <small class="text-muted"></small>
                            </h4>
                        </div>
                        <div class="col-sm-7">
                            @can('administrate all markets')
                                <div class="btn-toolbar justify-content-end" role="toolbar"
                                     aria-label="@lang('labels.general.toolbar_btn_groups')">
                                    <a href="{{ route('dashboard.market.create') }}" class="btn btn-success ml-1"
                                       data-toggle="tooltip"
                                       title="@lang('labels.general.create_new')"><i class="fas fa-plus-circle"></i></a>
                                </div>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($markets as $market)
                                <div class="col-lg-3 col-sm-6 mb-3">
                                    <div class="card h-100">
                                        <div class="card-header">
                                            {{ $market->name }}
                                        </div>
                                        <div class="card-body">

                                            <div class="py-1">
                                                Address
                                            </div>
                                            <div>

                                            </div>
                                            <div>

                                            </div>


                                            <div class="pt-1">

                                            </div>

                                        </div>
                                        <div class="card-body">
                                            <div class="btn-group float-right" role="group" aria-label="User Actions">
                                                <a href="{{ route('dashboard.market.show', $market->id) }}"
                                                   data-toggle="tooltip" data-placement="top" title=""
                                                   class="btn btn-info" data-original-title="View"><i
                                                        class="fas fa-eye"></i></a>
                                                @can('administrate all offices')
                                                    <a href="{{ route('dashboard.market.edit', $market) }}"
                                                       data-toggle="tooltip" data-placement="top" title=""
                                                       class="btn btn-primary" data-original-title="Edit"><i
                                                            class="fas fa-edit"></i></a>
                                                @endcan
                                                {{--                            <form method="POST"--}}
                                                {{--                                  action="{{ route('dashboard.destroy', $market->id) }}">--}}
                                                {{--                                <input type="hidden" name="_method" id="_method" value="DELETE">--}}
                                                {{--                                <input type="hidden" name="_token" value="{{ csrf_token() }}">--}}
                                                {{--                                <button class="btn btn-danger"--}}
                                                {{--                                        style="border-top-right-radius: 0.25rem;--}}
                                                {{--                                                                   border-bottom-right-radius: 0.25rem;--}}
                                                {{--                                                                    border-top-left-radius: 0px;--}}
                                                {{--                                                                    border-bottom-left-radius: 0px;"--}}
                                                {{--                                        type="submit">--}}
                                                {{--                                    <i data-original-title="Delete" class="fas fa-trash"></i>--}}
                                                {{--                                </button>--}}
                                                {{--                            </form>--}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
