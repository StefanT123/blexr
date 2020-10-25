<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\License;
use App\Http\Resources\UserResource;
use App\Http\Requests\EmployeeLicenseRequest;

class EmployeeLicenseController
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @param  \App\Models\User                $employee
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Complete a license for an employee.
     *
     * @param  \App\Models\User     $employee
     * @param  \App\Models\License  $license
     * @return \Illuminate\Http\Response
     */
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
