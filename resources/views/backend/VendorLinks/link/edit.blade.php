@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.dashboard.title'))

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-8 con-sm-12 align-self-center">
            <div class="card">
                <div class="card-header">
                    <strong>
                        Create
                    </strong>
                </div><!--card-header-->
                <div class="card-body">
                    <form action="{{ route('dashboard.vendorlinks.link.update', $link) }}" method="POST"
                          enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <input type="hidden" name="_method" value="PUT">

                        <div class="row justify-content-between">
                            <div class="col-lg-6 col-sm-12">
                                <div class="form-group">
                                    <label for="vendor">Vendor</label>
                                    <select id="vendor" name="vendor" class="form-control">
                                        @foreach($vendors as $vendor)
                                            @if($vendor->id != $link->vendor_id)
                                                <option
                                                    value="{{ $vendor->id }}"> {{ ucfirst($vendor->company_name) }} </option>
                                            @else
                                                <option value="{{ $vendor->id }}"
                                                        selected> {{ ucfirst($vendor->company_name) }} </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div><!--form-group-->
                            </div><!--col-->
                            <div class="col-lg-6 col-sm-12">
                                <div class="form-group">
                                    <label for="category">Category</label>
                                    <select id="category" name="category" class="form-control">
                                        <option value="null">Make A Selection</option>
                                        @foreach($categories as $category)
                                            @if($category->id != $link->category_id)
                                                <option
                                                    value="{{ $category->id }}"> {{ ucfirst($category->name) }} </option>
                                            @else
                                                <option value="{{ $category->id }}"
                                                        selected> {{ ucfirst($category->name) }} </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div><!--form-group-->
                            </div><!--col-->

                            <div class="col-lg-6 col-sm12">
                                <div class="form-group">
                                    {{ html()->label(__('Representative'))->for('representative') }}
                                    {{ html()->text('representative')
                                        ->class('form-control')
                                        ->placeholder(__('Ed Kemper'))
                                        ->value($link->representative)
                                        ->attribute('maxlength',80)
                                        ->required()
                                        ->autofocus() }}
                                </div><!--form-group-->
                            </div>
                            <div class="col-lg-6 col-sm12">
                                <div class="form-group">
                                    {{ html()->label(__('Email'))->for('email') }}
                                    {{ html()->email('email')
                                        ->class('form-control')
                                        ->value($link->email)
                                        ->placeholder(__('ed@monsanto.com'))
                                        ->attribute('maxlength',100)
                                       }}
                                </div><!--form-group-->
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <div class="form-group">
                                    {{ html()->label(__('Notes'))->for('notes') }}
                                    {{ html()->text('notes')
                                        ->class('form-control')
                                        ->value($link->notes)
                                        ->placeholder(__('Manager, Open m-f'))
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
                                        ->placeholder(__('dashboard.cuic.us/login'))
                                       }}
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm12">
                            </div>
                            <div class="col-lg-6 col-sm12">
                                <div class="form-group">
                                    {{ html()->label(__('Office phone'))->for('office_phone') }}
                                    {{ html()->text('office_phone')
                                        ->class('form-control')
                                        ->value($link->office_phone)
                                        ->id('officePhone')
                                        ->placeholder(__('888-555-1234'))
                                        ->attribute('maxlength', 12)
                                      }}
                                </div><!--form-group-->
                            </div>
                            <div class="col-lg-6 col-sm12">
                                <div class="form-group">
                                    {{ html()->label(__('Extension '))->for('extension') }}
                                    {{ html()->text('extension')
                                        ->class('form-control')
                                         ->value($link->extension)
                                        ->type('number')
                                        ->placeholder(__('333'))
                                        ->attribute('maxlength', 5)
                                      }}
                                </div><!--form-group-->
                            </div>
                            <div class="col-lg-6 col-sm12">
                                <div class="form-group">
                                    {{ html()->label(__('Cell phone'))->for('cell_phone') }}
                                    {{ html()->text('cell_phone')
                                        ->class('form-control')
                                        ->id('cellPhone')
                                        ->value($link->cell_phone)
                                        ->placeholder(__('888-555-1234'))
                                        ->attribute('maxlength', 12)
                                        ->autofocus() }}
                                </div><!--form-group-->
                            </div>
                        </div><!--row-->
                        <div class="row">
                            <div class="col">
                                <div class="form-group float-right">
                                    {{ form_submit(__('Update')) }}
                                </div><!--form-group-->
                            </div><!--col-->
                        </div><!--row-->
                    </form>
                </div><!--card-body-->
            </div><!--card-->
        </div><!--col-->
    </div><!--row-->

    <script>




        ////add's dashes in phone numbers
        function setOfficePhone() {
            let val = this.value;
            val = formatPhoneNumber(val);

            if (val) {
                officePhone.value = val;
            }
        }

        function setCellPhone() {
            let val = this.value;
            val = formatPhoneNumber(val);

            if (val) {
                cellPhone.value = val;
            }
        }

        function formatPhoneNumber(phoneNumberString) {
            let cleaned = ('' + phoneNumberString).replace(/\D/g, '');
            let match1 = cleaned.match(/^(\d{3})(\d{3})(\d{4})$/);
            let match2 = cleaned.match(/^(\d{3})(\d{3})$/);
            let match3 = cleaned.match(/^(\d{3})$/);

            if (match1) {
                return match1[1] + '-' + match1[2] + '-' + match1[3]
            } else if (match2) {
                return match2[1] + '-' + match2[2] + '-'
            } else if (match3) {
                return match3[1] + '-'
            }
            return null
        }

        var officePhone = document.getElementById("officePhone");
        officePhone.addEventListener("keyup", setOfficePhone);

        var cellPhone = document.getElementById("cellPhone");
        cellPhone.addEventListener("keyup", setCellPhone);

    </script>
@endsection
