@extends('DashBoard.layout.main')

@section('content')
    <div class="card w-75 mx-auto">
        <div class="card-body">
            <h4 class="card-title mb-4">Edit Product</h4>

            <form method="POST" action="{{ route('product.update', $product->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')


                <div class="form-group mb-3">
                    <label>Name</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" class="form-control">
                    @error('name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>


                <div class="form-group mb-3">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description', $product->description) }}</textarea>
                    @error('description')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>


                <div class="form-group mb-3">
                    <label>Price</label>
                    <input type="number" name="price" value="{{ old('price', $product->price) }}" class="form-control">
                    @error('price')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>


                <div class="form-group mb-3">
                    <label>Quantity</label>
                    <input type="number" name="quantity" value="{{ old('quantity', $product->quantity) }}"
                        class="form-control">
                    @error('quantity')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>


                <div class="form-group mb-3">
                    <label>Unit</label>
                    <input type="text" name="unit" value="{{ old('unit', $product->unit) }}" class="form-control">
                    @error('unit')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>


                <div class="form-group mb-3">
                    <label>Category</label>
                    <select name="category_id" class="form-control">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected($category->id == $product->category_id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>


                {{--  <h5 class="mt-4">Current Images</h5>
            <div class="row mb-3">
                @foreach ($product->images as $image)
                    <div class="col-md-3 text-center mb-2">
                        <img src="{{ asset('storage/' . $image->image_path) }}"
                             class="img-fluid rounded mb-2"
                             style="height:150px;object-fit:cover">


                    </div>
                @endforeach
            </div> --}}

             
                <div class="form-group mb-4">
                    <label>Add New Images</label>
                    <input type="file" name="images[]" multiple class="form-control">
                    @error('images')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('product.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
@endsection
