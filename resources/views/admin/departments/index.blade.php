@extends('layouts.admin')
@section('content')
    @can('department_create')



        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-6">
                <a class="btn btn-success" href="{{ route('admin.departments.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.department.title_singular') }}
                </a>
                @can('excel_delete')
                    <a class="btn btn-primary pl-2" href="{{ route('admin.department_excels.index') }}">
                        {{ trans('global.show') }} {{ trans('cruds.product_excel.title_singular') }}
                    </a>
                    @if($department_excel_not_read_count >0)
                        <a class="btn btn-warning rounded-circle ">

                            {{$department_excel_not_read_count}}
                        </a>
                    @endif
                @endcan
            </div>

            @include('partials.addExcel',['route_name'=>'upload_departments_excel'])
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.department.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Department">
                <thead>
                <tr>
                    <th width="10">

                    </th>

                    <th>
                        &nbsp;
                    </th>
                    {{--                    <th>--}}
                    {{--                        {{ trans('cruds.department.fields.id') }}--}}
                    {{--                    </th>--}}
                    <th>
                        {{ trans('cruds.department.fields.item_number') }}
                    </th>
                    <th>
                        {{ trans('cruds.department.fields.show_in_main_page') }}
                    </th>

                    <th>
                        {{ trans('cruds.department.fields.show_in_main_departments_page') }}
                    </th>
                    <th>
                        {{ trans('cruds.department.fields.name') }}
                    </th>
                    <th>
                        {{ trans('cruds.department.fields.logo') }}
                    </th>
                    <th>
                        {{ trans('cruds.department.fields.about') }}
                    </th>
                    <th>
                        {{ trans('cruds.department.fields.city') }}
                    </th>
                    <th>
                        {{ trans('cruds.department.fields.phone_number') }}
                    </th>
                    <th>
                        {{ trans('cruds.department.fields.category') }}
                    </th>
                    <th>
                        {{ trans('cruds.department.fields.sub_category') }}
                    </th>
                    <th>
                        {{ trans('cruds.department.fields.type_of_category') }}
                    </th>
                    <th>
                        {{ trans('cruds.department.fields.trader') }}
                    </th>
                </tr>
                <tr>
                    <td>
                    </td>
                    <td>
                    </td>

                    {{--                    <td>--}}
                    {{--                        <input class="search" type="text" placeholder="{{ trans('global.search') }}">--}}
                    {{--                    </td>--}}

                    <td>
                        <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                    </td>

                    <td>
                        <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                    </td>

                    <td>
                        <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                    </td>

                    <td>
                        <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                    </td>
                    <td>
                    </td>
                    <td>
                        <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                    </td>

                    <td>
                        <select class="search">
                            <option value>{{ trans('global.all') }}</option>
                            @foreach($cities as $key => $item)
                                <option value="{{ $item->name }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                    </td>


                    <td>
                        <select class="search">
                            <option value>{{ trans('global.all') }}</option>
                            @foreach($categories as $key => $item)
                                <option value="{{ $item->name }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </td>

                    <td>
                        <select class="search">
                            <option value>{{ trans('global.all') }}</option>
                            @foreach($sub_categories as $key => $item)
                                <option value="{{ $item->name }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </td>

                    <td>
                        <select class="search">
                            <option value>{{ trans('global.all') }}</option>
                            @foreach($constants as $key => $item)
                                <option value="{{ $constants_flips[$item] }}">{{ $item }}</option>
                            @endforeach
                        </select>
                    </td>

                    <td>
                        <select class="search">
                            <option value>{{ trans('global.all') }}</option>
                            @foreach($traders as $key => $item)
                                <option value="{{ $item->name }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </td>

                </tr>
                </thead>
            </table>
        </div>
    </div>



@endsection
@section('scripts')
    @parent
    <script>
        $(function () {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
            @can('department_delete')
            let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
            let deleteButton = {
                text: deleteButtonTrans,
                url: "{{ route('admin.departments.massDestroy') }}",
                className: 'btn-danger',
                action: function (e, dt, node, config) {
                    var ids = $.map(dt.rows({selected: true}).data(), function (entry) {
                        return entry.id
                    });

                    if (ids.length === 0) {
                        alert('{{ trans('global.datatables.zero_selected') }}')

                        return
                    }

                    if (confirm('{{ trans('global.areYouSure') }}')) {
                        $.ajax({
                            headers: {'x-csrf-token': _token},
                            method: 'POST',
                            url: config.url,
                            data: {ids: ids, _method: 'DELETE'}
                        })
                            .done(function () {
                                location.reload()
                            })
                    }
                }
            }
            dtButtons.push(deleteButton)
            @endcan

            let dtOverrideGlobals = {
                buttons: dtButtons,
                processing: true,
                serverSide: true,
                retrieve: true,
                aaSorting: [],
                ajax: "{{ route('admin.departments.index') }}",
                columns: [
                    {data: 'placeholder', name: 'placeholder', orderable: false, searchable: false},
                    {data: 'actions', name: '{{ trans('global.actions') }}', orderable: false, searchable: false},
                    // {data: 'id', name: 'id'},
                    {data: 'item_number', name: 'item_number', sortable: true, searchable: true},
                    {data: 'show_in_main_page', name: 'show_in_main_page'},
                    {data: 'show_in_main_departments_page', name: 'show_in_main_departments_page'},
                    {data: 'name', name: 'name'},
                    {data: 'logo', name: 'logo', sortable: false, searchable: false},
                    {data: 'about', name: 'about'},
                    {data: 'city_name', name: 'city.name'},
                    {data: 'phone_number', name: 'phone_number'},
                    {data: 'category_name', name: 'category.name'},
                    {data: 'sub_category_name', name: 'sub_category.name'},
                    {data: 'type_of_category', name: 'category.type'},
                    {data: 'trader_name', name: 'trader.name'},
                ],
                orderCellsTop: true,
                order: [[1, 'desc']],
                pageLength: 50,
            };
            let table = $('.datatable-Department').DataTable(dtOverrideGlobals);
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function (e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });
            $('.datatable thead').on('input', '.search', function () {
                let strict = $(this).attr('strict') || false
                let value = strict && this.value ? "^" + this.value + "$" : this.value
                table
                    .column($(this).parent().index())
                    .search(value, strict)
                    .draw()
            });
        });

    </script>
@endsection
