@extends('DashBoard.layout.main')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="card-title mb-0">Categories Table</h4>
    <a href="{{ route('categories.create') }}" class="btn btn-success">
        + Add Category
    </a>
</div>

<div class="card">
    <div class="card-body">
        
        @if (session('success'))
            <div id="success-alert" class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th> # </th>
                        <th> Name </th>
                        <th> Parent Category </th>
                        <th> Created By </th>
                        <th> Actions </th> 
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td>{{ $loop->iteration }}</td> 
                            
                            <td>{{ $category->name }}</td>
                            
                            <td>
                                @if($category->parent)
                                    <span class="badge badge-info">{{ $category->parent->name }}</span>
                                @else
                                    <span class="badge badge-secondary">Main Category</span>
                                @endif
                            </td>

                            <td>{{ $category->admin->name ?? 'Unknown' }}</td>

                            <td class="d-flex gap-2">
                                <a href="{{ route('categories.show', $category->id) }}" class="btn btn-primary btn-sm">
                                  View
                                </a>
                                <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-info btn-sm">
                                    Edit
                                </a>

                                <form action="{{ route('categories.destroy', $category->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted"> 
                                No Categories Found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection