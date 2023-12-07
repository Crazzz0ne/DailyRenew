@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.dashboard.title'))

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-10 col-md-8 col-sm-12 align-self-center">
            <div class="card">
                <div class="card-header">
                    <strong>
                        @lang('labels.backend.access.announcement.create')
                    </strong>
                </div>
                <div class="card-body">
                    {{--                    {{ html()->form('POST', route('dashboard.training.content.store'))->open() }}--}}
                    <form action="{{ route('dashboard.printable.content.store') }}" method="POST"
                          enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="row justify-content-between">
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    {{ html()->label(__('Name'))->for('name') }}
                                    {{ html()->text('name')
                                        ->class('form-control')
                                        ->placeholder(__('CustomerTableSeeder'))
                                        ->attribute('maxlength',80)
                                        ->required()
                                        ->autofocus() }}
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    {{ html()->label(__('Category'))->for('category') }}
                                    <select class="form-control" name="category">
                                        {{--ToDo:: auto slelects ca should select the real state--}}
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    {{ html()->label(__('State'))->for('state') }}
                                    <select class="form-control" name="state">
                                        {{--ToDo:: auto slelects ca should select the real state--}}
                                        @foreach($states as $abbreviation => $state)
                                            @if('CA' == $abbreviation)
                                                <option value="{{ $abbreviation }}" selected>{{ $state }}</option>
                                            @else
                                                <option value="{{ $abbreviation }}">{{ $state }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    {{ html()->label(__('Vendor'))->for('vendor_id') }}
                                    <select class="form-control" name="vendor_id">
                                        @foreach($vendors as $vendor)
                                            <option value="{{ $vendor->id }}">{{ $vendor->company_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div>
                                        <label>
                                            Description
                                        </label>
                                    </div>
                                    <div>
                                        <textarea id="body" name="description"></textarea>
                                    </div>
                                </div><!--form-group-->
                            </div>

                        </div>
                        <div class="col-5 pt-4 px-2 pb-1" id="pdf">
                            <div class="card card-body">

                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" name="pdf" class="custom-file-input"
                                               aria-describedby="">
                                        <label class="custom-file-label" for="pdf">Choose a PDF</label>
                                    </div>
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
