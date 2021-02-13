@extends('layouts.admin')
@section('content')
    @can('product_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.products.variants.create',$product) }}">
                    {{ trans('global.add') }} {{ trans('cruds.variant.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.variant.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-variant">
                <thead>
                <tr>
                    <th width="10">

                    </th>
                    <th>
                        &nbsp;
                    </th>
                    <th>
                        {{ trans('cruds.variant.fields.id') }}
                    </th>
                    <th>
                        {{ trans('cruds.variant.fields.is_available') }}
                    </th>
                    <th>
                        {{ trans('cruds.variant.fields.color_name') }}
                    </th>
                    <th>
                        {{ trans('cruds.variant.fields.size_name') }}
                    </th>
                    <th>
                        {{ trans('cruds.variant.fields.price') }}
                    </th>
                    <th>
                        {{ trans('cruds.variant.fields.count') }}
                    </th>
                    <th>
                        {{ trans('cruds.variant.fields.image') }}
                    </th>
                </tr>
                <tr>
                    <td>
                    </td>
                    <td>

                    </td>

                    <td>
                        <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                    </td>
                    <td>
                        <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                    </td>


                    <td>
                        <select class="search">
                            <option value>{{ trans('global.all') }}</option>
                            @foreach($colors as $key => $item)
                                <option value="{{ $item->name }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <select class="search">
                            <option value>{{ trans('global.all') }}</option>
                            @foreach($sizes as $key => $item)
                                <option value="{{ $item->name }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </td>


                    <td>
                        <input class="search" type="text" placeholder="{{ trans('global.search') }}">

                    </td>
                    <td>
                        <input class="search" type="text" placeholder="{{ trans('global.search') }}">

                    </td>
                    <td>
                    </td>
                </tr>
                </thead>
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.products.show',$product) }}">
                        مشاهدة المنتج {{$product->name }}
                    </a>
                </div>
            </table>
        </div>
    </div>

@endsection
@section('scripts')
    @parent
    <script>
        $(function () {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
            @can('product_delete')
            let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
            let deleteButton = {
                text: deleteButtonTrans,
                url: "{{ route('admin.variants.massDestroy') }}",
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
                ajax: "{{ route('admin.products.variants.index',$product) }}",
                columns: [
                    {data: 'placeholder', name: 'placeholder', orderable: false, searchable: false},
                    {data: 'actions', name: '{{ trans('global.actions') }}', orderable: false, searchable: false},
                    {data: 'id', name: 'id'},
                    {data: 'is_available', name: 'is_available', searchable: true},
                    {data: 'color_name', name: 'color.name'},
                    {data: 'size_name', name: 'size.name'},
                    {data: 'price', name: 'price'},
                    {data: 'count', name: 'count'},
                    {data: 'image', name: 'image'},
                ],
                orderCellsTop: true,
                order: [[1, 'desc']],
                pageLength: 50,
            };
            let table = $('.datatable-variant').DataTable(dtOverrideGlobals);
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
