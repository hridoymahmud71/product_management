@extends('layouts.app')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Products</h1>
    </div>


    <div class="card">
        <form action="" method="get" class="card-header" id="search-form">
            <div class="form-row justify-content-between">
                <div class="col-md-2">
                    <input type="text" name="title" placeholder="Product Title" value="{{ request()->get('title') }}"
                        class="form-control">
                </div>
                <div class="col-md-2">
                    <select name="variant" id="" class="form-control">
                        <option value="" >Nothing Selected</option>
                        @if ($filter_variants->isNotEmpty())
                            @foreach ($filter_variants as $variant)
                                <optgroup label="{{ $variant->title }}">
                                    @foreach ($variant->product_variants as $product_variant)
                                        <option value="{{ $product_variant->variant_id }}" {{request()->get('variant') == $product_variant->variant_id ? "selected" : "" }}>{{ $product_variant->variant }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="col-md-3">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Price Range</span>
                        </div>
                        <input type="text" name="price_from" value="{{ request()->get('price_from') }}"
                            aria-label="First name" placeholder="From" class="form-control">
                        <input type="text" name="price_to" value="{{ request()->get('price_to') }}" aria-label="Last name"
                            placeholder="To" class="form-control">
                    </div>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date" value="{{ request()->get('date') }}" placeholder="Date"
                        class="form-control">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary float-right"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>

        <div class="card-body">
            <div class="table-response">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th width="40%">Description</th>
                            <th>Variant</th>
                            <th width="150px">Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach ($products as $key => $product)
                            <tr>
                                <td> {{ $products->firstItem() + $key }}</td>
                                <td>{{ $product->title }} <br> Created at : {{ $product->created_at->diffForHumans() }}
                                </td>
                                <td>{{ $product->description }}</td>
                                <td>
                                    @foreach ($product->VARIANTDATA as $variant_data)
                                        <dl class="row mb-0" style="height: 80px; overflow: hidden"
                                            id="variantof-{{ $product->id }}">

                                            <dt class="col-sm-4 pb-0">
                                                <span class="d-block">
                                                    {{ $variant_data['string'] }}
                                                </span>
                                            </dt>
                                            <dt class="col-sm-4 pb-0">Price :
                                                {{ number_format($variant_data['price'], 2) }}</dt>
                                            <dt class="col-sm-4">InStock :
                                                {{ number_format($variant_data['stock'], 0) }}</dt>
                                        </dl>
                                    @endforeach
                                    <button onclick="$('#variantof-{{ $product->id }}').toggleClass('h-auto')"
                                        class="btn btn-sm btn-link">Show
                                        more</button>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('product.edit', 1) }}" class="btn btn-success">Edit</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>

                </table>
            </div>

        </div>

        <div class="card-footer">
            <div class="row justify-content-between">
                <div class="col-md-6">
                    <p>Showing {{ $products->firstItem() }} to {{ $products->lastItem() }}
                        of {{ $products->total() }}</p>
                </div>
                <div class="col-md-2">
                    {{ $products->appends(request()->input())->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
