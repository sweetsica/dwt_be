<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\RESTResponse;
use App\Models\Departement;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class DepartmentController extends Controller
{
    //
    use RESTResponse;
    public function __construct()
    {
        //only admin and  manager can create update delete
        $this->middleware('auth.role:admin,manager', ['except' => ['index', 'show']]);
        //user can only get and show
        $this->middleware('auth.role:user,admin,manager', ['only' => ['index', 'show']]);
    }

    public function index(Request $request)
    {
        try {
            $q = $request->query('q');
            $limit = $request->query('limit', 10);

            if ($q) {
                $departments = Departement::where('name', 'like', "%$q%")
                    ->orWhere('description', 'like', "%$q%")
                    ->paginate($limit);
                   
                return $this->setData($departments)
                    ->setMessage('Search departments successfully')
                    ->successResponse();
            }
            $departments = Departement::paginate($limit);
            return $this->setData($departments)
                ->setMessage('Get departments successfully')
                ->successResponse();
        } catch (Exception $e) {
            return $this->setError($e->getMessage())
                ->setMessage('Get departments failed')
                ->errorResponse();
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = Validator::make($request->all(), [
                'name' => 'required',
                'parent' => 'nullable|exists:departements,id',
                'description' => 'nullable',
                'salary_fund' => 'nullable|numeric',
                'in_charge' => 'nullable',
                'max_employees' => 'nullable|integer',
            ]);
            if ($validated->fails()) {
                return $this->setStatusCode(Response::HTTP_BAD_REQUEST)
                    ->setError($validated->errors())
                    ->setMessage($validated->errors()->first())
                    ->errorResponse();
            }
            $validated = $validated->validated();

            $department = Departement::create($validated);
            return $this->setData($department)
                ->setMessage('Create department successfully')
                ->successResponse();
        } catch (Exception $e) {
            return $this->setError($e->getMessage())
                ->setMessage('Create department failed')
                ->errorResponse();
        }
    }

    public function show($id)
    {
        try {
            $department = Departement::find($id);
            if (!$department) {
                return $this->setError('Department not found')
                    ->setMessage('Get department failed')
                    ->setStatusCode(Response::HTTP_NOT_FOUND)
                    ->errorResponse();
            }
            return $this->setData($department)
                ->setStatusCode(Response::HTTP_OK)
                ->setMessage('Get department successfully')
                ->successResponse();
        } catch (Exception $e) {
            return $this->setError($e->getMessage())
                ->setMessage('Get department failed')
                ->errorResponse();
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $department = Departement::find($id);

            if (!$department) {
                return $this->setError('Department not found')
                    ->setMessage('Update department failed')
                    ->setStatusCode(Response::HTTP_NOT_FOUND)
                    ->errorResponse();
            }
            $validated = Validator::make($request->all(), [
                'name' => 'required',
                'parent' => 'nullable|exists:departements,id',
                'description' => 'nullable',
                'salary_fund' => 'nullable|numeric',
                'in_charge' => 'nullable',
                'max_employees' => 'nullable|integer',
            ]);
            if ($validated->fails()) {
                return $this->setStatusCode(Response::HTTP_BAD_REQUEST)
                    ->setError($validated->errors())
                    ->setMessage($validated->errors()->first())
                    ->errorResponse();
            }
            $validated = $validated->validated();

            $department->update($validated);
            return $this->setData($department)
                ->setMessage('Update department successfully')
                ->successResponse();
        } catch (Exception $e) {
            return $this->setError($e->getMessage())
                ->setMessage('Update department failed')
                ->errorResponse();
        } catch (Exception $e) {
            return $this->setError($e->getMessage())
                ->setMessage('Update department failed')
                ->errorResponse();
        }
    }

    public function destroy($id)
    {
        try {
            $department = Departement::find($id);
            if (!$department) {
                return $this->setError('Department not found')
                    ->setMessage('Delete department failed')
                    ->setStatusCode(Response::HTTP_NOT_FOUND)
                    ->errorResponse();
            }
            $department->delete();
            return $this->setData($department)
                ->setMessage('Delete department successfully')
                ->successResponse();
        } catch (Exception $e) {
            return $this->setError($e->getMessage())
                ->setMessage('Delete department failed')
                ->errorResponse();
        }
    }
}
