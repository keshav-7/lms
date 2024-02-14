<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Employee;
use Redirect;
use Session;

class LoginController extends Controller
{
  public function HandleLoginContoller(Request $request)
  {
    
    $this->validate(
      $request,
      [
        'username' => 'required',
        'password' => 'required',
      ]
    );

    // (new Employee)->getEmployeeDetails();
    $user = (new Employee)->validateUser(
      [
          'username' => $request->username,
          'password' => $request->password
      ]
    );
    if(!empty($user))
    {
      if($user->user_type == '1'){
        Session::put('Session_Type', 'Admin');
        Session::put('Session_Value', $user->id);
        return Redirect::to("/view-home-page");
      }else if($user->user_type == '0'){
        Session::put('Session_Type', 'Staff');
        Session::put('Session_Value', $user->id);
        return Redirect::to("/view-home-page-of-staff-account");
      }
      return view('404_error');
    } else
    {
      return Redirect::to("/")->withErrors(['The username or password is incorrect.']);
    }
    // return view('404_error');
  }

  public function HandleLogoutContoller()
  {
    Session::flush();
    return Redirect('/');
  }


}
