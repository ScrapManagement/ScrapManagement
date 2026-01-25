@extends('DashBoard.layout.main')

@section('content')
    <div class="card-body">
        <h4 class="card-title">Default form</h4>
        <p class="card-description"> Basic form layout </p>
        <form class="forms-sample" method="POST" action="{{ route('user.update', $user->id) }}">
            @csrf
            @method('PUT')
            <div class="form-group">
                @error('name')
                    <p style="color: red">{{ $message }}</p>
                @enderror
                <label for="exampleInputUsername1">Username</label>
                <input type="text" value="{{ $user->name }}" name="name" class="form-control"
                    id="exampleInputUsername1" placeholder="Username">
            </div>
            <div class="form-group">
                @error('email')
                    <p style="color: red">{{ $message }}</p>
                @enderror
                <label for="exampleInputEmail1">Email </label>
                <input type="email" value="{{ $user->email }}" name="email" class="form-control"
                    id="exampleInputEmail1" placeholder="Email">
            </div>

            <div class="form-group">
                @error('phone')
                    <p style="color: red">{{ $message }}</p>
                @enderror
                <label for="exampleInputUsername1">Phone</label>
                <input type="number" value="{{ $user->phone }}" name="phone" class="form-control"
                    id="exampleInputUsername1" placeholder="Phone">
            </div>
            <div class="form-group">
                @error('city')
                    <p style="color: red">{{ $message }}</p>
                @enderror
                <label for="exampleInputUsername1">City</label>
                <input type="text" value="{{ $user->city }}" name="city" class="form-control"
                    id="exampleInputUsername1" placeholder="City">
            </div>
            <div class="form-group">
                @error('region')
                    <p style="color: red">{{ $message }}</p>
                @enderror
                <label for="exampleInputUsername1">Region</label>
                <input type="text" value="{{ $user->region }}" name="region" class="form-control"
                    id="exampleInputUsername1" placeholder="Region">
            </div>

            <div class="form-group mt-3">
                @error('category_id')
                    <p style="color: red">{{ $message }}</p>
                @enderror
                <label for="exampleFormControlSelect2">Category</label>
                <select class="form-control" name="category_id" id="exampleFormControlSelect2">
                    @foreach ($categories as $category)
                        <option @selected($category->id == $user->category_id) value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
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
