@extends('DashBoard.layout.main')

@section('content')

<div class="card w-50 mx-auto">
    <div class="card-body">
        <h4 class="card-title mb-3">Category Details</h4>

        <table class="table table-bordered">
            <tr>
                <th style="width: 30%">ID</th>
                <td>{{ $category->id }}</td>
            </tr>

            <tr>
                <th>Name</th>
                <td>{{ $category->name }}</td>
            </tr>

            <tr>
                <th>Description</th>
                <td>{{ $category->description ?? 'No Description' }}</td>
            </tr>

            <tr>
                <th>Created By</th>
                <td>{{ $category->admin->name }}</td>
            </tr>

            <tr>
                <th>Created At</th>
                <td>{{ $category->created_at->format('Y-m-d h:i A') }}</td>
            </tr>
        </table>

        <div class="d-flex gap-2 mt-3">
            <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-info">Update</a>

            <a href="{{ route('categories.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
</div>

@endsection