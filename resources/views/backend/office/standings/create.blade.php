@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.dashboard.title'))

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">
                Create New standings
            </div>
            <div class="card-body">
                <form action="{{ route('dashboard.officestanding.store') }}" method="post"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="input-row">
                        <select name="name">
                            <option value="Volume Glory">Volume Glory</option>
                            <option value="Efficiency Glory">Efficiency Glory</option>
                            <option value="PPW Glory">PPW Glory</option>
                        </select>
                        <input name="sdate" type="text" value="{{ $date }}">
                        <label class="col-md-4 control-label">Choose CSV File</label> <input
                            type="file" name="csv" id="file" accept=".csv">
                        <button type="submit" id="submit" name="import"
                                class="btn-submit">Import
                        </button>
                        <br/>

                    </div>
                    <div id="labelError"></div>
                </form>
            </div>

        </div>
    </div>


@endsection


