@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.dashboard.title'))

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-10 col-md-8 col-sm-12 align-self-center">
            <div class="card">
                <div class="card-header">
                    <h4><strong>Create Training Category</strong></h4>
                </div>
                <div class="card-body">
                    {{ html()->form('POST', route('dashboard.mastermind.category.store'))->open() }}
                    <div class="row justify-content-between">
                        <div class="col-sm-12">
                            <div class="form-group">
                                {{ html()->label(__('Name'))->for('name') }}
                                {{ html()->text('name')
                                    ->class('form-control')
                                    ->placeholder(__('Canvasing 101'))
                                    ->attribute('maxlength',80)
                                    ->required()
                                    ->autofocus() }}
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                {{ html()->label(__('Description'))->for('description') }}
                                {{ html()->textarea('description')
                                    ->class('form-control')
                                    ->placeholder(__('Great ambition is the passion of a great character. Those endowed with
                                    it may perform very good or very bad acts. All depends on the principles which direct them.
                                      ')) }}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="float-right">
                                {{ form_submit(__('buttons.general.crud.create')) }}
                            </div>
                        </div>
                    </div>
                    {{ html()->form()->close() }}
                </div>
            </div>
        </div>
    </div>
@endsection
