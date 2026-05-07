@extends('layouts.app')

@section('title', 'Product Detail')

@section('extra_css')
    <link rel="stylesheet" href="{{ asset('assets/css/slick.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/slick-theme.css') }}">
@endsection

@section('content')

    <section class="bg-light">
        <div class="container pb-5">
            <div class="row">

                <!-- LEFT: IMAGE -->
                <div class="col-lg-5 mt-5">
                    <div class="card mb-3">
                        <img class="card-img img-fluid" src="{{ asset($product->image) }}" id="product-detail">
                    </div>

                    <div class="row">
                        <div class="col-1 align-self-center">
                            <a href="#multi-item-example" role="button" data-bs-slide="prev">
                                <i class="text-dark fas fa-chevron-left"></i>
                            </a>
                        </div>

                        <div id="multi-item-example" class="col-10 carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner product-links-wap">

                                <div class="carousel-item active">
                                    <div class="row">
                                        <div class="col-4">
                                            <img class="img-fluid" src="{{ asset($product->image) }}">
                                        </div>
                                        <div class="col-4">
                                            <img class="img-fluid" src="{{ asset($product->image) }}">
                                        </div>
                                        <div class="col-4">
                                            <img class="img-fluid" src="{{ asset($product->image) }}">
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="col-1 align-self-center">
                            <a href="#multi-item-example" role="button" data-bs-slide="next">
                                <i class="text-dark fas fa-chevron-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- RIGHT: INFO -->
                <div class="col-lg-7 mt-5">
                    <div class="card">
                        <div class="card-body">

                            <h1 class="h2">{{ $product->name }}</h1>

                            <p class="h3 py-2">
                                {{ number_format($product->price) }} VNĐ
                            </p>

                            <p class="py-2">
                                <i class="fa fa-star text-warning"></i>
                                <i class="fa fa-star text-warning"></i>
                                <i class="fa fa-star text-warning"></i>
                                <i class="fa fa-star text-secondary"></i>
                            </p>

                            <h6>Description:</h6>
                            <p>{{ $product->description }}</p>

                            <form action="{{ route('cart.add') }}" method="POST">
                                @csrf

                                <input type="hidden" name="id" value="{{ $product->id }}">
                                <input type="hidden" name="name" value="{{ $product->name }}">
                                <input type="hidden" name="price" value="{{ $product->price }}">
                                <input type="hidden" name="image" value="{{ $product->image }}">

                                <div class="row pb-3">
                                    <div class="col d-grid">
                                        <button type="button" class="btn btn-success btn-lg">Buy</button>
                                    </div>
                                    <div class="col d-grid">
                                        <button type="submit" class="btn btn-success btn-lg">Add To Cart</button>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

@endsection

@section('extra_js')
    <script src="{{ asset('assets/js/slick.min.js') }}"></script>
    <script>
        $('#carousel-related-product').slick({
            infinite: true,
            arrows: false,
            slidesToShow: 4,
            slidesToScroll: 3,
            dots: true
        });
    </script>
@endsection
