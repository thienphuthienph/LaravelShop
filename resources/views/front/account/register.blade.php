@extends('front.layouts.app')

@section('content')
    <section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="#">Home</a></li>
                    <li class="breadcrumb-item">Register</li>
                </ol>
            </div>
        </div>
    </section>

    <section class=" section-10">
        <div class="container">
            <div class="login-form">
                <form action="" method="post" name="registrationForm" id="registrationForm">
                    <h4 class="modal-title">Register Now</h4>
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Name" id="name" name="name">
                        <p></p>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Email" id="email" name="email">
                        <p></p>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Phone" id="phone" name="phone">
                        <p></p>
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" placeholder="Password" id="password" name="password">
                        <p></p>
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" placeholder="Confirm Password" id="cpassword"
                            name="password_confirmation">
                        <p></p>
                    </div>
                    <div class="form-group small">
                        <a href="#" class="forgot-link">Forgot Password?</a>
                    </div>
                    <button type="submit" class="btn btn-dark btn-block btn-lg" value="Register">Register</button>
                </form>
                <div class="text-center small">Already have an account? <a href="{{route('account.login')}}">Login Now</a></div>
            </div>
        </div>
    </section>
@endsection

@section('customJs')
    <script>
        $("#registrationForm").submit(function(event) {
            event.preventDefault();
            var element = $(this);
            $("button[type='submit']").prop('disable',true);
            $.ajax({
                url: '{{ route('account.processRegister') }}',
                type: 'post',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response) {
                    $("button[type='submit']").prop('disable',false);
                    var error = response.error;

                    if(response.status == false)
                    {
                        if (error.name) {
                        $("#name").siblings("p").addClass('invalid-feedback').html(error.name);
                        $("#name").addClass("is-invalid");
                    } else {
                        $("#name").siblings("p").removeClass('invalid-feedback').html(error.name);
                        $("#name").removeClass("is-invalid");
                    }

                    if (error.email) {
                        $("#email").siblings("p").addClass('invalid-feedback').html(error.email);
                        $("#email").addClass("is-invalid");
                    } else {
                        $("#email").siblings("p").removeClass('invalid-feedback').html(error.email);
                        $("#email").removeClass("is-invalid");
                    }

                    if (error.password) {
                        $("#password").siblings("p").addClass('invalid-feedback').html(error.password);
                        $("#password").addClass("is-invalid");
                    } else {
                        $("#password").siblings("p").removeClass('invalid-feedback').html(error
                            .password);
                        $("#password").removeClass("is-invalid");
                    }
                    }
                    else
                    {
                        window.location.href="{{route('account.login')}}";
                    }
                    
                },
                error: function(jQXHR, exception) {
                    console.log("Something went wrong");
                }
            });
        });
    </script>
@endsection
