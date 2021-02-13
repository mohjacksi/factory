<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyOrderRequest;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\ProductVariant;
use App\Models\User;
use Dotenv\Exception\ValidationException;
use Exception;
use Gate;
use PDF;

//use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class OrdersController extends Controller
{
    public function index()
    {
        //abort_if(Gate::denies('orders_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $orders = Order::all();


        return view('admin.orders.index', compact('orders'));
    }

    public function create()
    {
        //abort_if(Gate::denies('orders_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // $product_variants = ProductVariant::whereHas('product', function ($q) {
        //     $q->whereNull('deleted_at');
        // })->get()->load('product', 'variant');
        $product_variants = ProductVariant::all();

        $coupons = Coupon::all()->pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $users = User::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.orders.create', compact('product_variants', 'coupons', 'users'));
    }

    public function store(StoreOrderRequest $request)
    {
//        dd($request->product_variant);

        DB::beginTransaction();
        try {
            $order = Order::create($request->all());


            if ($request->coupon_id) {
                $coupon = Coupon::findOrFail($request->coupon_id);
                if (!$coupon || $coupon->max_usage_per_user <= 0) {
                    return response()->json([
                        'message' => 'الكوبون غير صالح!'
                    ]);
                } else {
                    $coupon->update([
                        'max_usage_per_user' => $coupon->max_usage_per_user - 1
                    ]);

                }
            }

            foreach ($request->order_products as $product_variant) {

                $productVariant = ProductVariant::findOrFail($product_variant['product_variant_id']);
                $variant = $productVariant->variant;
                if ($variant->count < $product_variant['quantity']) {
                    throw new ValidationException('عدد المنتج المطلوب أكبر من المُتاح لدينا ' . $productVariant->product->name );

                }
                $variant->update([
                    'count' => $variant->count - $product_variant['quantity']
                ]);

                OrderProduct::create([

                    'product_variant_id' => $product_variant,

                    'order_id' => $order->id,
                    // todo
//                    'quantity' => $order_product->qunatity,
                ]);
            }

            DB::commit();

            return redirect()->route('admin.orders.index');
        } catch (Exception $e) {
            DB::rollback();

            return ($e->getMessage());
        }
    }

    public function edit(Order $order)
    {
        //abort_if(Gate::denies('orders_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $product_variants = ProductVariant::all()->load('product', 'variant');

        $coupons = Coupon::all()->pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $users = User::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $order_product_ids = $order->OrderProducts->pluck('product_variant_id');

        return view('admin.orders.edit', compact('users', 'product_variants', 'order', 'coupons', 'order_product_ids'));
    }

    public function update(UpdateOrderRequest $request, Order $order)
    {
        $order->update($request->all());

        $order_product_ids = $order->OrderProducts->pluck('id');

        foreach ($order_product_ids as $id) {
            OrderProduct::where('id', $id)->forcedelete();
        }

        foreach ($request->product_variant as $id) {
            $order->OrderProducts()->create([
                'product_variant_id' => $id,
            ]);
        }

        return redirect()->route('admin.orders.index');
    }

    public function show(Order $order)
    {
        $order_products = $order->OrderProducts;

        return view('admin.orders.show', compact('order', 'order_products'));
    }

    public function destroy(Order $order)
    {
        //abort_if(Gate::denies('orders_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $order->forcedelete();

        return back();
    }

    public function massDestroy(MassDestroyOrderRequest $request)
    {
        Order::whereIn('id', request('ids'))->forcedelete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * download single order
     *
     * @return mixed
     */
    public function download_pdf($id)
    {
        $order = Order::findOrFail($id);

        $data = [
            'title' => 'إسم الطلب',
            'heading' => 'إسم الطلب',
            'order' => $order
        ];


        /******************/
        $pdf = PDF::loadView('admin.orders.pdf_view', $data);

        return $pdf->stream('medium.pdf');
    }
}
