<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\LeaveTypes;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
     // API CONNECTION WITH BACKEND
     public function index()
     {

       $employees = Employee::all();
         if($employees->count() > 0)
         {

            return response()->json ([
                'status' => 200,
                'employees' => $employees
               ], 200);

         }else
         {

            return response()->json ([
                'status' => 404,
                'message' => 'No records found'
               ], 404);
         } 
     }

     //DATA STORE AND VALIDATION FUNCTION
     public function store(Request $request)
     {
      $validator = Validator::make($request->all(), [
        'firstname' => 'required|string|max:191',
        'lastname' => 'required|string|max:191',
        'first_appointment_date' => 'required|date',
        'last_leave_date' => 'nullable|date',
        'leave_balance'  => 'nullable|numeric',
        'is_new_employee' => 'required|boolean'
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

   // $first_appointment_date = Carbon::createFromFormat('d-m-Y', $request->first_appointment_date)->format('Y-m-d');
  //  $last_leave_date = Carbon::createFromFormat('d-m-Y', $request->last_leave_date)->format('Y-m-d');

    // Check if the employee is new and if first_appointment_date is less than 9 months
    if ($request->is_new_employee && Carbon::parse($request->first_appointment_date)->diffInMonths(now()) < 9) 
    {
      $request->merge(['is_new_employee' => true]);
    }

        $employee = Employee::create([
          'firstname' => $request->firstname,
          'lastname' => $request->lastname,
          //'first_appointment_date' => $first_appointment_date,
          //'last_leave_date' => Carbon::createFromFormat('d-m-Y', $request->last_leave_date)->format('Y-m-d'), 
          'first_appointment_date' => $request->first_appointment_date,
          'last_leave_date' => $request->last_leave_date ?? null,
          //'leave_balance' =>  $request->leave_balance, // Set leave_balance to 0 if not provided
          'leave_balance' =>  $request->leave_balance ?? 0,
          'is_new_employee' => $request->is_new_employee,
        ]);

        // Update last_leave_date for new employees applying annual leave for the first time
        if ($employee->is_new_employee && $request->leave_type === 'annual') {
            $employee->update(['last_leave_date' => $request->end_date]);
              }

        //IF VALDATION PASSES
  
        if($employee){

          return response()->json([
              'status' => 200,
              'message' => "Employee added successfully"
          ],200);
          }else {
              
              return response()->json([
                  'status' => 500,
                  'message' => "Something Went Wrong!"
              ],500);
          }

      }  

     }

     //SHOW EMPLOYEES
     public function show($id)
     {
       $employee = Employee::find($id);
 
       if($employee)
       {
         return response()->json([
             'status' => 200,
             'employee' => $employee
         ],200);
         
       } else 
       
       {
         return response()->json([
             'status' => 404,
             'message' => "No Such Employee is Found!"
         ], 404); 
       }
     }

        //EDIT EMPLOYEE DETAILS 
     public function edit($id)
     {
         $employee = Employee::find($id);
 
         if($employee)
         {
           return response()->json([
               'status' => 200,
               'employee' => $employee
           ],200);
           
         } 

         else 
         {
           return response()->json([
               'status' => 404,
               'message' => "No Such Employee is Found!"
           ], 404); 
         }
     }


      //UPDATE EMPLOYEE DETAILS

      public function update (Request $request, int $id)
      {
        
          $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:191',
            'lastname' => 'required|string|max:191',
            'first_appointment_date' => 'required|date',
            'last_leave_date' => 'nullable|date',
            'leave_balance'  => 'nullable|numeric',
            'is_new_employee' => 'required|boolean'
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
                
              $employee = Employee::find($id);
  
              if($employee){
                  $employee->update([
                    'firstname' => $request->firstname,
                    'lastname' => $request->lastname,
                    //'first_appointment_date' => Carbon::createFromFormat('d-m-Y', $request->first_appointment_date)->format('Y-m-d'),
                    //'last_leave_date' => Carbon::createFromFormat('d-m-Y', $request->last_leave_date)->format('Y-m-d'), 
                    //'leave_balance' =>  $request->leave_balance, // Set leave_balance to 0 if not provided
                    'first_appointment_date' => $request->first_appointment_date,
                    'last_leave_date' => $request->last_leave_date ?? null,
                    'leave_balance' =>  $request->leave_balance ?? 0,        
                    'is_new_employee' => $request->is_new_employee,
                  ]); 
  
              return response()->json([
                  'status' => 200,
                  'message' => "Employee Updated Successfully"
              ],200);
              }

              else 
              {
                  return response()->json([
                      'status' => 404,
                      'message' => "No Such Employee is Found!"
                  ],404);
              }
          }
      }
  

      //REMOVE EMPLOYEE
    public function destroy($id)
    {
      $employee = Employee::find($id);
      if($employee)
      {
        $employee -> delete();
        return response()->json([
            'status' => 200,
            'message' => "Employee Removed Successfully!"
        ],200);
      }

      else
       {
        return response()->json([
            'status' => 404,
            'message' => "No Such Employee is Found!"
        ],404);
      }
    }

     // Additional method to check if at least 12 months have passed since the last annual leave
     protected function checkAnnualLeaveEligibility($lastLeaveDate)
     {
         $lastLeaveDate = Carbon::parse($lastLeaveDate);
         $currentDate = Carbon::now();
         $differenceInMonths = $lastLeaveDate->diffInMonths($currentDate);

         return $differenceInMonths >= 12;
     }

}
