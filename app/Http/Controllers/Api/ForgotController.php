<?php

namespace App\Http\Controllers\Api;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Helpers\Helper;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PasswordReset;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ForgotController extends Controller
{
    public function forgotPassword(Request $request)
    {
        date_default_timezone_set('Asia/Kolkata');
        if ($request->isMethod('post')) {
            $data = $request->all();
            $rules = [
                'email'             => ['required', 'regex:/(.+)@(.+)\.(.+)/i'],
            ];
            $message = [
                'email.required'    => 'Please enter your email in format:yourname@gmail.com',
                'email.regex'       => 'Please enter a valid email address'
            ];
            //check validate data
            $validator = Validator::make($data, $rules, $message);
            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first(), 'responseCode' => Helper::statusCode('bed_request'), 'responseStatus' => Helper::response('bed_request'), 'userInfo' => (object)[]], 200);
            } else {
                try {
                    $userDetails = User::where('email', $data['email'])->first();
                    if ($userDetails) {
                        //check user active or not
                        if ($userDetails['status'] == 0) {
                            return response()->json(['message' => Helper::response('block_admin'), 'responseCode' => Helper::statusCode('unauthorized'), 'responseStatus' => Helper::response('unauthorized'), 'userInfo' => (object)[]]);
                        }
                        $userCount = User::where('email', $data['email'])->count();
                        if ($userCount == 0) {
                            return response()->json(['message' => Helper::response('email_exit'), 'responseCode' => Helper::statusCode('unauthorized'), 'responseStatus' => Helper::response('unauthorized'), 'data' => (object) []]);
                        } else {
                            $otp = random_int(100000, 999999);
                            $updateOTP = User::where('id', $userDetails['id'])->update(['otp' => $otp]);
                            Helper::SendMail($data, $otp);
                            // return response data
                            $response = array('message' => Helper::response('mail_sent'), 'email' => $request['email'], 'otp' => $otp, 'responsecode' => Helper::statusCode('ok'), 'responseStatus' => Helper::response('ok'));
                            return response()->json($response);
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
