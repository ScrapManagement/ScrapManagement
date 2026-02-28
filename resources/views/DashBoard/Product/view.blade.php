@extends('DashBoard.layout.main')

@section('content')

<a href="{{ route('product.create') }}" class="btn btn-success mb-3">
    Add Product
</a>

<div class="card">
    <div class="card-body">
        <h4 class="card-title">Product Table</h4>

        <table class="table table-bordered align-middle text-center">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Unit</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Images</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
            @forelse ($products as $key => $product)
                <tr>
                    <td>{{ $key + 1 }}</td>

                    <td>{{ $product->name }}</td>

                    <td>{{ $product->price }}</td>

                    <td>{{ $product->quantity }}</td>

                    <td>{{ $product->unit }}</td>

                    <td>
                        {{ $product->category?->name ?? '—' }}
                    </td>

                    <td>
                        <span class="badge bg-{{ $product->status == 'approved' ? 'success' : ($product->status == 'rejected' ? 'danger' : 'warning') }}">
                             {{ ucfirst($product->status) }}
                        </span>
                    </td>

                    <td>
                        <div class="d-flex gap-1 justify-content-center">
                            @foreach ($product->images as $image)
                                <img
                                    src="{{ asset('storage/'.$image->image_path) }}"
                                    width="50"
                                    height="50"
                                    class="rounded border"
                                >
                            @endforeach
                        </div>
                    </td>

                    <td>
                        <div class="d-flex gap-2 justify-content-center">
                            <a href="{{ route('product.show', $product->id) }}"
                               class="btn btn-sm btn-primary">
                                View
                            </a>
                            <a href="{{ route('product.edit', $product->id) }}"
                               class="btn btn-sm btn-info">
                                Edit
                            </a>

                            <form action="{{ route('product.destroy', $product->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')

                                <button class="btn btn-sm btn-danger">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">No products found</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
