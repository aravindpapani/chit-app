<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\RegisterService;
use App\Models\Permission;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;


class RegisterController extends Controller
{
    

    public function __construct(private RegisterService $registrationService)
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $this->registrationService->create($request);
    }

    public function login(Request $request)
    {
        return $this->registrationService->login($request);
    }

}
