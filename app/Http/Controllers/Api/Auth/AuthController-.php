<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Helpers\Otp;
use App\Http\Controllers\Api\ApiController;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController1 extends ApiController
{
    public function login(Request $request) {
        $this->validate($request, [
            'phone' => 'numeric|exists:customers,phone|required'
        ]);


        $check = Customer::where('phone', $request->phone)
            ->where('otp_expired', '<', Carbon::now())
            ->first();

        if($check !== null) {
            $otpService = new Otp();
            $otp = $otpService->generateOtp();
            $otpService->sendOtp($request->phone, $otp['otp']);
            $customer = Customer::where('phone', $request->phone)->first();

            $update = Customer::find($customer->customer_id);
            $update->otp = $otp['otp'];
            $update->otp_expired = $otp['expired'];
            $update->save();
        }

        return $this->setStatusCode(200)->makeResponse(null, 'User Found');
    }

    public function logout(Request $request) {
            $token = $request->token;
            JWTAuth::parseToken()->invalidate( $token );
            return $this->setStatusCode(200)->makeResponse(null, 'User Found');
    }
    public function register(Request $request) {

        $this->validate($request, [
            'email' => 'email|nullable|unique:customers,email',
            'name' => 'required',
            'phone' => 'required|numeric|unique:customers,phone'
        ]);

        DB::beginTransaction();

        try {
            $otpService = new Otp();
            $otp = $otpService->generateOtp();
            $otpService->sendOtp($request->phone, $otp['otp']);
            $data = new Customer();

            $data->customer_id = Uuid::uuid4();
            $data->name = $request->name;
            $data->phone = $request->phone;
            $data->email = $request->email;
            $data->password = rand(111111, 999999);
            $data->otp = $otp['otp'];
            $data->otp_expired = $otp['expired'];
            $data->status = 'active';
            $data->save();
        }catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status_code' => 422,
                'status' => 'error',
                'message' => 'Unknown Error',
                'data' => $e->getMessage()
            ], 422);
        }

        DB::commit();

        return response()->json([
            'status_code' => 200,
            'status' => 'success',
            'message' => 'Successfully register',
            'data' => []
        ], 200);

    }

    public function otpConfirmation(Request $request) {
        $this->validate($request, [
            'phone' => 'numeric|exists:customers,phone',
            'otp' => 'numeric|required|digits:4'
        ]);

        $user = Customer::where(['phone' => $request->phone, 'otp' => $request->otp])
                ->where('otp_expired', '>=', Carbon::now())
                ->first();

        if(!$user){
            return $this->setStatusCode(401)->makeResponse(null, 'Unauthorized Otp Code!');
        }

        if(!$userToken = JWTAuth::fromUser($user)){
            return $this->setStatusCode(401)->makeResponse(null, 'Unauthorized Otp Code!');
        }
        //if correct user token, we want to store that token in the db after deleting the previous one
        $token = $user->token;
        if ($token!="") JWTAuth::parseToken()->invalidate( $token );
        $affected = DB::update("update customers set token = '$userToken' where phone = ? and otp = ?", [$request->phone,$request->otp]);
        return $this->respondWithToken($userToken,$p_id);
    }
}
