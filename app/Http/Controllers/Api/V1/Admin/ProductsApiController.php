<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Filters\ProductsFilter;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\Admin\ProductResource;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Product;
use App\Models\Size;
use App\Models\Trader;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductsApiController extends Controller
{
    use MediaUploadingTrait;

    /**
     * @var ProductsFilter
     */
    private $filter;

    public function __construct(ProductsFilter $filter)
    {
        $this->filter = $filter;
    }

    public function index(Request $request)
    {
        //abort_if(Gate::denies('product_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $productQuery = Product::with('trader');

        $trader_id = $request['trader_id'];

        $main_product_type_id = $request['main_product_type_id'];

        $main_product_service_type_id = $request['main_product_service_type_id'];

        $sub_product_type_id = $request['sub_product_type_id'];

        $sub_product_service_type_id = $request['sub_product_service_type_id'];

        $details = $request['details'];

        $name = $request['name'];

        $city_id = $request['city_id'];

        $higher_price = $request['HigherPrice'];

        $SortByLowerPrice = $request['SortByLowerPrice'];

        $RecentlyAdded = $request['RecentlyAdded'];

        $Brand = $request['Brand'];

        $BrandNames = $request['BrandNames'];

        $PriceAfterDiscount = $request['PriceAfterDiscount'];

        $LowerPrice = $request['LowerPrice'];

        $Color = $request['Color'];

        $Size = $request['Size'];

        $ColorNames = $request['ColorNames'];

        $SizeNames = $request['SizeNames'];

        $TraderNames = $request['TraderNames'];

        if (isset($city_id)) {
            $productQuery = $productQuery->where('city_id', $city_id);
        }

        if (isset($higher_price)) {
            $higher_price = (float)$higher_price;
            $productQuery = $productQuery->Where('price_after_discount', '<=', $higher_price);
        }

        if (isset($RecentlyAdded)) {
            $productQuery = $productQuery->orderBy('updated_at', 'DESC');
        }


        if (isset($Brand)) {
            if (!is_array($Brand))
                $Brand = array($Brand);

            $productQuery = $productQuery->WhereHas('brand', function ($q) use ($Brand) {
                $q->whereIn('id', $Brand);
            });
        }


        if (isset($BrandNames)) {
            if (!is_array($BrandNames))
                $BrandNames = array($BrandNames);
            $arr = [];
            foreach ($BrandNames as $singleBrand) {
                $brandsID = Brand::where('name', 'like', '%' . $singleBrand . '%')->pluck('id')->toArray();
                foreach ($brandsID as $singleBrandId) {
                    $arr[] = $singleBrandId;
                }
            }
            $productQuery = $productQuery->WhereHas('brand', function ($q) use ($arr) {
                $q->whereIn('id', $arr);
            });
        }

        if (isset($TraderNames)) {
            if (!is_array($TraderNames))
                $TraderNames = array($TraderNames);
            $arr = [];
            foreach ($TraderNames as $singleTrader) {
                $brandsID = Trader::where('name', 'like', '%' . $singleTrader . '%')->pluck('id')->toArray();
                foreach ($brandsID as $singleTraderId) {
                    $arr[] = $singleTraderId;
                }
            }
            $productQuery = $productQuery->WhereHas('trader', function ($q) use ($arr) {
                $q->whereIn('id', $arr);
            });
        }


        if (isset($PriceAfterDiscount)) {
            $productQuery = $productQuery->whereNotNull('price_after_discount')->Where('price_after_discount', '!=', 0)->WhereColumn('price_after_discount', '!=', 'price');
        }

        if (isset($LowerPrice)) {
            $LowerPrice = (float)$LowerPrice;
            $productQuery = $productQuery->Where('price_after_discount', '>=', $LowerPrice);
        }


        if (isset($Color)) {

            if (!is_array($Color))
                $Color = array($Color);

            $productQuery = $productQuery->WhereHas('variants', function ($q) use ($Color) {
                $q->whereIn('color_id', $Color);
            });
        }

        if (isset($ColorNames)) {

            if (!is_array($ColorNames))
                $ColorNames = array($ColorNames);
            $arr = [];
            foreach ($ColorNames as $singleColor) {
                $colorsID = Color::where('name', 'like', '%' . $singleColor . '%')->pluck('id')->toArray();
                foreach ($colorsID as $singleColorId) {
                    $arr[] = $singleColorId;
                }
            }
            $productQuery = $productQuery->WhereHas('variants', function ($q) use ($arr) {
                $q->whereIn('color_id', $arr);
            });
        }


        if (isset($Size)) {
            if (!is_array($Size))
                $Size = array($Size);
            $productQuery = $productQuery->WhereHas('variants', function ($q) use ($Size) {
                $q->whereIn('size_id', $Size);
            });
        }


        if (isset($SizeNames)) {
            if (!is_array($SizeNames))
                $SizeNames = array($SizeNames);
            $arr = [];
            foreach ($SizeNames as $singleSize) {
                $sizesID = Size::where('name', 'like', '%' . $singleSize . '%')->pluck('id')->toArray();
                foreach ($sizesID as $singleSizeId) {
                    $arr[] = $singleSizeId;
                }
            }
            $productQuery = $productQuery->WhereHas('variants', function ($q) use ($arr) {
                $q->whereIn('size_id', $arr);
            });
        }


        if (isset($SortByLowerPrice)) {
            if ($SortByLowerPrice == 0) {
                $productQuery = $productQuery->orderBy('price_after_discount', 'DESC');
            } elseif ($SortByLowerPrice == 1) {
                $productQuery = $productQuery->orderBy('price_after_discount', 'ASC');
            }
        }


        if (isset($details)) {
            $productQuery = $productQuery->where('details', 'like', "%$details%");
        }


        if (isset($name)) {
            $productQuery = $productQuery->where('name', 'like', "%$name%");
        }

        if (isset($sub_product_service_type_id)) {
            $productQuery = $productQuery->where('sub_product_service_type_id', $sub_product_service_type_id);
        }
        if (isset($sub_product_type_id)) {
            $productQuery = $productQuery->where('sub_product_type_id', $sub_product_type_id);
        }
        if (isset($main_product_service_type_id)) {
            $productQuery = $productQuery->where('main_product_service_type_id', $main_product_service_type_id);
        }
        if (isset($main_product_type_id)) {
            $productQuery = $productQuery->where('main_product_type_id', $main_product_type_id);
        }
        if (isset($trader_id)) {
            if (!is_array($trader_id))
                $trader_id = array($trader_id);
            $productQuery = $productQuery->whereIn('trader_id', $trader_id);
        }
        return new ProductResource($productQuery->whereNull('deleted_at')->latest()->get());
    }

    public function store(StoreProductRequest $request)
    {
        $request['show_in_main_page'] = $request->has('show_in_main_page') ? 1 : 0;

        $request['show_in_trader_page'] = $request->has('show_in_trader_page') ? 1 : 0;

        $product = Product::create($request->all());

        if ($request->input('image', false)) {
            $product->addMedia(storage_path('tmp/uploads/' . $request->input('image')))->toMediaCollection('image');
        }

        return (new ProductResource($product))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show($product)
    {
        //abort_if(Gate::denies('product_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ProductResource(Product::findOrFail($product));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $request['show_in_main_page'] = $request->has('show_in_main_page') ? 1 : 0;

        $request['show_in_trader_page'] = $request->has('show_in_trader_page') ? 1 : 0;

        $product->update($request->all());

        if ($request->input('image', false)) {
            if (!$product->image || $request->input('image') !== $product->image->file_name) {
                if ($product->image) {
                    $product->image->delete();
                }

                $product->addMedia(storage_path('tmp/uploads/' . $request->input('image')))->toMediaCollection('image');
            }
        } elseif ($product->image) {
            $product->image->delete();
        }

        return (new ProductResource($product))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Product $product)
    {
        //abort_if(Gate::denies('product_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $product->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
