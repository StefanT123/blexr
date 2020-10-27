<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WorkFromHome;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\WorkFromHomeRequest;
use App\Notifications\WorkFromHomeRequestStatusChanged;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class WorkFromHomeController
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $workFromHomeRequests = $user->check()->isAdmin() ?
            WorkFromHome::with('employee')->filterBy(request()->all())->get() :
            $user->workFromHomeRequests;

        return response([
            'requests' => $workFromHomeRequests,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\WorkFromHomeRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(WorkFromHomeRequest $request)
    {
        $employee = $request->user();
        $data = $request->validated();

        abort_unless(
            $employee->canMakeWorkFromHomeRequest($data['date']),
            403,
            'It\'s too late to make a request now'
        );

        $employee->workFromHomeRequests()->create($data);

        return response([
            'message' => 'Work from home request has been made.',
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $employee
     * @return \Illuminate\Http\Response
     */
    public function show(User $employee)
    {
        return response([
            'requests' => $employee->workFromHomeRequests,
        ], 200);
    }

    /**
     * Approve request to work from home
     *
     * @param  \App\Models\WorkFromHome  $workFromHomeRequest
     * @return \Illuminate\Http\Response
     */
    public function approve(WorkFromHome $workFromHomeRequest)
    {
        $this->authorize('update', WorkFromHome::class);

        $workFromHomeRequest->update([
            'approved' => true,
        ]);

        $workFromHomeRequest->employee
            ->notify(
                new WorkFromHomeRequestStatusChanged($workFromHomeRequest)
            );

        return response([
            'message' => 'Work from home request has been approved.',
        ], 200);
    }

    /**
     * Deny request to work from home
     *
     * @param  \App\Models\WorkFromHome  $workFromHomeRequest
     * @return \Illuminate\Http\Response
     */
    public function deny(WorkFromHome $workFromHomeRequest)
    {
        $this->authorize('update', WorkFromHome::class);

        $workFromHomeRequest->update([
            'approved' => false,
        ]);

        $workFromHomeRequest->employee
            ->notify(
                new WorkFromHomeRequestStatusChanged($workFromHomeRequest)
            );

        return response([
            'message' => 'Work from home request has been denied.',
        ], 200);
    }
}
