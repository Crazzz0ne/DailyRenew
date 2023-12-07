@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.dashboard.title'))

@section('content')
    <LineApp></LineApp>
@endsection
        <script>
            import LineApp from "../../../../js/backend/views/Queue/LineApp";
            export default {
                components: {LineApp}
            }
        </script>
