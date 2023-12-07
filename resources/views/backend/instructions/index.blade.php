@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.dashboard.title'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <strong> Tools to help you use tools</strong>
                </div><!--card-header-->
                <div class="card-body">
                    <div class="row justify-content-md-start">
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header h-25">
                                    New Employee
                                </div>
                                <div class="card-body">
                                    <div>
                                        <a href="https://youtu.be/iERdwhwrWp4"
                                           class="btn btn-primary" target="_blank">
                                            <i class="fab fa-youtube"></i>
                                            Video Instructions
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    Adding A New Employee
                                </div>
                                <div class="card-body">
                                    <div>
                                        <a href="https://youtu.be/bdN2dgMj9IE"
                                           class="btn btn-primary" target="_blank">
                                            <i class="fab fa-youtube"></i>
                                            Video Instructions
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
