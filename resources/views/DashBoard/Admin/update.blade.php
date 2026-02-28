@extends('DashBoard.layout.main')

@section('content')
    <div class="card-body">
        <h4 class="card-title">Default form</h4>
        <p class="card-description"> Basic form layout </p>
        <form class="forms-sample" method="POST" action="{{ route('admin.update', $admin->id) }}">
            @csrf
            @method('PUT')
            <div class="form-group">
                @error('name')
                    <p style="color: red">{{ $message }}</p>
                @enderror
                <label for="exampleInputUsername1">Username</label>
                <input type="text" value="{{ $admin->name }}" name="name" class="form-control"
                    id="exampleInputUsername1" placeholder="Username">
            </div>
            <div class="form-group">
                @error('email')
                    <p style="color: red">{{ $message }}</p>
                @enderror
                <label for="exampleInputEmail1">Email </label>
                <input type="email" value="{{ $admin->email }}" name="email" class="form-control"
                    id="exampleInputEmail1" placeholder="Email">
            </div>

            <div class="form-group">
                @error('phone')
                    <p style="color: red">{{ $message }}</p>
                @enderror
                <label for="exampleInputUsername1">Phone</label>
                <input type="number" value="{{ $admin->phone }}" name="phone" class="form-control"
                    id="exampleInputUsername1" placeholder="Phone">
            </div>

            <div class="form-group">
                @error('old_password')
                    <p style="color:red">{{ $message }}</p>
                @enderror
                <label>Old Password</label>
                <input type="password" name="old_password" class="form-control">
            </div>

            <div class="form-group">
                @error('password')
                    <p style="color:red">{{ $message }}</p>
                @enderror
                <label>New Password</label>
                <input type="password" name="password" class="form-control">
            </div>

            <div class="form-group">
                <label>Confirm New Password</label>
                <input type="password" name="password_confirmation" class="form-control">
            </div>





            <button type="submit" class="btn btn-gradient-primary me-2">Update</button>
            <button class="btn btn-light">Cancel</button>
        </form>
    </div>
@endsection
