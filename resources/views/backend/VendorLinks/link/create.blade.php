@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.dashboard.title'))

@section('content')

    {{--    {{ Form::open(array('action' => array('Controller@method', $user->id))) }}--}}
    <div class="row justify-content-center">
        <div class="col-lg-8 con-sm-12 align-self-center">
            <div class="card">
                <div class="card-header">
                    <strong>
                        @lang('labels.backend.access.announcement.create')
                    </strong>
                </div>
                <div class="card-body">
                    {{ html()->form('POST', route('dashboard.vendorlinks.link.store'))->open() }}
                    {{--                    <form action="{{ route('dashboard.vendorlinks.link.store') }}" method="POST"--}}
                    {{--                          enctype="multipart/form-data">--}}
                        {{ csrf_field() }}
                        <div class="row justify-content-between">
                            <div class="col-lg-6 col-sm-12">
                                <div class="form-group">
                                    <label for="vendor">Partner</label>
                                    <select id="vendor" name="vendor" class="form-control">
                                        @foreach($vendors as $vendor)
                                            <option
                                                value="{{ $vendor->id }}"> {{ ucfirst($vendor->company_name) }} </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <div class="form-group">
                                    <label for="category">Category</label>
                                    <select id="category" name="category" class="form-control">
                                        <option value="null">Make A Selection</option>
                                        @foreach($categories as $category)
                                            <option
                                                value="{{ $category->id }}"> {{ ucfirst($category->name) }} </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            {{--                           TODO: Add tool tips for upload--}}

                            <div class="col-lg-6 col-sm12">
                                <div class="form-group">
                                    {{ html()->label(__('Representative'))->for('representative') }}
                                    {{ html()->text('representative')
                                        ->class('form-control')
                                        ->placeholder(__('Ed Kemper'))
                                        ->attribute('maxlength',80)
                                        ->required()
                                        ->autofocus() }}
                                </div>
                            </div>
                            {{--                            <div class="col-lg-6 col-sm-0">--}}

                            {{--                            </div>--}}
                            <div class="col-lg-6 col-sm-12">
                                <div class="form-group">
                                    {{ html()->label(__('Email'))->for('email') }}
                                    {{ html()->email('email')
                                        ->class('form-control')
                                        ->placeholder(__('ed@monsanto.com'))
                                        ->attribute('maxlength',100)
                                       }}
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <div class="form-group">
                                    {{ html()->label(__('Notes'))->for('notes') }}
                                    {{ html()->text('notes')
                                        ->class('form-control')
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
                                        ->placeholder(__('dashboard.cuic.us/login'))
                                       }}
                                </div>
                            </div>
                            {{--                            <div class="col-lg-6 col-sm12">--}}
                            {{--                            </div>--}}
                            <div class="col-lg-6 col-sm12">
                                <div class="form-group">
                                    {{ html()->label(__('Office phone'))->for('office_phone') }}
                                    {{ html()->text('office_phone')
                                        ->class('form-control')
                                        ->id('officePhone')
                                        ->placeholder(__('888-555-1234'))
                                        ->attribute('maxlength', 12)
                                      }}

                                </div>
                            </div>
                            <div class="col-lg-6 col-sm12">
                                <div class="form-group">
                                    {{ html()->label(__('Extension '))->for('extension') }}
                                    {{ html()->text('extension')
                                        ->class('form-control')
                                        ->type('number')
                                        ->placeholder(__('333'))
                                        ->attribute('maxlength', 5)
                                      }}
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm12">
                                <div class="form-group">
                                    {{ html()->label(__('Cell phone'))->for('cell_phone') }}
                                    {{ html()->text('cell_phone')
                                        ->class('form-control')
                                         ->id('cellPhone')
                                        ->placeholder(__('888-555-1234'))
                                        ->attribute('maxlength', 12)
                                        ->autofocus() }}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    {{ form_submit(__('Create')) }}
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script>




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

        var officePhone = document.getElementById("officePhone");
        officePhone.addEventListener("keyup", setOfficePhone);

        var cellPhone = document.getElementById("cellPhone");
        cellPhone.addEventListener("keyup", setCellPhone);


    </script>
@endsection


