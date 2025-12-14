@extends('DashBoard.layout.main')

@section('content')
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Add New Category</h4>
            <p class="card-description"> Enter category details </p>
            
            <form class="forms-sample" method="POST" action="{{ route('categories.store') }}">
                @csrf

                <div class="form-group">
                    <label for="exampleInputName1">Name</label>
                    <input type="text" name="name" class="form-control" id="exampleInputName1" placeholder="Category Name" value="{{ old('name') }}">
                    @error('name')
                        <p style="color:red">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="exampleInputDescription">Description</label>
                    <textarea name="description" class="form-control" id="exampleInputDescription" rows="4" placeholder="Description">{{ old('description') }}</textarea>
                    @error('description')
                        <p style="color:red">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="btn btn-gradient-primary me-2">Submit</button>
                <a href="{{ route('categories.index') }}" class="btn btn-light">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection