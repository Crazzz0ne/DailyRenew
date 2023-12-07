@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.dashboard.title'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10 col-md-8 col-sm-12 align-self-center">
            <div class="card">
                <div class="card-header">
                    <h4><strong>Edit Category</strong></h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('dashboard.mastermind.category.update', $category) }}" method="POST"
                          enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <input type="hidden" name="_method" value="PUT">
                        <div class="row justify-content-between">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    {{ html()->label(__('Name'))->for('name') }}
                                    {{ html()->text('name')
                                        ->class('form-control')
                                        ->placeholder(__('Ambition beats genius 99% of the time '))
                                        ->attribute('maxlength',80)
                                        ->value($category->name)
                                        ->required()
                                        ->autofocus() }}
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    {{ html()->label(__('Description'))->for('description') }}
                                    {{ html()->textarea('description')
                                        ->class('form-control')
                                        ->value($category->description)
                                        ->placeholder(__('A man without ambition is like a beautiful worm--it can creep, but it cannot fly')) }}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-group mb-0 clearfix">
                                    {{ form_submit(__('Submit')) }}
                                </div><!--form-group-->
                            </div><!--col-->
                        </div><!--row-->
                        <div class="row">
                            <div class="col">
                                <div class="float-right">
                                    <a href="{{ url()->previous() }}" data-toggle="tooltip"
                                       data-placement="top" title="" class="btn btn-sm btn-info"
                                       data-original-title="Back">
                                        Back</a>
                                    {{ form_submit(__('buttons.general.crud.update')) }}
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
