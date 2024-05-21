@extends('front.layouts.app')

@section('content')
    <section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="#">Home</a></li>
                    <li class="breadcrumb-item"><a class="white-text" href="#">Shop</a></li>
                    <li class="breadcrumb-item">Checkout</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="section-9 pt-4">

        <div class="container">
            <form id="orderForm" name="orderForm" action="" method="POST">
                <div class="row">
                    <div class="col-md-8">
                        <div class="sub-title">
                            <h2>Shipping Address</h2>
                        </div>
                        <div class="card shadow-lg border-0">
                            <div class="card-body checkout-form">
                                <div class="row">

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <input type="text" name="first_name" id="first_name" class="form-control"
                                                value="{{ $customerAddress->first_name }}" placeholder="First Name">
                                            <p></p>
                                        </div>

                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <input type="text" name="last_name" id="last_name" class="form-control"
                                                value="{{ $customerAddress->last_name }}" placeholder="Last Name">
                                            <p></p>
                                        </div>

                                    </div>

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <input type="text" name="email" id="email" class="form-control"
                                                value="{{ $customerAddress->email }}" placeholder="Email">
                                            <p></p>
                                        </div>

                                    </div>

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <select id="country" name="country" class="form-control">
                                                @foreach ($countries as $country)
                                                    <option
                                                        {{ $customerAddress->country_id == $country->id ? 'selected' : '' }}
                                                        value="{{ $country->id }}">{{ $country->name }}</option>
                                                @endforeach
                                            </select>
                                            <p></p>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <textarea name="address" id="address" cols="30" rows="3" placeholder="Address" class="form-control"></textarea>
                                            <p></p>
                                        </div>
                                    </div>


                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <input type="text" name="apartment" id="apartment" class="form-control"
                                                placeholder="Apartment, suite, unit, etc. (optional)">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <input type="text" name="city" id="city" class="form-control"
                                                placeholder="City">
                                            <p></p>
                                        </div>

                                    </div>

                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <input type="text" name="state" id="state" class="form-control"
                                                placeholder="State">
                                            <p></p>
                                        </div>

                                    </div>

                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <input type="text" name="zip" id="zip" class="form-control"
                                                placeholder="Zip">
                                            <p></p>
                                        </div>

                                    </div>

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <input type="text" name="mobile" id="mobile" class="form-control"
                                                placeholder="Mobile No.">
                                            <p></p>
                                        </div>

                                    </div>


                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <textarea name="order_notes" id="order_notes" cols="30" rows="2" placeholder="Order Notes (optional)"
                                                class="form-control"></textarea>
                                            <p></p>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="sub-title">
                            <h2>Order Summery</h3>
                        </div>
                        <div class="card cart-summery">
                            @if (!empty($cartContent))
                                @foreach ($cartContent as $item)
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between pb-2">
                                            <div class="h6">{{ $item->name }} X {{ $item->qty }}</div>
                                            <div class="h6">{{ number_format($item->price * $item->qty, 0) }} VND</div>
                                        </div>

                                    </div>
                                @endforeach
                                <div class="d-flex justify-content-between summery-end">
                                    <div class="h6"><strong>Subtotal</strong></div>
                                    <div class="h6"><strong>{{ number_format(Cart::subtotal(2, '.', '')) }}
                                            VND</strong>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between summery-end">
                                    <div class="h6"><strong>Discount</strong></div>
                                    <div class="h6"><strong id="discount_value" name="discount_value"> - {{ number_format($discount) }}
                                            VND</strong>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between mt-2">
                                    <div class="h6"><strong>Shipping</strong></div>
                                    <div class="h6" id="shippingAmount">
                                        <strong id= >{{ number_format($totalShippingCharge) }} VND</strong>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between mt-2 summery-end">
                                    <div class="h5"><strong>Total</strong></div>
                                    <div class="h5" id="grandTotal"><strong>{{ number_format($grandTotal) }}
                                            VND</strong></div>
                                </div>
                            @endif
                        </div>
                        <div class="input-group apply-coupan mt-4">
                            <input type="text" placeholder="Coupon Code" class="form-control" name="discount_code"
                                id="discount_code">
                            <button class="btn btn-dark" type="button" id="apply-discount">Apply Coupon</button>
                        </div>

                        <div id="discount-response-wrapper">
                            @if (Session::has('code'))
                                <div class="mt-4" id="discount-response">
                                    <strong id="coupon_code" name="coupon_code">{{ Session::get('code')->code }}</strong>
                                    <a class="btn btn-danger" id="remove-discount"><i class="fa fa-times"></i></a>
                                </div>
                            @endif
                        </div>

                        <div class="card payment-form ">

                            <h3 class="card-title h5 mb-3">Payment Method</h3>
                            <div class="">
                                <label for="card_number" class="mb-2">COD</label>
                                <input checked type="radio" name="payment_method" value="cod"
                                    id="payment_method_1">
                            </div>

                            <div class="">
                                <label for="payment_method_1" class="mb-2">Stripe</label>
                                <input type="radio" name="payment_method" value="stripe" id="payment_method_2">
                            </div>
                            <!--------------------------------------------------->
                            {{-- <h3 class="card-title h5 mb-3">Payment Details</h3> --}}

                            <div class="card-body p-0 d-none" id="card-payment-form">
                                <div class="mb-3">
                                    <label for="payment_method_2" class="mb-2">Card Number</label>
                                    <input type="text" name="card_number" id="card_number"
                                        placeholder="Valid Card Number" class="form-control">
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="expiry_date" class="mb-2">Expiry Date</label>
                                        <input type="text" name="expiry_date" id="expiry_date" placeholder="MM/YYYY"
                                            class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="expiry_date" class="mb-2">CVV Code</label>
                                        <input type="text" name="expiry_date" id="expiry_date" placeholder="123"
                                            class="form-control">
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="pt-4">
                            {{-- <a href="#" class="btn-dark btn btn-block w-100">Pay Now</a> --}}
                            <button type="submit" class="btn-dark btn btn-block w-100">Pay now</button>
                        </div>
                        <!-- CREDIT CARD FORM ENDS HERE -->

                    </div>
                </div>
            </form>

        </div>
    </section>
