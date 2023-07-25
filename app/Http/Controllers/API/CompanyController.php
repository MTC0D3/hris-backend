<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Models\Company;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    public function fetch(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);
        $companyQuery = Company::with(['users'])->whereHas('users', function($query){
            $query->where('user_id', Auth::id());
        });

        // TODO: Get single data (hris.com/api/company?id=1)
        if ($id) {
           $company = $companyQuery->find($id);

            if ($company) {
                return ResponseFormatter::success($company, 'Company found');
            }

            return ResponseFormatter::error('Company not found', 404);
        }

        // TODO: Get multiple data
        $companies = $companyQuery;

        // hris.com/api/company?name=Emard
        if ($name) {
            $companies->where('name', 'like', '%' . $name .'%' );
        }

        // Company::with(['users])->where('name', 'like', '%Emard%)->paginate(10);
        return ResponseFormatter::success(
            $companies->paginate($limit),
            'Companies found'
        );
    }

    public function create(CreateCompanyRequest $request)
    {
        try {
            // TODO: Upload Logo
            if ($request->hasFile('logo')) {
                $path = $request->file('logo')->store('public/logos');
            }
    
            // TODO: Create company
            $company = Company::create([
                'name' => $request->name,
                'logo' => $path,
            ]);

            if (!$company) {
                throw new Exception("Company not created");   
            }

            // TODO: Attach company to user
            $user = User::find(Auth::id());
            $user->companies()->attach($company->id);

            // TODO: Load user at Company
            $company->load('users');
    
            // TODO: Return response
            return ResponseFormatter::success($company, 'Company created');

        } catch (Exception $error) {
            // TODO: Return error response
            return ResponseFormatter::error($error->getMessage(), 500);
        }
       
    }

    public function update(UpdateCompanyRequest $request, $id)
    {
        try {

            // TODO: Get Company
            $company = Company::find($id);

            // TODO: Check if company not exists
            if (!$company) {
               throw new Exception('Company not found');
            }

             // TODO: Upload Logo
            if ($request->hasFile('logo')) {
                $path = $request->file('logo')->store('public/logos');
            }

             // TODO: Update company
            $company->update([
                'name' => $request->name,
                'logo' => $path,
            ]);

            // TODO: Return response
            return ResponseFormatter::success($company, 'Company updated');

        } catch (Exception $error) {
             // TODO: Return error response
             return ResponseFormatter::error($error->getMessage(), 500);
        }
    }
}
