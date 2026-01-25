@extends('DashBoard.layout.main')
@section('content')
    <a href= "{{ route('user.create') }}" class="btn btn-success  mb-3">Add user</a>
    <div class="card">
        <div class="card-body">
            @if (session('success'))
                <div id="success-alert" class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif
            <h4 class="card-title">user table</h4>
            </p>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th> # </th>
                        <th> Name </th>
                        <th> Email </th>
                        <th> Phone </th>
                        <th> City</th>
                        <th> Region</th>
                        <th> Category </th>
                        <th> Action </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $key => $user)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone }}</td>
                            <td>{{ $user->city }}</td>
                            <td>{{ $user->region }}</td>
                            <td>{{ $user->category->name ?? 'N/A' }}</td>
                            <td class="d-flex gap-2">

                                <a href="{{ route('user.show', $user->id) }}" class="btn btn-primary btn-sm">
                                    View
                                </a>

                                <a href="{{ route('user.edit', $user->id) }}" class="btn btn-info  btn-sm">Update</a>

                                <form action="{{ route('user.destroy', $user->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <input type="submit" value="Delete" class="btn btn-danger  btn-sm"
                                        onclick="return confirm('Are you sure you want to delete this user?')">
                                </form>

                            </td>

                        </tr>

                    @empty
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
