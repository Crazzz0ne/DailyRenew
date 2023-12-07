@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.dashboard.title'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10 col-md-8 col-sm-12 align-self-center">
            <div class="card">
                <div class="card-header">
                    <h4><strong>Edit Category</strong></h4>
                </div>
                <div class="card-body">
                    {{ html()->form('PUT', route('dashboard.printable.category.update', $category))->open() }}
                    <div class="row justify-content-between">
                        <div class="col-sm-12">
                            <div class="form-group">
                                {{ html()->label(__('Name'))->for('name') }}
                                {{ html()->text('name')
                                    ->class('form-control')
                                    ->placeholder(__('Make the best Prints ever'))
                                    ->attribute('maxlength',80)
                                    ->value($category->name)
                                    ->required()
                                    ->autofocus() }}
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                {{ html()->label(__('Description'))->for('description') }}
                                {{ html()->textarea('description')
                                    ->class('form-control')
                                    ->value($category->description)
                                    ->placeholder(__('Want to know the best way to....')) }}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="float-right">
                                <a href="{{ url()->previous() }}" data-toggle="tooltip"
                                   data-placement="top" title="" class="btn btn-sm btn-info"
                                   data-original-title="Back">
                                    Back</a>
                                {{ form_submit(__('buttons.general.crud.update')) }}
                            </div>
                        </div>
                    </div>
                    {{ html()->form()->close() }}
                </div>
            </div>
        </div>
    </div>
@endsection
