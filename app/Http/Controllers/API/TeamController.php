<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Team;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CreateTeamRequest;
use App\Http\Requests\UpdateTeamRequest;

class TeamController extends Controller
{
    public function fetch(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);
        $teamQuery = Team::query();

        // TODO: Get single data (hris.com/api/team?id=1)
        if ($id) {
           $team = $teamQuery->find($id);

            if ($team) {
                return ResponseFormatter::success($team, 'Team found');
            }

            return ResponseFormatter::error('Team not found', 404);
        }

        // TODO: Get multiple data
        $teams = $teamQuery->where('company_id', $request->company_id);

        // hris.com/api/team?name=Emard
        if ($name) {
            $teams->where('name', 'like', '%' . $name .'%' );
        }

        // Team::with(['users])->where('name', 'like', '%Emard%)->paginate(10);
        return ResponseFormatter::success(
            $teams->paginate($limit),
            'Teams found'
        );
    }

    public function create (CreateTeamRequest $request)
    {
        try {
            // TODO: Upload icon
            if ($request->hasFile('icon')) {
                $path = $request->file('icon')->store('public/icons');
            }
    
            // TODO: Create team
            $team = Team::create([
                'name' => $request->name,
                'icon' => $path,
                'company_id' => $request->company_id,
            ]);

            if (!$team) {
                throw new Exception("Team not created");   
            }
    
            // TODO: Return response
            return ResponseFormatter::success($team, 'Team created');

        } catch (Exception $error) {
            // TODO: Return error response
            return ResponseFormatter::error($error->getMessage(), 500);
        }
       
    }

    public function update(UpdateTeamRequest $request, $id)
    {
        try {

            // TODO: Get Team
            $team = Team::find($id);

            // TODO: Check if team not exists
            if (!$team) {
               throw new Exception('Team not found');
            }

             // TODO: Upload Logo
            if ($request->hasFile('icon')) {
                $path = $request->file('icon')->store('public/icons');
            }

             // TODO: Update team
            $team->update([
                'name' => $request->name,
                'icon' => isset($path) ? $path : $team->icon,
                'company_id' => $request->company_id,
            ]);

            // TODO: Return response
            return ResponseFormatter::success($team, 'Team updated');

        } catch (Exception $error) {
             // TODO: Return error response
             return ResponseFormatter::error($error->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            // TODO: Get Team
            $team = Team::find($id);

            // TODO: Check if team is owned by user

            // TODO: Check if team exists
            if (!$team) {
                throw new Exception('Team not found');
            }

            // TODO: Delete team
            $team->delete();

             // TODO: Return response
            return ResponseFormatter::success('Team deleted');

        } catch (Exception $error) {
             // TODO: Return error response
             return ResponseFormatter::error($error->getMessage(), 500);
        }
    }
}
