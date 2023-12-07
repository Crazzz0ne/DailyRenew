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

                    <form action="{{ route('dashboard.mastermind.content.store') }}" method="POST"
                          enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="row justify-content-between">
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    {{ html()->label(__('Name'))->for('name') }}
                                    {{ html()->text('name')
                                        ->class('form-control')
                                        ->placeholder(__('Best idea ever'))
                                        ->attribute('maxlength',80)
                                        ->required()
                                        ->autofocus() }}
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    {{ html()->label(__('Category'))->for('category') }}
                                    <select class="form-control" name="category">
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            {{--                            <div class="col-md-6 col-sm-12">--}}
                            {{--                                <div class="form-group">--}}
                            {{--                                    {{ html()->label(__('State'))->for('state') }}--}}
                            {{--                                    <select class="form-control" name="state">--}}
                            {{--                                        @foreach($states as $abbreviation => $state)--}}
                            {{--                                            @if('CA' == $abbreviation)--}}
                            {{--                                                <option value="{{ $abbreviation }}" selected>{{ $state }}</option>--}}
                            {{--                                            @else--}}
                            {{--                                                <option value="{{ $abbreviation }}">{{ $state }}</option>--}}
                            {{--                                            @endif--}}
                            {{--                                        @endforeach--}}
                            {{--                                    </select>--}}
                            {{--                                </div>--}}
                            {{--                            </div>--}}
                            {{--                            <div class="col-md-6 col-sm-12">--}}
                            {{--                                <div class="form-group">--}}
                            {{--                                    {{ html()->label(__('Vendor'))->for('vendor_id') }}--}}
                            {{--                                    <select class="form-control" name="vendor_id">--}}
                            {{--                                        <option value="0">All</option>--}}
                            {{--                                        @foreach($vendors as $vendor)--}}

                            {{--                                            <option value="{{ $vendor->id }}">{{ $vendor->company_name }}</option>--}}
                            {{--                                        @endforeach--}}
                            {{--                                    </select>--}}
                            {{--                                </div>--}}
                            {{--                            </div>--}}
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
                            <div class="col-sm-12">
                                <div class="form-group">
                                    {{ html()->label(__('Tags'))->for('tags') }}
                                    {{ html()->text('tags')
                                        ->class('form-control')
                                        ->placeholder(__('Inspiring, 130IQ,')) }}
                                </div>
                            </div>
                        </div>
                        <div class="container" id="mastermindType">
                            <p>
                                <a class="btn btn-primary" data-toggle="collapse" href="#YouTube" role="button"
                                   aria-expanded="false" aria-controls="YouTube">
                                    YouTube
                                </a>
                                <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#pdf"
                                        aria-expanded="false" aria-controls="pdf">
                                    PDF
                                </button>
                                <button class="btn btn-primary" type="button" data-toggle="collapse"
                                        data-target="#audio"
                                        aria-expanded="false" aria-controls="audio">
                                    Audio
                                </button>
                            </p>
                            <div class="collapse" id="YouTube" data-parent="#mastermindType">
                                <div class="card card-body">
                                    {{ html()->label(__('Youtube URL'))->for('youTube') }}
                                    {{ html()->text('youTube')
                                        ->class('form-control')
                                        ->placeholder(__('https://www.youtube.com/watch?v=oHg5SJYRHA0'))
                                        ->attribute('maxlength', 80)
                                         }}

                                </div>
                            </div>
                            <div class="collapse" id="pdf" data-parent="#mastermindType">
                                <div class="card card-body">
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input"
                                                   aria-describedby="logoDiscription" name="pdf">
                                            <label class="custom-file-label" for="pdf">Choose a PDF</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="collapse" id="audio" data-parent="#mastermindType">
                                <div class="card card-body">
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" name="audio"
                                                   aria-describedby="logoDiscription">
                                            <label class="custom-file-label" for="audio">Chose a Audio File</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-start">
                            @can('administrate all masterminds')
                            <div class="col">
                                <div class="form-group">
                                    {{ html()->label(__('Approved'))->for('approved') }}
                                    {{ html()->checkbox('approved')
                                        ->class('form-control')
                                       }}
                                </div><!--form-group-->
                            </div><!--col-->
                            @endcan
                            <div class="row">
                                <div class="col">
                                    <div class="float-right">
                                        {{ form_submit(__('buttons.general.crud.create')) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    {{ html()->form()->close() }}

                </div>
            </div>
        </div>
@endsection
