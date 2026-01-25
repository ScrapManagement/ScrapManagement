@extends('DashBoard.layout.main')
@section('content')
    <a href= "{{ route('admin.create') }}" class="btn btn-success  mb-3">Add Admin</a>
    <div class="card">
        <div class="card-body">
            @if (session('success'))
                <div id="success-alert" class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif
            <h4 class="card-title">Admin table</h4>
            </p>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th> # </th>
                        <th> Name </th>
                        <th> Email </th>
                        <th> Phone </th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($Admin as $key => $admin)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $admin->name }}</td>
                            <td>{{ $admin->email }}</td>
                            <td>{{ $admin->phone }}</td>
                            <td class="d-flex gap-2">
                                @if (auth('admin')->id() === $admin->id)
                                    <a href="{{ route('admin.show', $admin->id) }}" class="btn btn-primary  btn-sm">
                                        View
                                    </a>
                                    
                                    <a href="{{ route('admin.edit', $admin->id) }}" class="btn btn-info  btn-sm">Update</a>
                                @endif
                            </td>

                        </tr>

                    @empty
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
