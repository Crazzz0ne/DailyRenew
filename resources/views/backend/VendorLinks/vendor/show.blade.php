@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.dashboard.title'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10 col-md-8 col-sm-12 align-self-center">
            <div class="card">
                <div class="card-header">
                    <strong>
                        Edit Company
                    </strong>
                </div>
                <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6 col-sm-12">
                                <div class="form-group">
                                    {{ html()->label(__('Name'))->for('name') }}
                                    {{ html()->text('name')
                                        ->class('form-control')
                                        ->value($vendor->company_name)
                                        ->attribute('maxlength',80)
                                        ->required()
                                        ->readonly()
                                        ->autofocus() }}
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <div class="form-group">
                                    {{ html()->label(__('Web Address'))->for('web_address') }}
                                    {{ html()->text('web_address')
                                        ->class('form-control')
                                        ->value($vendor->web_address)
                                        ->placeholder(__('dashboard.cuic.us/login'))
                                        ->readonly()
                                        ->attribute('maxlength',250) }}
                                </div>
                            </div>
                            <div class="col-12 my-4">
                                <p>Current Logo</p>
                                <div class="row mb-3">
                                    <div class="col-lg-4 col-sm-6">
                                        <div class="vendor-img"
                                             style='background-image: url("{{ Storage::disk('s3')->url($vendor->picture) }}")'></div>
                                    </div>
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
                                        <a href="{{route('dashboard.vendorlinks.vendor.edit', $vendor->id)}}"
                                           data-toggle="tooltip" data-placement="top" title=""
                                           class="btn btn-sm btn-primary"
                                           data-original-title="Edit">
                                            Edit</a>
                                    @endif
                                </div>
                            </div>
                            {{ html()->form()->close() }}
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
