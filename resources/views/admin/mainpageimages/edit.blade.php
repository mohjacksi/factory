@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.edit') }} {{ trans('cruds.advertisement.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route("admin.mainpageimages.update", [$advertisement->id]) }}"
                  enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="form-group">
                    <label class="required" for="images">{{ trans('cruds.advertisement.fields.images') }}</label>
                    <div class="needsclick dropzone {{ $errors->has('images') ? 'is-invalid' : '' }}"
                         id="images-dropzone">
                    </div>
                    @if($errors->has('images'))
                        <div class="invalid-feedback">
                            {{ $errors->first('images') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.advertisement.fields.images_helper') }}</span>
                </div>


                <div class="form-group">
                    <label class="required"
                           for="city_id">{{ trans('cruds.item_advertisement.fields.city_name') }}</label>
                    <select class="form-control select2 {{ $errors->has('city') ? 'is-invalid' : '' }}" name="city_id"
                            id="city_id" required>
                        @foreach($cities as $id => $city)
                            <option
                                value="{{ $id }}" {{ (old('city_id') ? old('city_id') : $advertisement->city->id ?? '') == $id ? 'selected' : '' }}>{{ $city }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('city'))
                        <div class="invalid-feedback">
                            {{ $errors->first('city') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.item_advertisement.fields.city_name_helper') }}</span>
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

@section('scripts')
    <script>
        var uploadedImagesMap = {}
        Dropzone.options.imagesDropzone = {
            url: '{{ route('admin.mainpageimages.storeMedia') }}',
            maxFilesize: 10, // MB
            acceptedFiles: '.jpeg,.jpg,.png,.gif',
            maxFiles: 1000,
            addRemoveLinks: true,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            params: {
                size: 10,
                width: 4096,
                height: 4096
            },
            success: function (file, response) {
                $('form').append('<input type="hidden" name="images[]" value="' + response.name + '">')
            },
            removedfile: function (file) {
                // console.log($('form'));
                // console.log(file);
                file.previewElement.remove();
                if (file.status !== 'error') {
                    $('form').find('input[value="' + file.file_name + '"]').remove();
                    this.options.maxFiles = this.options.maxFiles + 1
                }
            },
            init: function () {
                @if(isset($advertisement) && $advertisement->images)
                let file = {!! json_encode($advertisement->getMedia('images')) !!};
                for (var i in file) {
                    this.options.addedfile.call(this, file[i])
                    // console.log(file[i]);
                    this.options.thumbnail.call(this, file[i], location.origin + '/public/storage/' + file[i].id + '/' + file[i].file_name)
                    file[i].previewElement.classList.add('dz-complete')
                    $('form').append('<input type="hidden" name="images[]" value="' + file[i].file_name + '">')
                    this.options.maxFiles = this.options.maxFiles - 1
                }
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
