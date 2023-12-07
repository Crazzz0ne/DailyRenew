@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.dashboard.title'))

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-5 col-sm-12 align-self-center">
            <div class="card">
                <div class="card-header">
                    <strong>
                        @lang('labels.backend.access.announcement.create')
                    </strong>
                </div><!--card-header-->
                <div class="card-body">
                    {{ html()->form('POST', route('dashboard.office.market.store'))->open() }}
                    <div class="row justify-content-between">
                        <div class="col-lg-3 col-sm-12">
                            <div class="form-group">
                                {{ html()->label(__('Market Name'))->for('name') }}
                                {{ html()->text('name')
                                    ->class('form-control')
                                    ->placeholder(__('Ragnarok'))
                                    ->attribute('maxlength',80)
                                    ->required()
                                    ->autofocus() }}
                            </div><!--form-group-->
                        </div>
                        <div class="col-lg-3  col-sm-12">
{{--                            <div class="form-group">--}}
{{--                                {{ html()->label(__('Street Address'))->for('address') }}--}}
{{--                                {{ html()->text('address')--}}
{{--                                    ->class('form-control')--}}
{{--                                    ->placeholder(__('123 Main st')) }}--}}
{{--                            </div><!--form-group-->--}}
                        </div><!--col-->
                        <div class="col-lg-3  col-sm-12">
{{--                            <div class="form-group">--}}
{{--                                {{ html()->label(__('City'))->for('city') }}--}}
{{--                                {{ html()->text('city')--}}
{{--                                ->placeholder(__('City of Angels'))--}}
{{--                                    ->class('form-control')--}}
{{--                                    }}--}}
{{--                            </div><!--form-group-->--}}
                        </div><!--col-->
                    </div><!--row-->
                    <div class="row justify-content-between">
                        <div class="col-lg-3  col-sm-12">
                            <div class="form-group">
                                {{ html()->label(__('State'))->for('state') }}
                                <select class="form-control" name="state">
{{--                                    @foreach($states as $state)--}}
{{--                                        @if($state == 'CA')--}}
{{--                                            <option value="{{ $state }}" selected>{{ $state }}</option>--}}
{{--                                        @else--}}
{{--                                            <option value="{{ $state }}">{{ $state }}</option>--}}
{{--                                        @endif--}}
{{--                                    @endforeach--}}
                                </select>
                            </div><!--form-group-->
                        </div><!--col-->
                        <div class="col-lg-3  col-sm-12">
                            <div class="form-group">
{{--                                {{ html()->label(__('Phone Number'))->for('phone') }}--}}
{{--                                {{ html()->text('phone')--}}
{{--                                ->placeholder(__('310-555-5555'))--}}
{{--                                    ->class('form-control')--}}
{{--                                    }}--}}
                            </div><!--form-group-->
                        </div><!--col-->
                        <div class="col-lg-3  col-sm-12">
                            <div class="form-group">
{{--                                {{ html()->label(__('Email'))->for('email') }}--}}
{{--                                {{ html()->text('email')--}}
{{--                                ->placeholder(__('Info@cuic.us'))--}}
{{--                                    ->class('form-control')--}}
{{--                                    }}--}}
                            </div><!--form-group-->
                        </div><!--col-->
                        <div class="col-lg-3 col-sm-8">
                            <div class="form-group">
                                <label>Market</label>
                                <select class="form-control" name="market">
{{--                                    @foreach($markets as $id => $name)--}}
{{--                                        <option value={{ $id }}">{{ ucwords($name) }}</option>--}}
{{--                                    @endforeach--}}
                                            </select>
                                        </div><!--form-group-->
                                    </div><!--col-->
                                </div><!--row-->
                                <div class=" row pt-5">
                                        <div class="col">
                                            <div class="form-group mb-0 clearfix">
                                                {{ form_submit(__('Create')) }}
                                            </div><!--form-group-->
                                        </div><!--col-->
                            </div><!--row-->
                            {{ html()->form()->close() }}
                        </div><!--card-body-->
                    </div><!--card-->
                </div><!--col-->
            </div><!--row-->
@endsection
