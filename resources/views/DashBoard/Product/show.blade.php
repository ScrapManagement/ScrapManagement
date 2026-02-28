@extends('DashBoard.layout.main')

@section('content')
    <div class="card w-75 mx-auto">
        <div class="card-body">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="card-title mb-0">Product Details</h4>
                <span class="badge bg-{{ $product->status == 'approved' ? 'success' : ($product->status == 'rejected' ? 'danger' : 'warning') }}">
                    {{ ucfirst($product->status) }}
                </span>
            </div>

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
                    <td>{{ $product->price }} EGP</td>
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
                    <th>Seller</th>
                    <td>{{ $product->seller->name ?? 'N/A' }}</td>
                </tr>
                
                @if($product->reviewed_by)
                <tr>
                    <th>Reviewed By</th>
                    <td class="text-primary fw-bold">{{ $product->reviewer->name ?? 'Admin #' . $product->reviewed_by }}</td>
                </tr>
                @endif
            </table>
            
            <div class="card mb-4 border border-secondary">
                <div class="card-body bg-light">
                    <h5 class="card-title text-dark">Admin Decision</h5>
                    <div class="d-flex gap-3">
                        
                        @if($product->status !== 'approved')
                            <form action="{{ route('product.changeStatus', $product->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" class="btn btn-success text-white" onclick="return confirm('Approve this product?')">
                                    Approve Product
                                </button>
                            </form>
                        @endif

                        @if($product->status !== 'rejected')
                            <form action="{{ route('product.changeStatus', $product->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit" class="btn btn-danger text-white" onclick="return confirm('Reject this product?')">
                                     Reject Product
                                </button>
                            </form>
                        @endif

                    </div>
                </div>
            </div>
            


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


            <div class="d-flex gap-2 mt-4 pt-3 border-top">
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
