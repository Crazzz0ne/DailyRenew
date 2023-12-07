@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.dashboard.title'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10 col-md-8 col-sm-12 align-self-center">
            <div class="card">
                <div class="card-header">
                    <strong>
                        New Partner
                    </strong>
                </div>
                <div class="card-body">
                    {{--                {{ html()->form('POST', route('dashboard.VendorLinks.store'))->open() }}--}}
                    <form action="{{ route('dashboard.vendorlinks.vendor.store') }}" method="POST"
                          enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-lg-6 col-sm-12">
                                <div class="form-group">
                                    {{ html()->label(__('Name'))->for('name') }}
                                    {{ html()->text('name')
                                        ->class('form-control')
                                        ->placeholder(__('Monsanto'))
                                        ->attribute('maxlength',80)
                                        ->required()
                                        ->autofocus() }}
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <div class="form-group">
                                    {{ html()->label(__('Web Address'))->for('web_address') }}
                                    {{ html()->text('web_address')
                                        ->class('form-control')
                                        ->placeholder(__('dashboard.cuic.us/login'))
                                        ->attribute('maxlength',250) }}
                                </div>
                            </div>
                            <div class="col-lg-12 my-4">
                                <p>Logo</p>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="logo" name="logo"
                                               aria-describedby="logoDiscription">
                                        <label class="custom-file-label" for="logo">Choose Logo</label>
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
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
