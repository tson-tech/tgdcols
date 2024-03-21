<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\LeaveTypes;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class LeaveTypesController extends Controller
{
        // API CONNECTION WITH BACKEND
        public function index()
        {
   
          $leavetypes = LeaveTypes::all();
            if($leavetypes->count() > 0)
            {
   
               return response()->json ([
                   'status' => 200,
                   'leavetypes' => $leavetypes
                  ], 200);
   
            }

            else
            {
               return response()->json ([
                   'status' => 404,
                   'message' => 'No Such Leave Type is Found!'
                  ], 404);
            } 
        }


             //DATA STORAGE API
             public function store(Request $request)
             {
                 $validator = Validator::make($request->all(), [
                    // 'employee_id' => 'required|exists:tblemployees,id',
                     'leave_type' => 'required|string',
                     'duration' => 'required|numeric:4',
                     'description' => 'required|string|max:500'
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
                 $leavetype = LeaveTypes::create([
                    //'employee_id' => $request->employee_id,
                     'leave_type' => $request->leave_type,
                     'duration' => $request->duration,
                     'description' => $request->description
                 ]);
     
                 if($leavetype){
     
                     return response()->json([
                         'status' => 200,
                         'message' => "Leave Added Successfully"
                     ],200);
                     }else {
                         
                         return response()->json([
                             'status' => 500,
                             'message' => "Something Went Wrong!"
                         ],500);
                     }
             }
     
             }
 
             //SHOW LEAVES TYPES

             public function show($id)
             {
               $leavetype = LeaveTypes::find($id);
         
               if($leavetype)
               {
                 return response()->json([
                     'status' => 200,
                     'leavetype' => $leavetype
                 ],200);
                 
               } else 
               
               {
                 return response()->json([
                     'status' => 404,
                     'message' => "No Such Leave Found!"
                 ], 404); 
               }
             }

             //EDIT LEAVES TYPES

             public function edit($id)
             {
                 $leavetype = LeaveTypes::find($id);
         
                 if($leavetype)
                 {
                   return response()->json([
                       'status' => 200,
                       'leavetype' => $leavetype
                   ],200);
                   
                 } 

                 else 
                 {
                   return response()->json([
                       'status' => 404,
                       'message' => "No Such Leave is Found!"
                   ], 404); 
                 }
             }


             //UPDATE LEAVETYPES DETAILS

             public function update (Request $request, int $id)
             {
               
                 $validator = Validator::make($request->all(), [
                    'leave_type' => 'required|string',
                    'duration' => 'required|numeric:4',
                    'description' => 'required|string|max:500'
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
                       
                     $leavetype = LeaveTypes::find($id);
         
                     if($leavetype){
                         $leavetype->update([
                            'leave_type' => $request->leave_type,
                            'duration' => $request->duration,
                            'description' => $request->description
                         ]); 
         
                     return response()->json([
                         'status' => 200,
                         'message' => "Leave Updated Successfully"
                     ],200);
                     }

                     else 
                     {
                         return response()->json([
                             'status' => 404,
                             'message' => "No Such Leave is Found!"
                         ],404);
                     }
                 }
             }
         

             //REMOVE LEAVES TYPES

    public function destroy($id)
    {
      $leavetype = LeaveTypes::find($id);
      if($leavetype)
      {
        $leavetype -> delete();
        return response()->json([
            'status' => 200,
            'message' => "Leave Removed Successfully!"
        ],200);
      }

      else
       {
        return response()->json([
            'status' => 404,
            'message' => "No Such Leave is Found!"
        ],404);
      }
    }
   
}
