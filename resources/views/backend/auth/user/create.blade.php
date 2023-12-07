@extends('backend.layouts.app')

@section('title', __('labels.backend.access.users.management') . ' | ' . __('labels.backend.access.users.create'))

@section('breadcrumb-links')
    @include('backend.auth.user.includes.breadcrumb-links')
@endsection

@section('content')
    <form action="{{ route('dashboard.auth.user.store') }}" method="POST"
          enctype="multipart/form-data">
        {{ csrf_field() }}
        {{--    {{ html()->form('POST', route('dashboard.auth.user.store'))->class('form-horizontal')->attribute('autocomplete', 'off')->open()}}--}}
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-5">
                        <h4 class="card-title mb-0">
                            @lang('labels.backend.access.users.management')
                            <small class="text-muted">@lang('labels.backend.access.users.create')</small>
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
                                    ->required()
                                    ->autofocus() }}
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
                                    ->attribute('maxlength', 13)
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
                        <div class="form-group row">
                            {{ html()->label(__('validation.attributes.backend.access.users.password'))->class('col-md-2 form-control-label')->for('password') }}
                            <div class="col-md-10">
                                {{ html()->password('password')
                                    ->class('form-control')
                                    ->placeholder(__('validation.attributes.backend.access.users.password'))
                                    ->required() }}
                            </div><!--col-->
                        </div><!--form-group-->
                        <div class="form-group row">
                            {{ html()->label(__('validation.attributes.backend.access.users.password_confirmation'))->class('col-md-2 form-control-label')->for('password_confirmation') }}
                            <div class="col-md-10">
                                {{ html()->password('password_confirmation')
                                    ->class('form-control')
                                    ->placeholder(__('validation.attributes.backend.access.users.password_confirmation'))
                                    ->required() }}
                            </div><!--col-->
                        </div><!--form-group-->
                        <label class="switch switch-label switch-pill switch-primary">
                            {{ html()->checkbox('active', true)->class('switch-input') }}
                            <span class="switch-slider" data-checked="yes" data-unchecked="no" hidden></span>
                        </label>
                        <label class="switch switch-label switch-pill switch-primary">
                            {{ html()->checkbox('confirmed', true)->class('switch-input') }}
                            <span class="switch-slider" data-checked="yes" data-unchecked="no" hidden></span>
                        </label>
{{--                        @if(! config('access.users.requires_approval'))--}}
{{--                            <div class="form-group row">--}}
{{--                                {{ html()->label(__('validation.attributes.backend.access.users.send_confirmation_email') . '<br/>' . '<small>' .  __('strings.backend.access.users.if_confirmed_off') . '</small>')->class('col-md-2 form-control-label')->for('confirmation_email') }}--}}

{{--                                <div class="col-md-10">--}}
{{--                                    <label class="switch switch-label switch-pill switch-primary">--}}
{{--                                        {{ html()->checkbox('confirmation_email')->class('switch-input') }}--}}
{{--                                        <span class="switch-slider" data-checked="yes" data-unchecked="no"></span>--}}
{{--                                    </label>--}}
{{--                                </div><!--col-->--}}
{{--                            </div><!--form-group-->--}}
{{--                        @endif--}}
                        @if(!auth()->user()->can('administrate all offices'))
                            <div class="form-group row" hidden>
                                <div class="col-2">
                                    {{ html()->label(__('Office'))->for('Office') }}
                                </div>
                                <div class="col-3">
                                    <select class="form-control" name="office" hidden>
                                        @foreach($offices as $office)
                                            @if($office->id == auth()->user()->office_id)
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
                                <div class="col-3">
                                    <select class="form-control" name="office">
                                        @foreach($offices as $office)
                                            @if($office->id == auth()->user()->office_id)
                                                <option value="{{ $office->id }}" selected>{{ $office->name }}</option>
                                            @else
                                                <option value="{{ $office->id }}">{{ $office->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif
{{--                        Positions --}}
                        <div class="container pb-3">

                            <input type="checkbox" name="roles[1]" value="user" checked hidden>
                        </div>

                    </div><!--col-->
                </div><!--row-->
            </div><!--card-body-->

            <div class="card-footer clearfix">
                <div class="row">
                    <div class="col">
                        {{ form_cancel(route('dashboard.auth.user.index'), __('buttons.general.cancel')) }}
                    </div><!--col-->

                    <div class="col text-right">
                        {{ form_submit(__('buttons.general.crud.create')) }}
                    </div><!--col-->
                </div><!--row-->
            </div><!--card-footer-->
        </div><!--card-->
    {{ html()->form()->close() }}
@endsection
