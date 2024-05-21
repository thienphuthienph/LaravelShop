@extends('front.layouts.app')


@section('content')
    <section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="{{ route('front.home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a class="white-text" href="{{ route('front.shop') }}">Shop</a></li>
                    <li class="breadcrumb-item">{{ $product->title }}</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="section-7 pt-3 mb-3">
        <div class="container">
            <div class="row ">
                <div class="col-md-5">
                    <div id="product-carousel" class="carousel slide" data-bs-ride="carousel">

                        <div class="carousel-inner bg-light">

                            <div class="carousel-item active">
                                <img class="w-75 h-75" src="{{ asset('uploads/product/small/' . $thumbnail->image) }}"
                                    alt="Image">
                            </div>
                            @foreach ($productImages as $item)
                                <div class="carousel-item">
                                    <img class="w-75 h-75" src="{{ asset('uploads/product/small/' . $item->image) }}"
                                        alt="Image">
                                </div>
                            @endforeach
                        </div>

                        <a class="carousel-control-prev" href="#product-carousel" data-bs-slide="prev">
                            <i class="fa fa-2x fa-angle-left text-dark"></i>
                        </a>
                        <a class="carousel-control-next" href="#product-carousel" data-bs-slide="next">
                            <i class="fa fa-2x fa-angle-right text-dark"></i>
                        </a>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="bg-light right">
                        <h1>{{ $product->title }}</h1>
                        <div class="d-flex mb-3">
                            @if (!empty($reviews))
                                <small class="pt-1">{{ number_format($totalRating, 1) }}</small>
                            @endif
                            <div class="text-primary mr-2">
                                <small class="fas fa-star"></small>
                            </div>
                            <small class="pt-1">({{$totalReview}}) Reviews</small>
                        </div>
                        @if ($product->compare_price > 0)
                            <h2 class="price text-secondary"><del>{{ number_format($product->compare_price) }} VND</del>
                            </h2>
                            <h2 class="price ">{{ number_format($product->price) }} VND</h2>
                        @else
                            <h2 class="price text-secondary"><del>{{ number_format($product->compare_price) }} VND</del>
                            </h2>
                        @endif


                        <p>{!! $product->description !!}</p>
                        <a href="javascipt:void(0);" onclick="addToCart({{ $product->id }});" class="btn btn-dark"><i
                                class="fas fa-shopping-cart"></i> &nbsp;ADD TO CART</a>
                    </div>
                </div>

                <div class="col-md-12 mt-5">
                    <div class="bg-light">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="description-tab" data-bs-toggle="tab"
                                    data-bs-target="#description" type="button" role="tab" aria-controls="description"
                                    aria-selected="true">Description</button>
                            </li>
                           
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews"
                                    type="button" role="tab" aria-controls="reviews"
                                    aria-selected="false">Reviews</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="description" role="tabpanel"
                                aria-labelledby="description-tab">
                                <p>
                                    {!! $product->description !!}
                                </p>
                            </div>
                           
                            <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
                                <form action="" id="reviewForm" name="reviewForm" method="POST">
                                    <div class="col-md-8">
                                        <div class="row">
                                            <h3 class="h4 pb-3">Write a Review</h3>
                                            @if ($userId == 0)
                                                <div class="form-group col-md-6 mb-3">
                                                    <label for="name">Name</label>

                                                    <input type="text" class="form-control" name="name"
                                                        id="name" placeholder="Name" value="">
                                                </div>
                                                <div class="form-group col-md-6 mb-3">
                                                    <label for="email">Email</label>
                                                    <input type="text" class="form-control" name="email"
                                                        id="email" placeholder="Email" value="">
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label for="rating">Rating</label>
                                                    <br>
                                                    <div class="rating" style="width: 10rem">
                                                        <input id="rating-5" type="radio" name="rating"
                                                            value="5" /><label for="rating-5"><i
                                                                class="fas fa-3x fa-star"></i></label>
                                                        <input id="rating-4" type="radio" name="rating"
                                                            value="4" /><label for="rating-4"><i
                                                                class="fas fa-3x fa-star"></i></label>
                                                        <input id="rating-3" type="radio" name="rating"
                                                            value="3" /><label for="rating-3"><i
                                                                class="fas fa-3x fa-star"></i></label>
                                                        <input id="rating-2" type="radio" name="rating"
                                                            value="2" /><label for="rating-2"><i
                                                                class="fas fa-3x fa-star"></i></label>
                                                        <input id="rating-1" type="radio" name="rating"
                                                            value="1" /><label for="rating-1"><i
                                                                class="fas fa-3x fa-star"></i></label>
                                                    </div>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label for="">How was your overall experience?</label>
                                                    <textarea name="comment" id="comment" class="form-control" cols="30" rows="10"
                                                        placeholder="How was your overall experience?"></textarea>
                                                </div>
                                            @else
                                                <div class="form-group col-md-6 mb-3">
                                                    <label for="name">Name</label>
                                                    <input type="text" class="form-control" name="name"
                                                        id="name" placeholder="Name" value="{{ $user->name }}">


                                                </div>
                                                <div class="form-group col-md-6 mb-3">
                                                    <label for="email">Email</label>
                                                    <input type="text" class="form-control" name="email"
                                                        id="email" placeholder="Email" value="{{ $user->email }}">
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label for="rating">Rating</label>
                                                    <br>
                                                    <div class="rating" style="width: 10rem">
                                                        <input id="rating-5" type="radio" name="rating"
                                                            value="5" /><label for="rating-5"><i
                                                                class="fas fa-3x fa-star"></i></label>
                                                        <input id="rating-4" type="radio" name="rating"
                                                            value="4" /><label for="rating-4"><i
                                                                class="fas fa-3x fa-star"></i></label>
                                                        <input id="rating-3" type="radio" name="rating"
                                                            value="3" /><label for="rating-3"><i
                                                                class="fas fa-3x fa-star"></i></label>
                                                        <input id="rating-2" type="radio" name="rating"
                                                            value="2" /><label for="rating-2"><i
                                                                class="fas fa-3x fa-star"></i></label>
                                                        <input id="rating-1" type="radio" name="rating"
                                                            value="1" /><label for="rating-1"><i
                                                                class="fas fa-3x fa-star"></i></label>
                                                    </div>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label for="">How was your overall experience?</label>
                                                    <textarea name="comment" id="comment" class="form-control" cols="30" rows="10"
                                                        placeholder="How was your overall experience?"></textarea>
                                                </div>
                                            @endif
                                            <div>
                                                <button class="btn btn-dark">Submit</button>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="col-md-12 mt-5">
                                        <div class="overall-rating mb-3">
                                            <div class="d-flex">
                                                @if (!empty($reviews))
                                                    <h1 class="h3 pe-3">{{ number_format($totalRating, 1) }}</h1>
                                                @endif
                                                <div class="star-rating mt-2" title="70%">
                                                    <div class="back-stars">
                                                        <i class="fa fa-star" aria-hidden="true"></i>


                                                        <div class="front-stars" style="width: 70%">
                                                            <i class="fa fa-star" aria-hidden="true"></i>
                                                            <i class="fa fa-star" aria-hidden="true"></i>
                                                            <i class="fa fa-star" aria-hidden="true"></i>
                                                            <i class="fa fa-star" aria-hidden="true"></i>
                                                            <i class="fa fa-star" aria-hidden="true"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                @php
                                                @endphp
                                                <div class="pt-2 ps-2">({{ $totalReview }} Reviews)</div>
                                            </div>

                                        </div>
                                        
                                        <div class="rating-group mb-4">
                                            @foreach ($reviews as $item)
                                            @if ($item->status==1)
                                            
                                                <span> <strong>{{ $item->name }} </strong></span>

                                                <div class="text-primary mr-2">
                                                    {{$item->rating}}<small class="fas fa-star"></small>
                                                </div>
                                                <div class="my-3">
                                                    <p>
                                                        {{ $item->comment }}
                                                    </p>
                                                </div>
                                                    
                                            @endif
                                            @endforeach

                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('customJs')
    <script>
        $("#reviewForm").submit(function(event) {
            event.preventDefault();
            var element = $(this);
            $("button[type=submit]").prop('disable', true);
            $.ajax({
                url: '{{ route('front.saveReview', $product->id) }}',
                type: 'post',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response) {
                    if (response.status == true) {
                        window.location.href = "{{ route('front.shop') }}";
                    } else {
                        window.location.href = "{{ route('account.login') }}";
                    }
                }
            })
        })
    </script>
@endsection
