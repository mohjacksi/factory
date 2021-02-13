@extends('layouts.admin')
@section('content')
    @can('offer_create')



        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-6">
                <a class="btn btn-success" href="{{ route('admin.offers.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.offer.title_singular') }}
                </a>

                @can('excel_delete')
                    <a class="btn btn-primary pl-2" href="{{ route('admin.offer_excels.index') }}">
                        {{ trans('global.show') }} {{ trans('cruds.product_excel.title_singular') }}
                    </a>
                    @if($offer_excel_not_read_count >0)
                        <a class="btn btn-warning rounded-circle ">

                            {{$offer_excel_not_read_count}}
                        </a>
                    @endif
                @endcan
            </div>
            @include('partials.addExcel',['route_name'=>'upload_offers_excel'])
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.offer.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Offer">
                <thead>
                <tr>
                    <th width="10">

                    </th>
                    <th>
                        &nbsp;
                    </th>
                    <th>
                        {{ trans('cruds.offer.fields.id') }}
                    </th>
                    <th>
                        {{ trans('cruds.offer.fields.show_in_trader_page') }}
                    </th>
                    <th>
                        {{ trans('cruds.offer.fields.show_in_main_page') }}
                    </th>
                    <th>
                        {{ trans('cruds.offer.fields.show_in_main_offers_page') }}
                    </th>
                    <th>
                        {{ trans('cruds.offer.fields.name') }}
                    </th>
                    <th>
                        {{ trans('cruds.offer.fields.description') }}
                    </th>
                    <th>
                        {{ trans('cruds.offer.fields.category') }}
                    </th>
                    <th>
                        {{ trans('cruds.offer.fields.sub_category') }}
                    </th>
                    <th>
                        {{ trans('cruds.offer.fields.type_of_category') }}
                    </th>
                    <th>
                        {{ trans('cruds.offer.fields.add_date') }}
                    </th>
                    <th>
                        {{ trans('cruds.offer.fields.date_end') }}
                    </th>
                    <th>
                        {{ trans('cruds.offer.fields.phone_number') }}
                    </th>
                    <th>
                        {{ trans('cruds.offer.fields.location') }}
                    </th>
                    <th>
                        {{ trans('cruds.offer.fields.price') }}
                    </th>
                    <th>
                        {{ trans('cruds.offer.fields.trader') }}
                    </th>
                    <th>
                        {{ trans('cruds.offer.fields.city') }}
                    </th>
                    <th>
                        {{ trans('cruds.offer.fields.images') }}
                    </th>
                </tr>
                <tr>
                    <td>
                    </td>
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
                    <input class="search" type="text" placeholder="{{ trans('global.search') }}">

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
                        <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                    </td>
                    <td>
                        <select class="search">
                            <option value>{{ trans('global.all') }}</option>
                            @foreach($traders as $key => $item)
                                <option value="{{ $item->name }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
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
            @can('offer_delete')
            let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
            let deleteButton = {
                text: deleteButtonTrans,
                url: "{{ route('admin.offers.massDestroy') }}",
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
                ajax: "{{ route('admin.offers.index') }}",
                columns: [
                    {data: 'placeholder', name: 'placeholder', orderable: false, searchable: false},
                    {data: 'actions', name: '{{ trans('global.actions') }}', orderable: false, searchable: false},
                    {data: 'id', name: 'id'},
                    {data: 'show_in_trader_page', name: 'show_in_trader_page',searchable: true},
                    {data: 'show_in_main_page', name: 'show_in_main_page',searchable: true},
                    {data: 'show_in_main_offers_page', name: 'show_in_main_offers_page',searchable: true},
                    {data: 'name', name: 'name'},
                    {data: 'description', name: 'description'},
                    {data: 'category_name', name: 'category.name'},
                    {data: 'sub_category_name', name: 'sub_category.name'},
                    {data: 'type_of_category', name: 'category.type'},
                    {data: 'add_date', name: 'add_date'},
                    {data: 'date_end', name: 'date_end'},
                    {data: 'phone_number', name: 'phone_number'},
                    {data: 'location', name: 'location'},
                    {data: 'price', name: 'price'},
                    {data: 'trader_name', name: 'trader.name'},
                    {data: 'city_name', name: 'city.name'},
                    {data: 'images', name: 'images', sortable: false, searchable: false},
                ],
                orderCellsTop: true,
                order: [[1, 'desc']],
                pageLength: 50,
            };
            let table = $('.datatable-Offer').DataTable(dtOverrideGlobals);
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
