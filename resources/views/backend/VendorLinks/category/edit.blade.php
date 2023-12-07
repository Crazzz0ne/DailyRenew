@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.dashboard.title'))

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-10 col-md-8 col-sm-12 align-self-center">
            <div class="card">
                <div class="card-header">
                    <strong>Edit Category</strong>
                </div>
                <div class="card-body">
                    {{ html()->form('put', route('dashboard.vendorlinks.category.update', $category))->open() }}

                    {{--                    {{ html()->form('PUT', route('dashboard.category.update', $category))->open() }}--}}
                    <form>
                        {{ csrf_field() }}
                        <div class="row justify-content-between">
                            <div class="col-lg-6 col-sm-12">
                                <div class="form-group">
                                    {{ html()->label(__('Name'))->for('name') }}
                                    {{ html()->text('name')
                                        ->class('form-control')
                                        ->value($category->name)
                                        ->placeholder(__('Install'))
                                        ->attribute('maxlength',80)
                                        ->required()
                                        ->autofocus() }}
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <div class="form-group">
                                    {{ html()->label(__('Description'))->for('description') }}
                                    {{ html()->text('description')
                                        ->class('form-control')
                                        ->value($category->description)
                                        ->placeholder(__('Install'))
                                        ->attribute('maxlength',80)
                                                   }}
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <div class="form-group">
                                    {{ html()->label(__('Sort ID'))->for('sort_order') }}
                                    {{ html()->text('sort_order')
                                        ->class('form-control')
                                        ->value($category->sort_order)

                                        ->attribute('maxlength',20)
                                        ->type('number')
                                        ->required()
                                        ->autofocus() }}
                                </div>
                            </div>
                        </div>
                        <div class="row pb-3 pr-3">
                            <div class="col">
                                <div class="float-right">
                                    <a href="{{ url()->previous() }}" data-toggle="tooltip"
                                       data-placement="top" title="" class="btn btn-sm btn-info"
                                       data-original-title="Back">
                                        Back</a>
                                    {{ form_submit(__('Update')) }}
                                </div>
                            </div>
                            {{ html()->form()->close() }}
                        </div>
                </div>
            </div>
        </div>
@endsection


