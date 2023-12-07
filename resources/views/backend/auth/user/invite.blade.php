@extends('backend.layouts.app')

@section('title', __('labels.backend.access.users.management') . ' | ' . __('labels.backend.access.users.edit'))




@section('content')

    <form action="{{ route('dashboard.auth.user.invite.create') }}" method="POST"
          enctype="multipart/form-data">
        {{ csrf_field() }}
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-sm-5">
                    <h4 class="card-title mb-0">
                       Invite User
                        <small class="text-muted">enter email </small>
                    </h4>
                </div><!--col-->
            </div><!--row-->
            <hr>
            <div class="row mt-4 mb-4">
                <div class="col-md-6 col-sm-12">
                    <div class="form-group row">
                        <label> Email </label>

                        <div class="col-md-10">
                            {{ html()->email('email')
                                ->class('form-control')

                                ->attribute('maxlength', 191)
                                ->required() }}
                        </div><!--col-->
                    </div><!--form-group-->


                </div><!--col-->
            </div><!--row-->
        </div><!--card-body-->

        <div class="card-footer">
            <div class="row">
                <div class="col">
                    {{ form_cancel(route('dashboard.auth.user.index'), __('buttons.general.cancel')) }}
                </div><!--col-->
                <div class="col text-right">
                    {{ form_submit(__('buttons.general.crud.update')) }}
                </div><!--row-->
            </div><!--row-->
        </div><!--card-footer-->
    {{ html()->closeModelForm() }}
@endsection
