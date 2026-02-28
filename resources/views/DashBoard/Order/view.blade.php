@extends('DashBoard.layout.main')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="card-title mb-0">Orders Management</h4>
    <a href="{{ route('order.create') }}" class="btn btn-success">Create New Order</a>
</div>

<div class="card">
    <div class="card-body">
        
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Buyer Name</th>
                        <th>Total Price</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td>#{{ $order->id }}</td>
                            
                            <td>{{ $order->buyer->name ?? 'Unknown User' }}</td>
                            
                            <td>{{ number_format($order->total_price, 2) }} EGP</td>

                            <td>
                                <span class="badge bg-{{ $order->status == 'completed' ? 'success' : ($order->status == 'pending' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>

                            <td>{{ $order->created_at->format('d M Y') }}</td>

                            <td class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('order.show', $order->id) }}" class="btn btn-primary btn-sm">View</a>
                                <a href="{{ route('order.edit', $order->id) }}" class="btn btn-info btn-sm">Status</a>
                                
                                <form action="{{ route('order.destroy', $order->id) }}" method="POST" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm" onclick="return confirm('Delete order?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6">No Orders Found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection