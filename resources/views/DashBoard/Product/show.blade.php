@extends('DashBoard.layout.main')

@section('content')
    <div class="card w-75 mx-auto">
        <div class="card-body">
            <h4 class="card-title mb-4">Product Details</h4>

            <table class="table table-bordered mb-4">
                <tr>
                    <th>Name</th>
                    <td>{{ $product->name }}</td>
                </tr>

                <tr>
                    <th>Description</th>
                    <td>{{ $product->description ?? 'N/A' }}</td>
                </tr>

                <tr>
                    <th>Price</th>
                    <td>{{ $product->price }}</td>
                </tr>

                <tr>
                    <th>Quantity</th>
                    <td>{{ $product->quantity }}</td>
                </tr>

                <tr>
                    <th>Unit</th>
                    <td>{{ $product->unit }}</td>
                </tr>

                <tr>
                    <th>Category</th>
                    <td>{{ $product->category->name ?? 'N/A' }}</td>
                </tr>

                <tr>
                    <th>Status</th>
                    <td>
                        <span class="badge bg-warning text-dark">
                            {{ ucfirst($product->status) }}
                        </span>
                    </td>
                </tr>

                <tr>
                    <th>Created By</th>
                    <td>{{ $product->seller->name ?? 'N/A' }}</td>
                </tr>
            </table>


            <h5 class="mb-3">Product Images</h5>

            @if ($product->images->count())
                <div class="row">
                    @foreach ($product->images as $image)
                        <div class="col-md-4 mb-3">
                            <div class="border p-2 text-center">
                                <img src="{{ asset('storage/' . $image->image_path) }}" class="img-fluid rounded"
                                    style="height: 250px; object-fit: cover; width: 100%;" alt="Product Image">
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted">No images available</p>
            @endif


            <div class="d-flex gap-2 mt-4">
                <a href="{{ route('product.edit', $product->id) }}" class="btn btn-info">
                    Update
                </a>

                <form action="{{ route('product.destroy', $product->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger"
                        onclick="return confirm('Are you sure you want to delete this product?')">
                        Delete
                    </button>
                </form>

                <a href="{{ route('product.index') }}" class="btn btn-secondary">
                    Back
                </a>
            </div>

        </div>
    </div>
@endsection
