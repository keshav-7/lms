<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon\Carbon;
use App\Models\LeaveMaster;

class Employee extends Model
{
    use HasFactory;

    // Assuming these constants for leave types
    const PL = 'PL';
    const CL = 'CL';
    const SL = 'SL';
    
    // Define maximum leave balances (dynamic with potential future updates)
    const MAX_PL_BALANCE = 12;
    const MAX_CL_BALANCE = 2;
    const MAX_SL_BALANCE = 4;    

    public function leaveApplications()
    {
        return $this->hasMany(LeaveApplication::class);
    }

    public function validateUser(array $value) : ?object
    {
        return DB::table('employee')->select('id', 'name', 'joining_date', 'email', 'password', 'user_type')->where('email', $value['username'])->where('password', $value['password'])->first();
    }

    public function getUserList() : ?object
    {
        return DB::table('employee')->select('id', 'name', 'joining_date', 'email', 'password', 'user_type', 'designation')->get();
    }
    
    public function getUserDetails(int $userId) : ?object
    {
        return DB::table('employee')->select(
            'id', 'name', 'joining_date', 'email', 'password', 'user_type', 'designation'
        )->where('id', $userId)->get();
    }

    // public function getEmployeeDetails()
    // {
    //     //7.5 PL, 0.25 CL, 2.5 SL
    //     $employee       = (new Employee);
    //     $leaveMaster    = (new LeaveMaster);
    //     $employeeList   = $employee->getUserList();        
    //     foreach ($employeeList as $employeeData) {
    //         $joinDate   = Carbon::createFromDate($employeeData->joining_date);
    //         $year       = Carbon::now()->year;
    //         list($startOfSession, $endOfSession) = $this->getSessionDate($year);
    //         $currentDate = Carbon::now();
    //         $daysInYear = $joinDate->diffInDays($endOfSession); // Account for leap years
    //         $monthsSinceJoin = ($joinDate->diffInMonths($startOfSession) === 0) ? $joinDate->diffInMonths($endOfSession) + 1 : $joinDate->diffInMonths($endOfSession);
    //         $quartersSinceJoin = $joinDate->diffInQuarters($endOfSession);
    //         // dd($quartersSinceJoin);
    //         $totalDays = $joinDate->diffInDays($currentDate);
    //         $accrualRates = $this->getAccrualRates($year);
    //         $adjustedMonthsForPL = $joinDate->day === 1 ? $monthsSinceJoin : $monthsSinceJoin + 0.5;    
    //         $plBalance = min($adjustedMonthsForPL * $accrualRates[self::PL], self::MAX_PL_BALANCE);
    //         $clBalance = min(floor($monthsSinceJoin / 6) * $accrualRates[self::CL], self::MAX_CL_BALANCE);
    //         $slBalance = min($quartersSinceJoin * $accrualRates[self::SL], self::MAX_SL_BALANCE);
    //         if ($joinDate->day > 1 && $joinDate->day <= 15) {
    //             $remainingDaysInMonth = $joinDate->endOfMonth()->diffInDays($joinDate);
    //             // dd($slBalance, ($remainingDaysInMonth / 30) * $accrualRates[self::SL]);
    //             $clBalance += ($remainingDaysInMonth / 30) * $accrualRates[self::CL];
    //             $slBalance += 0.5;//($remainingDaysInMonth / 30) * $accrualRates[self::SL];
    //         } else if ($totalDays % 30 > 15) {
    //             $clBalance += 0.25 * $accrualRates[self::CL];
    //             $slBalance += 0.5 * $accrualRates[self::SL];
    //         }
    //         $leaveArray = ['pl' => $plBalance, 'cl' => $clBalance, 'sl' => $slBalance];
    //         $leaveMaster->updateEmpLeave($employeeData->id, $leaveArray);
    //         return true;
    //     }
    // }

    private function getSessionDate($year = null) {
        $year = $year ?: Carbon::now()->year;
        return [
            Carbon::createFromDate($year, 4, 1),
            Carbon::createFromDate($year + 1, 3, 31),
        ];
    }
      
    private function getAccrualRates($year = null) {
        $year = $year ?: Carbon::now()->year;
        return [
            self::PL => 1,
            self::CL => 0.25,
            self::SL => 1,
        ];
    }    
}
