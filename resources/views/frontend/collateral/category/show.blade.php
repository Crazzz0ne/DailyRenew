@extends('frontend.layouts.app')

@section('title', app_name() . ' | ' . __('Collateral') )

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="d-inline-block">Collateral</h3>
        </div>
        <div class="container mt-3">

            <div class="container">
                <div class="row justify-content-center">
                    @foreach($contents as $content)
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 py-2">
                            <div class="card h-100 pt-4">
                                <div class="card-body">
                                    <h5> {{ $content->name }} </h5>
                                    <p>{{ $content->description }}</p>

                                    <div class="pt-3">
                                        <div class="float-left">
                                            <a href="{{ route('frontend.collateral.content.show', $content->id) }}"
                                               data-toggle="tooltip" data-placement="top" title=""
                                               class="btn btn-primary" data-original-title="View"><i
                                                    class="fas fa-file-pdf fa-2x"></i></a>
                                        </div>

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
