@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.dashboard.title'))

@section('content')

    <div class="container">

        <table class="table table-sm table-dark">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Date</th>
                <th scope="col">Subject</th>
                <th scope="col">Body</th>
                <th scope="col">URL</th>
                <th scope="col">Status</th>
            </tr>
            </thead>
            <tbody>
            @foreach($supports as $support)
                <tr>
                    <td>{{ $support->id }}</td>
                    <td>{{ $support->created_at }}</td>
                    <td>{{ $support->subject }}</td>
                    <td>{!! $support->body !!}</td>
                    <td>{{ $support->url }}</td>
                    <td>
                        {{ html()->form('PUT', route('dashboard.auth.support.update'))->open() }}
                        <input name="id" value="{{ $support->id }}" hidden>
                        <select name="status">
                            @if($support->status == 'Open')
                                <option value="Open" selected>Open</option>
                            @else
                                <option value="Open">Open</option>
                            @endif
                            @if($support->status == 'Pending')
                                <option value="Pending" selected>Pending</option>
                            @else
                                <option value="Pending">Pending</option>
                            @endif
                            @if($support->status == 'Closed')
                                <option value="Closed" selected>Closed</option>
                            @else
                                <option value="Closed">Closed</option>
                            @endif
                            @if($support->status == 'Hopeless')
                                <option value="Hopeless" selected>Hopeless</option>
                            @else
                                <option value="Hopeless">Hopeless</option>
                            @endif
                        </select>
                        {{ form_submit(__('Update')) }}
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {{--        <div class="row pt-5">--}}
        {{--            <div class="col">--}}
        {{--                <div class="form-group mb-0 clearfix">--}}
        {{--                   --}}
        {{--                </div><!--form-group-->--}}
        {{--            </div><!--col-->--}}
        {{--        </div><!--row-->--}}
        {{--        </form>--}}
        <div>
            <form action="{{ route('dashboard.support.csv') }}" method="POST"
                  enctype="multipart/form-data">
            {{ csrf_field() }}
                <div class="card card-body">
                    <div class="input-group">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" name="csv"
                                   aria-describedby="logoDiscription">
                            <label class="custom-file-label" for="audio">CSV</label>
                        </div>
                        {{ form_submit(__('buttons.general.crud.create')) }}
                    </div>
                </div>
            {{ html()->form()->close() }}
        </div>

    </div>

@endsection
