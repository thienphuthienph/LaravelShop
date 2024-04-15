@extends('admin.layouts.app')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Create Category</h1>
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
            <form action="" method="POST" id="categoryForm" name="categoryForm">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Name</label>
                                    <input type="text" name="name" id="name" class="form-control"
                                        placeholder="Name">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!--Khong the thay doi gia tri trong slug-->
                                <div class="mb-3">
                                    <label for="email">Slug</label>
                                    <input type="text" name="slug" id="slug" readonly
                                        class="form-control"placeholder="Slug">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="col-md-3">
                                    <input type="hidden" id="image_id" name="image_id" value="">
                                    <label for="image">image</label>
                                    <div id="image" class="dropzone dz-clickable">
                                        <div class="dz-message needsclick">
                                            <br>Drop files here or click to upload.<br><br>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="1">Active</option>
                                        <option value="0">Block</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pb-5 pt-3">
                    <button type="submit" class="btn btn-primary">Create</button>
                    <a href="{{ route('categories.index') }}" class="btn btn-outline-dark ml-3">Cancel</a>
                </div>
            </form>
        </div>
        <!-- /.card -->
    </section>
    <!-- /.content -->
@endsection

@section('customJs')
    <script>
        $("#categoryForm").submit(function(event) {
            event.preventDefault();
            var element = $(this);
            $("button[type=submit]").prop('disable', true);
            $.ajax({
                url: '{{ route('categories.store') }}',
                type: 'post',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response) {

                    if (response["status"] == true) {

                        window.location.href = "{{ route('categories.index') }}";

                        $("#name").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback').html("");

                        $("#slug").removeClass('is-invalid')
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

                        if (errors['slug']) {
                            $("#slug").addClass('is-invalid')
                                .siblings('p')
                                .addClass('invalid-feedback').html(errors['slug']);
                        } else {
                            $("#slug").removeClass('is-invalid')
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

        //Lay du lieu cua name bo vao slug
        $(document).ready(function() {
            $('#name').on('input', function() {
                var name = $(this).val();
                var slug = slugify(name); 

                $('#slug').val(slug); 
            });
        });
        //Dinh dang slug name-name
        function slugify(text) {
            return text.toString().toLowerCase()
                 .replace(/\s+/g, '-') // Thay the khoang trong thanh dau gach ngang
        }


        Dropzone.autoDiscover = false;
        const dropzone = $("#image").dropzone({
            init: function() {
                this.on('addedfile', function(file) {
                    if (this.files.length > 1) {
                        this.removeFile(this.files[0]);
                    }
                });
            },
            url: "{{ route('temp-images.create') }}",
            maxFiles: 1,
            paramName: 'image',
            addRemoveLinks: true,
            acceptedFiles: "image/jpeg,image/png,image/gif,image/PNG",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(file, response) {
                $("#image_id").val(response.img_id);
                console.log(response)
            }
        });
    </script>
@endsection
