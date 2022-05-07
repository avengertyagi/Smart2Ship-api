<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use App\Helpers\Helper;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Expr\FuncCall;

class AddressController extends Controller
{
    public function userAddress(Request $request)
    {
        date_default_timezone_set('Asia/Kolkata');
        if ($request->isMethod('post')) {
            $data = $request->all();
            $rules = [
                'contact_name' => ['required', 'alpha', 'max:60'],
                'mobile_no'    => ['required', 'regex:/^([0-9\s\-\+\(\)]*)$/', 'min:10', 'max:10'],
                'alternate_no' => ['required', 'regex:/^([0-9\s\-\+\(\)]*)$/', 'min:10', 'max:10'],
                'email'        => ['required', 'email', 'regex:/(.+)@(.+)\.(.+)/i'],
                'company_name' => ['required', 'alpha', 'max:100'],
                'country'      => ['required', 'alpha', 'max:60'],
                'address'      => ['required'],
                'area'         => ['required'],
                'postal_code'  => ['required', 'regex:/^([0-9\s\-\+\(\)]*)$/', 'min:6', 'max:6'],
                'region'       => ['required'],
                'unit_no'      => ['required'],
            ];
            $message = [
                'contact_name' => 'Please enter a contact name',
                'mobile_no'    => 'Please eneter a mobile number',
            ];
            $validator = Validator::make($data, $rules, $message);
            if ($validator->fails()) {
                return response()->json([
                    'message' => $validator->errors()->first(), 'responseCode' => Helper::statusCode('bed_request'), 'responseStatus' => Helper::response('bed_request'), 'userInfo' => (object)[]
                ], 200);
            } else {
                try {
                    $users = User::where('remember_token', $request->header('token'))->first();
                    if ($users) {
                        // check user already exits or not
                        $user_address = UserAddress::where(['email' => $data['email']])->count();
                        if ($user_address > 0) {
                            return response()->json(['message' => Helper::response('email_alredy'), 'responseCode' => Helper::statusCode('unauthorized'), 'responseStatus' => Helper::response('unauthorized'), 'userInfo' => (object)[]]);
                        } else {
                            // save user data
                            $user_address = new UserAddress();
                            $user_address['user_id'] = $users['id'];
                            $user_address['contact_name']   = $request['contact_name'];
                            $user_address['mobile_no']      = $request['mobile_no'];
                            $user_address['alternate_no']   = $request['alternate_no'];
                            $user_address['email']          = $request['email'];
                            $user_address['company_name']   = $request['company_name'];
                            $user_address['country']        = $request['country'];
                            $user_address['address']        = $request['address'];
                            $user_address['area']           = $request['area'];
                            $user_address['postal_code']    = $request['postal_code'];
                            $user_address['region']         = $request['region'];
                            $user_address['unit_no']        = $request['unit_no'];
                            $user_address['created_at'];
                            $user_address->save();
                            //return response user_address data
                            $data = array();
                            $data['id']             = $user_address['id'];
                            $data['contact_name']   = $user_address['contact_name'];
                            $data['mobile_no']      = $user_address['mobile_no'];
                            $data['alternate_no']   = $user_address['alternate_no'];
                            $data['email']          = $user_address['email'];
                            $data['company_name']   = $user_address['company_name'];
                            $data['country']        = $user_address['country'];
                            $data['address']        = $user_address['address'];
                            $data['area']           = $user_address['area'];
                            $data['postal_code']    = $user_address['postal_code'];
                            $data['region']         = $user_address['region'];
                            $data['unit_no']        = $user_address['unit_no'];
                            $data['created_at'] = date('Y-m-d H:i:s', strtotime($user_address['created_at']));
                            $response = array('message' => Helper::response('user_address_success'), 'responsecode' => Helper::statusCode('ok'), 'responseStatus' => Helper::response('ok'), 'userInfo' => $data);
                            return response()->json($response);
                        }
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
    public function updateAddress(Request $request)
    {
        date_default_timezone_set('Asia/Kolkata');
        if ($request->isMethod('post')) {
            $data = $request->all();
            $rules = [
                'contact_name' => ['required', 'alpha', 'max:60'],
                'mobile_no'    => ['required', 'regex:/^([0-9\s\-\+\(\)]*)$/', 'min:10', 'max:10'],
                'alternate_no' => ['required', 'regex:/^([0-9\s\-\+\(\)]*)$/', 'min:10', 'max:10'],
                'email'        => ['required', 'email', 'regex:/(.+)@(.+)\.(.+)/i'],
                'company_name' => ['required', 'alpha', 'max:100'],
                'country'      => ['required', 'alpha', 'max:60'],
                'address'      => ['required'],
                'area'         => ['required'],
                'postal_code'  => ['required', 'regex:/^([0-9\s\-\+\(\)]*)$/', 'min:6', 'max:6'],
                'region'       => ['required'],
                'unit_no'      => ['required'],
            ];
            $message = [
                'contact_name' => 'Please enter a contact name',
                'mobile_no'    => 'Please eneter a mobile number',
            ];
            $validator = Validator::make($data, $rules, $message);
            if ($validator->fails()) {
                return response()->json([
                    'message' => $validator->errors()->first(), 'responseCode' => Helper::statusCode('bed_request'), 'responseStatus' => Helper::response('bed_request'), 'userInfo' => (object)[]
                ], 200);
            } else {
                try {
                    $users = User::where('remember_token', $request->header('token'))->first();
                    if ($users) {
                        $user_address = UserAddress::where('id', $users['id'])->first();
                        // update user data
                        $user_address['user_id']        = $user_address['id'];
                        $user_address['contact_name']   = $request['contact_name'];
                        $user_address['mobile_no']      = $request['mobile_no'];
                        $user_address['alternate_no']   = $request['alternate_no'];
                        $user_address['email']          = $request['email'];
                        $user_address['company_name']   = $request['company_name'];
                        $user_address['country']        = $request['country'];
                        $user_address['address']        = $request['address'];
                        $user_address['area']           = $request['area'];
                        $user_address['postal_code']    = $request['postal_code'];
                        $user_address['region']         = $request['region'];
                        $user_address['unit_no']        = $request['unit_no'];
                        $user_address['created_at'];
                        $user_address->update();
                        //return response user_address data
                        $data = array();
                        $data['id']             = $user_address['id'];
                        $data['contact_name']   = $user_address['contact_name'];
                        $data['mobile_no']      = $user_address['mobile_no'];
                        $data['alternate_no']   = $user_address['alternate_no'];
                        $data['email']          = $user_address['email'];
                        $data['company_name']   = $user_address['company_name'];
                        $data['country']        = $user_address['country'];
                        $data['address']        = $user_address['address'];
                        $data['area']           = $user_address['area'];
                        $data['postal_code']    = $user_address['postal_code'];
                        $data['region']         = $user_address['region'];
                        $data['unit_no']        = $user_address['unit_no'];
                        $data['created_at'] = date('Y-m-d H:i:s', strtotime($user_address['created_at']));
                        $response = array('message' => Helper::response('user_address_update_success'), 'responsecode' => Helper::statusCode('ok'), 'responseStatus' => Helper::response('ok'), 'userInfo' => $data);
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
    public function cardPrimary(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            $makePrimary = UserAddress::where('id', $id)->first();
            if ($makePrimary['is_primary'] == '1') {
                $status   = '0';
            } else {
                $status   = '1';
            }
            UserAddress::where('id', $id)->update(['is_primary' => $status]);
            $response = array('message' => Helper::response('addresscard_makeprimary'), 'responsecode' => Helper::statusCode('ok'), 'responseStatus' => Helper::response('ok'), 'userInfo' => $status);
            return response()->json($response);
        } else {
            return response()->json(['message' => Helper::response('something_went'), 'responsecode' => Helper::response('bed_request'), 'responseStatus' => Helper::response('bed_request')]);
        }
    }
    public function delete($id)
    {
        try {
            $users =  UserAddress::where('id', $id)->first();
            if ($users) {
                $user = UserAddress::where('id', $id)->delete();
                return response()->json(['message' => Helper::response('addresscard_delete'), 'responsecode' => Helper::statusCode('ok'), 'responsestatus' => Helper::response('ok'), 'userInfo' => (object)[]]);
            } else {
                return response()->json(['message' => Helper::response('session_expire'), 'responseCode' => Helper::statusCode('session_expire'), 'responseStatus' => Helper::response('unauthorized'), 'data' => (object)[]]);
            }
        } catch (Exception $e) {
            return response()->json(['message' => 'ExpectationFailed', 'responseCode' => Helper::statusCode('expectation_failed'), 'userInfo' => $e]);
        }
    }
}
