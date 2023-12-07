@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.dashboard.title'))

@section('content')
    {{--{{dd($market)}}--}}

        <div class="align-self-center">
            <div class="card">
                <div class="card-header">
                    <strong>
                        Update Market
                    </strong>
                </div><!--card-header-->
                <div class="card-body">
                    {{ html()->form('put', route('dashboard.market.update', $market))->open() }}
                    <div class="row">
                        <div class="col-lg-3 col-sm-8">
                            <div class="form-group">
                                {{ html()->label(__('Office Name'))->for('name') }}
                                {{ html()->text('name')
                                    ->class('form-control')
                                    ->value($market->name)
                                    ->placeholder(__('Ragnarok'))
                                    ->attribute('maxlength',80)
                                    ->required()
                                    ->autofocus() }}
                            </div><!--form-group-->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label>Permissions</label>
                                <select id="id-name" class="form-control" style="height: 500px" name="permissions[]"
                                        multiple="multiple">
                                    @foreach($permissions as $permission)
                                        @if(isset($market->permissions))
                                        @if(in_array($permission->name, $market->permissions))
                                        <option value="{{ $permission->name }}" selected> {{ $permission->name }}</option>
                                        @else
                                            <option value="{{ $permission->name }}"> {{ $permission->name }}</option>
                                        @endif
                                        @else
                                            <option value="{{ $permission->name }}"> {{ $permission->name }}</option>
                                        @endif

                                    @endforeach
                                </select>

                            </div><!--form-group-->
                        </div><!--col-->
                        <div class="col-lg-3 col-sm-8">
                            <div class="form-group">
                                {{--                                {{ html()->label(__('City'))->for('city') }}--}}
                                {{--                                {{ html()->text('city')--}}
                                {{--                                    ->value($market->city)--}}
                                {{--                                    ->placeholder(__('City of Angels'))--}}
                                {{--                                    ->class('form-control')--}}
                                {{--                                    }}--}}
                            </div><!--form-group-->
                        </div><!--col-->
                    </div><!--row-->
                    <div class="row justify-content-between">
                        <div class="col-lg-3 col-sm-8">

                        </div>
                        <div class="col-lg-3 col-sm-8">
                            <div class="form-group">

                            </div><!--form-group-->
                        </div>
                        <div class="col-lg-3 col-sm-3 col-sm-8">
                            <div class="form-group">

                            </div><!--form-group-->
                        </div>
                        <div class="col-lg-3 col-sm-8">


                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-8">
                            <div class="form-group row">
                                <div class="custom-control">

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class=" row pt-5">
                        <div class="col">
                            <div class="form-group mb-0 clearfix">
                                {{ form_submit(__('Update')) }}
                            </div><!--form-group-->
                        </div><!--col-->
                    </div><!--row-->
                    {{ html()->form()->close() }}
                </div>
            </div><!--col-->
        </div><!--row-->
@endsection
