<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;


class UserProfileController extends Controller
{
    public function updateProfile(Request $request)
    {
        date_default_timezone_set('Asia/Kolkata');
        if ($request->isMethod('post')) {
            $data = $request->all();
            $detail = User::where('remember_token', $request->header('token'))->first();
            $rules = [
                'first_name' => ['required', 'alpha', 'max:91'],
                'last_name'  => ['required', 'alpha', 'max:92'],
                'mobile'     => ['required', 'regex:/^([0-9\s\-\+\(\)]*)$/', 'min:10', 'max:10'],
                'email'      => ['required', 'regex:/(.+)@(.+)\.(.+)/i', 'email', 'max:255', 'unique:users,email,' . $detail['id']],
                'gender'     => ['required', 'alpha'],
                'race'       => ['required', 'alpha'],
                'birthday'   => ['required', 'date_format:m/d/Y'],
                'account_type' => ['required', 'alpha'],
                'image'       => ['required', 'mimes:jpeg,jpg,png', 'max:2000'],
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
                    $users =  User::where('remember_token', $request->header('token'))->first();
                    if ($users) {
                        $getDetail = User::where('id', $users['id'])->first();
                        if ($request->hasfile('image')) {
                            $file = $request->file('image');
                            $privious_image = config('location.user.path') . $getDetail['image'];
                            if (file_exists($privious_image)) {
                                @unlink($privious_image);
                            }
                            try {
                                $extension = $file->getClientoriginalExtension();
                                $destination = config('location.user.path');
                                $filename = rand(1111, 9999) . '.' . $extension;
                                $path = $request->image->move($destination, $filename);
                            } catch (Exception $e) {
                                return response()->json(['message' => 'Could not upload your file']);
                            }
                        }
                        $getDetail['first_name']   = $request['first_name'];
                        $getDetail['last_name']    = $request['last_name'];
                        $getDetail['email']        = $request['email'];
                        $getDetail['mobile']       = $request['mobile'];
                        $getDetail['gender']       = $request['gender'];
                        $getDetail['race']         = $request['race'];
                        $getDetail['birthday']     = $request['birthday'];
                        $getDetail['account_type'] = $request['account_type'];
                        $getDetail['about_us']     = $request['about_us'];
                        $getDetail['device_type']  = $request['device_type'];
                        $getDetail['device_token'] = $request['device_token'];
                        $getDetail['image']        = $filename;
                        $getDetail['created_at'];
                        $getDetail->update();
                        //return response user data
                        $data = array();
                        $data['id']            = $getDetail['id'];
                        $data['firstname']     = $getDetail['first_name'];
                        $data['lastname']      = $getDetail['last_name'];
                        $data['email']         = $getDetail['email'];
                        $data['mobile']        = $getDetail['mobile'];
                        $data['gender']        = $getDetail['gender'];
                        $data['race']          = $getDetail['race'];
                        $data['birthday']      = $getDetail['birthday'];
                        $data['account_type']  = $getDetail['account_type'];
                        $data['about_us']      = $getDetail['about_us'];
                        $data['image']         = url('/') . '/public/assets/uploads/users/' . $getDetail['image'];
                        $data['device_type']   = $getDetail['device_type'];
                        $data['device_token']  = $getDetail['device_token'];
                        $data['created_at']    = date('Y-m-d H:i:s', strtotime($getDetail['created_at']));
                        $response = array('message' => Helper::response('user_profile_update'), 'responsecode' => Helper::statusCode('ok'), 'responsestatus' => Helper::response('ok'), 'userInfo' => $data);
                        return response()->json($response);
                    } else {
                        return response()->json(['message' => Helper::response('session_expire'), 'responseCode' => Helper::statusCode('session_expire'), 'responseStatus' => Helper::response('unauthorized'), 'data' => (object)[]]);
                    }
                } catch (Exception $e) {
                    return response()->json(['message' => 'ExpectationFailed', 'responseCode' => Helper::statusCode('expectation_failed'), 'userInfo' => $e]);
                }
            }
        } else {
            return response()->json(['message' => Helper::response('something_went'), 'responsecode' => Helper::response('bed_request'), 'responseStatus' => Helper::response('bed_request')]);
        }
    }
    public function getProfile(Request $request)
    {
        try {
            $users =  User::where('remember_token', $request->header('token'))->first();
            if ($users) {
                if ($users['status'] == 0) {
                    return response()->json(['message' => Helper::response('block_admin'), 'responseCode' => Helper::statusCode('unauthorized'), 'responseStatus' => Helper::response('unauthorized'), 'userInfo' => (object)[]]);
                }
                //return response user data
                $data = array();
                $data['id']             = $users['id'];
                $data['firstname']      = $users['first_name'];
                $data['lastname']       = $users['last_name'];
                $data['mobile']         = $users['mobile'];
                $data['gender']         = $users['gender'];
                $data['birthday']       = $users['birthday'];
                $data['race']           = $users['race'];
                $data['account_type']   = $users['account_type'];
                $data['about_us']       = $users['about_us'];
                $data['image']          = url('/') . '/public/assets/uploads/user/' . $users['image'];
                $data['device_type']    = $users['device_type'];
                $data['device_token']   = $users['device_token'];
                $data['created_at']     = date('Y-m-d H:i:s', strtotime($users['created_at']));
                $response = array('message' => Helper::response('user_fetch_list'), 'responsecode' => Helper::statusCode('ok'), 'responseStatus' => Helper::response('ok'), 'userInfo' => $data);
                return response()->json($response);
            } else {
                return response()->json(['message' => Helper::response('session_expire'), 'responseCode' => Helper::statusCode('session_expire'), 'responseStatus' => Helper::response('unauthorized'), 'data' => (object)[]]);
            }
        } catch (Exception $e) {
            return response()->json(['message' => 'ExpectationFailed', 'responseCode' => Helper::statusCode('expectation_failed'), 'userInfo' => $e]);
        }
    }
}
