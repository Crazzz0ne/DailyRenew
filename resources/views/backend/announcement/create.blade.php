@extends('backend.layouts.createAnnouncment')

@section('title', app_name() . ' | ' . __('strings.backend.dashboard.title'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12 col-sm-8 align-self-center">
            <div class="card shadow-lg">
                <div class="card-header">
                    <strong>
                        @lang('labels.backend.access.announcement.create')
                    </strong>
                </div><!--card-header-->
                <div class="card-body">
                    {{ html()->form('POST', route('dashboard.announcement.store'))->open() }}
                    <div class="row">
                        <div class="col">
                            <div class="form-group">

                                <label > Subject</label>
                                <input  class="form-control" name="subject">
                            </div>
                        </div><!--form-group-->
                    </div><!--row-->


                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <div>
                                <label class="px-2">
                                    Body
                                </label>
                            </div>
                            <div class="px-2">
                                @trix(App\Models\Announcement::class, 'body', [ 'hideTools' =>[ 'file-tools', 'history-tools'] ])
                                {{--                                <textarea id="body" name="body"></textarea>--}}
                            </div>

                        </div><!--form-group-->
                    </div><!--col-->
                </div><!--row-->

                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            {{ html()->label(__('Stick to top'))->for('sticky') }}
                            {{ html()->checkbox('sticky')
                                ->class('form-control')
                               }}
                        </div><!--form-group-->
                    </div><!--col-->
                    <div class="col">
                        <div class="form-group">
                            {{ html()->label(__('Choose Color'))->for('color') }}
                            <select class="form-control" name="color">
                                <option value="normal">Normal</option>
                                <option value="green">Green</option>
                                <option value="yellow">Yellow</option>
                                <option value="red">Red</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-group mb-2 clearfix">
                            {{ form_submit(__('Submit')) }}
                        </div><!--form-group-->
                    </div><!--col-->
                </div><!--row-->
                {{ html()->form()->close() }}
                </div>
            </div><!--card-body-->
        </div><!--card-->
    </div><!--col-->
    </div><!--row-->
@endsection
