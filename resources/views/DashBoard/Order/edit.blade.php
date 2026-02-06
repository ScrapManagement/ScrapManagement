@extends('DashBoard.layout.main')

@section('content')
<div class="col-md-6 grid-margin stretch-card mx-auto">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Update Order Status #{{ $order->id }}</h4>
            <p class="card-description">Change the current stage of the order</p>
            
            <form class="forms-sample" method="POST" action="{{ route('order.update', $order->id) }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label>Buyer Name</label>
                    <input type="text" class="form-control" value="{{ $order->buyer->name ?? 'Unknown' }}" readonly>
                </div>

                <div class="form-group">
                    <label>Total Price</label>
                    <input type="text" class="form-control" value="{{ $order->total_price }} EGP" readonly>
                </div>

                <div class="form-group">
                    <label for="status">Order Status</label>
                    <select name="status" id="status" class="form-control">
                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="paid" {{ $order->status === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                        <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-gradient-primary me-2">Update Status</button>
                <a href="{{ route('order.index') }}" class="btn btn-light">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection