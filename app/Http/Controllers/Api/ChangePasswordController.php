<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class ChangePasswordController extends Controller
{
    public function changePassword(Request $request)
    {
        date_default_timezone_set('Asia/Kolkata');
        if ($request->isMethod('post')) {
            $data = $request->all();
            //check validate data
            $rules = [
                'old_password' => ['required', Password::min(8)->mixedCase()],
                'new_password' => ['required', Password::min(8)->mixedCase()],
            ];
            $message = [
                'old_password.required' => 'Please enter a old password',
                'new_password.required' => 'Please enter a new password',
            ];
            $validator = Validator::make($data, $rules, $message);
            if ($validator->fails()) {
                return response()->json([
                    'message' => $validator->errors()->first(), 'responseCode' => Helper::statusCode('bed_request'), 'responseStatus' => Helper::response('bed_request'), 'userInfo' => (object)[]
                ], 200);
            } else {
                //check user registered or not
                try {
                    $user = User::where('remember_token', $request->header('token'))->first();
                    if ($user) {
                        //check user active or not
                        if ($user['status'] == 0) {
                            return response()->json(['message' => Helper::response('block_admin'), 'responseCode' => Helper::statusCode('unauthorized'), 'responseStatus' => Helper::response('unauthorized'), 'userInfo' => (object)[]]);
                        }
                        if (!$token = Auth::attempt(['id' => $user['id'], 'password' =>  $data['old_password']])) {
                            return response()->json(['message' => Helper::response('old_password'), 'responseCode' => Helper::statusCode('bed_request'), 'responseStatus' => Helper::response('bed_request'), 'userInfo' => (object)[]]);
                        } else {
                            $updateUser = User::where('id', $user['id'])->update(['password' => Hash::make($request['new_password'])]);
                            //get response data
                            $response = array('message' => Helper::response('password_change'), 'responsecode' => Helper::statusCode('ok'), 'responseStatus' => Helper::response('ok'), 'userInfo' => (object)[]);
                            return response()->json($response);
                        }
                    } else {
                        return response()->json(['message' => Helper::response('session_expire'), 'responseCode' => Helper::statusCode('session_expire'), 'responseStatus' => Helper::response('unauthorized'), 'data' => (object)[]]);
                    }
                } catch (Exception $e) {
                    return response()->json(['message' => 'ExpectationFailed', 'responseCode' => Helper::statusCode('expectation_failed'), 'userINfo' => $e]);
                }
            }
        } else {
            return response()->json(['message' => 'something went wrong.Please try again!']);
        }
    }
}
