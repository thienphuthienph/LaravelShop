@extends('front.layouts.app')

@section('content')
    <section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="#">My Account</a></li>
                    <li class="breadcrumb-item">Settings</li>
                </ol>
            </div>
        </div>
    </section>

    <section class=" section-11 ">
        <div class="container  mt-5">
            <div class="row">
                <div class="col-md-3">
                    @include('front.account.common.sidebar')
                </div>
                @include('admin.message')
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="h5 mb-0 pt-2 pb-2">Personal Information</h2>
                        </div>

                        <form action="" id="profileForm" name="profileForm">
                            <div class="card-body p-4">
                                <div class="row">
                                    <div class="mb-3">
                                        <label for="name">Name</label>
                                        <input type="text" name="name" id="name" placeholder="Enter Your Name"
                                            class="form-control" value="{{ $user->name }}">
                                        <p></p>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email">Email</label>
                                        <input type="text" name="email" id="email" placeholder="Enter Your Email"
                                            class="form-control" value="{{ $user->email }}">
                                        <p></p>
                                    </div>
                                    <div class="mb-3">
                                        <label for="phone">Phone</label>
                                        <input type="text" name="phone" id="phone" placeholder="Enter Your Phone"
                                            class="form-control" value="{{ $user->phone }}">
                                        <p></p>
                                    </div>
                                    <div class="d-flex">
                                        <button class="btn btn-dark">Update</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h2 class="h5 mb-0 pt-2 pb-2">Address</h2>
                        </div>

                        <form action="" id="addressForm" name="addressForm">
                            <div class="card-body p-4">
                                <div class="row">
                                    <div class="mb-3">
                                        <label for="name">First Name</label>
                                        <input type="text" name="first_name" id="first_name"
                                            placeholder="Enter Your First Name" class="form-control" 
                                            value="{{(!empty($address)) ? $address->first_name : "" }}">
                                        <p></p>
                                    </div>

                                    <div class="mb-3">
                                        <label for="name">Last Name</label>
                                        <input type="text" name="last_name" id="last_name"
                                            placeholder="Enter Your Last Name" class="form-control"
                                            value="{{(!empty($address)) ? $address->last_name : "" }}"
                                            >
                                        <p></p>
                                    </div>

                                    <div class="mb-3">
                                        <label for="email">Email</label>
                                        <input type="text" name="email" id="email" placeholder="Enter Your Email"
                                            class="form-control"  value="{{(!empty($address)) ? $address->email : "" }}">
                                        <p></p>
                                    </div>
                                    <div class="mb-3">
                                        <label for="phone">Mobile</label>
                                        <input type="text" name="mobile" id="mobile" placeholder="Enter Your Phone"
                                        value="{{(!empty($address)) ? $address->mobile : "" }}"
                                            class="form-control" >
                                        <p></p>
                                    </div>
                                    <div class="mb-3">
                                        <label for="phone">Country</label>
                                        <select name="country_id" id="country_id" class="form-control">
                                            @foreach ($countries as $item)
                                                <option {{(!empty($address) && $address->country_id == $item->id) ? 'selected' : "" }} value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                        <p></p>
                                    </div>

                                    <div class="mb-3">
                                        <label for="phone">Address</label>
                                        <input type="text" name="address" id="address" placeholder="Enter Your Address"
                                            class="form-control"   value="{{(!empty($address)) ? $address->address : "" }}">
                                        <p></p>
                                    </div>

                                    <div class="mb-3">
                                        <label for="phone">Apartment</label>
                                        <input type="text" name="apartment" id="apartment" placeholder="Apartment"
                                        value="{{(!empty($address)) ? $address->apartment : "" }}"
                                            class="form-control">
                                        <p></p>
                                    </div>

                                    <div class="mb-3">
                                        <label for="phone">City</label>
                                        <input type="text" name="city" id="city" placeholder="City"
                                        value="{{(!empty($address)) ? $address->city : "" }}"
                                            class="form-control" >
                                        <p></p>
                                    </div>

                                    <div class="mb-3">
                                        <label for="phone">State</label>
                                        <input type="text" name="state" id="state" placeholder="State"
                                        value="{{(!empty($address)) ? $address->state : "" }}"
                                            class="form-control" >
                                        <p></p>
                                    </div>

                                    
                                    <div class="mb-3">
                                        <label for="phone">Zip</label>
                                        <input type="text" name="zip" id="zip" placeholder="Zip"   value="{{(!empty($address)) ? $address->zip : "" }}"
                                            class="form-control">
                                        <p></p>
                                    </div>


                                    <div class="d-flex">
                                        <button class="btn btn-dark">Update</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('customJs')
    <script>
        $("#profileForm").submit(function(event) {
            event.preventDefault();
            var element = $(this);
            $("button[type=submit]").prop('disable', true);
            $.ajax({
                url: '{{ route('account.updateProfile') }}',
                type: 'post',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response) {
                    if (response["status"] == true) {

                        window.location.href = "{{ route('account.profile') }}";

                        $("#name").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback').html("");

                        $("#phone").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback').html("");

                        $("#email").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback').html("");

                    } else {
                        var errors = response['errors']

                        if (errors['name']) {
                            $("#name").addClass('is-invalid')
                                .siblings('p')
                                .addClass('invalid-feedback').html(errors['name']);
                        } else {
                            $("#name").removeClass('is-invalid')
                                .siblings('p')
                                .removeClass('invalid-feedback').html("");
                        }

                        if (errors['email']) {
                            $("#email").addClass('is-invalid')
                                .siblings('p')
                                .addClass('invalid-feedback').html(errors['email']);
                        } else {
                            $("#email").removeClass('is-invalid')
                                .siblings('p')
                                .removeClass('invalid-feedback').html("");
                        }

                        if (errors['phone']) {
                            $("#phone").addClass('is-invalid')
                                .siblings('p')
                                .addClass('invalid-feedback').html(errors['phone']);
                        } else {
                            $("#phone").removeClass('is-invalid')
                                .siblings('p')
                                .removeClass('invalid-feedback').html("");
                        }
                    }

                }
            })
        })

        $("#addressForm").submit(function(event) {
            event.preventDefault();
            var element = $(this);
            $("button[type=submit]").prop('disable', true);
            $.ajax({
                url: '{{ route('account.updateAddress') }}',
                type: 'post',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response) {
                    if (response["status"] == true) {

                        window.location.href = "{{ route('account.profile') }}";

                    } else {
                      
                    }

                }
            })
        })
    </script>
@endsection
