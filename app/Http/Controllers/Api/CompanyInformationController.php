<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class CompanyInformationController extends Controller
{
    public function createCompany(Request $request)
    {
        date_default_timezone_set('Asia/Kolkata');
        if ($request->isMethod('post')) {
            $data = $request->all();
            $rules = [
                'company_name'          => ['required', 'alpha', 'max:100'],
                'company_reg_no'        => ['required', 'numeric'],
                //'social_media_platform' => ['required', 'regex:/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/'],
                'nature_bussiness'      => ['required', 'alpha'],
                'selling_channel'       => ['required', 'alpha'],
                'gst_no'                => ['required'],
            ];
            $message = [
                'company_name.required'          => 'Please enter a company name',
                'company_reg_no.required'        => 'Please enter a company register number',
                //'social_media_platform.required' => 'Please enter a social media platform',
                'nature_bussiness.required'      => 'Please enetr a nature of business',
                'selling_channel.required'       => 'Please enter a selling channel',
                'gst_no.required'                => 'Please enter sstno',
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
                        // save user data
                        $user_company = new Company();
                        $user_company['user_id']          = $users['id'];
                        $user_company['company_name']     = $request['company_name'];
                        $user_company['company_reg_no']   = $request['company_reg_no'];
                        $user_company['facebook_link']    = $request['facebook_link'];
                        $user_company['instagram_link']   = $request['instagram_link'];
                        $user_company['twitter_link']     = $request['twitter_link'];
                        $user_company['nature_bussiness'] = $request['nature_bussiness'];
                        $user_company['selling_channel']  = $request['selling_channel'];
                        $user_company['gst_no']           = $request['gst_no'];
                        $user_company['created_at'];
                        $user_company->save();
                        //return response user data
                        $data = array();
                        $data['id']                = $user_company['id'];
                        $data['company_name']      = $user_company['company_name'];
                        $data['company_reg_no']    = $user_company['company_reg_no'];
                        $data['facebook_link']     = $user_company['facebook_link'];
                        $data['instagram_link']    = $user_company['instagram_link'];
                        $data['twitter_link']      = $user_company['twitter_link'];
                        $data['nature_bussiness']  = $user_company['nature_bussiness'];
                        $data['selling_channel']   = $user_company['selling_channel'];
                        $data['gst_no']            = $user_company['gst_no'];
                        $data['created_at']        = date('Y-m-d H:i:s', strtotime($user_company['created_at']));
                        $response = array('message' => Helper::response('company_success'), 'responsecode' => Helper::statusCode('ok'), 'responseStatus' => Helper::response('ok'), 'userInfo' => $data);
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
    public function updateCompany(Request $request)
    {
        date_default_timezone_set('Asia/Kolkata');
        if ($request->isMethod('post')) {
            $data = $request->all();
            $rules = [
                'company_name'          => ['required', 'alpha', 'max:100'],
                'company_reg_no'        => ['required', 'numeric'],
                //'social_media_platform' => ['required', 'regex:/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/'],
                'nature_bussiness'      => ['required', 'alpha'],
                'selling_channel'       => ['required', 'alpha'],
                'gst_no'                => ['required'],
            ];
            $message = [
                'company_name.required'          => 'Please enter a company name',
                'company_reg_no.required'        => 'Please enter a company register number',
                //'social_media_platform.required' => 'Please enter a social media platform',
                'nature_bussiness.required'      => 'Please enetr a nature of business',
                'selling_channel.required'       => 'Please enter a selling channel',
                'gst_no.required'                => 'Please enter sstno',
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
                        $updateCompany = Company::where('id', $users['id'])->first();
                        // update user data
                        $updateCompany['user_id']          = $users['id'];
                        $updateCompany['company_name']     = $request['company_name'];
                        $updateCompany['company_reg_no']   = $request['company_reg_no'];
                        $updateCompany['facebook_link']    = $request['facebook_link'];
                        $updateCompany['instagram_link']   = $request['instagram_link'];
                        $updateCompany['twitter_link']     = $request['twitter_link'];
                        $updateCompany['nature_bussiness'] = $request['nature_bussiness'];
                        $updateCompany['selling_channel']  = $request['selling_channel'];
                        $updateCompany['gst_no']           = $request['gst_no'];
                        $updateCompany['created_at'];
                        //dd($updateCompany);
                        $updateCompany->update();
                        //return response user data
                        $data = array();
                        $data['id']                = $updateCompany['id'];
                        $data['company_name']      = $updateCompany['company_name'];
                        $data['company_reg_no']    = $updateCompany['company_reg_no'];
                        $data['facebook_link']     = $updateCompany['facebook_link'];
                        $data['instagram_link']    = $updateCompany['instagram_link'];
                        $data['twitter_link']      = $updateCompany['twitter_link'];
                        $data['nature_bussiness']  = $updateCompany['nature_bussiness'];
                        $data['selling_channel']   = $updateCompany['selling_channel'];
                        $data['gst_no']            = $updateCompany['gst_no'];
                        $data['created_at']        = date('Y-m-d H:i:s', strtotime($updateCompany['created_at']));
                        $response = array('message' => Helper::response('company_update_success'), 'responsecode' => Helper::statusCode('ok'), 'responseStatus' => Helper::response('ok'), 'userInfo' => $data);
                        return response()->json($response);
                    } else {
                        return response()->json(['message' => Helper::response('session_expire'), 'responseCode' => Helper::statusCode('session_expire'), 'responseStatus' => Helper::response('unauthorized'), 'data' => (object)[]]);
                    }
                } catch (Exception $e) {
                }
            }
        } else {
            return response()->json(['message' => Helper::response('something_went'), 'responsecode' => Helper::response('bed_request'), 'responseStatus' => Helper::response('bed_request')]);
        }
    }
}
