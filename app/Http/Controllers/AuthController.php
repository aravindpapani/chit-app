<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Services\OtpService;
use Carbon\Carbon;
use DB;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\ApiErrorResponse;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ValidatorHelper;


class AuthController extends Controller
{
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService; // Inject OtpService
    }

    // Request OTP for the provided mobile number
    public function sendOtp(Request $request)
    {
        $request->validate([
            'mobile_number' => 'required|numeric'
        ]);

        $mobile = $request->input('mobile_number');
        // Check if the user already exists (optional)
        $user = User::where('mobile_number', $mobile)->first();
        if (!$user) {
            $user = User::create(['mobile_number' => $mobile]);
        }
        // Generate OTP using OtpService
        $otp = $this->otpService->generateOtp($mobile);

        // Send OTP using OtpService
        $this->otpService->sendOtp($mobile, $otp);

        return new ApiSuccessResponse(
            [
                "OTP" => $otp,
                "User" => $user],
            ['message' => 'OTP sent successfully.'],
            Response::HTTP_OK
        );
    }

    // Verify the OTP entered by the user
    public function verifyOtp(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'mobile_number' => 'required|numeric',
            'otp' => 'required|numeric|digits:6',
        ]);
    
        // If validation fails, return errors
        if ($validator->fails()) {
            $validationError = ValidatorHelper::FormatValidatorErrors($validator->errors()->toArray());
            return new ApiErrorResponse(
                null,
                $validationError,
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    
        $mobile = $request->input('mobile_number');
        $otp = $request->input('otp');
    
        // Fetch user data from the database by mobile number
        $user = User::where('mobile_number', $mobile)->first();
    
        // Check if user exists
        if (!$user) {
            return new ApiErrorResponse(
                null,
                ['message' => 'User not found.'],
                Response::HTTP_NOT_FOUND
            );
        }
    
        // Check if OTP is expired
        if (Carbon::parse($user->otp_expires_at)->isPast()) {
            return new ApiErrorResponse(
                null,
                ['message' => 'OTP has expired.'],
                Response::HTTP_BAD_REQUEST
            );
        }
    
        // Check if OTP matches
        if ($otp != $user->otp) {
            return new ApiErrorResponse(
                null,
                ['message' => 'Invalid OTP.'],
                Response::HTTP_BAD_REQUEST
            );
        }
    
        // OTP is valid, update user verification status
        $user->is_verified = true;
        $user->save();
    
        return new ApiSuccessResponse(
            [
                "message" =>'OTP verified successfully.',
                "User" => $user],
            ['message' => 'OTP sent successfully.'],
            Response::HTTP_OK
        );
    }
    
    // Verify the OTP entered by the user
    public function registerUser(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'mobile_number' => 'required|numeric',
            'otp' => 'required|numeric|digits:6',
        ]);
    
        // If validation fails, return errors
        if ($validator->fails()) {
            $validationError = ValidatorHelper::FormatValidatorErrors($validator->errors()->toArray());
            return new ApiErrorResponse(
                null,
                $validationError,
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    
        $mobile = $request->input('mobile_number');
        $otp = $request->input('otp');
    
        // Fetch user data from the database by mobile number
        $user = User::where('mobile_number', $mobile)->first();
    
        // Check if user exists
        if (!$user) {
            return new ApiErrorResponse(
                null,
                ['message' => 'User not found.'],
                Response::HTTP_NOT_FOUND
            );
        }
    
        // Check if OTP is expired
        if (Carbon::parse($user->otp_expires_at)->isPast()) {
            return new ApiErrorResponse(
                null,
                ['message' => 'OTP has expired.'],
                Response::HTTP_BAD_REQUEST
            );
        }
    
        // Check if OTP matches
        if ($otp != $user->otp) {
            return new ApiErrorResponse(
                null,
                ['message' => 'Invalid OTP.'],
                Response::HTTP_BAD_REQUEST
            );
        }
    
        // OTP is valid, update user verification status
        $user->is_verified = true;
        $user->save();
    
        return new ApiSuccessResponse(
            [
                "message" =>'OTP verified successfully.',
                "User" => $user],
            ['message' => 'OTP sent successfully.'],
            Response::HTTP_OK
        );
    }
}
