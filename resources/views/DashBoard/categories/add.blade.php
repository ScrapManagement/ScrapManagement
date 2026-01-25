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
                    <label for="parent_id">Parent Category</label>
                    <select name="parent_id" class="form-control" id="parent_id">
                        <option value="" selected>Primary Category (No Parent)</option>
                        
                        @foreach($categories as $mainCategory)
                            <option value="{{ $mainCategory->id }}" style="font-weight:bold;" {{ old('parent_id') == $mainCategory->id ? 'selected' : '' }}>
                                {{ $mainCategory->name }}
                            </option>

                            @foreach($mainCategory->children as $subCategory)
                                <option value="{{ $subCategory->id }}" {{ old('parent_id') == $subCategory->id ? 'selected' : '' }}>
                                    &nbsp;&nbsp;&nbsp; -- {{ $subCategory->name }}
                                </option>
                            @endforeach  
                        
                        @endforeach     
                    </select>
                    @error('parent_id')
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