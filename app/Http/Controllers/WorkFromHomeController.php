<?php

namespace App\Http\Controllers;

use App\Http\Requests\WorkFromHomeRequest;

class WorkFromHomeController
{
    public function store(WorkFromHomeRequest $request)
    {
        $employee = $request->user();

        abort_unless(
            $employee->canMakeWorkFromHomeRequest(),
            403,
            'It\'s too late to make a request now'
        );

        $data = $request->validated();

        $employee->workFromHomeRequest()->create($data);

        return response([
            'message' => 'Work from home request has been made.',
        ], 201);
    }
}
