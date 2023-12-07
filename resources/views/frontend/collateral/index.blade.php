@extends('frontend.layouts.app')

@section('title', app_name() . ' | ' . __('Announcement') )

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="d-inline-block">Collateral</h3>
        </div>
        <div class="card-body">
            <div class="container mt-3">
                <div class="row justify-content-center">
                    @foreach($categories as $category)
                        <div class="col-xl-3 col-md-6 col-sm-12 py-2">
                            <div class="card h-100 pt-4">
                                <div class="card-body">
                                    <h5> {{ $category->name }} </h5>
                                    <p>{{ $category->description }}</p>
                                    <div class="btn-group float-right" role="group">
                                        <a class="btn btn-info" data-original-title="View" data-placement="top"
                                           data-toggle="tooltip"
                                           href="{{ route('frontend.collateral.show', $category->id) }}"
                                           title="View Category">
                                            <i class="fas fa-eye">
                                            </i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

@endsection
