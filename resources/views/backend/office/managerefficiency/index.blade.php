@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.dashboard.title'))

@section('content')
    <div class="card">
        <div class="card-body" id="parent-card">
            <div class="container mt-3">
                <div class="row justify-content-center">
                    @if (isset($months))
                        @foreach($months as $key => $month)
                            <div class="col-xl-3 col-md-6 col-sm-12 py-2">
                                <div class="card h-100 fancy-body-card shadow-lg">
                                    <div class="card-body pb-0">
                                        <h5>{{ $month }}, {{ mb_substr($key, 3) }}</h5>
                                        <div class="btn-group float-right" role="group">
                                            <a class="btn btn-info" data-original-title="View" data-placement="top"
                                               data-toggle="tooltip"
                                               href="{{ route('dashboard.managerefficiency.show', $key) }}"
                                               title="View Category">
                                                <i class="fas fa-eye">
                                                </i>
                                            </a>

                                            {{--                                            <a class="btn btn-primary" data-original-title="Edit" data-placement="top"--}}
                                            {{--                                               data-toggle="tooltip"--}}
                                            {{--                                               title="Edit Category">--}}
                                            {{--                                                <i class="fas fa-edit">--}}
                                            {{--                                                </i>--}}
                                            {{--                                            </a>--}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection
