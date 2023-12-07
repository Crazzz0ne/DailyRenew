<div class="table-responsive">
    <table class="table table-striped table-hover table-bordered">
                <tr>
                    <th>@lang('labels.frontend.user.profile.avatar')</th>
                    <td><img src="{{ $logged_in_user->picture }}" class="user-profile-image" /></td>
                </tr>
        <tr>
            <th>@lang('labels.frontend.user.profile.name')</th>
            <td>{{ $logged_in_user->name }}</td>
        </tr>
        <tr>
            <th>@lang('labels.frontend.user.profile.email')</th>
            <td>{{ $logged_in_user->email }}</td>
        </tr>
        <tr>
            <th>
                Office
            </th>
            {{--            {{ dd($office) }}--}}
            <td>{{ $office->name }}
                <br>{{ $office->address }}
                <br>{{ $office->city }}, {{ $office->state }} {{ $office->zip_code }}
                <br> {{ $office->email }}, {{ $office->phone_number }}
            </td>
        </tr>
        <tr>
            <th>
                Test
            </th>
            <td>
                {{ html()->form('POST', route('dashboard.user.test-text'))->open() }}

                    {{ form_submit(__('Text')) }}

                {{ html()->form()->close() }}
            </td>
        </tr>

    </table>
</div>
