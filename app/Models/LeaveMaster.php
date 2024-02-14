<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class LeaveMaster extends Model
{
    use HasFactory;

    public function getEmpLeaveData(int $userId): ?object
    {
        return DB::table('leave_master')
        ->where('staff_id', $userId)
        ->select('pl as PL', 'sl as SL', 'cl as CL', DB::raw('pl + cl + sl as leavCount'))
        ->get();
    }

    public function updateLeave(int $userId, string $leaveType): ?bool
    {
        $leave = DB::table('leave_master')
        ->where('staff_id', $userId)
        ->select($leaveType)
        ->first();
        $request = $leave->{$leaveType} - 1;
        return DB::table('leave_master')->where('staff_id', $userId)->update([$leaveType => $request]);
    }    

    public function updateEmpLeave(int $userId, array $leaveTypes): ?bool
    {
        $leave = DB::table('leave_master')
        ->where('staff_id', $userId)
        ->select('pl', 'cl', 'sl')
        ->first();
        $updateArr = [
            'pl' => $leave->pl + $leaveTypes['pl'],
            'cl' => $leave->cl + $leaveTypes['cl'],
            'sl' => $leave->sl + $leaveTypes['sl']
        ];
        return DB::table('leave_master')->where('staff_id', $userId)->update($updateArr);
    }
}
