<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
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
                'image'       => ['required','mimes:jpeg,jpg,png', 'max:2000'],
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
                        if ($request->hasFile('image')) {
                            $image_tmp = $request->file('image');
                            if ($image_tmp->isValid()) {
                                $extension = $image_tmp->getClientOriginalExtension();
                                $imageName = rand(111, 99999) . '.' . $extension;
                                $imagepath = config('location.user.path') . date('Y') . '/' . date('m') . '/' . date('d') .'/'. $imageName;
                                dd($imagepath);
                                Image::make($image_tmp)->save($imagepath);
                            } else {
                                return response()->json(['message' => 'Could not upload this image'], 400);
                            }
                        }
                        $getDetail['first_name']   = $request['first_name'];
                        dd($getDetail);
                        $getDetail['last_name']    = $request['last_name'];
                        $getDetail['email']        = $request['email'];
                        $getDetail['mobile']       = $request['mobile'];
                        $getDetail['gender']       = $request['gender'];
                        $getDetail['race']         = $request['dob'];
                        $getDetail['birthday']     = $request['country'];
                        $getDetail['account_type'] = $request['state'];
                        $getDetail['about_us']     = $request['city'];
                        $getDetail['device_type']  = $request['device_type'];
                        $getDetail['device_token'] = $request['device_token'];
                        $getDetail['image']        = $path;
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
                        $data['race']          = $getDetail['dob'];
                        $data['birthday']      = $getDetail['country'];
                        $data['account_type']  = $getDetail['state'];
                        $data['about_us']      = $getDetail['city'];
                        $data['image']         = url('/') . '/public/assets/uploads/users/' . $getDetail['image'];
                        $data['device_type']   = $getDetail['device_type'];
                        $data['device_token']  = $getDetail['device_token'];
                        $data['created_at']    = $getDetail['created_at'];
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
}
