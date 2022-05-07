<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class OtpVerifyController extends Controller
{
    public function otpverify(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->all();
            //validate the data
            $rules = [
                'email' => ['required', 'regex:/(.+)@(.+)\.(.+)/i'],
                'otp'   => ['required', 'numeric', 'min:6'],
            ];
            $message = [
                'email.required'        => 'Please enter your email in format:yourname@gmail.com',
                'email.regex'           => 'Please enter a valid email address',
                'otp.required'          => 'Please enter your otp',
            ];
            //check validate data
            $validator = Validator::make($data, $rules, $message);
            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first(), 'responseCode' => Helper::statusCode('bed_request'), 'responseStatus' => Helper::response('bed_request'), 'userInfo' => (object)[]], 200);
            } else {
                try {
                    $users = User::where('email', $data['email'])->first();
                    if ($users) {
                        if ($users['status'] == 0) {
                            return response()->json(['message' => Helper::response('blocked_admin'), 'responseCode' => Helper::statusCode('unauthorized'), 'responseStatus' => Helper::response('unauthorized'), 'userInfo' => (object)[]]);
                        }
                        $otpVery = User::where('id', $users['id'])->where('otp', $data['otp'])->count();
                        if ($otpVery > 0) {
                            $token = $users->createToken('MyApp')->accessToken;
                            $updateOTP = User::where('id', $users['id'])->update(['otp_varified' => 1, 'remember_token' => $token]);
                            $data['otp']               = $users['otp'];
                            $data['is_otp_varified']   = 1;
                            $data['created_at']        = date('Y-m-d H:i:s', strtotime($users['created_at']));
                            return response()->json(['message' => Helper::response('verified_success'), 'responseCode' => Helper::statusCode('ok'), 'responseStatus' => Helper::response('ok'), 'userInfo' => $data]);
                        } else {
                            return response()->json(['message' => Helper::response('otp_verified_not'), 'responseCode' => Helper::statusCode('unauthorized'), 'responseStatus' => Helper::response('unauthorized'), 'userInfo' => (object)[]]);
                        }
                    } else {
                        return response()->json(['message' => Helper::response('not_registerd'), 'responseCode' => Helper::statusCode('unauthorized'), 'responseStatus' => Helper::response('unauthorized'), 'data' => (object) []]);
                    }
                } catch (Exception $e) {
                    return response()->json(['message' => 'ExpectationFailed', 'responseCode' => Helper::statusCode('expectation_failed'), 'userInfo' => $e]);
                }
            }
        } else {
            return response()->json(['message' => Helper::response('something_went'), 'responsecode' => Helper::response('bed_request'), 'responseStatus' => Helper::response('bed_request')]);
        }
    }
}
