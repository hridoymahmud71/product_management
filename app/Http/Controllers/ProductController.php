<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use App\Models\TempImage;
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

        return view('products.index', compact('products', 'filter_variants'));
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

        if($request->title == ""  || $request->sku == ""){
            return response()->json([
                'result'        =>  false,
                'message'       => "Need product name and sku",                
            ]);
        }

        if(Product::where('sku',$request->sku)->first() != null){
            return response()->json([
                'result'        =>  false,
                'message'       => "sku already exists",                
            ]);
        }

        if(empty($request->product_variant) || empty($request->product_variant_prices)){
            return response()->json([
                'result'        =>  false,
                'message'       => "need variation and prices",                
            ]);
        }
    
        // save product
        $product = new Product;
        $product->title = $request->title;
        $product->sku = $request->sku;
        $product->description = $request->description;
        $product->save();


        //then save product image , if any
        if (!empty($request->product_image)) {
            foreach ($request->product_image as  $an_image) {
                $product_image = new ProductImage;
                $product_image->file_path = $an_image;
                $product_image->thumbnail = 0;
                $product_image->product_id = $product->id;
                $product_image->save();
            }
        }

      

        //then save product variants
        if (!empty($request->product_variant)) {
            foreach ($request->product_variant as  $a_variant) {
                foreach ($a_variant['tags'] as  $a_tag) {
                    $product_variant = new ProductVariant;
                    $product_variant->variant  = $a_tag;
                    $product_variant->variant_id  = $a_variant['option'];
                    $product_variant->product_id  =  $product->id;
                    $product_variant->save();
                }
            }
        }

        //then save product variant prices
        if (!empty($request->product_variant_prices)) {
            foreach ($request->product_variant_prices as  $a_variant_price) {
                $product_variant_one = null;
                $product_variant_two = null;
                $product_variant_three = null;
                $title =  substr($a_variant_price['title'], 0, -1); // removing last '/'
                $title_array = explode('/', $title);
                foreach ($title_array as $key => $item) {
                    if($key == 0){
                        $product_variant_one = ProductVariant::where('product_id',$product->id)->where('variant',$item)->first()->id;
                    }
                    if($key == 1){
                        $product_variant_two = ProductVariant::where('product_id',$product->id)->where('variant',$item)->first()->id;
                    }
                    if($key == 2){
                        $product_variant_three = ProductVariant::where('product_id',$product->id)->where('variant',$item)->first()->id;
                    }
                }

                $product_variant_price = new ProductVariantPrice;
                $product_variant_price->product_variant_one = $product_variant_one;
                $product_variant_price->product_variant_two = $product_variant_two;
                $product_variant_price->product_variant_three = $product_variant_three;
                $product_variant_price->price = $a_variant_price['price'];
                $product_variant_price->stock = $a_variant_price['stock'];
                $product_variant_price->product_id = $product->id;
                $product_variant_price->save();

            }
        }

        if(Product::where('sku',$request->sku)->first() != null){
            return response()->json([
                'result'        =>  true,
                'message'       => "Product Uploaded",                
            ]);
        }
    }

    public function product_image_upload(Request $request)
    {
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('uploads');



            return $path;
        }
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
