<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;

class EmployeeController extends Controller
{
    public function fetch(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $email = $request->input('email');
        $age = $request->input('age');
        $phone = $request->input('phone');
        $team_id = $request->input('team_id');
        $role_id = $request->input('role_id');
        $limit = $request->input('limit', 10);
        $employeeQuery = Employee::query();

        // TODO: Get single data (hris.com/api/employee?id=1)
        if ($id) {
           $employee = $employeeQuery->with(['team', 'role'])->find($id);

            if ($employee) {
                return ResponseFormatter::success($employee, 'Employee found');
            }

            return ResponseFormatter::error('Employee not found', 404);
        }

        // TODO: Get multiple data
        $employees = $employeeQuery;

        // hris.com/api/employee?name=Emard
        if ($name) {
            $employees->where('name', 'like', '%' . $name .'%' );
        }

        if ($email) {
            $employees->where('email', $email);
        }

        if ($age) {
            $employees->where('age', $age);
        }

        if ($phone) {
            $employees->where('phone', 'like', '%' . $phone .'%' );
        }

        if ($team_id) {
            $employees->where('team_id', $team_id);
        }

        if ($role_id) {
            $employees->where('role_id', $role_id);
        }

        // Employee::with(['users])->where('name', 'like', '%Emard%)->paginate(10);
        return ResponseFormatter::success(
            $employees->paginate($limit),
            'Employees found'
        );
    }

    public function create (CreateEmployeeRequest $request)
    {
        try {
            // TODO: Upload icon
            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('public/photos');
            }
    
            // TODO: Create employee
            $employee = Employee::create([
                'name' => $request->name,
                'email' => $request->email,
                'gender' => $request->gender,
                'age' => $request->age,
                'phone' => $request->phone,
                'photo' => $path,
                'team_id' => $request->team_id,
                'role_id' => $request->role_id,
            ]);

            if (!$employee) {
                throw new Exception("Employee not created");   
            }
    
            // TODO: Return response
            return ResponseFormatter::success($employee, 'Employee created');

        } catch (Exception $error) {
            // TODO: Return error response
            return ResponseFormatter::error($error->getMessage(), 500);
        }
       
    }

    public function update(UpdateEmployeeRequest $request, $id)
    {
        try {

            // TODO: Get Employee
            $employee = Employee::find($id);

            // TODO: Check if employee not exists
            if (!$employee) {
               throw new Exception('Employee not found');
            }

             // TODO: Upload Logo
            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('public/photos');
            }

             // TODO: Update employee
            $employee->update([
                'name' => $request->name,
                'name' => $request->name,
                'email' => $request->email,
                'gender' => $request->gender,
                'age' => $request->age,
                'phone' => $request->phone,
                'photo' => isset($path) ? $path : $employee->photo,
                'team_id' => $request->team_id,
                'role_id' => $request->role_id,
            ]);

            // TODO: Return response
            return ResponseFormatter::success($employee, 'Employee updated');

        } catch (Exception $error) {
             // TODO: Return error response
             return ResponseFormatter::error($error->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            // TODO: Get Employee
            $employee = Employee::find($id);

            // TODO: Check if employee is owned by user

            // TODO: Check if employee exists
            if (!$employee) {
                throw new Exception('Employee not found');
            }

            // TODO: Delete employee
            $employee->delete();

             // TODO: Return response
            return ResponseFormatter::success('Employee deleted');

        } catch (Exception $error) {
             // TODO: Return error response
             return ResponseFormatter::error($error->getMessage(), 500);
        }
    }
}
