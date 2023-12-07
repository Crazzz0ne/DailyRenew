<!DOCTYPE html>


<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title', app_name())</title>
    <meta name="description" content="@yield('meta_description', 'Scout')">
    <meta name="author" content="@yield('meta_author', 'Chris Furman')">
    <link rel="shortcut icon" href="{{ asset('img/backend/brand/sces.ico') }}"/>


@yield('meta')

{{-- See https://laravel.com/docs/5.8/blade#stacks for usage --}}
@stack('before-styles')

<!-- Check if the language is set to RTL, so apply the RTL layouts -->
    <!-- Otherwise apply the normal LTR layouts -->
    {{ style(mix('css/backend.css')) }}

    @stack('after-styles')


</head>

<body class="">

<div class="app-body">


    <main class="main">

        {{--            {!! Breadcrumbs::render() !!}--}}
        <div class="container text-center">
            <h2> Bee back soon </h2>
            <iframe width="560" height="315" src="https://www.youtube.com/watch?v=thOifuHs6eY&t" frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen></iframe>

        </div><!--container-fluid-->
    </main><!--main-->


</div><!--app-body-->


<!-- Scripts -->
@stack('before-scripts')


@stack('after-scripts')
</body>
</html>
