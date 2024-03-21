<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Leave;
use App\Models\Employee;
use App\Models\LeaveTypes;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class LeaveController extends Controller
{
         // API CONNECTION WITH BACKEND
     public function index()
     {

       $leaves = Leave::all();
         if($leaves->count() > 0)
         {

            return response()->json ([
                'status' => 200,
                'leaves' => $leaves
               ], 200);

         }
         else
         {
            return response()->json ([
                'status' => 404,
                'message' => 'No Such Leave is Found!'
               ], 404);
         } 
     }

            //DATA STORAGE API
        public function store(Request $request)
        {
            $validator = Validator::make($request->all(), [
                'employee_id' => 'required|exists:tblemployees,id',
                'leave_type' => 'required|exists:tblleavestpes,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date'
            ]);

            if($validator->fails())
        {
             return response()->json([
                'status' => 422,
                'errors' => $validator->messages()
             ], 422);
        } 

        else 
        {
                // Check if start_date is not in the past
        if (Carbon::createFromFormat('d-m-Y', $request->start_date)->isPast()) {
                return response()->json([
               'status' => 422,
               'errors' => 'Start date can not be in the past'
               ], 422);
                  }
            
                        // Get leave type
            $leaveType = LeaveTypes::findOrFail($request->leave_type);
            
                    // Calculate duration of leave in days
            $startDate = strtotime($request->start_date);
            $endDate = strtotime($request->end_date);
            $duration = round(($endDate - $startDate) / (60 * 60 * 24));
   
                      // Parse start_date and end_date as Carbon instances or strtotime
            $startDate = Carbon::createFromFormat('d-m-Y', $request->start_date);
            $endDate = Carbon::createFromFormat('d-m-Y', $request->end_date);

                   // Calculate duration of leave in days
            $duration = $endDate->diffInDays($startDate);

                   // Check if duration is within the allowed range
        if ($duration > $leaveType->duration) 
        {
           return response()->json([
            'status' => 422,
            'errors' => 'Leave duration exceeds allowed limit for'. $leaveType->leave_type
            ], 422);
        }

         // Process emergency leave request
         if ($request->leave_type === 3) { // Assuming emergency leave type ID is 3
            $employee = Employee::findOrFail($request->employee_id);
            $emergencyDays = $duration; // Number of emergency days taken
            // Deduct emergency days from the annual leave balance
            $newAnnualLeaveBalance = $employee->leave_balance - $emergencyDays;

            // Check if annual leave balance goes negative
            if ($newAnnualLeaveBalance < 0) {
                return response()->json([
                    'status' => 422,
                    'errors' => 'Insufficient annual leave balance.'
                ], 422);
            }
   
            $employee->update(['leave_balance' => $newAnnualLeaveBalance]);
        }

           

            ///NEW FEATURE TO CHECK NEW EMPLOYEE
            $employee = Employee::findOrFail($request->employee_id);

            // Define the ID of the annual leave type
                   $annualLeaveTypeId = 1;

                   // Define the last leave date of the employee
            //  $lastLeaveDate = $employee->last_leave_date;

                   // Check if leave type is annual and employee is new
         if ($request->leave_type == $annualLeaveTypeId && $employee->is_new_employee) {
                        // Check if employee has served for at least 9 months
            if (Carbon::parse($employee->first_appointment_date)->diffInMonths(now()) < 9) {
                   return response()->json([
                      'status' => 422,
                     'error' => 'Sorry, you need to have served for at least 9 months to apply for annual leave.'
                    ], 422);
                 }
              }



                       // Check if leave type is annual and employee is not new
              if ($request->leave_type == $annualLeaveTypeId && !$employee->is_new_employee) {
                     // Check if at least 12 months have passed since the last annual leave
                  if (!$this->checkAnnualLeaveEligibility($employee->last_leave_date)) {
                      return response()->json([
                           'status' => 422,
                     'error' => 'Sorry, you need to have at least 12 months since your last annual leave to apply for annual leave.'
                         ], 422);
                           }
                      }
                 
                    //CREATE LEAVE REQUEST AFTER VALIDATION
             $leave = Leave::create([
                'employee_id' => $request->employee_id,
                'leave_type' => $request->leave_type,
                'start_date' => Carbon::createFromFormat('d-m-Y', $request->start_date)->format('Y-m-d'),
                'end_date' => Carbon::createFromFormat('d-m-Y', $request->end_date)->format('Y-m-d'),
            ]);

            // Update leave balance for the employee
            $leave->updateEmployeeLeaveBalance();

            // Update leave balance for the employee if it's an annual leave
    if ($leave->leave_type === 1) { // Assuming annual leave type ID is 2
        $employee = Employee::findOrFail($request->employee_id);
        // Update leave balance to 28 days
        $employee->update(['leave_balance' => 28]);
    }

            if($leave){
                return response()->json([
                    'status' => 200,
                    'message' => "Leave applied successfully"
                ],200);
                }

                else
                 {
                    
                    return response()->json([
                        'status' => 500,
                        'message' => "Something Went Wrong!"
                    ],500);
                }
        }

        }

        //OTHER FUNCTIONS
        public function show($id)
        {
            $leave = Leave::with('leaveType')->find($id);
    
          if($leave)
          {
            return response()->json([
                'status' => 200,
                'leave' => [
                    'id' => $leave->id,
                    'employee_id' => $leave->employee_id,
                    'leave_type' => $leave->leaveType->leave_type, // Accessing leave type name
                    'start_date' => $leave->start_date,
                    'end_date' => $leave->end_date,
                ]
            ],200);
            
          } else 
          
          {
            return response()->json([
                'status' => 404,
                'message' => "No Such Leave is Found!"
            ], 404); 
          }
        }

                       //UPDATE LEAVE


           //REMOVE LEAVE APPLICATION
    public function destroy($id)
    {
      $leave = Leave::find($id);
      if($leave)
      {
        $leave -> delete();
        return response()->json([
            'status' => 200,
            'message' => "Leave Application has been Removed Successfully!"
        ],200);
      }

      else
       {
        return response()->json([
            'status' => 404,
            'message' => "No Such Leave application is Found!"
        ],404);
      }
    }

     // Additional method to check if at least 12 months have passed since the last annual leave
     protected function checkAnnualLeaveEligibility($lastLeaveDate)
     {

        if (!$lastLeaveDate) {
            return true; // If there's no last leave date, assume it's eligible
        }

         // Logic to check if at least 12 months have passed since the last annual leave
         $lastLeaveDate = Carbon::parse($lastLeaveDate);
         $currentDate = Carbon::now();
         $differenceInMonths = $lastLeaveDate->diffInMonths($currentDate);

         return $differenceInMonths >= 12;
     }

}
