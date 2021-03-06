@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.sub_product_service_type.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.sub_product_service_types.update", [$sub_product_service_type->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.sub_product_service_type.fields.name') }}</label>
                <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', $sub_product_service_type->name) }}" required>
                @if($errors->has('name'))
                    <div class="invalid-feedback">
                        {{ $errors->first('name') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.sub_product_service_type.fields.name_helper') }}</span>
            </div>


            <div class="form-group">
                <label class="required" for="main_product_service_type_id">{{ trans('cruds.sub_product_service_type.fields.main_product_service_type_id') }}</label>

                <select class="form-control select2 {{ $errors->has('main_product_service_type_id') ? 'is-invalid' : '' }}" name="main_product_service_type_id" id="main_product_service_type_id"  required>
                    @foreach($main_product_service_types as $id => $value)
                        <option value="{{ $id }}" {{ in_array($id, old('value', [$sub_product_service_type->MainProductServiceType->id])) ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </select>
                @if($errors->has('main_product_service_type_id'))
                    <div class="invalid-feedback">
                        {{ $errors->first('main_product_service_type_id') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.sub_product_service_type.fields.main_product_service_type_id_helper') }}</span>
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
