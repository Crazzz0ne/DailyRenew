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
                    <form action="{{ route('dashboard.masstext.store') }}" method="POST"
                          enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="row justify-content-between">
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <div class="form-group">
                                        <label for="exampleFormControlFile1">csv</label>
                                        <input type="file" class="form-control-file" id="exampleFormControlFile1" name="csv">
                                    </div>
                                    <div class="form-group">
                                        <label >Type</label>
                                        <select name="type">
                                            <option value="mailchimp">Mail Chimp</option>
                                            <option value="review">Review</option>

                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="float-right">
                                    {{ form_submit(__('buttons.general.crud.create')) }}
                                </div>
                            </div>

                        </div>

                    {{ html()->form()->close() }}
                </div>
            </div>
@endsection
