@extends('layouts.admin')
@section('content')

    <div class="card">

    <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.products.show',$product) }}">
                    مشاهدة المنتج     {{$product->name }}
                    </a>
                </div>
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('cruds.variant.title_singular') }}
        </div>



        <div class="card-body">
            <form method="POST" action="{{ route("admin.products.variants.store",$product) }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="image">{{ trans('cruds.variant.fields.image') }}</label>
                    <div class="needsclick dropzone {{ $errors->has('image') ? 'is-invalid' : '' }}"
                         id="image-dropzone">
                    </div>
                    @if($errors->has('image'))
                        <div class="invalid-feedback">
                            {{ $errors->first('image') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.variant.fields.image_helper') }}</span>
                </div>


                <div class="form-group">
                    <label for="price">{{ trans('cruds.variant.fields.price') }}</label>
                    <input class="form-control {{ $errors->has('price') ? 'is-invalid' : '' }}" type="text"
                           name="price"
                           id="price" value="{{ old('price', '') }}">
                    @if($errors->has('price'))
                        <div class="invalid-feedback">
                            {{ $errors->first('price') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.variant.fields.price_helper') }}</span>
                </div>



                <div class="form-group">
                    <label for="count">{{ trans('cruds.variant.fields.count') }}</label>
                    <input class="form-control {{ $errors->has('count') ? 'is-invalid' : '' }}"
                           type="number" name="count"
                           id="count" value="{{ old('count', '') }}">
                    @if($errors->has('count'))
                        <div class="invalid-feedback">
                            {{ $errors->first('count') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.variant.fields.count_helper') }}</span>
                </div>

                <div class="form-group">
                    <label for="color_id">{{ trans('cruds.variant.fields.color_name') }}</label>
                    <select class="form-control select2 {{ $errors->has('color') ? 'is-invalid' : '' }}"
                            name="color_id" id="color_id">
                        @foreach($colors as $id => $color)
                            <option
                                value="{{ $id }}" {{ old('color_id') == $id ? 'selected' : '' }}>{{ $color }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('color'))
                        <div class="invalid-feedback">
                            {{ $errors->first('color') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.variant.fields.color_name_helper') }}</span>
                </div>

                <div class="form-group">
                    <label for="size_id">{{ trans('cruds.variant.fields.size_name') }}</label>
                    <select class="form-control select2 {{ $errors->has('size') ? 'is-invalid' : '' }}"
                            name="size_id" id="size_id">
                        @foreach($sizes as $id => $size)
                            <option
                                value="{{ $id }}" {{ old('size_id') == $id ? 'selected' : '' }}>{{ $size }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('size'))
                        <div class="invalid-feedback">
                            {{ $errors->first('size') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.variant.fields.size_name_helper') }}</span>
                </div>



                <div class="form-group">
                    <div class="form-check {{ $errors->has('is_available') ? 'is-invalid' : '' }}">
                        <input class="form-check-input" type="checkbox" name="is_available" id="is_available"
                               value="1"
                            {{ old('is_available', 0) == 1 ? 'checked' : '' }}>
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


                <div class="form-group">
                    <button class="btn btn-danger" type="submit">
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


    <script>

        Dropzone.options.imageDropzone = {
            url: '{{ route('admin.variants.storeMedia') }}',
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
                @if(isset($variant) && $variant->image)
                var file = {!! json_encode($variant->image) !!}
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