@endsection

@section('customJs')
    <script>
        $("#payment_method_1").click(function() {
            if ($(this).is(":checked") == true) {
                $("#card-payment-form").addClass('d-none');
            }
        });

        $("#payment_method_2").click(function() {
            if ($(this).is(":checked") == true) {
                $("#card-payment-form").removeClass('d-none');
            }
        });

        $("#orderForm").submit(function(event) {
            event.preventDefault();
            var element = $(this);
            $("button[type=submit]").prop('disable', true);
            $.ajax({
                url: '{{ route('front.processCheckout') }}',
                type: 'post',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response) {
                    var errors = response.errors;
                    if (response.status == false) {
                        if (errors.first_name) {
                            $("#first_name").addClass('is-invalid')
                                .siblings('p')
                                .addClass('invalid-feedback').html(errors.first_name);
                        } else {
                            $("#first_name").removeClass('is-invalid')
                                .siblings('p')
                                .removeClass('invalid-feedback').html("");
                        }

                        if (errors.last_name) {
                            $("#last_name").addClass('is-invalid')
                                .siblings('p')
                                .addClass('invalid-feedback').html(errors.last_name);
                        } else {
                            $("#last_name").removeClass('is-invalid')
                                .siblings('p')
                                .removeClass('invalid-feedback').html("");
                        }

                        if (errors.country) {
                            $("#country").addClass('is-invalid')
                                .siblings('p')
                                .addClass('invalid-feedback').html(errors.country);
                        } else {
                            $("#country").removeClass('is-invalid')
                                .siblings('p')
                                .removeClass('invalid-feedback').html("");
                        }

                        if (errors.address) {
                            $("#address").addClass('is-invalid')
                                .siblings('p')
                                .addClass('invalid-feedback').html(errors.address);
                        } else {
                            $("#address").removeClass('is-invalid')
                                .siblings('p')
                                .removeClass('invalid-feedback').html("");
                        }

                        if (errors.city) {
                            $("#city").addClass('is-invalid')
                                .siblings('p')
                                .addClass('invalid-feedback').html(errors.city);
                        } else {
                            $("#city").removeClass('is-invalid')
                                .siblings('p')
                                .removeClass('invalid-feedback').html("");
                        }

                        if (errors.state) {
                            $("#state").addClass('is-invalid')
                                .siblings('p')
                                .addClass('invalid-feedback').html(errors.state);
                        } else {
                            $("#state").removeClass('is-invalid')
                                .siblings('p')
                                .removeClass('invalid-feedback').html("");
                        }

                        if (errors.zip) {
                            $("#zip").addClass('is-invalid')
                                .siblings('p')
                                .addClass('invalid-feedback').html(errors.zip);
                        } else {
                            $("#zip").removeClass('is-invalid')
                                .siblings('p')
                                .removeClass('invalid-feedback').html("");
                        }

                        if (errors.mobile) {
                            $("#mobile").addClass('is-invalid')
                                .siblings('p')
                                .addClass('invalid-feedback').html(errors.mobile);
                        } else {
                            $("#mobile").removeClass('is-invalid')
                                .siblings('p')
                                .removeClass('invalid-feedback').html("");
                        }

                    } else {
                        window.location.href = ("{{ url('/thankyou/') }}/") + response.orderId;
                    }

                }
            })
        });

        $("#country").change(function(event) {
            $.ajax({
                url: '{{ route('cart.getOrderSummary') }}',
                type: 'post',
                data: {
                    country_id: $(this).val()
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status == true) {
                        $("#shippingAmount").html(response.shippingCharge + " VND");
                        $("#grandTotal").html(response.grandTotal + " VND");
                    }
                }
            })
        });

        
        $("#apply-discount").click(function() {
            $.ajax({
                url: '{{ route('cart.applyDiscount') }}',
                type: 'post',
                data: {
                    code: $("#discount_code").val(),
                    country_id: $("#country").val()
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status == true) {
                        $("#shippingAmount").html(response.shippingCharge + " VND");
                        $("#grandTotal").html(response.grandTotal + " VND");
                        $("#discount_value").html(response.discount + " VND");
                        $("#discount-response-wrapper").html(response.discountString);
                    }
                    else
                    {
                        $("#discount-response-wrapper").html(response.message);
                    }
                }
            })
        });

        $("#remove-discount").click(function() {
            $.ajax({
                url: '{{ route('cart.removeCoupon') }}',
                type: 'post',
                data: {
                    country_id: $("#country").val()
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status == true) {
                        $("#shippingAmount").html(response.shippingCharge + " VND");
                        $("#grandTotal").html(response.grandTotal + " VND");
                        $("#discount_value").html(response.discount + " VND");
                        $("#discount-response").html("");
                    }
                }
            })
        });
    </script>
@endsection
