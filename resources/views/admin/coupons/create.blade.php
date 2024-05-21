@extends('admin.layouts.app')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Create Coupon</h1>
                </div>
                <!--Back to index-->
                <div class="col-sm-6 text-right">
                    <a href="{{ route('coupons.index') }}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="container-fluid">
            <form action="" method="POST" id="couponForm" name="couponForm">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Code</label>
                                    <input type="text" name="code" id="code" class="form-control"
                                        placeholder="Code">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Name</label>
                                    <input type="text" name="name" id="name"
                                        class="form-control"placeholder="Name">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Description</label>
                                    <textarea name="description" id="description" cols="30" rows="5" placeholder="Description"
                                        class="form-control"></textarea>
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Max Uses</label>
                                    <input type="text" name="max_uses" id="max_uses"
                                        class="form-control"placeholder="Max uses">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Max Uses user</label>
                                    <input type="text" name="max_uses_user" id="max_uses_user"
                                        class="form-control"placeholder="Max uses user">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Discount Amount</label>
                                    <input type="text" name="discount_amount" id="discount_amount"
                                        class="form-control"placeholder="Discount amount">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Min Amount</label>
                                    <input type="text" name="min_amount" id="min_amount"
                                        class="form-control"placeholder="Min amount">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="1">Active</option>
                                        <option value="0">Block</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Start At</label>
                                    <input type="text" name="start_at" id="start_at"
                                        class="form-control"placeholder="Start at">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Expire At</label>
                                    <input type="text" name="expire_at" id="expire_at"
                                        class="form-control"placeholder="Expire at">
                                    <p></p>
                                </div>
                            </div>
                            <!----------------------------------------------------->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type">Type</label>
                                    <select name="type" id="type" class="form-control">
                                        <option value="1">Percent</option>
                                        <option value="2">Fixed</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pb-5 pt-3">
                    <button type="submit" class="btn btn-primary">Create</button>
                    <a href="{{ route('coupons.index') }}" class="btn btn-outline-dark ml-3">Cancel</a>
                </div>
            </form>
        </div>
        <!-- /.card -->
    </section>
    <!-- /.content -->
@endsection

@section('customJs')
    <script>
        // Datetime picker for exprire at and create at
        $(document).ready(function() {
            $('#start_at').datetimepicker({
                format: 'Y-m-d H:i:s',
            });
        });
        $(document).ready(function() {
            $('#expire_at').datetimepicker({
                format: 'Y-m-d H:i:s',
            });
        });
        ///////////////////////////////////////////


        $("#couponForm").submit(function(event) {
            event.preventDefault();
            var element = $(this);
            $("button[type=submit]").prop('disable', true);
            $.ajax({
                url: '{{ route('coupons.store') }}',
                type: 'post',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response) {

                    if (response["status"] == true) {

                        window.location.href = "{{ route('coupons.index') }}";

                        $("#name").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback').html("");

                        $("#code").removeClass('is-invalid')
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

                        if (errors['code']) {
                            $("#code").addClass('is-invalid')
                                .siblings('p')
                                .addClass('invalid-feedback').html(errors['code']);
                        } else {
                            $("#code").removeClass('is-invalid')
                                .siblings('p')
                                .removeClass('invalid-feedback').html("");
                        }

                        if (errors['discount_amount']) {
                            $("#discount_amount").addClass('is-invalid')
                                .siblings('p')
                                .addClass('invalid-feedback').html(errors['discount_amount']);
                        } else {
                            $("#discount_amount").removeClass('is-invalid')
                                .siblings('p')
                                .removeClass('invalid-feedback').html("");
                        }

                        if (errors['status']) {
                            $("#status").addClass('is-invalid')
                                .siblings('p')
                                .addClass('invalid-feedback').html(errors['status']);
                        } else {
                            $("#status").removeClass('is-invalid')
                                .siblings('p')
                                .removeClass('invalid-feedback').html("");
                        }

                        if (errors['start_at']) {
                            $("#start_at").addClass('is-invalid')
                                .siblings('p')
                                .addClass('invalid-feedback').html(errors['start_at']);
                        } else {
                            $("#start_at").removeClass('is-invalid')
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

    </script>
@endsection
