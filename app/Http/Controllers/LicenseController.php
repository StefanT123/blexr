<?php

namespace App\Http\Controllers;

use App\Models\License;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LicenseRequest;
use App\Http\Resources\LicenseResource;

class LicenseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response([
            'licenses' => LicenseResource::collection(License::all()),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\LicenseRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LicenseRequest $request)
    {
        $data = $request->validated();

        $license = License::create($data);

        return response([
            'license' => new LicenseResource($license),
            'message' => 'New license has been created.',
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $licenses = Auth::user()->licenses;

        return response([
            'licenses' => LicenseResource::collection($licenses),
        ], 200);
    }
}
