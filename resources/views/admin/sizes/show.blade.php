@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.size.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.sizes.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.size.fields.id') }}
                        </th>
                        <td>
                            {{ $size->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.size.fields.name') }}
                        </th>
                        <td>
                            {{ $size->name }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.sizes.index') }}">
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
            <a class="nav-link" href="#size_departments" role="tab" data-toggle="tab">
                {{ trans('cruds.department.title') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#size_news" role="tab" data-toggle="tab">
                {{ trans('cruds.news.title') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#size_job_offers" role="tab" data-toggle="tab">
                {{ trans('cruds.jobOffer.title') }}
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane" role="tabpanel" id="size_departments">
            @includeIf('admin.sizes.relationships.sizeDepartments', ['departments' => $size->sizeDepartments])
        </div>
        <div class="tab-pane" role="tabpanel" id="size_news">
            @includeIf('admin.sizes.relationships.sizeNews', ['news' => $size->sizeNews])
        </div>
        <div class="tab-pane" role="tabpanel" id="size_job_offers">
            @includeIf('admin.sizes.relationships.sizeJobOffers', ['jobOffers' => $size->sizeJobOffers])
        </div>
    </div>
</div>

@endsection
