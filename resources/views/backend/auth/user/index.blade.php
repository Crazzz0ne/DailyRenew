@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('labels.backend.access.users.management'))

@section('breadcrumb-links')
    @include('backend.auth.user.includes.breadcrumb-links')
@endsection

@section('content')
    {{--    TODO: Clicking accounts button drops down all on this screen drops all the menus --}}
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-sm-5">
                    <h4 class="card-title mb-0">
                        {{ __('labels.backend.access.users.management') }}
                        <small class="text-muted">{{ __('labels.backend.access.users.active') }}</small>
                    </h4>
                </div><!--col-->

                <div class="col-sm-7">
                    @include('backend.auth.user.includes.header-buttons')
                </div><!--col-->
            </div><!--row-->

            <div class="row mt-4">
                <div class="col">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>@lang('labels.backend.access.users.table.last_name')</th>
                                <th>@lang('labels.backend.access.users.table.first_name')</th>
                                <th>@lang('labels.backend.access.users.table.email')</th>
                                <th>@lang('labels.backend.access.users.table.confirmed')</th>

                                <th>@lang('labels.backend.access.users.table.other_permissions')</th>
                                <th>Office</th>

                                <th>@lang('labels.backend.access.users.table.roles')</th>
                                <th>@lang('labels.backend.access.users.table.last_updated')</th>
                                <th>@lang('labels.general.actions')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php($i = 0)

                            @foreach($users as $user)



                                    <span hidden>{{  $i++ }}</span>

                                        <tr>
                                            <td>{{ $user->last_name }}</td>
                                            <td>{{ $user->first_name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{!! $user->confirmed_label !!}</td>
                                            <td>{!! $user->permissions_label !!}</td>
                                            @if($user->role === 'admin')
                                                <td>Office of none</td>
                                            @else
                                                <td>{{ $user->homeOffice->name }}</td>
                                            @endif

                                            <td>{!! $user->roles_label !!}</td>
                                            <td>{{ $user->updated_at->diffForHumans() }}</td>
                                            <td>{!! $user->action_buttons !!}</td>
                                        </tr>
                                    {{--                                    @endif--}}


                            @endforeach                        </tbody>
                        </table>
                    </div>
                </div><!--col-->
            </div><!--row-->
            <div class="row">
                <div class="col-7">
                    <div class="float-left">
                        {!! $i++ !!} {{ trans_choice('labels.backend.access.users.table.total', $users->total()) }}
                    </div>
                    <div class="float-right">
                        <a href="{{ route('dashboard.auth.user.deactivated') }}"><button class="btn btn-danger" >
                                Terminated
                            </button></a>

                    </div>
                </div><!--col-->

                <div class="col-5">
                    <div class="float-right">
                        {!! $users->render() !!}
                    </div>
                </div><!--col-->
            </div><!--row-->
        </div><!--card-body-->
    </div><!--card-->
@endsection
