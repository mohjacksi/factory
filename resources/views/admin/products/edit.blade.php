@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.edit') }} {{ trans('cruds.product.title_singular') }}
        </div>

        <div class="card-body">
            <form name="product_edit" method="POST" action="{{ route("admin.products.update", [$product->id]) }}"
                  enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="form-group">
                    <label for="image">{{ trans('cruds.product.fields.image') }}</label>
                    <div class="needsclick dropzone {{ $errors->has('image') ? 'is-invalid' : '' }}"
                         id="image-dropzone">
                    </div>
                    @if($errors->has('image'))
                        <div class="invalid-feedback">
                            {{ $errors->first('image') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.product.fields.image_helper') }}</span>
                </div>


                <div class="form-group">
                    <label for="name">{{ trans('cruds.product.fields.name') }}</label>
                    <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name"
                           id="name" value="{{ old('name', $product->name) }}">
                    @if($errors->has('name'))
                        <div class="invalid-feedback">
                            {{ $errors->first('name') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.product.fields.name_helper') }}</span>
                </div>


                <div class="form-group">
                    <label for="product_code">{{ trans('cruds.product.fields.product_code') }}</label>
                    <input class="form-control {{ $errors->has('product_code') ? 'is-invalid' : '' }}" type="text"
                           name="product_code"
                           id="product_code" value="{{ old('product_code', $product->product_code) }}">
                    @if($errors->has('product_code'))
                        <div class="invalid-feedback">
                            {{ $errors->first('product_code') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.product.fields.product_code_helper') }}</span>
                </div>


                <div class="form-group">
                    <label for="brand_id">{{ trans('cruds.product.fields.brand_name') }}</label>
                    <select class="form-control select2 {{ $errors->has('brand') ? 'is-invalid' : '' }}"
                            name="brand_id" id="brand_id">
                        @foreach($brands as $id => $brand)
                            <option
                                value="{{ $id }}" {{ (old('brand_id') ? old('brand_id') : $product->brand->id ?? '') == $id ? 'selected' : '' }}>{{  $brand }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('brand'))
                        <div class="invalid-feedback">
                            {{ $errors->first('brand') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.product.fields.brand_name_helper') }}</span>
                </div>


                <div class="form-group">
                    <label class="required"
                           for="main_product_type_id">{{ trans('cruds.product.fields.main_product_type_name') }}</label>
                    <select class="form-control select2 {{ $errors->has('main_product_type') ? 'is-invalid' : '' }}"
                            name="main_product_type_id" id="main_product_type_id">
                        @foreach($main_product_types as $id => $main_product)
                            <option
                                value="{{ $id }}" {{ (old('main_product_type_id') ? old('main_product_type_id') : $product->main_product_type_id ?? '') == $id ? 'selected' : '' }}>{{ $main_product }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('main_product_type'))
                        <div class="invalid-feedback">
                            {{ $errors->first('main_product_type') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.product.fields.main_product_type_name_helper') }}</span>
                </div>


                <div class="form-group">
                    <label class="required"
                           for="sub_product_type_id">{{ trans('cruds.product.fields.sub_product_type_name') }}</label>
                    <select class="form-control select2 {{ $errors->has('sub_product_type') ? 'is-invalid' : '' }}"
                            name="sub_product_type_id" id="sub_product_type_id">

                    </select>
                    @if($errors->has('sub_product_type'))
                        <div class="invalid-feedback">
                            {{ $errors->first('sub_product_type') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.product.fields.sub_product_type_name_helper') }}</span>
                </div>


                <div class="form-group">
                    <label class="required"
                           for="main_product_service_type_id">{{ trans('cruds.product.fields.main_product_service_type_name') }}</label>
                    <select
                        class="form-control select2 {{ $errors->has('main_product_service_type') ? 'is-invalid' : '' }}"
                        name="main_product_service_type_id" id="main_product_service_type_id">
                        @foreach($main_product_service_types as $id => $main_product)
                            <option
                                value="{{ $id }}" {{ (old('main_product_service_type_id') ? old('main_product_service_type_id') : $product->main_product_service_type_id ?? '') == $id ? 'selected' : '' }}>{{ $main_product }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('main_product_service_type'))
                        <div class="invalid-feedback">
                            {{ $errors->first('main_product_service_type') }}
                        </div>
                    @endif
                    <span
                        class="help-block">{{ trans('cruds.product.fields.main_product_service_type_name_helper') }}</span>
                </div>


                <div class="form-group">
                    <label class="required"
                           for="sub_product_service_type_id">{{ trans('cruds.product.fields.sub_product_service_type_name') }}</label>
                    <select
                        class="form-control select2 {{ $errors->has('sub_product_service_type') ? 'is-invalid' : '' }}"
                        name="sub_product_service_type_id" id="sub_product_service_type_id">

                    </select>
                    @if($errors->has('sub_product_service_type'))
                        <div class="invalid-feedback">
                            {{ $errors->first('sub_product_service_type') }}
                        </div>
                    @endif
                    <span
                        class="help-block">{{ trans('cruds.product.fields.sub_product_service_type_name_helper') }}</span>
                </div>


                <div class="form-group">
                    <label for="price">{{ trans('cruds.product.fields.price') }}</label>
                    <input class="form-control {{ $errors->has('price') ? 'is-invalid' : '' }}" type="text" name="price"
                           id="price" value="{{ old('price', $product->price) }}">
                    @if($errors->has('price'))
                        <div class="invalid-feedback">
                            {{ $errors->first('price') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.product.fields.price_helper') }}</span>
                </div>


                <div class="form-group">
                    <label for="name">{{ trans('cruds.product.fields.price_after_discount') }}</label>
                    <input class="form-control {{ $errors->has('price_after_discount') ? 'is-invalid' : '' }}"
                           type="text" name="price_after_discount"
                           id="price_after_discount"
                           value="{{ old('price_after_discount', $product->price_after_discount) }}">
                    @if($errors->has('price_after_discount'))
                        <div class="invalid-feedback">
                            {{ $errors->first('price_after_discount') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.product.fields.price_after_discount_helper') }}</span>
                </div>


                <div class="form-group">
                    <label for="city_id">{{ trans('cruds.product.fields.city_name') }}</label>
                    <select class="form-control select2 {{ $errors->has('city') ? 'is-invalid' : '' }}"
                            name="city_id" id="city_id">
                        @foreach($cities as $id => $city)
                            <option
                                value="{{ $id }}" {{ (old('city_id') ? old('city_id') : $product->city->id ?? '') == $id ? 'selected' : '' }}>{{ $city }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('city'))
                        <div class="invalid-feedback">
                            {{ $errors->first('city') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.product.fields.city_name_helper') }}</span>
                </div>



                <div class="form-group" id="div_trader_id" style="display: none;">
                    <label for="trader_id">{{ trans('cruds.product.fields.trader') }}</label>
                    <select class="form-control select2 {{ $errors->has('trader') ? 'is-invalid' : '' }}"
                            name="trader_id" id="trader_id">
                        @foreach($traders as $id => $trader)
                            <option
                                value="{{ $id }}" {{ (old('trader_id') ? old('trader_id') : $product->trader->id ?? '') == $id ? 'selected' : '' }}>{{  $id . ' - '. $trader }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('trader'))
                        <div class="invalid-feedback">
                            {{ $errors->first('trader') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.product.fields.trader_helper') }}</span>
                </div>



                <div class="form-group" id="div_department_id" style="display: none;">
                    <label for="department_id">{{ trans('cruds.product.fields.department_name') }}</label>
                    <select class="form-control select2 {{ $errors->has('department') ? 'is-invalid' : '' }}"
                            name="department_id" id="department_id">
                        @foreach($departments as $id => $department)
                            <option
                                    value="{{ $id }}" {{ (old('department_id') ? old('department_id') : $product->department->id ?? '') == $id ? 'selected' : '' }}>{{ $department }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('department'))
                        <div class="invalid-feedback">
                            {{ $errors->first('department') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.product.fields.department_name_helper') }}</span>
                </div>



                <div class="form-group">
                    <label for="detailed_title">{{ trans('cruds.product.fields.detailed_title') }}</label>
                    <input class="form-control {{ $errors->has('detailed_title') ? 'is-invalid' : '' }}" type="text"
                           name="detailed_title"
                           id="detailed_title" value="{{ old('detailed_title', $product->detailed_title) }}">
                    @if($errors->has('detailed_title'))
                        <div class="invalid-feedback">
                            {{ $errors->first('detailed_title') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.product.fields.detailed_title_helper') }}</span>
                </div>

                <div class="form-group">
                    <label for="details">{{ trans('cruds.product.fields.details') }}</label>
                    <textarea class="form-control {{ $errors->has('details') ? 'is-invalid' : '' }}" type="text"
                              name="details"
                              id="details">{{ old('detailed_title', $product->details) }}</textarea>
                    @if($errors->has('details'))
                        <div class="invalid-feedback">
                            {{ $errors->first('details') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.product.fields.details_helper') }}</span>
                </div>


                <div class="form-group">
                    <div class="form-check {{ $errors->has('show_in_main_page') ? 'is-invalid' : '' }}">
                        <input class="form-check-input" type="checkbox" name="show_in_main_page" id="show_in_main_page"
                               value="1"
                            {{ old('show_in_main_page', $product->show_in_main_page) == 1 ? 'checked' : '' }}>
                        <label class="required form-check-label"
                               for="show_in_main_page">{{ trans('cruds.product.fields.show_in_main_page') }}</label>
                    </div>
                    @if($errors->has('show_in_main_page'))
                        <div class="invalid-feedback">
                            {{ $errors->first('show_in_main_page') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.product.fields.show_in_main_page_helper') }}</span>
                </div>

                <div class="form-group">
                    <div class="form-check {{ $errors->has('show_in_trader_page') ? 'is-invalid' : '' }}">
                        <input class="form-check-input" type="checkbox" name="show_in_trader_page"
                               id="show_in_trader_page" value="1"
                            {{ old('show_in_trader_page', $product->show_in_trader_page) == 1 ? 'checked' : '' }}>
                        <label class="required form-check-label"
                               for="show_in_trader_page">{{ trans('cruds.product.fields.show_in_trader_page') }}</label>
                    </div>
                    @if($errors->has('show_in_trader_page'))
                        <div class="invalid-feedback">
                            {{ $errors->first('show_in_trader_page') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.product.fields.show_in_trader_page_helper') }}</span>
                </div>

                <div class="form-group">
                    <div class="form-check {{ $errors->has('show_trader_name') ? 'is-invalid' : '' }}">
                        <input class="form-check-input" type="checkbox" name="show_trader_name"
                               id="show_trader_name" value="1"
                            {{ old('show_trader_name', $product->show_trader_name) == 1 ? 'checked' : '' }}>
                        <label class="required form-check-label"
                               for="show_trader_name">{{ trans('cruds.product.fields.show_trader_name') }}</label>
                    </div>
                    @if($errors->has('show_trader_name'))
                        <div class="invalid-feedback">
                            {{ $errors->first('show_trader_name') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.product.fields.show_trader_name_helper') }}</span>
                </div>


                <div class="form-group">
                    <div class="form-check {{ $errors->has('is_available') ? 'is-invalid' : '' }}">
                        <input class="form-check-input" type="checkbox" name="is_available"
                               id="is_available" value="1"
                            {{ old('is_available', $product->is_available) == 1 ? 'checked' : '' }}>
                        <label class="required form-check-label"
                               for="is_available">{{ trans('cruds.product.fields.is_available') }}</label>
                    </div>
                    @if($errors->has('is_available'))
                        <div class="invalid-feedback">
                            {{ $errors->first('is_available') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.product.fields.is_available_helper') }}</span>
                </div>


                <hr/>


                <div class="form-group">
                    <button class="btn btn-danger" type="submit" name="product_edit">
                        {{ trans('global.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>










@endsection



@php
    $user = \Illuminate\Support\Facades\Auth::User();
    $token = $user->createToken($user->email.'-'.now());
    $token = $token->accessToken;

@endphp



@section('scripts')







    @include('admin.products.components.form_scripts',[
        'token'=>$token,
        'component_id'=>isset($product)?$product->sub_product_type_id:'0',
        'main_name_id'=>'#main_product_type_id',
        'sub_name_id'=>'sub_product_type_id',
        'api_url'=>'/public/api/v1/get_main_product_type_ajax/',
    ]);

    @include('admin.products.components.form_scripts',[
        'token'=>$token,
        'component_id'=>isset($product)?$product->sub_product_service_type_id:'0',
        'main_name_id'=>'#main_product_service_type_id',
        'sub_name_id'=>'sub_product_service_type_id',
        'api_url'=>'/public/api/v1/get_main_product_service_type_ajax/',
    ]);



    @include('admin.products.components.trader_city_form_scripts',
    [
        'token'=>$token,
        'component_id'=>isset($product)?$product->trader_id:'0',
        'main_name_id'=>'#city_id',
        'sub_name_id'=>'trader_id',
        'api_url'=>'/public/api/v1/get_traders_of_city/',
    ])


    @include('admin.products.components.trader_city_form_scripts',
    [
        'token'=>$token,
        'component_id'=>isset($product)?$product->department_id:'0',
        'main_name_id'=>'#trader_id',
        'sub_name_id'=>'department_id',
        'api_url'=>'/public/api/v1/get_departments_of_trader/',
    ])



    <script>

        Dropzone.options.imageDropzone = {
            url: '{{ route('admin.products.storeMedia') }}',
            maxFilesize: 5, // MB
            acceptedFiles: '.jpeg,.jpg,.png,.gif',
            maxFiles: 1,
            addRemoveLinks: true,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            params: {
                size: 5,
                width: 4096,
                height: 4096
            },
            success: function (file, response) {
                $('form').find('input[name="image"]').remove()
                $('form').append('<input type="hidden" name="image" value="' + response.name + '">')
            },
            removedfile: function (file) {
                file.previewElement.remove()
                if (file.status !== 'error') {
                    $('form').find('input[name="image"]').remove()
                    this.options.maxFiles = this.options.maxFiles + 1
                }
            },
            init: function () {
                @if(isset($product) && $product->image)
                var file = {!! json_encode($product->image) !!}
                    this.options.addedfile.call(this, file)
                this.options.thumbnail.call(this, file, file.preview)
                file.previewElement.classList.add('dz-complete')
                $('form').append('<input type="hidden" name="image" value="' + file.file_name + '">')
                this.options.maxFiles = this.options.maxFiles - 1
                @endif
            },
            error: function (file, response) {
                if ($.type(response) === 'string') {
                    var message = response //dropzone sends it's own error messages in string
                } else {
                    var message = response.errors.file
                }
                file.previewElement.classList.add('dz-error')
                _ref = file.previewElement.querySelectorAll('[data-dz-errormessage]')
                _results = []
                for (_i = 0, _len = _ref.length; _i < _len; _i++) {
                    node = _ref[_i]
                    _results.push(node.textContent = message)
                }

                return _results
            }
        }
    </script>

@endsection
