<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\UpdateRoleRequest;

class RoleController extends Controller
{
    public function fetch(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);
        $with_responsibilities = $request->input('with_responsibilities', false);
        $roleQuery = Role::query();

        // TODO: Get single data (hris.com/api/role?id=1)
        if ($id) {
           $role = $roleQuery->with('responsibilities')->find($id);

            if ($role) {
                return ResponseFormatter::success($role, 'Role found');
            }

            return ResponseFormatter::error('Role not found', 404);
        }

        // TODO: Get multiple data
        $roles = $roleQuery->where('company_id', $request->company_id);

        // hris.com/api/role?name=Emard
        if ($name) {
            $roles->where('name', 'like', '%' . $name .'%' );
        }

        if ($with_responsibilities) {
            $roles->with('responsibilities');
        }

        // Role::with(['users])->where('name', 'like', '%Emard%)->paginate(10);
        return ResponseFormatter::success(
            $roles->paginate($limit),
            'Roles found'
        );
    }

    public function create (CreateRoleRequest $request)
    {
        try {
            // TODO: Create role
            $role = Role::create([
                'name' => $request->name,
                'company_id' => $request->company_id,
            ]);

            if (!$role) {
                throw new Exception("Role not created");   
            }
    
            // TODO: Return response
            return ResponseFormatter::success($role, 'Role created');

        } catch (Exception $error) {
            // TODO: Return error response
            return ResponseFormatter::error($error->getMessage(), 500);
        }
       
    }

    public function update(UpdateRoleRequest $request, $id)
    {
        try {

            // TODO: Get Role
            $role = Role::find($id);

            // TODO: Check if role not exists
            if (!$role) {
               throw new Exception('Role not found');
            }

             // TODO: Update role
            $role->update([
                'name' => $request->name,
                'company_id' => $request->company_id,
            ]);

            // TODO: Return response
            return ResponseFormatter::success($role, 'Role updated');

        } catch (Exception $error) {
             // TODO: Return error response
             return ResponseFormatter::error($error->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            // TODO: Get Role
            $role = Role::find($id);

            // TODO: Check if role is owned by user

            // TODO: Check if role exists
            if (!$role) {
                throw new Exception('Role not found');
            }

            // TODO: Delete role
            $role->delete();

             // TODO: Return response
            return ResponseFormatter::success('Role deleted');

        } catch (Exception $error) {
             // TODO: Return error response
             return ResponseFormatter::error($error->getMessage(), 500);
        }
    }
}
