@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.dashboard.title'))

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-8 con-sm-12 align-self-center">
            <div class="card">
                <div class="card-header">
                    <strong>
                        create new Password
                    </strong>

                </div>
                <div class="card-body">
                    <form action="{{ route('dashboard.vendorlinks.linklogin.store') }}" method="POST"
                          enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="row justify-content-between">
                            <div class="col-lg-6 col-sm12">
                                <div class="form-group">
                                    <label for="user_name">User Name</label>
                                    <input id="user_name" name="user_name" class="form-control">
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <label for="password">Password</label>
                                <input id="password" name="password" class="form-control">
                                <input name="linkId" value="{{ $linkId }}" hidden>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    {{ form_submit(__('Create')) }}
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


