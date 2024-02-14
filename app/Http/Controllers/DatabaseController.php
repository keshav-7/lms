<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Redirect;
use Session;
use DB;
use App\Models\LeaveMaster;
use App\Models\LeaveRequest;

use Mail;
use App\Mail\MailNotify;

class DatabaseController extends Controller
{

  public function AddLeaveType(Request $request){
    
    $session_type = Session::get('Session_Type');
    $session_value = Session::get('Session_Value');

    if($session_type == "Admin"){

      $this->validate($request, [
        'ltype' => 'required',
        'lcount' => 'required',
        'lfrom' => 'required',
        'lto' => 'required',
      ]);
      $ltype       = $request->ltype;
      $lcount      = $request->lcount;
      $lfrom      = $request->lfrom;
      $lto       = $request->lto;
      
      if (DB::table('leave_type')->where('leave_type_name', $ltype)->doesntExist()) {

        if(DB::insert('INSERT INTO leave_type (leave_type_name, count, from_date, to_date, active) values (?, ?, ?, ?, ?)', [ $ltype , $lcount , $lfrom , $lto ,1])){

            return redirect()->back()->with('message', 'Operation Successful.');

        }

      }else{
        return redirect()->back()->withErrors("<strong>Unable to register:</strong> The given leave type already exists in the database");
      }

    }
    return view("login-page");

  }




    public function InsertStaffData(Request $request)
    {
      $session_type = Session::get('Session_Type');
      if($session_type == "Admin"){
        $this->validate(
          $request,
          [
            'name'          => 'required',
            'joining_date'  => 'required',
            'email'         => 'required',
            'designation'   => 'required',
        ]);

        $first_name     = $request->name;
        $joining_date   = $request->joining_date;
        $email          = $request->email;
        $position       = $request->designation;

        if (DB::insert('INSERT INTO employee (name, joining_date, email, designation, password) values (?, ?, ?, ?, ?)', [$first_name, $joining_date, $email, $position, 'test123']))
        {
          return redirect()->back()->with('message', 'Registeration is Successful.');
        }
      }
    }

    public function DeleteStaffData($auto_id){

       $session_type = Session::get('Session_Type');

       if($session_type == "Admin"){

           if(DB::table('staff_data')->where('auto_id', '=', $auto_id)->delete()){

               return redirect()->back()->with('message', 'Deletion is Successful.');
           }

       }else{

           return Redirect::to("/");

       }

   }


  public function DeleteUserAccount($auto_id){

     $session_type = Session::get('Session_Type');

     if($session_type == "Admin"){

         if(DB::table('user_account')->where('auto_id', '=', $auto_id)->delete()){

             return redirect()->back()->with('message', 'Deletion is Successful.');
         }

     }else{

         return Redirect::to("/");

     }

 }

 public function InsertUserAccount(Request $request){

   $session_type = Session::get('Session_Type');
   $session_value = Session::get('Session_Value');

   if($session_type == "Admin"){

     $this->validate($request, [
       'staff_id' => 'required',
       'username' => 'required',
       'password' => 'required',
     ]);

     $staff_id  =  $request->staff_id;
     $username  =  $request->username;
     $password  =  $request->password;
     
     $staff_email_data = DB::table('staff_data')->where('staff_id',$staff_id)->select("email")->get();


     $data = [
      "subject"=>"New User Created",
      "body"=>"Username:$username \n Password:$password"
      ];
    // MailNotify class that is extend from Mailable class.
    try
    {
      Mail::to( $staff_email_data )->send(new MailNotify($data));
      //return response()->json(['Great! Successfully send your mail']);
    }
    catch(Exception $e)
    {
      return response()->json(['Mail not sent']);
    }




     if (DB::table('user_account')->where('staff_id', $staff_id)->doesntExist()) {

       if (DB::table('user_account')->where('username', $username)->doesntExist()) {

         if(DB::insert('INSERT INTO user_account (staff_id, username, password, account_type) values (?, ?, ?, ?)', [$staff_id, $username, $password, "staff"])){

             return redirect()->back()->with('message', 'Account creation is Successful.');
         }

       }else{

         return redirect()->back()->withErrors("<strong>Unable to create:</strong> The given username already exists in the database.");

       }

     }else{

       return redirect()->back()->withErrors("<strong>Unable to create:</strong> The staff already has an account");

     }
   }
 }

  public function AcceptRequest($auto_id)
  {
    $session_type = Session::get('Session_Type');
    if($session_type == "Admin"){
      if(DB::table('leave_data')->where(["auto_id" => $auto_id])->update(['approval_status' => '1'])){
        return redirect()->back()->with('message', 'Accepted');
      } else
      {
        return redirect()->back()->with('message', 'No changes made.');
      }
    } else
    {
      return Redirect::to("/");
    }
  }

 public function DeclineRequest($auto_id){

   $session_type = Session::get('Session_Type');
  //  $session_value = Session::get('Session_Value');
   if($session_type == "Admin"){

     if(DB::table('leave_data')->where(["auto_id" => $auto_id])->update(['approval_status' => '2'])){

         return redirect()->back()->with('message', 'Declined');

     }else{

       return redirect()->back()->with('message', 'No changes made.');

     }

   }else{

        return Redirect::to("/");

   }

 }
  public function InsertLeaveDataOfStaffAccount(Request $request)
  {
    $session_value = Session::get('Session_Value');
    $this->validate($request, [
      'type_of_leave' => 'required',
      'description' => 'required',
      'date_of_leave' => 'required',
    ]);
    $insertArr = [
      'staff_id' => $session_value,
      'leave_type' => $request->type_of_leave,
      'remarks' => $request->description,
      'leave_date' => $request->date_of_leave,
      'request_date' => date('Y-m-d H:i:s'),
      'status' => 0
    ];
    if((new LeaveRequest)->addNewLeaveRequest($insertArr)){
      (new LeaveMaster)->updateLeave($session_value, strtolower($request->type_of_leave));
      return redirect()->back()->with('message', 'Leave request has been submited successfully.');
    }
  }

  public function DeleteLeavePendingRequestInStaffAccount($id)
  {
    if((new LeaveRequest)->removePendingRequest($id))
    {
      return redirect()->back()->with('message', 'Deletion is Successful.');
    }
  }
}