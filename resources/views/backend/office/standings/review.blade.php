@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.dashboard.title'))

@section('content')

    <div class="container">
        {{--        {{ dd($months) }}--}}
        <div class="card">
            <div class="card-header py-3">
                                <h1 class="d-inline-block"> Pending - {{ $month }}/{{ $year }}</h1>
                <span class="d-inline-block btn-toolbar float-right mt-2" role="toolbar"
                      aria-label="@lang('labels.general.toolbar_btn_groups')">
                    <a href="{{ route('dashboard.officestanding.create') }}" class="btn btn-third ml-1"
                       data-toggle="tooltip"
                       title="@lang('labels.general.create_new')"><i class="fas fa-plus-circle"></i></a>
                </span>
            </div>
            <div class="container my-4">
                <div class="row pt-2">


                </div>
                <div class="card-body">
                    {{ html()->form('POST', route('dashboard.office.officestandings.update' ))->style('display: contents;')->open() }}
                    <div class="row">
                        @foreach($approvals as $approve)
                            <div class="col-md-4 col-sm-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3>{{$approve->name}}</h3>
                                    </div>
                                    <div class="card-body">
                                        {{--                                               {{ dd($approve->approved) }}--}}
                                        <label><h4>Date: </h4></label> {{ $approve->sdate }}
                                        <p><label>Approve</label>
                                            <select name="approved[{{ $approve->id }}]">
                                                @if($approve->approved)
                                                    <option value="1" selected>Yes</option>
                                                    <option value="0">No</option>
                                                @else
                                                    <option value="1">Yes</option>
                                                    <option value="0" selected>no</option>
                                                @endif
                                            </select>
                                        </p>
                                        <a href="{{ route('dashboard.officestanding.destroy', $approve->id) }}"
                                           data-method="DELETE"
                                           data-token="{{csrf_token()}}" data-confirm="Are you sure?">
                                            <button class="btn btn-danger trash-btn"
                                                    type="submit">
                                                <i data-original-title="Delete"
                                                   class="fas fa-trash"></i>
                                            </button>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
{{--                        <input type="hidden" name="currentMonth" value="{{$currentMonth }}">--}}
                    </div>
                    <div>
                        <input class="check" type="checkbox" name="sendEmail">
                        <label>Send Email</label>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group mb-0 clearfix">
                                {{ form_submit(__('Submit')) }}
                                {{ html()->form()->close() }}
                            </div><!--form-group-->
                        </div><!--col-->
                    </div><!--row-->
                </div>

            </div>
        </div>
        <div class="col-md-4 col-sm-12">
            <div class="card">
                <div class="card-header text-center">
                    <h3>Office ID and Name</h3>
                </div>
                <table class="table table-bordered">
                    <tr>
                        <th>Name</th>
                        <th>ID</th>
                    </tr>
                    @foreach($offices as $id => $name)
                        <tr>
                            <td>{{ $offices[$id]['name'] }}</td>
                            <td>{{ $offices[$id]['id'] }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
    </div>

@endsection
