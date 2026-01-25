@extends("DashBoard.layout.main")

@section('content')

<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Edit Category</h4>
            <p class="card-description"> Edit category details </p>
            
            <form class="forms-sample" method="POST" action="{{ route('categories.update', $category->id) }}">
                @csrf
                @method("PUT") 

                <div class="form-group">
                    <label for="exampleInputName1">Name</label>
                    <input type="text" name="name" value="{{ old('name', $category->name) }}" class="form-control" id="exampleInputName1" placeholder="Category Name">
                    @error('name') 
                        <p style="color: red">{{ $message }}</p> 
                    @enderror
                </div>

                <div class="form-group">
                    <label for="parent_id">Parent Category</label>
                    <select name="parent_id" class="form-control" id="parent_id">
                        <option value="" {{ $category->parent_id == null ? 'selected' : '' }}>Primary Category (No Parent)</option>
                        
                        @foreach($allCategories as $cat)
                            <option value="{{ $cat->id }}" 
                                {{ old('parent_id', $category->parent_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('parent_id')
                        <p style="color:red">{{ $message }}</p>
                    @enderror
                </div>


                <button type="submit" class="btn btn-gradient-primary me-2">Update</button>
                <a href="{{ route('categories.index') }}" class="btn btn-light">Cancel</a>
            </form>
        </div>
    </div>
</div>

@endsection