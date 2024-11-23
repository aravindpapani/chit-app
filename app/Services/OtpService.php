<?php

namespace App\Services;

use Twilio\Rest\Client;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;

class OtpService
{
    protected $twilio;

    public function __construct()
    {
        // Initialize Twilio client
        $this->twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
    }

    // Generate a 6-digit OTP and save it to the users table with an expiration time
    public function generateOtp($mobile)
    {
        $otp = rand(100000, 999999); // Generate a 6-digit OTP

        // Find the user by mobile number and update OTP and expiration time
        $user = User::where('mobile_number', $mobile)->first();

        if ($user) {
            $user->otp = $otp;
            $user->otp_expires_at = Carbon::now()->addMinutes(5);  // OTP expires in 5 minutes
            $user->save();
        }

        return $otp;
    }

    // Send OTP via SMS using Twilio
    public function sendOtp($mobile, $otp)
    {
        $message = $this->twilio->messages->create(
            $mobile,
            [
                'from' => env('TWILIO_PHONE_NUMBER'),
                'body' => "Your OTP is: $otp"
            ]
        );

        return $message->sid;
    }
}
