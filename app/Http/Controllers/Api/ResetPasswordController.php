<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class ResetPasswordController extends Controller
{
    public function resetpassword(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->all();
            //validate the data
            $rules = [
                'email'          => ['required', 'regex:/(.+)@(.+)\.(.+)/i'],
                'password'   => ['required', 'confirmed', Password::min(8)->mixedCase()],
            ];
            $message = [
                'email.required'        => 'Please enter your email in format:yourname@gmail.com',
                'email.regex'           => 'Please enter a valid email address',
                'password.required' => 'Please enter your password',
            ];
            //check validate data
            $validator = Validator::make($data, $rules, $message);
            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first(), 'responseCode' => Helper::statusCode('bed_request'), 'responseStatus' => Helper::response('bed_request'), 'userInfo' => (object)[]], 200);
            } else {
                try {
                    $users = User::where('email', $data['email'])->first();
                    // dd($users);
                    if ($users) {
                        if ($users['status'] == 0) {
                            return response()->json(['message' => Helper::response('block_admin'), 'responseCode' => Helper::statusCode('unauthorized'), 'responseStatus' => Helper::response('unauthorized'), 'userInfo' => (object)[]]);
                        }
                        $updateUser = User::where('id', $users['id'])->update(['password' => Hash::make($data['password'])]);
                        return response()->json(['message' => Helper::response('password_reset_success'), 'responseCode' => Helper::statusCode('ok'), 'responseStatus' => Helper::response('ok'), 'userInfo' => (object)[]]);
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
