@extends('DashBoard.layout.main')

@section('content')
<div class="card w-75 mx-auto">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0 text-white">Order #{{ $order->id }} Details</h4>
        <span class="badge bg-light text-dark">{{ strtoupper($order->status) }}</span>
    </div>
    
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <h6 class="text-muted">Buyer Info:</h6>
                <h5>{{ $order->buyer->name ?? 'Unknown' }}</h5>
                <p>{{ $order->buyer->email ?? '' }}</p>
            </div>
            <div class="col-md-6 text-end">
                <h6 class="text-muted">Order Date:</h6>
                <p class="fw-bold">{{ $order->created_at->format('d M Y, h:i A') }}</p>
                <h6 class="text-muted">Total Amount:</h6>
                <h3 class="text-success">{{ number_format($order->total_price, 2) }} EGP</h3>
            </div>
        </div>

        <hr>

        <h5 class="mb-3">Order Items</h5>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Unit Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product->name ?? 'Deleted Product' }}</td>
                    <td>{{ number_format($item->price, 2) }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->subtotal, 2) }} EGP</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4 text-center">
            <a href="{{ route('order.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>
</div>
@endsection