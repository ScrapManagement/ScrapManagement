@extends('DashBoard.layout.main')

@section('content')
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Add Product</h4>

            <form method="POST" action="{{ route('product.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-control">
                    @error('name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group mt-3">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group mt-3">
                    <label>Price</label>
                    <input type="number" step="0.01" name="price" value="{{ old('price') }}" class="form-control">
                    @error('price')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>


                <div class="form-group mt-3">
                    <label>Quantity</label>
                    <input type="number" name="quantity" value="{{ old('quantity') }}" class="form-control">
                    @error('quantity')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>


                <div class="form-group mt-3">
                    <label>Unit</label>
                    <input type="text" name="unit" value="{{ old('unit') }}" class="form-control"
                        placeholder="kg / piece / ton">
                    @error('unit')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>


                <div class="form-group mt-3">
                    <label>Category</label>
                    <select name="category_id" class="form-control">
                        <option value="">-- Select Category --</option>

                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>

                    @error('category_id')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>


                <div class="form-group mt-3">
                    <label>Product Images</label>
                    <input type="file" name="images[]" multiple class="form-control">

                    @error('images')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>


                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        Save Product
                    </button>

                    <a href="{{ route('product.index') }}" class="btn btn-light">
                        Cancel
                    </a>
                </div>

            </form>
        </div>
    </div>
@endsection
