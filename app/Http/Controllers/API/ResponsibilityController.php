<?php

namespace App\Http\Controllers\API;

use Exception;
use Illuminate\Http\Request;
use App\Models\Responsibility;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateResponsibilityRequest;

class ResponsibilityController extends Controller
{
    public function fetch(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);
        $responsibilityQuery = Responsibility::query();

        // TODO: Get single data (hris.com/api/responsibility?id=1)
        if ($id) {
           $responsibility = $responsibilityQuery->find($id);

            if ($responsibility) {
                return ResponseFormatter::success($responsibility, 'Responsibility found');
            }

            return ResponseFormatter::error('Responsibility not found', 404);
        }

        // TODO: Get multiple data
        $responsibilities = $responsibilityQuery->where('role_id', $request->role_id);

        // hris.com/api/responsibility?name=Emard
        if ($name) {
            $responsibilities->where('name', 'like', '%' . $name .'%' );
        }

        // Responsibility::with(['users])->where('name', 'like', '%Emard%)->paginate(10);
        return ResponseFormatter::success(
            $responsibilities->paginate($limit),
            'Responsibilities found'
        );
    }

    public function create (CreateResponsibilityRequest $request)
    {
        try {
            // TODO: Create responsibility
            $responsibility = Responsibility::create([
                'name' => $request->name,
                'role_id' => $request->role_id,
            ]);

            if (!$responsibility) {
                throw new Exception("Responsibility not created");   
            }
    
            // TODO: Return response
            return ResponseFormatter::success($responsibility, 'Responsibility created');

        } catch (Exception $error) {
            // TODO: Return error response
            return ResponseFormatter::error($error->getMessage(), 500);
        }
       
    }

    public function destroy($id)
    {
        try {
            // TODO: Get Responsibility
            $responsibility = Responsibility::find($id);

            // TODO: Check if responsibility is owned by user

            // TODO: Check if responsibility exists
            if (!$responsibility) {
                throw new Exception('Responsibility not found');
            }

            // TODO: Delete responsibility
            $responsibility->delete();

             // TODO: Return response
            return ResponseFormatter::success('Responsibility deleted');

        } catch (Exception $error) {
             // TODO: Return error response
             return ResponseFormatter::error($error->getMessage(), 500);
        }
    }
}
