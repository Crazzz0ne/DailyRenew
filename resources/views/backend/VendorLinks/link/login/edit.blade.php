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
                    {{ html()->modelForm($login, 'POST', route('dashboard.vendorlinks.linklogin.update', $login->id))->class('form-horizontal')->open() }}
                        {{ csrf_field() }}
                        <div class="row justify-content-between">
                            <div class="col-lg-6 col-sm12">
                                <div class="form-group">
                                    <label for="user_name">User Name</label>
                                    <input id="user_name" name="user_name" class="form-control"
                                           value="{{ $login->user_name }}">
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <label for="password">Password</label>
                                <input id="password" name="password" class="form-control"
                                       value="{{ $login->password }}">
                                <input name="linkId" class="form-control"
                                       value="{{ $login->id }}" hidden>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    {{ form_submit(__('Update'))->class('float-right') }}
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


