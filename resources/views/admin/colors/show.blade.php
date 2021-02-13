@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.color.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.colors.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.color.fields.id') }}
                        </th>
                        <td>
                            {{ $color->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.color.fields.name') }}
                        </th>
                        <td>
                            {{ $color->name }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.colors.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        {{ trans('global.relatedData') }}
    </div>
    <ul class="nav nav-tabs" role="tablist" id="relationship-tabs">
        <li class="nav-item">
            <a class="nav-link" href="#color_departments" role="tab" data-toggle="tab">
                {{ trans('cruds.department.title') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#color_news" role="tab" data-toggle="tab">
                {{ trans('cruds.news.title') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#color_job_offers" role="tab" data-toggle="tab">
                {{ trans('cruds.jobOffer.title') }}
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane" role="tabpanel" id="color_departments">
            @includeIf('admin.colors.relationships.colorDepartments', ['departments' => $color->colorDepartments])
        </div>
        <div class="tab-pane" role="tabpanel" id="color_news">
            @includeIf('admin.colors.relationships.colorNews', ['news' => $color->colorNews])
        </div>
        <div class="tab-pane" role="tabpanel" id="color_job_offers">
            @includeIf('admin.colors.relationships.colorJobOffers', ['jobOffers' => $color->colorJobOffers])
        </div>
    </div>
</div>

@endsection
