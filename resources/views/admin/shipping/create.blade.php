@extends('admin.layouts.app')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Shipping Management</h1>
                </div>
                <!--Back to index-->
                <div class="col-sm-6 text-right">
                    <a href="{{ route('categories.index') }}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="container-fluid">
            @include('admin.message')
            <form action="" method="POST" id="shippingForm" name="shippingForm">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <select id="country" name="country" class="form-control">
                                        @if ($countries->isNotEmpty())
                                            @foreach ($countries as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach
                                        @endif
                                        <option value="rest_of_the_world">Rest of the world</option>
                                    </select>
                                    <p></p>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <input type="text" id="amount" name="amount" class="form-control">
                                <p></p>
                            </div>


                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary">Save</button>
                               
                            </div>


                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-striped">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>

                            @if ($shippingCharge->isNotEmpty())
                                @foreach ($shippingCharge as $item)
                                    <tr>
                                        <th>{{ $item->id }}</th>
                                        <th>{{($item->country_id == 'rest_of_the_world') ? "Rest of the world" : $item->name}}</th>
                                        <th>{{number_format($item->amount,0) }} VND</th>
                                        <th><a href="{{route('shipping.edit',$item->id)}}" class="btn btn-primary">Edit</a>
                                            {{-- <a href="#" onclick="deleteShippingCharge({{$item->id}})" class="btn btn-danger">Delete</a> --}}
                                        </th>     
                                    </tr>
                                @endforeach
                            @endif


                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.card -->
    </section>
    <!-- /.content -->
@endsection

@section('customJs')
    <script>
        $("#shippingForm").submit(function(event) {
            event.preventDefault();
            var element = $(this);
            $("button[type=submit]").prop('disable', true);
            $.ajax({
                url: '{{ route('shipping.store') }}',
                type: 'post',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response) {

                    if (response["status"] == true) {

                        window.location.href = "{{ route('shipping.create') }}";

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

                        if (errors['amount']) {
                            $("#amount").addClass('is-invalid')
                                .siblings('p')
                                .addClass('invalid-feedback').html(errors['amount']);
                        } else {
                            $("#amount").removeClass('is-invalid')
                                .siblings('p')
                                .removeClass('invalid-feedback').html("");
                        }
                    }
                },
                error: function(jqXHR, exception) {
                    console.log("Something went wrong");
                }
            })
        });

        function deleteShippingCharge(id) 
        {
            if (confirm("Are you sure you want to delete")) {
                $.ajax({
                    url: '{{ route('shipping.delete','id')}}',
                    type: 'delete',
                    data: {},
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response["status"]) {

                            window.location.href = "{{ route('shipping.create') }}";
                        }
                    }
                })      
            }   
        }
    </script>
@endsection
