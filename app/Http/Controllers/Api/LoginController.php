<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function userlogin(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->all();
            $rules = [
                'email'    => ['required', 'regex:/(.+)@(.+)\.(.+)/i', 'email'],
                'password' => ['required'],
            ];
            $message = [
                'email.required'    => 'Please enter your email in format:John@gmail.com',
                'password.required' => 'Please enter your password',
            ];
            //check validate data
            $validator = Validator::make($data, $rules, $message);
            if ($validator->fails()) {
                return response()->json([
                    'message' => $validator->errors()->first(), 'responseCode' => Helper::statusCode('bed_request'), 'responseStatus' => Helper::response('bed_request'), 'userInfo' => (object)[]
                ], 200);
            } else {
                try {
                    $user = User::where('email', $data['email'])->first();
                    if ($user) {
                        //check user active or not
                        if ($user['status'] == 0) {
                            return response()->json(['message' => Helper::response('block_admin'), 'responseCode' => Helper::statusCode('unauthorized'), 'responseStatus' => Helper::response('unauthorized'), 'userInfo' => (object)[]]);
                        }
                        if (Auth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
                            $token = $user->createToken('MyApp')->accessToken;
                            $update = User::where('id', $user['id'])->update(['remember_token' => $token]);
                            //return response user data
                            $data = array();
                            $data['id'] = $user['id'];
                            $data['first_name'] = $user['first_name'];
                            $data['last_name'] = $user['last_name'];
                            $data['email'] = $user['email'];
                            $data['password'] = $user['password'];
                            $data['mobile'] = $user['mobile'];
                            $data['gender'] = $user['gender'];
                            $data['race']   = $user['race'];
                            $data['birthday'] = $user['birthday'];
                            $data['account_type'] = $user['account_type'];
                            $data['about_us']     = $user['about_us'];
                            $data['device_type'] = $user['device_type'];
                            $data['device_token'] = $user['device_token'];
                            $data['token'] = $token;
                          
                            $response = array('message' => Helper::response('login_success'), 'responsecode' => Helper::statusCode('ok'), 'responseStatus' => Helper::response('ok'), 'userInfo' => $data);
                            return response()->json($response);
                        } else {
                            return response()->json(['message' => Helper::response('password_match'), 'responseCode' => Helper::statusCode('unauthorized'), 'responseStatus' => Helper::response('unauthorized'), 'data' => (object) []]);
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
