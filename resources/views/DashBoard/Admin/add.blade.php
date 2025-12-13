
@extends("DashBoard.layout.main")

@section('content')

<div class="card-body">
    <h4 class="card-title">Default form</h4>
    <p class="card-description"> Basic form layout </p>
    <form class="forms-sample" method="POST" action="{{ route("admin.store") }}">
      @csrf
      <div class="form-group">
        @error('name') <p style="color: red" >{{ $message }}</p> @enderror
        <label for="exampleInputUsername1">Username</label>
        <input type="text"  name="name" class="form-control" id="exampleInputUsername1" placeholder="Enter Username">
      </div>
      <div class="form-group">
        @error('email') <p style="color: red" >{{ $message }}</p> @enderror
        <label for="exampleInputEmail1">Email </label>
        <input type="email" name="email" class="form-control" id="exampleInputEmail1" placeholder="Enter Email">
      </div>

      <div class="form-group">
        @error('phone') <p style="color: red" >{{ $message }}</p> @enderror
        <label for="exampleInputUsername1">Phone</label>
        <input type="number" name="phone" class="form-control" id="exampleInputUsername1" placeholder="Enter  Phone">
      </div>

    <div class="form-group">
        @error('password') <p style="color: red" >{{ $message }}</p> @enderror
        <label for="exampleInputUsername1">Password</label>
        <input type="password" name="password" class="form-control" id="exampleInputUsername1" placeholder="Enter  Password">
      </div>

       <div class="form-group">
            <label>Confirm  Password</label>
            <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm  Password">
        </div>





      <button type="submit" class="btn btn-gradient-primary me-2">Submit</button>
      <button class="btn btn-light">Cancel</button>
    </form>
  </div>

@endsection
