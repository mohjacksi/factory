@extends('layouts.admin')
@section('content')
    @can('excel_delete')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.news.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>

        <div class="col-md-6 text-right ">

            @if(session('success'))
                <div class="alert alert-success">تم الإضافة</div>
            @elseif(session('error'))
                <div class="alert alert-danger">{!! \Session::get('error') !!}</div>
            @endif
        </div>

    @endcan
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.product_excel.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-NewsExcel">
                <thead>
                <tr>
                    <th width="10">

                    </th>
                    <th>
                        &nbsp;
                    </th>
                    <th>
                        {{ trans('cruds.product_excel.fields.id') }}
                    </th>
                    <th>
                        {{ trans('cruds.product_excel.fields.user_name') }}
                    </th>
                    <th>
                        {{ trans('cruds.product_excel.fields.file') }}
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
            @can('excel_delete')
            let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
            let deleteButton = {
                text: deleteButtonTrans,
                url: "{{ route('admin.news_excels.massDestroy') }}",
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
                                // sessionStorage.setItem('message','لا يمكن حذف هذه المدن');
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
                ajax: "{{ route('admin.news_excels.index') }}",
                columns: [
                    {data: 'placeholder', name: 'placeholder', orderable: false, searchable: false},
                    {data: 'actions', name: '{{ trans('global.actions') }}', orderable: false, searchable: false},
                    {data: 'id', name: 'id'},
                    {data: 'user_name', name: 'user.name', searchable: true},
                    {data: 'file', name: 'file', sortable: false, searchable: false},

                ],
                orderCellsTop: true,
                order: [[1, 'desc']],
                pageLength: 50,
            };
            let table = $('.datatable-NewsExcel').DataTable(dtOverrideGlobals);
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
