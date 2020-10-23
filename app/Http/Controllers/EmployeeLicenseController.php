<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\License;
use App\Http\Resources\UserResource;
use App\Http\Requests\EmployeeLicenseRequest;

class EmployeeLicenseController
{
    public function store(EmployeeLicenseRequest $request, User $employee)
    {
        $data = $request->validated();
        $ids = $data['ids'];

        $employee->licenses()->sync($ids);

        return response([
            'employee' => new UserResource($employee),
            'message' => 'Licenses have been added to the employee.',
        ], 201);
    }

    public function complete(User $employee, License $license)
    {
        $employee->licenses()
            ->updateExistingPivot($license, ['completed' => 1]);

        return response([
            'employee' => new UserResource($employee),
            'message' => 'License has been marked as completed.',
        ], 200);
    }
}
