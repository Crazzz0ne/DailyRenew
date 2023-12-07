@extends('backend.layouts.app')

@section('content')

    <div class="row justify-content-center align-items-center mb-3">
        <div class="col col-sm-10 align-self-center">
            <div class="card">
                <div class="card-header">
                    <strong>
                        @lang('navs.frontend.user.account')
                    </strong>
                </div>

                <div class="card-body">
                    <div role="tabpanel">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a href="#profile" class="nav-link active" aria-controls="profile" role="tab" data-toggle="tab">@lang('navs.frontend.user.profile')</a>
                            </li>

                            <li class="nav-item">
                                <a href="#edit" class="nav-link" aria-controls="edit" role="tab" data-toggle="tab">@lang('labels.frontend.user.profile.update_information')</a>
                            </li>

                            @if($logged_in_user->canChangePassword())
                                <li class="nav-item">
                                    <a href="#password" class="nav-link" aria-controls="password" role="tab" data-toggle="tab">@lang('navs.frontend.user.change_password')</a>
                                </li>
                            @endif
                        </ul>

                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane fade show active pt-3" id="profile" aria-labelledby="profile-tab">
                                @include('backend.user.account.tabs.profile')
                            </div>

                            <div role="tabpanel" class="tab-pane fade show pt-3" id="edit" aria-labelledby="edit-tab">
                                @include('backend.user.account.tabs.edit')
                            </div>

                            @if($logged_in_user->canChangePassword())
                                <div role="tabpanel" class="tab-pane fade show pt-3" id="password" aria-labelledby="password-tab">
                                    @include('backend.user.account.tabs.change-password')
                                </div>
                            @endif

                        </div>
                    </div>
                    <div class="row pt-3">
                        @foreach($officeUser as $user)
{{--                            @if($user->roles[0]->name == 'manager')--}}
{{--                            @else--}}
                                <div class="col-md-6 col-sm-12">
                                    <div class="card">
                                        <div class="card-header">{{ $user->first_name }} {{ $user->last_name }}</div>
                                        <div class="card-body">
                                            <div class="container">
                                                <div class="row justify-content-center text-center">
{{--                                                    <div class="col-12 text-left">Phone: {{ ($user->phone_number) }}</div>--}}
                                                    <div class="col-md-6 col-sm-12">
{{--                                                        <a class="btn btn-primary btn-xlg mt-2"--}}
{{--                                                           href="tel:{{ $user->phone_number }}">Click to Call--}}
{{--                                                            <i class="fas fa-phone-square-alt"></i>--}}
{{--                                                        </a>--}}
                                                    </div>
                                                    <div class="col-md-6 col-sm-12">
{{--                                                        <a class="btn btn-secondary btn-xlg mt-2"--}}
{{--                                                           href="sms:{{ $user->phone_number }}">Click to text--}}
{{--                                                            <i class="fas fa-sms"></i>--}}
{{--                                                        </a>--}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
{{--                            @endif--}}
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
