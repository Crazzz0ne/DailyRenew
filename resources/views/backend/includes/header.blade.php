<header class="app-header navbar">

    <button class="navbar-toggler sidebar-toggler d-lg-none mr-auto" type="button" data-toggle="sidebar-show">
        <span class="navbar-toggler-icon"></span>
    </button>
    <a class="navbar-brand" href="/dashboard">
        <img class="navbar-brand-full" src="{{ asset('img/backend/brand/sces-name.png') }}" height="27" alt="SCES Logo">
        <img class="navbar-brand-minimized" src="{{ asset('img/backend/brand/scout124x124.png') }}" height="27"
             alt="SCES Logo">
    </a>
    <button class="navbar-toggler sidebar-toggler d-md-down-none" type="button" data-toggle="sidebar-lg-show">
        <span class="navbar-toggler-icon"></span>
    </button>

    {{--    <ul class="nav navbar-nav d-md-down-none">--}}
    {{--                <li class="nav-item px-3">--}}
    {{--                    <a class="nav-link" href="/dashboard"><i class="fas fa-home"></i></a>--}}
    {{--                </li>--}}
    {{--    </ul>--}}

        <ul class="nav navbar-nav ml-auto">
            @can('view lead')
            <li class="nav-item">
                <notification-container
                    :owner="{{json_encode(Auth::user())}}"
                />
            </li>
            @endcan
            <li class="nav-item dropdown pr-5">
                <a class="nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
                   aria-expanded="false">
                    <img src="{{ $logged_in_user->picture }}" class="img-avatar" alt="{{ $logged_in_user->email }}">
                    <span class="d-md-down-none">{{ $logged_in_user->full_name }}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <div class="dropdown-header text-center">
                        <strong>Account</strong>
                    </div>
                    <a class="dropdown-item" href="{{ route('dashboard.user.account') }}">
                        <i class="fas fa-lock"></i> Account Settings
                    </a>
                    <a class="dropdown-item" href="{{ route('frontend.auth.logout') }}">
                        <i class="fas fa-lock"></i> @lang('navs.general.logout')
                    </a>
                </div>
            </li>
        </ul>

        {{--    <button class="navbar-toggler aside-menu-toggler d-md-down-none" type="button" data-toggle="aside-menu-lg-show">--}}
        {{--        <span class="navbar-toggler-icon"></span>--}}
        {{--    </button>--}}
        {{--    <button class="navbar-toggler aside-menu-toggler d-lg-none" type="button" data-toggle="aside-menu-show">--}}
        {{--        <span class="navbar-toggler-icon"></span>--}}
        {{--    </button>--}}
</header>
