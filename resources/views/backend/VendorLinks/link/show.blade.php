@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.dashboard.title'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10 col-md-8 col-sm-12 align-self-center">
            <div class="card">
                <div class="card-header">
                    <strong>
                        Partner Link
                    </strong>
                    {{--                    <span>{{ $vendors[$link->id]->picture }}</span>--}}
                    @if (!$login)
                        @can('administrate all logins')
                            <div class="btn-toolbar float-right" role="toolbar"
                                 aria-label="@lang('labels.general.toolbar_btn_groups')">
                                <form action="{{ route('dashboard.vendorlinks.linklogin.create') }}" method="POST"
                                      enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="linkId" value="{{ $link->id }}">
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-plus-circle"></i>
                                    </button>
                                </form>
                            </div>
                        @endcan
                    @endif
                </div>
                <div class="card-body">
                    {{--                {{ html()->form('POST', route('dashboard.vendor.store'))->open() }}--}}
                    {{--                    <form>--}}
                    {{ csrf_field() }}
                    <div class="row justify-content-between">
                        <div class="col-lg-6 col-sm-12 text-center">

                            <img
                                src="{{\Illuminate\Support\Facades\Storage::disk('s3')->url($link->vendors->picture) }}"
                                class="rounded mx-auto" alt="Partner picture" style="height: 200px">
                        </div>
                        @if (isset($link->representative))
                            <div class="col-lg-6 col-sm-12 ">
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
                                <div class="form-group">
                                    {{ html()->label(__('Office phone'))->for('office_phone') }}
                                    {{ html()->text('office_phone')
                                        ->class('form-control')
                                        ->value($link->extension ? $link->office_phone . ';' . $link->extension : $link->office_phone)
                                        ->attribute('maxlength', 50)
                                        ->readonly()
                                        ->required()
                                      }}
                                </div>
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
                        @endif
                        @if ($link->email)
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
                        @endif

                        @if (isset($link->notes))
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
                        @endif
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label for="vendor">Vendor</label>

                                <input class="form-control" type="text" name="VendorName" id="vendor name"
                                       value="{{ $link->vendors->company_name }}" readonly="" maxlength="100">
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label for="category">Category</label>
                                <input class="form-control" type="text" name="VendorName" id="vendor name"
                                       value="{{ $link->categories->name }}" readonly="" maxlength="100">
                            </div>
                        </div>
                        {{--                            {{ html()->form()->close() }}--}}
                    </div>
                    @can('view logins')
                        @if ($login)
                            <div class="border border-primary rounded container p-3 mt-3" id="passwordshow">
                                <h2>Login Information</h2>
                                <div class="row">
                                    <div class="col-12"><a href="{{$link->web_address}}"
                                                           target="_blank">{{$link->web_address}}</a>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="col-12">
                                            <label for="username">User Name</label>
                                            <!-- Target -->
                                            <input type="text" name="username" id="username"
                                                   value="{{ $login->user_name }}" readonly="" maxlength="100">
                                        </div>
                                        <div class="col-12">
                                            <label for="username">Password&ensp;</label>
                                            <!-- Target -->
                                            <input type="text" name="password" id="password" class="collapse"
                                                   value="{{ $login->password }}">
                                            <span onclick="toggle()">
                                                    <i class="fas fa-eye" id="eyeball"></i>
                                                </span>
                                            <span class="collapse" id="password" data-parent="#passwordshow">
                                                        <strong>Password</strong>&emsp; {{ $login->password }}
                                                    </span>
                                            <!-- Trigger -->
                                            <button class="btn" data-clipboard-text="{{ $login->password }}">
                                                <img
                                                    src="/img/backend/svg/clippy.svg"
                                                    width="20" alt="Copy to clipboard">
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="col-12">
                                            @if (isset($link->web_address))
                                                <a href="https://www.{{ $link->web_address }}" target="_blank">
                                                    <button class="btn btn-primary">Login</button>
                                                </a>
                                            @endif
                                            @can('administrate all logins')
                                                <a href="{{ route('dashboard.vendorlinks.linklogin.edit', $login) }}"
                                                   data-toggle="tooltip"
                                                   data-placement="top" title=""
                                                   class="btn btn-primary"
                                                   data-original-title="Edit"><i
                                                        class="fas fa-edit"></i></a>
                                                <a href="{{ route('dashboard.vendorlinks.linklogin.destroy', $login->id) }}"
                                                   data-method="delete"
                                                   data-token="{{csrf_token()}}" data-confirm="Are you sure?">
                                                    <button class="btn btn-danger trash-btn"
                                                            type="submit">
                                                        <i data-original-title="Delete"
                                                           class="fas fa-trash"></i>
                                                    </button>
                                                </a>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endcan
                    <div class="row pt-4">
                        <div class="col">
                            <div class="float-right">
                                <a href="{{ route('dashboard.vendorlinks.index') }}" data-toggle="tooltip"
                                   data-placement="top" title="" class="btn btn-sm btn-info"
                                   data-original-title="Back">
                                    Back</a>
                                {{--                                    --}}
                                @can('administrate all partnerlinks')
                                    <a href="{{route('dashboard.vendorlinks.link.edit', $link)}}"
                                       data-toggle="tooltip" data-placement="top" title=""
                                       class="btn btn-sm btn-primary"
                                       data-original-title="Edit">
                                        Edit</a>

                                    <a href="{{ route('dashboard.vendorlinks.link.destroy', $link) }}"
                                       data-method="delete"
                                       data-token="{{csrf_token()}}" data-confirm="Are you sure?">
                                        <button class="btn btn-danger trash-btn"
                                                type="submit">
                                            <i data-original-title="Delete"
                                               class="fas fa-trash"></i>
                                        </button>
                                    </a>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/clipboard@2/dist/clipboard.min.js"></script>
    <script>
        var clipboard = new ClipboardJS('.btn');

        clipboard.on('success', function (e) {
            console.log(e);
        });

        clipboard.on('error', function (e) {
            console.log(e);
        });

        function toggle() {
            $('#password').toggleClass('collapse');
            $('#eyeball').toggleClass('collapse');
        }
    </script>
@endsection


