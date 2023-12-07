{{--{{dd($offices)}}--}}
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
                            @can('administrate all offices')
                                <div class="btn-toolbar justify-content-end" role="toolbar"
                                     aria-label="@lang('labels.general.toolbar_btn_groups')">
                                    <a href="{{ route('dashboard.office.create') }}" class="btn btn-success ml-1"
                                       data-toggle="tooltip"
                                       title="@lang('labels.general.create_new')"><i class="fas fa-plus-circle"></i></a>
                                </div>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($offices as $office)
                                <div class="col-lg-3 col-sm-6 mb-3">
                                    <div class="card h-100">
                                        <div class="card-header">
                                            {{ $office->name }}
                                        </div>
                                        <div class="card-body">
                                            @if($office->address && $office->zip_code && $office->city)
                                            <div class="py-1">
                                                Address
                                            </div>
                                            <div>
                                                {{ $office->address }}
                                            </div>
                                            <div>
                                                <span>{{ $office->zip_code }} </span><span> {{ $office->city }}, </span><span> {{ $office->state  }}</span>
                                            </div>
                                            @endif
                                            @if($office->phone_number)
                                            <div class="pt-1">
                                                <span>Phone: <a
                                                        href="tel:{{ $office->phone_number }}">{{ $office->phone_number }}</a></span>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="card-body">
                                            {{ $office->id }}
                                            <div class="btn-group float-right" role="group" aria-label="User Actions">
                                                <a href="{{ route('dashboard.office.show', $office->id) }}"
                                                   data-toggle="tooltip" data-placement="top" title=""
                                                   class="btn btn-info" data-original-title="View"><i
                                                        class="fas fa-eye"></i></a>
                                                @if(Auth::user()->office_id == $office->id)
                                                    <a href="{{ route('dashboard.office.edit', $office) }}"
                                                       data-toggle="tooltip" data-placement="top" title=""
                                                       class="btn btn-primary" data-original-title="Edit"><i
                                                            class="fas fa-edit"></i></a>

                                                @else
                                                    @can('administrate all offices')
                                                        <a href="{{ route('dashboard.office.edit', $office) }}"
                                                           data-toggle="tooltip" data-placement="top" title=""
                                                           class="btn btn-primary" data-original-title="Edit"><i
                                                                class="fas fa-edit"></i></a>
                                                    @endcan

                                                    @endif

                                                {{--                            <form method="POST"--}}
                                                {{--                                  action="{{ route('dashboard.office.destroy', $office->id) }}">--}}
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
