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

class RegisterController extends Controller
{
    public function createUser(Request $request)
    {
        date_default_timezone_set('Asia/Kolkata');
        if ($request->isMethod('post')) {
            $data = $request->all();
            //validated the data
            $rules = [
                'first_name' => ['required', 'alpha', 'max:91'],
                'last_name'  => ['required', 'alpha', 'max:92'],
                'mobile'     => ['required', 'regex:/^([0-9\s\-\+\(\)]*)$/', 'min:10', 'max:10'],
                'email'      => ['required', 'regex:/(.+)@(.+)\.(.+)/i', 'email', 'max:255', 'unique:users,email'],
                'password'   => ['required', 'confirmed', Password::min(8)->mixedCase()],
            ];
            $message = [
                'first_name.required' => 'Please enter your firstname in format:John',
                'last_name.required'  => 'Please enter your lastname in format:Die',
                'mobile.required'     => 'Please enter your mobile number',
                'mobile.regex'        => 'Please enter a valid mobile number',
                'email.required'      => 'Please enter your email in format:John@gmail.com',
                'email.regex'         => 'Please enter a valid email',
                'password.required'   => 'Please enter your password',
            ];
            $validator = Validator::make($data, $rules, $message);
            if ($validator->fails()) {
                return response()->json([
                    'message' => $validator->errors()->first(), 'responseCode' => Helper::statusCode('bed_request'), 'responseStatus' => Helper::response('bed_request'), 'userInfo' => (object)[]
                ], 200);
            } else {
                try {
                    // check user already exits or not
                    $user = User::where(['mobile' => $data['mobile']])->count();
                    if ($user > 0) {
                        return response()->json(['message' => Helper::response('mobile_alredy'), 'responseCode' => Helper::statusCode('unauthorized'), 'responseStatus' => Helper::response('unauthorized'), 'userInfo' => (object)[]]);
                    } else {
                        // save user data
                        $user = new User();
                        $user['first_name']   =   $request['first_name'];
                        $user['last_name']    =   $request['last_name'];
                        $user['email']        =   $request['email'];
                        $user['mobile']       =   $request['mobile'];
                        $user['password']     =   Hash::make($request['password']);
                        $user['device_type']  =   $request['device_type'];
                        $user['device_token'] =   $request['device_token'];
                        $user['status'] = 1;
                        $user['created_at'];
                        $user->save();
                        //return response user data
                        $data = array();
                        $data['id'] = $user['id'];
                        $data['firstname'] = $user['first_name'];
                        $data['lastname'] = $user['last_name'];
                        $data['email'] = $user['email'];
                        $data['mobile'] = $user['mobile'];
                        $data['device_type'] = $user['device_type'];
                        $data['device_token'] = $user['device_token'];
                        $data['created_at'] = date('Y-m-d H:i:s', strtotime($user['created_at']));
                        $response = array('message' => Helper::response('register_success'), 'responsecode' => Helper::statusCode('ok'), 'responseStatus' => Helper::response('ok'), 'userInfo' => $data);
                        return response()->json($response);
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
