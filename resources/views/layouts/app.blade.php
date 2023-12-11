<!DOCTYPE html>
@langrtl
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
@else
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@endlangrtl
<head>

    @trixassets
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', app_name())</title>
    <meta name="description" content="@yield('meta_description', 'Scout')">
    <meta name="author" content="@yield('meta_author', 'Chris Furman')">
    <link rel="shortcut icon" href="{{ asset('img/backend/brand/daily.ico') }}" />

    <script>
        @auth
            window.Permissions = {!! json_encode(Auth::user()->allPermissions, true) !!};
             window.LeadMarket = {!! json_encode(Auth::user()->marketName, true) !!};
             window.apiKey = {!! json_encode(Auth::user()->api_token, true) !!}
        @else
            window.Permissions = ['ish'];
        @endauth
    </script>
{{--    {{ dd(Auth::user()) }}--}}
    @yield('meta')


    {{-- See https://laravel.com/docs/5.8/blade#stacks for usage --}}
    @stack('before-styles')

<!-- Check if the language is set to RTL, so apply the RTL layouts -->
    <!-- Otherwise apply the normal LTR layouts -->
    {{ style(mix('css/backend.css')) }}

    @stack('after-styles')


</head>

<body class="{{ config('backend.body_classes') }} sidebar-minimized brand-minimized" id="app">
    @include('backend.includes.header')
    <div class="app-body">
        @include('backend.includes.sidebar')

        <main class="main">
            @include('includes.partials.demo')
            @include('includes.partials.logged-in-as')
{{--            {!! Breadcrumbs::render() !!}--}}
            <div class="container">
                @include('includes.partials.messages')
            </div>
            <div class="container-fluid">
{{--                class="animated fadeIn"--}}
                <div >
                    <div class="content-header">
                        @yield('page-header')
                    </div><!--content-header-->

                    @yield('content')
                </div><!--animated-->
            </div><!--container-fluid-->
        </main><!--main-->

        @include('backend.includes.aside')
    </div><!--app-body-->

    @include('backend.includes.footer')

    <!-- Scripts -->
    @stack('before-scripts')
    {!! script(mix('js/manifest.js')) !!}
    {!! script(mix('js/vendor.js')) !!}
    {!! script(mix('js/backend.js')) !!}
    {!! script(mix('js/app.js')) !!}
    @stack('after-scripts')
</body>
</html>
