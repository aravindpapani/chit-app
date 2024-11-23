<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\ApiErrorResponse;
use App\Helpers\ValidatorHelper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\User;

class RegisterService
{
    public function __construct()
    {
        //
    }
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'mobile_number' => 'nullable|string|max:20|unique:users,mobile_number',
        ]);

        if($validator->fails()){
            $validationError = ValidatorHelper::FormatValidatorErrors($validator->errors()->toArray());
            return new ApiErrorResponse(
                null,
                $validationError,
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'mobile_number' => $request->mobile_number,
        ]);

        return new ApiSuccessResponse(
            $user,
            ['message' => 'User registered successfully.'],
            Response::HTTP_OK
        );
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $validator = Validator::make($credentials,[
            'email' => 'required|string|exists:users,email',
            'password' => 'required|string',
        ]);

        if($validator->fails()){
            $validationError = ValidatorHelper::FormatValidatorErrors($validator->errors()->toArray());
            return new ApiErrorResponse(
                null,
                $validationError,
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            $token = $user->createToken('CHIT-APP')->plainTextToken;

            return new ApiSuccessResponse(
                [
                    'access_token' => $token,
                    'token_type' => 'bearer',
                ],
                ['message' => 'User login successful!'],
                Response::HTTP_OK
            );
        }

        return new ApiErrorResponse(
            null,
            ['error' => 'Unauthorized'],
            Response::HTTP_UNAUTHORIZED
        );
    }

}
