@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-10 col-md-8 col-sm-12 align-self-center">
            <div class="card">
                <div class="card-header">
                    <strong>
                        Edit
                    </strong>
                </div>
                <div class="card-body">
                    {{--                {{ html()->form('POST', route('admin.vendor.store'))->open() }}--}}
                    {{--                    <form>--}}
                    {{ csrf_field() }}
                    <div class="row justify-content-between">
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                {{ html()->label(__('Sort ID'))->for('sort_id') }}
                                {{ html()->text('sort_id')
                                    ->class('form-control')
                                    ->value($link->sort_id)

                                    ->attribute('maxlength',2)
                                    ->required()
                                    ->readonly()
                                    ->autofocus() }}
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                {{ html()->label(__('Representative'))->for('representative') }}
                                {{ html()->text('representative')
                                    ->class('form-control')
                                    ->value($link->representative)

                                    ->attribute('maxlength',80)
                                    ->required()
                                    ->readonly()
                                    ->autofocus() }}
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                {{ html()->label(__('Notes'))->for('notes') }}
                                {{ html()->text('notes')
                                    ->class('form-control')
                                    ->value($link->notes)
                                    ->readonly()

                                    ->attribute('maxlength',80)
                                   }}
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                {{ html()->label(__('Email'))->for('email') }}
                                {{ html()->email('email')
                                    ->class('form-control')
                                     ->value($link->email)

                                    ->readonly()
                                    ->attribute('maxlength',100)
                                   }}
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                {{ html()->label(__('Notes'))->for('notes') }}
                                {{ html()->text('notes')
                                    ->class('form-control')
                                    ->value($link->notes)

                                    ->readonly()
                                    ->attribute('maxlength',80)
                                   }}
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                {{ html()->label(__('Web Address'))->for('web_address') }}
                                {{ html()->text('web_address')
                                    ->class('form-control')
                                    ->value($link->web_address)
                                    ->readonly()

                                   }}
                            </div>
                        </div>

                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                {{ html()->label(__('Office phone'))->for('office_phone') }}
                                {{ html()->text('office_phone')
                                    ->class('form-control')
                                      ->value($link->office_phone)

                                    ->attribute('maxlength', 50)
                                    ->readonly()
                                    ->required()
                                  }}
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm12">
                            <div class="form-group">
                                {{ html()->label(__('Extension '))->for('extension') }}
                                {{ html()->text('extension')
                                    ->class('form-control')
                                     ->value($link->extension)
                                    ->type('number')
                                    ->readonly()

                                    ->attribute('maxlength', 5)
                                  }}
                            </div><!--form-group-->
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                {{ html()->label(__('Cell phone'))->for('cell_phone') }}
                                {{ html()->text('cell_phone')
                                    ->class('form-control')

                                      ->value($link->cell_phone)
                                    ->attribute('maxlength',11)
                                    ->readonly()
                                    ->autofocus() }}
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm12">
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label for="vendor">Partner</label>
                                <select name="vendor" class="form-control" readonly="">
                                    @foreach($vendors as $vendor)
                                        @if($vendor->id == $link->vendor_id)
                                            <option
                                                value="{{ $vendor->id }}"
                                                selected> {{ ucfirst($vendor->company_name) }} </option>
                                        @else
                                            <option
                                                value="{{ $vendor->id }}"> {{ ucfirst($vendor->company_name) }} </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label for="category">Category</label>
                                <select name="category" class="form-control" readonly="">
                                    @foreach($categories as $category)
                                        @if($category->id == $link->category_id)
                                            <option value="{{ $category->id }}"
                                                    selected> {{ ucfirst($category->name) }} </option>
                                        @else
                                            <option
                                                value="{{ $category->id }}"> {{ ucfirst($category->name) }} </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        {{--                            {{ html()->form()->close() }}--}}

                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="float-right">
                                <a href="{{ url()->previous() }}" data-toggle="tooltip"
                                   data-placement="top" title="" class="btn btn-sm btn-info"
                                   data-original-title="Back">
                                    Back</a>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection


