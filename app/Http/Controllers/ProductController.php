<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use App\Models\Variant;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index()
    {
        $products_query = Product::query();

        $filter_variants =  Variant::with(['product_variants' => function ($query) {
            $query->groupBy('variant')
                ->orderBy('id', 'asc');
        }])->get();


        if (request()->has('title') && request()->get('title') != "") {
            $products_query->where('title', 'like', "%" . request()->get('title') . "%");
        }

        if (request()->has('price_from') && request()->get('price_from') != "") {
            $products_query->whereIn('id', function ($query) {
                $query->select('product_id')
                    ->from(with(new ProductVariantPrice())->getTable())
                    ->where('price', '>=', request()->get('price_from'));
            });
        }

        if (request()->has('price_to') && request()->get('price_to') != "") {
            $products_query->whereIn('id', function ($query) {
                $query->select('product_id')
                    ->from(with(new ProductVariantPrice())->getTable())
                    ->where('price', '<=', request()->get('price_to'));
            });
        }

        if (request()->has('date') && request()->get('date') != "") {
            $products_query->where('created_at', 'like', "%" . request()->get('date') . "%");
        }

        if (request()->has('variant') && request()->get('variant') != "") {
            $products_query->whereIn('id', function ($query) {
                $query->select('product_id')
                    ->from(with(new ProductVariant())->getTable())
                    ->where('variant_id', '=', request()->get('variant'));
            });
        }

        $products = $products_query->paginate(5);

        return view('products.index', compact('products','filter_variants'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        $variants = Variant::all();
        return view('products.create', compact('variants'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
    }


    /**
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show($product)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        $variants = Variant::all();
        return view('products.edit', compact('variants'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
    }
}
