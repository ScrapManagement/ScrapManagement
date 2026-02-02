@extends('DashBoard.layout.main')

@section('content')

    <div class="card">
        <div class="card-body">

            <h4 class="card-title mb-4">Edit Role</h4>

            <form method="POST" action="{{ route('role.update', $role->id) }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label>Role Name</label>
                    <input type="text"
                           name="name"
                           value="{{ old('name', $role->name) }}"
                           class="form-control"
                           placeholder="Enter role name">

                    @error('name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        Update
                    </button>

                    <a href="{{ route('role.index') }}" class="btn btn-light">
                        Cancel
                    </a>
                </div>

            </form>

        </div>
    </div>

@endsection
