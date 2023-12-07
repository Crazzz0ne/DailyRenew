@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.dashboard.title'))

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-10 col-md-8 col-sm-12 align-self-center">
            <div class="card">
                <div class="card-header">
                    <h4><strong>Create Printable Category</strong></h4>
                </div>
                <div class="card-body">
                    {{ html()->form('POST', route('dashboard.printable.category.store'))->open() }}
                    <div class="row justify-content-between">
                        <div class="col-sm-12">
                            <div class="form-group">
                                {{ html()->label(__('Name'))->for('name') }}
                                {{ html()->text('name')
                                    ->class('form-control')
                                    ->placeholder(__('SDGE'))
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
                                    ->placeholder(__('Use this paperwork to start stage three of make all the money...')) }}
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
