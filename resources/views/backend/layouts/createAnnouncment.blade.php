<!DOCTYPE html>


<head>


    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-L1RYGZZXRJ"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-L1RYGZZXRJ');
    </script>

    @trixassets
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', app_name())</title>
    <meta name="description" content="@yield('meta_description', 'Scout')">
    <meta name="author" content="@yield('meta_author', 'Chris Furman')">
    <link rel="shortcut icon" href="{{ asset('img/backend/brand/sces.ico') }}"/>

    <script>

        @auth
            window.Permissions = {!! json_encode(Auth::user()->allPermissions, true) !!};
        window.LeadMarket = {!! json_encode(Auth::user()->marketName, true) !!};
        window.apiKey = {!! json_encode(Auth::user()->api_token, true) !!}
            @else
            window.Permissions = [];
        @endauth
    </script>

    @yield('meta')

    {{-- See https://laravel.com/docs/5.8/blade#stacks for usage --}}
    @stack('before-styles')

<!-- Check if the language is set to RTL, so apply the RTL layouts -->
    <!-- Otherwise apply the normal LTR layouts -->
    {{ style(mix('css/backend.css')) }}

    @stack('after-styles')


</head>

<body class="{{ config('backend.body_classes') }} sidebar-minimized brand-minimized">
<script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

    ga('create', 'G-L1RYGZZXRJ', 'auto');
    ga('send', 'pageview');
    ga('send', 'title');
</script>
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
        <div class="container-fluid pt-4" id="app">
            {{--                class="animated fadeIn"--}}
            <div>
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
