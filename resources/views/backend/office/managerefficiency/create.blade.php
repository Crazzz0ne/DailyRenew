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
                    {{ html()->form('POST', route('dashboard.managerefficiency.store'))->open() }}
                    <div class="row justify-content-between">
                        <div class="col-sm-12">
                            <div class="form-group">
                                {{ html()->label(__('How many canvassers, openers, and closers did you have on average this month'))->for('employeeCount') }}
                                {{ html()->number('employeeCount')
                                    ->class('form-control')
                                    ->placeholder(__('10'))
                                    ->attribute('maxlength',3)
                                    ->required()
                                    ->autofocus() }}
                            </div><!--form-group-->
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                {{ html()->label(__('How many managers did you have on average this month'))->for('managerCount') }}
                                {{ html()->number('managerCount')
                                    ->class('form-control')
                                    ->placeholder(__('4'))
                                    ->attribute('maxlength',2)
                                     }}
                            </div><!--form-group-->
                        </div><!--col-->
                        <div class="col-sm-12">
                            <div class="form-group">
                                {{ html()->label(__('How many other people did you have working in the dealership on
                                average this month (part-time counts as 1/2)'))->for('partTime') }}
                                {{ html()->number('partTime')
                                ->placeholder(__('12'))
                                    ->class('form-control')
                                    }}
                            </div><!--form-group-->
                        </div><!--col-->
                    </div><!--row-->
                    <div class="row justify-content-between">
                        <div class="col-sm-12">
                            <div class="form-group">

                            </div><!--form-group-->
                        </div><!--col-->
                        <div class="col-sm-12">
                            <div class="form-group">
                                {{ html()->label(__('Please check this box if this total accurately represents the
                                number of people working in your dealership on average this month'))->for('truth') }}
                                {{ html()->checkbox('truth')
                                    ->class('form-control')
                                    }}
                            </div><!--form-group-->
                        </div><!--col-->
                    </div><!--row-->
                    <div class="row pt-5">
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
