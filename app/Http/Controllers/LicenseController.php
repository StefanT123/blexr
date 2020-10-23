<?php

namespace App\Http\Controllers;

use App\Models\License;
use App\Http\Requests\LicenseRequest;
use App\Http\Resources\LicenseResource;

class LicenseController
{
    public function store(LicenseRequest $request)
    {
        $data = $request->validated();

        $license = License::create($data);

        return response([
            'license' => new LicenseResource($license),
            'message' => 'New license has been created.',
        ], 201);
    }
}
