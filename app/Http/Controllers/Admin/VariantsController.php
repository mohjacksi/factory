<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Size;
use App\Models\Variant;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class VariantsController extends Controller
{
    use MediaUploadingTrait;

    public function index(Product $product, Request $request)
    {
        //abort_if(Gate::denies('variant_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {


            $query = Variant::with('color', 'size', 'products')->whereHas('products', function ($q) use ($product) {

                $q->where('product_id', $product->id);
            })->select(sprintf('%s.*', (new Variant)->table));

            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) use ($product) {
                $viewGate = 'product_show';
                $editGate = 'product_edit';
                $deleteGate = 'product_delete';
                $crudRoutePart = 'products.variants';
                $parent = $product;

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row',
                    'parent'
                ));
            });

            $table->editColumn('id', function ($row) use ($product) {
                return $row->id ? $row->id : "";
            });
            $table->editColumn('is_available', function ($row) use ($product) {

                return $row->is_available;
            });


            $table->editColumn('price', function ($row) use ($product) {
                return $row->price ? $row->price : "";
            });
            $table->editColumn('count', function ($row) use ($product) {
                return $row->count ? $row->count : "";
            });

            $table->addColumn('color_name', function ($row) {
                return $row->color ? $row->color->name : "";
            });
            $table->addColumn('size_name', function ($row) {
                return $row->size ? $row->size->name : "";
            });

            $table->editColumn('image', function ($row) use ($product) {
                if ($photo = $row->image) {
                    return sprintf(
                        '<a href="%s" target="_blank"><img src="%s" width="50px" height="50px"></a>',
                        $photo->url,
                        $photo->thumbnail
                    );
                }
                return '';
            });

            $table->rawColumns(['actions', 'placeholder', 'image']);

            return $table->make(true);
        }

        $colors = Color::get();
        $sizes = Size::get();

        return view('admin.variants.index', compact('product', 'colors', 'sizes'));
    }

    public function create(Product $product)
    {
        //abort_if(Gate::denies('variant_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $colors = Color::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $sizes = Size::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.variants.create', compact('product', 'colors', 'sizes'));
    }

    public function store(Product $product, Request $request)
    {
        $request['is_available'] = $request->is_available ? 1 : 0;


        try {
            $variant = $product->variants()->create($request->all());

            if ($request->input('image', false)) {
                $variant->addMedia(storage_path('tmp/uploads/' . $request->input('image')))->toMediaCollection('image');
            }

            if ($media = $request->input('ck-media', false)) {
                Media::whereIn('id', $media)->update(['model_id' => $variant->id]);
            }


            return redirect()->route('admin.products.variants.index', $product);
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
    }

    public function edit(Product $product, Variant $variant)
    {
        //abort_if(Gate::denies('variant_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');


        $colors = Color::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $sizes = Size::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');


        return view('admin.variants.edit', compact('product', 'variant', 'sizes', 'colors'));
    }

    public function update(Product $product, Request $request, Variant $variant)
    {
        $request['is_available'] = $request->is_available ? 1 : 0;

        $variant->update($request->all());

        if ($request->input('image', false)) {
            if (!$variant->image || $request->input('image') !== $variant->image->file_name) {
                if ($variant->image) {
                    $variant->image->delete();
                }

                $variant->addMedia(storage_path('tmp/uploads/' . $request->input('image')))->toMediaCollection('image');
            }
        } elseif ($variant->image) {
            $variant->image->delete();
        }

        return redirect()->route('admin.products.variants.show', [$product, $variant]);
    }

    public function show(Product $product, Variant $variant)
    {
        //abort_if(Gate::denies('variant_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.variants.show', compact('variant', 'product'));
    }

    public function destroy(Product $product, Variant $variant)
    {
        //abort_if(Gate::denies('variant_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        // $product_variants = ProductVariant::where('variant_id', $variant->id)->get();
        // foreach ($product_variants as $product_variant) {
        //     $product_variant->delete();
        // }
        $product->variants()->detach($variant->id);

        $variant->forcedelete();

        return back();
    }

    public function massDestroy(Request $request)
    {
        $product_variants = ProductVariant::whereIn('variant_id', request('ids'))->get();
        foreach ($product_variants as $product_variant) {
            $product_variant->forcedelete();
        }
        Variant::whereIn('id', request('ids'))->forcedelete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        //abort_if(Gate::denies('variant_create') && Gate::denies('variant_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model = new Variant();
        $model->id = $request->input('crud_id', 0);
        $model->exists = true;
        $media = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
