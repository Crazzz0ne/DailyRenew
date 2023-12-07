@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.dashboard.title'))

@section('content')
   <TeamApp></TeamApp>
@endsection
<script>
    import TeamApp from "../../../../js/backend/views/Office/Team/TeamApp";
    export default {
        components: {TeamApp}
    }
</script>
