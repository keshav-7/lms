<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Carbon\Carbon;
use App\Models\Employee;
use App\Models\LeaveMaster as LM;

class LeaveMaster extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leave:manager';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    const PL = 'PL';
    const CL = 'CL';
    const SL = 'SL';    
    const MAX_PL_BALANCE = 12;
    const MAX_CL_BALANCE = 2;
    const MAX_SL_BALANCE = 4;    

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $employee       = (new Employee);
        $leaveMaster    = (new LM);
        $employeeList   = $employee->getUserList();        
        foreach ($employeeList as $employeeData) {
            $joinDate   = Carbon::createFromDate($employeeData->joining_date);
            $year       = Carbon::now()->year;
            list($startOfSession, $endOfSession) = $this->getSessionDate($year);
            $currentDate = Carbon::now();
            $daysInYear = $joinDate->diffInDays($endOfSession); // Account for leap years
            $monthsSinceJoin = ($joinDate->diffInMonths($startOfSession) === 0) ? $joinDate->diffInMonths($endOfSession) + 1 : $joinDate->diffInMonths($endOfSession);
            if($monthsSinceJoin == 12){
                $leaveArray = ['pl' => self::MAX_PL_BALANCE, 'cl' => self::MAX_CL_BALANCE, 'sl' => self::MAX_SL_BALANCE];                
            } else {
                $quartersSinceJoin = $joinDate->diffInQuarters($endOfSession);
                $totalDays = $joinDate->diffInDays($currentDate);
                $accrualRates = $this->getAccrualRates($year);
                $adjustedMonthsForPL = $joinDate->day === 1 ? $monthsSinceJoin : $monthsSinceJoin + 0.5;    
                $plBalance = min($adjustedMonthsForPL * $accrualRates[self::PL], self::MAX_PL_BALANCE);
                $clBalance = min(floor($monthsSinceJoin / 6) * $accrualRates[self::CL], self::MAX_CL_BALANCE);
                $slBalance = min($quartersSinceJoin * $accrualRates[self::SL], self::MAX_SL_BALANCE);
                if ($joinDate->day > 1 && $joinDate->day <= 15) {
                    $remainingDaysInMonth = $joinDate->endOfMonth()->diffInDays($joinDate);
                    $clBalance += ($remainingDaysInMonth / 30) * $accrualRates[self::CL];
                    $slBalance += 0.5;//($remainingDaysInMonth / 30) * $accrualRates[self::SL];
                } else if ($totalDays % 30 > 15) {
                    $clBalance += 0.25 * $accrualRates[self::CL];
                    $slBalance += 0.5 * $accrualRates[self::SL];
                }
                $leaveArray = ['pl' => $plBalance, 'cl' => $clBalance, 'sl' => $slBalance];
            }
            $leaveMaster->updateEmpLeave($employeeData->id, $leaveArray);
        }
        return true;
    }

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
            self::CL => 1,
            self::SL => 1,
        ];
    }      
}
