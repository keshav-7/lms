<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class LeaveRequest extends Model
{
    use HasFactory;

    public function addNewLeaveRequest(array $values): bool
    {
        $status = $values['status'];
        return DB::insert('INSERT INTO leave_request (staff_id, leave_type, remarks, leave_date, request_date, status ) values (?, ?, ?, ?, ?, ?)', [$values['staff_id'], $values['leave_type'], $values['remarks'], $values['leave_date'], $values['request_date'], "$status"]);        
    }

    public function removePendingRequest(int $id): ?bool
    {
        $leave = DB::table('leave_request')
        ->where('id', $id)
        ->select('leave_type', 'staff_id')
        ->first();
        $leaveM = DB::table('leave_master')
        ->where('staff_id', $leave->staff_id)
        ->select(strtolower($leave->leave_type))
        ->first();   
        $newData = $leaveM->{strtolower($leave->leave_type)} + 1;     
        DB::table('leave_master')->where('staff_id', $leave->staff_id)->update([strtolower($leave->leave_type) => $newData]);
        return DB::table('leave_request')->where('id', $id)->update(['status' => 3]);
    }
}
