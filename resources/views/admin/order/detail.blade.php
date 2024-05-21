@extends('admin.layouts.app')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Order: #{{ $order->id }}</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('orders.list') }}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header pt-3">
                            <div class="row invoice-info">
                                <div class="col-sm-4 invoice-col">
                                    <h1 class="h5 mb-3">Shipping Address</h1>
                                    <address>
                                        <strong>{{ $order->first_name . ' ' . $order->last_name }}</strong><br>
                                        {{ $order->address }}<br>
                                        {{ $order->city . ' ' . $order->state }}<br>
                                        Phone: {{ $order->mobile }}<br>
                                        Email: {{ $order->email }}
                                    </address>
                                </div>



                                <div class="col-sm-4 invoice-col">
                                    <b>Ship date: {{\Carbon\Carbon::Parse($order->shipped_date)->format("d-m-Y h:i:s")}}</b><br>
                                    <br>
                                    <b>Order ID:</b> {{ $order->id }}<br>
                                    <b>Total:</b> {{ number_format($order->grand_total) }} VND<br>
                                    <b>Status:</b>
                                    @if ($order->status == 'pending')
                                        <span class="text-danger">Pending</span>
                                    @elseif($order->status == 'shipping')
                                        <span class="text-info">Shipping</span>
                                    @elseif($order->status == 'delivered')
                                        <span class="text-success">Delivered</span>
                                    @else
                                        <span class="text-danger">cancelled</span>
                                    @endif
                                    <br>
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-responsive p-3">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th width="100">Price</th>
                                        <th width="100">Qty</th>
                                        <th width="100">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orderItem as $item)
                                        <tr>

                                            <td>{{ $item->name }}</td>
                                            <td>{{ number_format($item->price) }} VND</td>
                                            <td>{{ $item->qty }}</td>
                                            <td>{{ number_format($item->total) }} VND</td>

                                        </tr>
                                    @endforeach
                                    <tr>
                                        <th colspan="3" class="text-right">Subtotal:</th>
                                        <td class="text-right text-success">{{ number_format($order->subtotal) }} VND</td>
                                    </tr>

                                    <tr>
                                        <th colspan="3" class="text-right">Shipping:</th>
                                        <td class="text-right text-success">{{ number_format($order->shipping) }} VND</td>
                                    </tr>
                                    <tr>
                                        <th colspan="3" class="text-right">Discount:</th>
                                        <td class="text-right text-danger">{{ number_format($order->discount) }} VND</td>
                                    </tr>
                                    <tr>
                                        <th colspan="3" class="text-right">Grand Total:</th>
                                        <td class="text-right text-success">{{ number_format($order->grand_total) }} VND
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <form action="" method="POST" name="changeOrderStatusForm" id="changeOrderStatusForm">
                            <div class="card-body">
                                <h2 class="h4 mb-3">Order Status</h2>
                                <div class="mb-3">
                                    <select name="status" id="status" class="form-control">
                                        <option {{ $order->status == 'pending' ? 'selected' : '' }} value="pending">
                                            Pending
                                        </option>
                                        <option {{ $order->status == 'shipping' ? 'selected' : '' }} value="shipping">
                                            Shipping
                                        </option>
                                        <option {{ $order->status == 'delivered' ? 'selected' : '' }}value="delivered">
                                            Delivered
                                        </option>
                                        <option {{ $order->status == 'cancelled' ? 'selected' : '' }} value="cancelled">
                                            Cancelled
                                        </option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="">Ship Date</label>
                                    <input type="text" name="shipped_date" id="shipped_date" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                        </form>
                    </div>

                </div>
                {{-- <div class="card">
                    <div class="card-body">
                        <form action="" method="POST" id="sendInvoice" name="sendInvoice">
                        <h2 class="h4 mb-3">Send Invoice Email</h2>
                        <div class="mb-3">
                            <select name="status" id="status" class="form-control">
                                <option value="">Customer</option>
                                <option value="">Admin</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <button class="btn btn-primary">Send</button>
                        </div>
                    </form>
                    </div>
                </div> --}}
            </div>
        </div>
        </div>
        <!-- /.card -->
    </section>
    <!-- /.content -->
@endsection

@section('customJs')
    <script>
        $(document).ready(function() {
            $('#shipped_date').datetimepicker({
                format: 'Y-m-d H:i:s',
            });
        });

        $('#changeOrderStatusForm').submit(function event() {
            $.ajax({
                url: '{{ route('orders.changeOrderStatus', $order->id) }}',
                type: 'post',
                data: $(this).serializeArray(),
                dataType: 'json',
                success: function(response) {
                    if (response["status"] == true) {

                        window.location.href = "{{ route('orders.detail', $order->id) }}";
                    }
                }
            })
        });
        $('#sendInvoice').submit(function event() {
            $.ajax({
                url: '{{ route('orders.sendInvoice', $order->id) }}',
                type: 'post',
                data: $(this).serializeArray(),
                dataType: 'json',
                success: function(response) {
                    if (response["status"] == true) {

                        window.location.href = "{{ route('orders.detail', $order->id) }}";
                    }
                }
            })
        });
    </script>
@endsection
