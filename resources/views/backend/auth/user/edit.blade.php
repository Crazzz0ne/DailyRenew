@extends('backend.layouts.app')

@section('title', __('labels.backend.access.users.management') . ' | ' . __('labels.backend.access.users.edit'))

@section('breadcrumb-links')
    @include('backend.auth.user.includes.breadcrumb-links')
@endsection

@section('content')

    {{ html()->modelForm($user, 'PATCH', route('dashboard.auth.user.update', $user->id))->acceptsFiles()->class('form-horizontal')->open() }}

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-sm-5">
                    <h4 class="card-title mb-0">
                        @lang('labels.backend.access.users.management')
                        <small class="text-muted">@lang('labels.backend.access.users.edit')</small>
                    </h4>
                </div><!--col-->
            </div><!--row-->
            <hr>
            <div class="row mt-4 mb-4">
                <div class="col">
                    <div class="form-group row">
                        {{ html()->label(__('validation.attributes.backend.access.users.first_name'))->class('col-md-2 form-control-label')->for('first_name') }}

                        <div class="col-md-10">
                            {{ html()->text('first_name')
                                ->class('form-control')
                                ->placeholder(__('validation.attributes.backend.access.users.first_name'))
                                ->attribute('maxlength', 191)
                                ->required() }}
                        </div><!--col-->
                    </div><!--form-group-->
                    <div class="form-group row">
                        {{ html()->label(__('validation.attributes.backend.access.users.last_name'))->class('col-md-2 form-control-label')->for('last_name') }}

                        <div class="col-md-10">
                            {{ html()->text('last_name')
                                ->class('form-control')
                                ->placeholder(__('validation.attributes.backend.access.users.last_name'))
                                ->attribute('maxlength', 191)
                                ->required() }}
                        </div><!--col-->
                    </div><!--form-group-->
                    <div class="form-group row">
                        {{ html()->label(__('validation.attributes.backend.access.users.phone'))->class('col-md-2 form-control-label')->for('phone_number') }}
                        <div class="col-md-10">
                            {{ html()->text('phone_number')
                                ->class('form-control')
                                ->placeholder(__('310-555-5555'))
                                ->attribute('maxlength', 13)
                                 }}
                        </div><!--col-->
                    </div><!--form-group-->
                    <div class="form-group row">
                        {{ html()->label('HIS License')->class('col-md-2 form-control-label')->for('his_license') }}
                        <div class="col-md-10">
                            {{ html()->text('his_license')
                                ->class('form-control')
                                ->placeholder(__('123456 SP'))
                                ->attribute('maxlength', 20)
                                 }}
                        </div><!--col-->
                    </div><!--form-group-->
                    <div class="form-group row">
                        {{ html()->label('HIS License')->class('col-md-2 form-control-label')->for('his_file') }}
                        <div class="col-md-10">
                            <div class="input-group form-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input"
                                           aria-describedby="logoDiscription" name="his_file">
                                    <label class="custom-file-label" for="his_file">HIS License</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        {{ html()->label(__('validation.attributes.backend.access.users.email'))->class('col-md-2 form-control-label')->for('email') }}

                        <div class="col-md-10">
                            {{ html()->email('email')
                                ->class('form-control')
                                ->placeholder(__('validation.attributes.backend.access.users.email'))
                                ->attribute('maxlength', 191)
                                ->required() }}
                        </div><!--col-->
                    </div><!--form-group-->
                    @if(auth()->user()->roles[0]->name == 'manager')
                        <div class="form-group row" hidden>
                            <div class="col-2">
                                {{ html()->label(__('Office'))->for('Office') }}
                            </div>
                            {{--                        {{ dd(auth()->user()->office[0]) }}--}}
                            {{--                        {{ dd(auth()->user()->roles[0]->name ) }}--}}
                            <div class="col-3">
                                <select class="form-control" name="office" hidden>
                                    @foreach($offices as $office)
                                        @if($office->id === $user->office_id)
                                            <option value="{{ $office->id }}" selected>{{ $office->name }}</option>
                                        @else
                                            <option value="{{ $office->id }}">{{ $office->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @else
                        <div class="form-group row">
                            <div class="col-2">
                                {{ html()->label(__('Office'))->for('Office') }}
                            </div>
                            <div class="col-5">

                                <select class="form-control" name="office">
                                    @foreach($offices as $office)
                                        @if($office->id == $user->office_id)
                                            <option value="{{ $office->id }}" selected>{{ $office->name }}</option>
                                        @else
                                            <option value="{{ $office->id }}">{{ $office->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            {{--                            Daves --}}
                            {{--                            <div class="col-3">--}}
                            {{--                                {{ html()->label(__('Manager Efficiency'))->for('manager_efficiency') }}--}}
                            {{--                                {{ html()->checkbox('manager_efficiency')--}}
                            {{--                                    ->class('form-control in-line')--}}
                            {{--                                   }}--}}
                            {{--                            </div>--}}
                        </div>
                    @endif

                    <div class="form-group row">


                            <div class="row justify-content-center">
                                <div class="col-sm-12 col-md-8">
                                    <div class="card">
                                        <div class="card-header"> @lang('labels.backend.access.users.table.roles')</div>
                                        <div class="card-body">
                                            @if($roles->count())
                                                @foreach($roles as $role)
                                                    @if(auth()->user()->roles[0]->name === 'manager' && ($role->name == 'user' || $role->name == 'manager'))

                                                        <div class="card">
                                                            <div class="card-header">
                                                                <div class="checkbox d-flex align-items-center">
                                                                    {{ html()->label(
                                                                            html()->checkbox('roles[]', in_array($role->name, $userRoles), $role->name)
                                                                                    ->class('switch-input')
                                                                                    ->id('role-'.$role->id)
                                                                            . '<span class="switch-slider" data-checked="on" data-unchecked="off"></span>')
                                                                        ->class('switch switch-label switch-pill switch-primary mr-2')
                                                                        ->for('role-'.$role->id) }}
                                                                    {{ html()->label(ucwords($role->name))->for('role-'.$role->id) }}
                                                                </div>
                                                            </div>
                                                            <div class="card-body">
                                                                @if($role->id != 1)
                                                                    @if($role->permissions->count())
                                                                        @foreach($role->permissions as $permission)
                                                                            <i class="fas fa-dot-circle"></i> {{ ucwords($permission->name) }}
                                                                        @endforeach
                                                                    @else
                                                                        @lang('labels.general.none')
                                                                    @endif
                                                                @else
                                                                    @lang('labels.backend.access.users.all_permissions')
                                                                @endif
                                                            </div>
                                                        </div><!--card-->
                                                    @elseif (auth()->user()->roles[0]->name === 'executive' && ($role->name != 'administrator' && $role->name != 'super'))
                                                        <div class="card">
                                                            <div class="card-header">
                                                                <div class="checkbox d-flex align-items-center">
                                                                    {{ html()->label(
                                                                            html()->checkbox('roles[]', in_array($role->name, $userRoles), $role->name)
                                                                                    ->class('switch-input')
                                                                                    ->id('role-'.$role->id)
                                                                            . '<span class="switch-slider" data-checked="on" data-unchecked="off"></span>')
                                                                        ->class('switch switch-label switch-pill switch-primary mr-2')
                                                                        ->for('role-'.$role->id) }}
                                                                    {{ html()->label(ucwords($role->name))->for('role-'.$role->id) }}
                                                                </div>
                                                            </div>
                                                            <div class="card-body">
                                                                @if($role->id != 1)
                                                                    @if($role->permissions->count())
                                                                        @foreach($role->permissions as $permission)
                                                                            <i class="fas fa-dot-circle"></i> {{ ucwords($permission->name) }}
                                                                        @endforeach
                                                                    @else
                                                                        @lang('labels.general.none')
                                                                    @endif
                                                                @else
                                                                    @lang('labels.backend.access.users.all_permissions')
                                                                @endif
                                                            </div>
                                                        </div><!--card-->
                                                    @elseif(auth()->user()->roles[0]->name === 'administrator')

                                                        {{--                                                    {{ auth()->user()->roles[0]->name }}--}}
                                                        <div class="card">
                                                            <div class="card-header">
                                                                <div class="checkbox d-flex align-items-center">
                                                                    {{ html()->label(
                                                                            html()->checkbox('roles[]', in_array($role->name, $userRoles), $role->name)
                                                                                    ->class('switch-input')
                                                                                    ->id('role-'.$role->id)
                                                                            . '<span class="switch-slider" data-checked="on" data-unchecked="off"></span>')
                                                                        ->class('switch switch-label switch-pill switch-primary mr-2')
                                                                        ->for('role-'.$role->id) }}
                                                                    {{ html()->label(ucwords($role->name))->for('role-'.$role->id) }}
                                                                </div>
                                                            </div>
                                                            <div class="card-body">
                                                                @if($role->id != 1)
                                                                    @if($role->permissions->count())
                                                                        @foreach($role->permissions as $permission)
                                                                            <i class="fas fa-dot-circle"></i> {{ ucwords($permission->name) }}
                                                                        @endforeach
                                                                    @else
                                                                        @lang('labels.general.none')
                                                                    @endif
                                                                @else
                                                                    @lang('labels.backend.access.users.all_permissions')
                                                                @endif
                                                            </div>
                                                        </div><!--card-->
                                                    @endif
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @if(auth()->user()->can('administrate company'))
                                    <div class="col-sm-12 col-md-4">
                                        <div class="card">
                                            <div class="card-header">
                                                @lang('labels.backend.access.users.table.permissions')
                                            </div>
                                            <div class="card-body">
                                                @if($permissions->count())
                                                    @foreach($permissions as $permission)

                                                        <div class="checkbox d-flex align-items-center">
                                                            {{ html()->label(
                                                                    html()->checkbox('permissions[]', in_array($permission->name, $userPermissions), $permission->name)
                                                                            ->class('switch-input')
                                                                            ->id('permission-'.$permission->id)
                                                                        . '<span class="switch-slider" data-checked="on" data-unchecked="off"></span>')
                                                                    ->class('switch switch-label switch-pill switch-primary mr-2')
                                                                ->for('permission-'.$permission->id) }}
                                                            {{ html()->label(ucwords($permission->name))->for('permission-'.$permission->id) }}
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                        </div><!--form-group-->

                </div><!--col-->
            </div><!--row-->
        </div><!--card-body-->

        <div class="card-footer">
            <div class="row">
                <div class="col">
                    {{ form_cancel(route('dashboard.auth.user.index'), __('buttons.general.cancel')) }}
                </div><!--col-->
                <div class="col text-right">
                    {{ form_submit(__('buttons.general.crud.update')) }}
                </div><!--row-->
            </div><!--row-->
        </div><!--card-footer-->
    {{ html()->closeModelForm() }}
@endsection
