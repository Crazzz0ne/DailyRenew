@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.dashboard.title'))

@section('content')
    {{--{{dd($office)}}--}}
    <div class="row justify-content-center">
        <div class="align-self-center">
            <div class="card">
                <div class="card-header">
                    <strong>
                        @lang('labels.backend.access.office.update')
                    </strong>
                </div><!--card-header-->
                {{ html()->form('put', route('dashboard.office.update', $office))->open() }}
                <div class="card-body">

                    <div class="row ">
                        <div class="col-lg-3 col-sm-8">
                            <div class="form-group">
                                {{ html()->label(__('Office Name'))->for('name') }}
                                {{ html()->text('name')
                                    ->class('form-control')
                                    ->value($office->name)
                                    ->placeholder(__('Ragnarok'))
                                    ->attribute('maxlength',80)
                                    ->required()
                                    ->autofocus() }}
                            </div><!--form-group-->
                        </div>
                        <div class="col-lg-3 col-sm-8">
                            <div class="form-group">
                                {{ html()->label(__('Street Address'))->for('address') }}
                                {{ html()->text('address')
                                    ->value($office->address)
                                    ->class('form-control')
                                    ->placeholder(__('123 Main st')) }}
                            </div><!--form-group-->
                        </div><!--col-->
                        <div class="col-lg-3 col-sm-8">
                            <div class="form-group">
                                {{ html()->label(__('City'))->for('city') }}
                                {{ html()->text('city')
                                    ->value($office->city)
                                    ->placeholder(__('City of Angels'))
                                    ->class('form-control')
                                    }}
                            </div><!--form-group-->
                        </div><!--col-->
                    </div><!--row-->
                    <div class="row justify-content-between">
                        <div class="col-lg-3 col-sm-8">
                            <div class="form-group">
                                {{ html()->label(__('State'))->for('state') }}
                                <select class="form-control" name="state">
                                    {{--ToDo:: auto slelects ca should select the real state--}}
                                    @foreach($states as $state)
                                        @if($state == 'CA')
                                            <option value="{{ $state }}" selected>{{ $state }}</option>
                                        @else
                                            <option value="{{ $state }}">{{ $state }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-8">
                            <div class="form-group">
                                {{ html()->label(__('Phone Number'))->for('phone') }}
                                {{ html()->text('phone')
                                ->placeholder(__('310-555-5555'))
                                ->value($office->phone_number)
                                    ->class('form-control')
                                    }}
                            </div><!--form-group-->
                        </div>
                        <div class="col-lg-3 col-sm-3 col-sm-8">
                            <div class="form-group">
                                {{ html()->label(__('Email'))->for('email') }}
                                {{ html()->email('email')
                                ->placeholder(__('Info@scout.solar'))
                                ->value($office->email)
                                ->class('form-control')
                                    }}
                            </div><!--form-group-->
                        </div>
                        <div class="col-lg-3 col-sm-8">

                            @can('administrate company')
                                <label>Market</label>
                                <select class="form-control" name="market">
                                    @foreach($markets as $id => $name)
                                        @if ($office->market_id == $id)
                                            <option value={{ $id }}" selected>{{ ucwords($name) }}</option>
                                        @else
                                                <option value={{ $id }}">{{ ucwords($name) }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            @endCan
                        </div>
                        @can('administrate company')
                            <div class="col-lg-3 col-md-3 col-sm-8">
                                <div class="form-group row">
                                    <div class="custom-control">
                                        {{ html()->label(__('Default Price per watt'))->for('default_ppw') }}
                                        {{ html()->text('default_ppw')
                                            ->value($office->default_ppw)
                                            ->placeholder(__('3.50'))
                                            ->class('form-control')
                                            }}
                                    </div>
                                </div>
                            </div>
                        @endcan
                    </div>


                    @can('administrate company')
                        <div class="text-center">
                            <h3>Rules</h3>
                        </div>
                        <div class="row justify-content-start pt-3 m-2">
                            <div class="col-3">
                                <div class="custom-control custom-switch text-center">
                                    <label class="" for="require_integrations">Require Integrations</label>
                                    @if($office->require_integrations)
                                        <input type="checkbox" class="" id="require_integrations"
                                               name="require_integrations" checked>
                                    @else
                                        <input type="checkbox" class="" id="require_integrations"
                                               name="require_integrations">
                                    @endif
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="custom-control custom-switch text-center">
                                    <label class="" for="call_center">Call Center</label>
                                    @if($office->call_center)
                                        <input type="checkbox" class="" id="call_center"
                                               name="call_center" checked>
                                    @else
                                        <input type="checkbox" class="" id="call_center"
                                               name="call_center">
                                    @endif
                                </div>
                            </div>

                        </div>
                        <div class="row justify-content-start pt-3 m-2">
                            <div class="col-md-6 col-sm-12">

                                <div class="form-group">
                                    <label>Roles</label>
                                    <select id="id-name" class="form-control" style="height: 500px" name="roles[]"
                                            multiple="multiple">
                                        @foreach($roles as $role)
                                            @if(isset($options->roles))
                                                @if(in_array($role->name, $options->roles))
                                                    <option value="{{ $role->name }}"
                                                            selected> {{ $role->name }}</option>
                                                @else
                                                    <option value="{{ $role->name }}"> {{ $role->name }}</option>
                                                @endif
                                            @else
                                                <option value="{{ $role->name }}"> {{ $role->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>

                                </div><!--form-group-->
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="form-group">
                                    <label>Permissions</label>
                                    <select id="id-name" class="form-control" style="height: 500px"
                                            name="permissions[]"
                                            multiple="multiple">
                                        @foreach($permissions as $permission)
                                            @if(isset($options->permissions))
                                                @if(in_array($permission->name, $options->permissions))
                                                    <option value="{{ $permission->name }}"
                                                            selected> {{ $permission->name }}</option>
                                                @else
                                                    <option
                                                        value="{{ $permission->name }}"> {{ $permission->name }}</option>
                                                @endif
                                            @else
                                                <option
                                                    value="{{ $permission->name }}"> {{ $permission->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>

                                </div><!--form-group-->
                            </div>
                        </div>
                    @endcan

                </div>
                <div class="px-2">
                    <p>Round Robin Cities</p>
                    <eligible-city-by-office-city-select-group


                        :office_id="{{ $office->id }}"
                    ></eligible-city-by-office-city-select-group>
                </div>
                <div>
                    <Teams
                    :user="{{auth()->user()}}"
                    :office-id="{{$office->id}}">

                    </Teams>
                </div>

                <div class="row pt-5 pl-2">
                    <div class="col">
                        <div class="form-group clearfix">
                            {{ form_submit(__('Update')) }}
                        </div><!--form-group-->
                    </div><!--col-->
                </div><!--row-->
                {{ html()->form()->close() }}
            </div><!--col-->
        </div><!--row-->
@endsection
