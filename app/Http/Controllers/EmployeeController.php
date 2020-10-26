<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Str;
use App\Http\Requests\UserRequest;
use App\Mail\EmployeeLoginDetails;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class EmployeeController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', User::class);

        $employees = User::with('licenses')
            ->whereHas(
                'role',
                fn ($q) => $q->where('name', '!=', 'admin')
            )
            ->get();

        return response([
            'employees' => UserResource::collection($employees),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        $this->authorize('create', User::class);

        $data = $request->validated();
        $randomPass = Str::random(20);
        $data = array_merge($data, ['password' => bcrypt($randomPass)]);
        $role = Role::firstOrCreate(['name' => 'user']);

        $employee = $role->users()->create($data);

        Mail::to($employee)
            ->send(
                new EmployeeLoginDetails($employee, $randomPass)
            );

        return response([
            'user' => new UserResource($employee),
            'message' => 'New employee has been created.',
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $employee
     * @return \App\Http\Resources\PropertyResource
     */
    public function show(User $employee)
    {
        $this->authorize('view', $employee);

        return response([
            'user' => new UserResource($employee->load('licenses')),
        ], 200);
    }
}
