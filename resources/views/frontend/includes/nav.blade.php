<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
    {{--    <a href="{{ route('frontend.user.dashboard') }}" class="navbar-brand">{{ app_name() }}</a>--}}

    {{--    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="@lang('labels.general.toggle_navigation')">--}}
    {{--        <span class="navbar-toggler-icon"></span>--}}
    {{--    </button>--}}

    <div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
        <ul class="navbar-nav">
{{--            Start Multilanguage settings --}}
{{--            @if(config('locale.status') && count(config('locale.languages')) > 1)--}}
{{--                <li class="nav-item dropdown">--}}
{{--                    <a href="#" class="nav-link dropdown-toggle" id="navbarDropdownLanguageLink" data-toggle="dropdown"--}}
{{--                       aria-haspopup="true" aria-expanded="false">@lang('menus.language-picker.language') ({{ strtoupper(app()->getLocale()) }})</a>--}}

{{--                    @include('includes.partials.lang')--}}
{{--                </li>--}}
{{--            @endif--}}
            @auth

                {{--                <li class="nav-item"><a href="{{route('frontend.user.dashboard')}}"--}}
                {{--                                        class="nav-link {{ active_class(Active::checkRoute('frontend.user.dashboard')) }}">--}}
                {{--                        @lang('navs.frontend.dashboard')</a></li>--}}
                {{--                <li class="nav-item"><a href="{{route('frontend.announcement.index')}}"--}}
                {{--                                        class="nav-link {{ active_class(Active::checkRoute('frontend.announcement.index')) }}">Announcements @if ($unread_count > 0)--}}
                {{--                            <span class="badge badge-danger">{{ $unread_count }}</span>--}}
                {{--                        @endif</a>--}}
                {{--                </li>--}}
                {{--                <li class="nav-item">--}}
                {{--                    <a href="{{route('frontend.training.all')}}"--}}
                {{--                       class="nav-link {{ active_class(Active::checkRoute('frontend.training.all')) }}">Training</a>--}}
                {{--                </li>--}}
                {{--                <li class="nav-item"><a href="{{route('frontend.collateral.all')}}"--}}
                {{--                                        class="nav-link {{ active_class(Active::checkRoute('frontend.collateral.all')) }}">Collateral</a>--}}
                {{--                </li>--}}
                {{--                <li class="nav-item"><a href="{{route('frontend.mastermind.all')}}"--}}
                {{--                                        class="nav-link {{ active_class(Active::checkRoute('frontend.mastermind.all')) }}">Mastermind</a>--}}
                {{--                </li>--}}
                {{--                <li class="nav-item"><a href="{{route('frontend.vendorlinks.index')}}"--}}
                {{--                                        class="nav-link {{ active_class(Active::checkRoute('frontend.vendorlinks.index')) }}">Partner--}}
                {{--                        Links</a>--}}
                {{--                </li>--}}
                {{--                <li class="nav-item">--}}
                {{--                    <a class="nav-link {{ active_class(Active::checkRoute('frontend.user.dashboard')) }}"--}}
                {{--                       href="https://app.smartsheet.com/b/form/0c2d777ca00649909dfee61cb2b1a9ed" target="_blank">--}}
                {{--                        <i class="nav-icon fas fa-comment"></i>--}}
                {{--                        Issues for Dana--}}
                {{--                    </a>--}}
                {{--                </li>--}}

            @endauth

            @guest
                <li class="nav-item"><a href="{{route('frontend.auth.login')}}" class="nav-link {{ active_class(Active::checkRoute('frontend.auth.login')) }}">@lang('navs.frontend.login')</a></li>

                {{--                @if(config('access.registration'))--}}
                {{--                    <li class="nav-item"><a href="{{route('frontend.auth.register')}}" class="nav-link {{ active_class(Active::checkRoute('frontend.auth.register')) }}">@lang('navs.frontend.register')</a></li>--}}
                {{--                @endif--}}
            @else
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" id="navbarDropdownMenuUser" data-toggle="dropdown"
                       aria-haspopup="true" aria-expanded="false">{{ $logged_in_user->name }}</a>

                    <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuUser">
                        @can('view backend')
                            {{--                            <a href="{{ route('admin.dashboard') }}" class="dropdown-item">@lang('navs.frontend.user.administration')</a>--}}
                        @endcan

                        {{--                        <a href="{{ route('frontend.user.account') }}" class="dropdown-item {{ active_class(Active::checkRoute('frontend.user.account')) }}">@lang('navs.frontend.user.account')</a>--}}
                        {{--                        <a href="{{ route('frontend.auth.logout') }}" class="dropdown-item">@lang('navs.general.logout')</a>--}}
                    </div>
                </li>
            @endguest
            {{--            <li class="nav-item"><a href="{{route('frontend.contact')}}" class="nav-link {{ active_class(Active::checkRoute('frontend.contact')) }}">@lang('navs.frontend.contact')</a></li>--}}
        </ul>
    </div>
</nav>
