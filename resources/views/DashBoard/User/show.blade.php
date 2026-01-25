@extends('DashBoard.layout.main')
@section('content')
    <div class="card w-50 mx-auto">
        <div class="card-body">
            <h4 class="card-title mb-3">user Details</h4>

            <table class="table table-bordered">

                <tr>
                    <th>Name</th>
                    <td>{{ $user->name }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $user->email }}</td>
                </tr>
                <tr>
                    <th>Phone</th>
                    <td>{{ $user->phone }}</td>
                </tr>
                <tr>
                    <th>Region</th>
                    <td>{{ $user->region }}</td>
                </tr>
                <tr>
                    <th>City</th>
                    <td>{{ $user->city }}</td>
                </tr>
                <tr>
                    <th>Category</th>
                    <td>{{ $user->category->name ?? 'N/A' }}</td>
                </tr>

            </table>

            <div class="d-flex gap-2 mt-3">
                <a href="{{ route('user.edit', $user->id) }}" class="btn btn-info">Update</a>

                <form action="{{ route('user.destroy', $user->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <input type="submit" value="Delete" class="btn btn-danger"
                        onclick="return confirm('Are you sure you want to delete this user?')">
                </form>

                <a href="{{ route('user.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </div>
    </div>
@endsection
