@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.dashboard.title'))

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-10 col-md-8 col-sm-12 align-self-center">
            <div class="card">
                <div class="card-header">
                    <strong>Category</strong>
                </div>
                <div class="card-body">
                    {{ html()->form('POST', route('dashboard.vendorlinks.vendor.store'))->open() }}
                    <form>
                        {{ csrf_field() }}
                        <div class="row justify-content-between">
                            <div class="col-lg-6 col-sm-12">
                                <div class="form-group">
                                    {{ html()->text('name')
                                        ->class('form-control')
                                        ->value($category->name)
                                        ->placeholder(__('Install'))
                                        ->attribute('maxlength',80)
                                        ->readonly()
                                        ->autofocus()
                                        }}
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                {{ html()->text('name')
                                    ->class('form-control')
                                    ->value($category->description)
                                    ->placeholder(__('Install'))
                                    ->attribute('maxlength',80)
                                    ->readonly()
                                    ->autofocus()
                                    }}
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <div class="form-group">
                                    {{ html()->label(__('Sort ID'))->for('sort_id') }}
                                    {{ html()->text('sort_id')
                                        ->class('form-control')
                                        ->value($category->sort_order)

                                        ->attribute('maxlength',2)
                                        ->readonly()
                                        ->autofocus() }}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="float-right">
                                    <a href="{{ url()->previous() }}" data-toggle="tooltip"
                                       data-placement="top" title="" class="btn btn-sm btn-info"
                                       data-original-title="Back">
                                        Back</a>
                                    @can('administrate all partnerlinks')
                                        <a href="{{route('dashboard.vendorlinks.category.edit', $category->id)}}"
                                           data-toggle="tooltip" data-placement="top" title=""
                                           class="btn btn-sm btn-primary"
                                           data-original-title="Edit">
                                            Edit</a>
                                    @endcan
                                </div>
                            </div>
                            {{ html()->form()->close() }}
                        </div>
                    </form>
                </div>
            </div>
        </div>
@endsection


