@extends('admin-dashboard-layout.dashboard-template')

@section('dashboard-admin-content')

@if($errors->any())
  @foreach ($errors->all() as $error)
      <div id="errorBox" style="text-align:center;margin-top:20px;" class="alert alert-danger col-md-12 alert-dismissible fade show" role="alert">
          <strong>{!!$error!!}</strong>
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
          </button>
      </div>

      <script>

        window.onload=function(){

          $("#errorBox").delay(3000).fadeOut("slow");

        }

      </script>

  @endforeach
@endif


@if(session()->has('message'))

    <div id="successBox" style="text-align:center;margin-top:20px;" class="alert alert-success col-md-12 alert-dismissible fade show" role="alert">
        <strong> {{ session()->get('message') }}</strong>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
    </div>

    <script>

        setTimeout(
        function()
        {
            $("#successBox").delay(3000).fadeOut("slow");

        }, 1000);

    </script>

@endif



<div class="card">
      <div class="card-body">

        <h3 class="panel-title" style="text-align:center;">Register Staffs</h3>
        <br>

        <form action="/insert-staff-data" method="POST">
          {{ csrf_field() }}
          <div class="form-row">

            <div class="col-md-4 mb-3">
              <label for="user_type">User Type</label>
              <select class="form-control" name="user_type" id="user_type">
                <option value="0">Staff</option>
                <option value="1">Admin</option>
              </select>
              <!-- <input type="text" class="form-control" id="staff_id" name="staff_id" placeholder="Enter Staff ID" required> -->
            </div>

            <div class="col-md-4 mb-3">
              <label for="name">Name</label>
              <input type="text" class="form-control" id="name" name="name" placeholder="Enter Name" required>
            </div>

            <!-- <div class="col-md-4 mb-3">
              <label for="last_name">Last Name</label>
              <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Enter Last Name" required>
            </div> -->

            <div class="col-md-4 mb-3">
              <label for="joining_date">Joining Date</label>
              <input type="date" class="form-control" id="joining_date" name="joining_date" required>
            </div>

            <div class="col-md-4 mb-3">
              <label for="email">Email</label>
              <input type="email" class="form-control" id="email" name="email" placeholder="Enter Email Address" required>
            </div>

            <div class="col-md-4 mb-3">
              <label for="designation">Designation</label>
              <input type="text" class="form-control" id="designation" name="designation" placeholder="Enter designation/Role" required>
            </div>

          </div>
          <input class="btn btn-lg btn-primary" value="Register" type="submit">
        </form>

      </div>
</div>

<br>

<div class="card">
      <div class="card-body">

          <table class="table table-bordered table-hover ">
              <thead>
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">Emp ID</th>
                  <th scope="col">Name</th>
                  <th scope="col">Date of Joining</th>
                  <th scope="col">Email</th>
                  <th scope="col">Designation</th>
                  <th scope="col">Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($staff_data as $key => $data)

                    <tr>
                        <th scope="row">{{ $key + 1 }}</th>
                        <td>{{$data->id}}</td>
                        <td>{{$data->name}}</td>
                        <td>{{$data->joining_date}}</td>
                        <td>{{$data->email}}</td>
                        <td>{{$data->designation}}</td>
                        <td><a class="btn btn-danger confirmation" href="/delete-staff-data/{{$data->id}}">Delete</a></td>
                    </tr>

                @endforeach

              </tbody>
          </table>

      </div>
</div>





@endsection

<script>

    window.onload=function(){
      $(".nav-item:eq(1)").addClass("active");

      $('.confirmation').on('click', function () {
          return confirm('Are you sure to delete?');
      });

    }

</script>
