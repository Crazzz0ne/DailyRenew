@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.dashboard.title'))

@section('content')
    <div class="row justify-content-center">
        <div class="col col-sm-8 align-self-center">
            <div class="card shadow-lg">
                <div class="card-header">
                    <strong>
                        @lang('labels.backend.access.announcement.create')
                    </strong>
                </div><!--card-header-->

                <div class="card-body">
                    {{ html()->form('put', route('dashboard.announcement.update', $announcement))->open() }}
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                {{ html()->label(__('Subject'))->for('subject') }}
                                {{ html()->text('subject')
                                    ->class('form-control')
                                    ->value($announcement->subject)
                                    ->attribute('maxlength',250)
                                    ->required()
                                    ->autofocus() }}
                            </div><!--form-group-->
                        </div><!--col-->
                    </div><!--row-->
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <div>
                                    {{--                                    TODO does not load data for edit.--}}
                                   {!!  $announcement->trix('body')  !!}
                                </div>

                            </div><!--form-group-->
                        </div><!--col-->
                    </div><!--row-->

                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                {{ html()->label(__('Stick to Top'))->for('sticky') }}
                                {{ html()->checkbox('sticky')
                                    ->checked($announcement->sticky)
                                    ->class('form-control')
                                   }}
                            </div><!--form-group-->
                        </div><!--col-->
                        <div class="col">
                            <div class="form-group">
                                {{ html()->label(__('Choose Color'))->for('color') }}
                                <select class="form-control" name="color">
                                    @switch($announcement->color)
                                        @case('normal')
                                            <option selected value="0">Normal</option>
                                        <option value="green">Green</option>
                                        <option value="yellow">Yellow</option>
                                        <option value="red">Red</option>
                                        @break
                                        @case('green')
                                        <option value="0">Normal</option>
                                        <option selected value="green">Green</option>
                                        <option value="yellow">Yellow</option>
                                        <option value="red">Red</option>
                                        @break
                                        @case('yellow')
                                        <option value="0">Normal</option>
                                        <option value="green">Green</option>
                                        <option selected value="yellow">Yellow</option>
                                        <option value="red">Red</option>
                                        @break
                                        @case('red')
                                        <option selected value="0">Normal</option>
                                        <option value="green">Green</option>
                                        <option value="yellow">Yellow</option>
                                        <option selected value="red">Red</option>
                                        @break
                                        @default
                                        <option selected value="0">Normal</option>
                                        <option value="green">Green</option>
                                        <option value="yellow">Yellow</option>
                                        <option value="red">Red</option>
                                    @endswitch
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group mb-0 clearfix">
                                {{ form_submit(__('Submit')) }}
                            </div><!--form-group-->
                        </div><!--col-->
                    </div><!--row-->
                    {{ html()->form()->close() }}
                </div><!--card-body-->
            </div><!--card-->
        </div><!--col-->
    </div><!--row-->
@endsection
