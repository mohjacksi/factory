<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
          integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">


    <title>{{ $order->id }}</title>

    <style>
        @page {
            size: a3
        }

        @page {
            size: auto
        }

        * {
            font-family: DejaVu Sans, sans-serif;
        }

        /*font-family: examplefont, sans-serif;*/

        /********************/
        .invoice-title h2, .invoice-title h3 {
            display: inline-block;
        }

        .table > tbody > tr > .no-line {
            border-top: none;
        }

        .table > thead > tr > .no-line {
            border-bottom: none;
        }

        .table > tbody > tr > .thick-line {
            border-top: 2px solid;
        }

        .page-break {
            page-break-after: always;
        }

    </style>
</head>
<body>


<div class="container-fluid">

    <div class="row py-0">
        <div class="col-xs-12">
            <div class="invoice-title text-right">
                <p class="pull-right">
                <p>

                    رقم الطلب #
                </p>
                {{$order->id}}</p>
            </div>
            <hr>

        </div>
    </div>


    <div class="row text-center">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <p class="panel-title text-right"><strong>البيانات</strong></p>
                </div>
                <div class="panel-body">
                    <div class="table-responsive text-center">
                        <table class="table table-condensed text-center">
                            <thead>
                            <tr>
                                <td><strong>رقم التليفون</strong></td>
                                <td><strong>المستخدم</strong></td>
                                <td class="text-center"><strong>تاريخ الطلب</strong></td>
                                <td class="text-center"><strong>تفاصيل الطلب</strong></td>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    {{$order->phone_number}}<br>
                                    {{$order->address}}
                                </td>
                                <td>
                                    {{$order->user->name}}<br>
                                </td>

                                <td>
                                    {{$order->created_at}}
                                </td>

                                <td>
                                    {{$order->details}}
                                </td>

                            </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <hr>
        </div>
    </div>


    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <p class="panel-title text-right"><strong>ملخص الطلب</strong></p>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-condensed">
                            <thead>
                            <tr>
                                <td><strong>المنتج</strong></td>
                                <td><strong>الكود</strong></td>
                                <td><strong>التاجر</strong></td>
                                <td class="text-center"><strong>اللون</strong></td>
                                <td class="text-center"><strong>الحجم</strong></td>
                                <td class="text-center"><strong>السعر</strong></td>
                                <td class="text-center"><strong>الكمية</strong></td>
                                <td class="text-right"><strong>القيمة</strong></td>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($order->OrderProducts as  $order_product)
                                @if( $order_product->ProductVariant &&  $order_product->ProductVariant->product)
                                    <tr>
                                        <td>
                                            @if($order_product->ProductVariant->product)
                                                {{ $order_product->ProductVariant->product->name}}
                                            @endif
                                        </td>
                                        <td>
                                            @if($order_product->ProductVariant->product)
                                                {{ $order_product->ProductVariant->product->product_code}}
                                            @endif
                                        </td>
                                        <td>
                                            @if($order_product->ProductVariant->product && $order_product->ProductVariant->product->trader)
                                                {{ $order_product->ProductVariant->product->trader->name}}
                                            @endif
                                        </td>
                                        <td>
                                            @if($order_product->ProductVariant->variant->color)
                                                {{ $order_product->ProductVariant->variant->color->name}}
                                            @endif
                                        </td>
                                        @if( $order_product->ProductVariant->variant)

                                            <td>
                                                @if($order_product->ProductVariant->variant->size)
                                                    {{ $order_product->ProductVariant->variant->size->name}}
                                                @endif
                                            </td>

                                            <td>
                                                {{ $order_product->ProductVariant->variant->price}}
                                            </td>

                                            <td>
                                                {{ $order_product->quantity}}
                                            </td>

                                            @php($total_price_per_item = $order_product->ProductVariant->variant->price * $order_product->quantity)

                                            <td>
                                                {{ $total_price_per_item}}
                                            </td>
                                        @endif
                                    </tr>
                                @endif
                            @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <hr>
        </div>
        {{--        <div class="page-break"></div>--}}
        <div class="row">

            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-condensed">
                                <thead>
                                <tr>
                                    <td>السعر</td>
                                    <td>قيمة التخفيض</td>
                                    <td>المبلغ المطلوب</td>
                                </tr>
                                </thead>
                                <tbody class="text-right">
                                <tr>
                                    <td class="thick-line text-right">
                                        <del>{{$order->subtotal}} </del>
                                        جنيه
                                    </td>
                                    <td class="no-line text-right">
                                        {{$order->discount}}
                                        جنيه
                                    </td>
                                    <td class="no-line text-right">
                                        {{$order->total}}
                                        جنيه
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{--<script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>--}}
{{--<script src="https://netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>--}}
{{--<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"--}}
{{--        integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n"--}}
{{--        crossorigin="anonymous"></script>--}}
{{--<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"--}}
{{--        integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"--}}
{{--        crossorigin="anonymous"></script>--}}
{{--<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"--}}
{{--        integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6"--}}
{{--        crossorigin="anonymous"></script>--}}

</body>
</html>
