<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Support\Str;
use App\Http\Requests\UserRequest;
use App\Mail\EmployeeLoginDetails;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class EmployeeController extends Controller
{
    use AuthorizesRequests;

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
}
