@extends('layouts.app')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Product</h1>
    </div>
    <div id="app">
        <edit-product 
            :variants='@json($variants)' 
            :product='@json($product)'
            :product-images='@json($product_images)'
            :product-variants='@json($product_variants)'
            :product-variant-prices='@json($product_variant_prices)'
        >Loading</edit-product>
    </div>
@endsection

