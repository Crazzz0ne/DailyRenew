@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.dashboard.title'))

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-10 col-md-8 col-sm-12 align-self-center">
            <div class="card">
                <div class="card-header">
                    <strong>
                        Edit Printable
                    </strong>
                </div>
                <div class="card-body">
                    <form action="{{ route('dashboard.printable.content.update', $content) }}" method="POST"
                          enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <input type="hidden" name="_method" value="PUT">
                        <div class="row justify-content-between">
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    {{ html()->label(__('Name'))->for('name') }}
                                    {{ html()->text('name')
                                        ->class('form-control')
                                        ->value($content->name)
                                        ->placeholder(__('Canvasing 101'))
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
                                            @if($content->category_id == $category->id)
                                                <option value="{{ $category->id }}"
                                                        selected>{{ $category->name }}</option>
                                            @else
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endif
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
                                            @if($content->state == $abbreviation)
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
                                            @if($vendor->id == $content->vendor_id)
                                                <option value="{{ $vendor->id }}"
                                                        selected>{{ $vendor->company_name }}</option>
                                            @else
                                                <option value="{{ $vendor->id }}">{{ $vendor->company_name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div>
                                        <textarea id="body" name="description">{{$content->description}}</textarea>
                                    </div>

                                </div><!--form-group-->

                            </div>
                        </div>
                        <div class="card card-body">
                            {{--                                <div class="form-group">--}}
                            {{--                                    <label>PDF</label>--}}
                            {{--                                    <div>--}}
                            {{--                                        <input class="form-control-file" type="file" name="pdf"/>--}}
                            {{--                                    </div>--}}
                            {{--                                </div>--}}
                            <div class="input-group">
                                <div class="custom-file">
                                    {{--                                    TODO: not sure what to do here? HOW do we show that this was a thing if it was? and then how do we tell the controller? --}}
                                    <input type="file" class="custom-file-input"
                                           aria-describedby="logoDiscription" name="pdf">
                                    <label class="custom-file-label" for="pdf" data-name="pdf">Select a new PDF to
                                        replace old one if
                                        you want.</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="float-right">
                                    {{ form_submit(__('buttons.general.crud.update')) }}
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
