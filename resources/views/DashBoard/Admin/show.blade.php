@extends('DashBoard.layout.main')
@section('content')

<div class="card w-50 mx-auto">
    <div class="card-body">
        <h4 class="card-title mb-3">Admin Details</h4>

        <table class="table table-bordered">
            <tr>
                <th>ID</th>
                <td>{{ $admin->id }}</td>
            </tr>
            <tr>
                <th>Name</th>
                <td>{{ $admin->name }}</td>
            </tr>
            <tr>
                <th>Email</th>
                <td>{{ $admin->email }}</td>
            </tr>
            <tr>
                <th>Phone</th>
                <td>{{ $admin->phone }}</td>
            </tr>

        </table>

        <div class="d-flex gap-2 mt-3">
            <a href="{{ route('admin.edit', $admin->id) }}" class="btn btn-info">Update</a>
{{--
            <form action="{{ route('admin.destroy', $admin->id) }}" method="POST">
                @csrf
                @method("DELETE")
                <input type="submit"
                       value="Delete"
                       class="btn btn-danger"
                       onclick="return confirm('Are you sure you want to delete this admin?')">
            </form> --}}

            <a href="{{ route('admin.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
</div>




@endsection
