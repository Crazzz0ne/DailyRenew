@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.dashboard.title'))

@section('content')
    <div class="card">
        <div class="card-header">
            <h2 class="d-inline-block">{{ $logged_in_user->name }} here's what's going on.</h2>
            @can('administrate all office standings')
                <div class="btn-toolbar float-right" role="toolbar"
                     aria-label="@lang('labels.general.toolbar_btn_groups')">
                    <a href="{{ route('dashboard.officestanding.create') }}" class="btn btn-third ml-1"
                       data-toggle="tooltip"
                       title="@lang('labels.general.create_new')"><i class="fas fa-plus-circle"></i></a>
                </div>

            @endcan
        </div>
        <div class="card-body" id="parent-card">
            {{--test--}}
            <div class="container mt-3">
                <div class="row justify-content-center">
                    @if (isset($approvedMonths))

                        @foreach($approvedMonths as $key => $month)
                            <div class="col-xl-3 col-md-6 col-sm-12 py-2">
                                <div class="card h-100 fancy-body-card shadow-lg">
                                    <div class="card-body pb-0">
                                        <h5>{{ $month }}, {{ mb_substr($key, 3) }}</h5>
                                        <div class="btn-group float-right" role="group">
                                            <a class="btn btn-info" data-original-title="View" data-placement="top"
                                               data-toggle="tooltip"
                                               href="{{ route('dashboard.officestanding.show', $key) }}"
                                               title="View Category">
                                                <i class="fas fa-eye">
                                                </i>
                                            </a>
                                            @can('administrate all office standings')

                                                <a href="{{  route('dashboard.office.officestandings.review', [mb_substr($key, 0, -5 ), mb_substr($key, 3)]) }}"
                                                   class="btn btn-third ml-1"
                                                   data-toggle="tooltip"
                                                   title="review pending Office Standings"><i
                                                        class="far fa-check-square"></i></a>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            </div>

                        @endforeach
                    @else
                        <span>Error</span>
                    @endif
                </div>

                @can('administrate all office standings')
                @if (isset($disApprovedMonths))
                    <div class="row justify-content-center">
                        <div><h1>Not Approved</h1></div>
                    </div>
                    <div class="row justify-content-center">
                        @foreach($disApprovedMonths as $key => $month)
                            <div class="col-xl-3 col-md-6 col-sm-12 py-2">
                                <div class="card h-100 fancy-body-card shadow-lg">
                                    <div class="card-body pb-0">
                                        <h5>{{ $month }}, {{ mb_substr($key, 3) }}</h5>
                                        <div class="btn-group float-right" role="group">

                                            @can('administrate all office standings')

                                                <a href="{{  route('dashboard.office.officestandings.review', [mb_substr($key, 0, -5 ), mb_substr($key, 3)]) }}"
                                                   class="btn btn-third ml-1"
                                                   data-toggle="tooltip"
                                                   title="review pending Office Standings"><i
                                                        class="far fa-check-square"></i></a>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            </div>

                        @endforeach
                        @else
                            <span>Error</span>
                        @endif
                    </div>
                    @endcan
            </div>
        </div>
    </div>
@endsection
