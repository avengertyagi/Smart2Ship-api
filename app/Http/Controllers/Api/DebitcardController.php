<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use App\Helpers\Helper;
use App\Models\Debitcard;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class DebitcardController extends Controller
{
    public function userCardsave(Request $request)
    {
        date_default_timezone_set('Asia/Kolkata');
        if ($request->isMethod('post')) {
            $data = $request->all();
            $rules = [
                'card_no'          => ['required', 'unique:debit_card,card_no', 'min:16', 'max:16'],
                'expiry_year'      => ['required', 'numeric', 'min:2022', 'max:2030'],
                'expiry_month'     => ['required', 'numeric', 'min:1', 'max:12'],
                'cvc'              => ['required', 'numeric', 'min:3', 'max:3'],
                'card_holder_name' => ['required', 'alpha', 'max:60'],
            ];
            $message = [
                'card_no.required' => 'Please enter your card number',
                'expiry_month'     => 'Please enter your expiry month',
                'expiry_year'      => 'Please enetr your expiry year',
                'cvc'              => 'Please enter your cvc',
                'card_holder_name' => 'Please enetr your card holder name',
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
                        // check user already exits or not
                        $user_card = Debitcard::where(['card_no' => $data['card_no']])->count();
                        if ($user_card > 0) {
                            return response()->json(['message' => Helper::response('mobile_alredy'), 'responseCode' => Helper::statusCode('unauthorized'), 'responseStatus' => Helper::response('unauthorized'), 'userInfo' => (object)[]]);
                        } else {
                            // save user data
                            $user_card = new Debitcard();
                            $user_card['user_id']           = $users['id'];
                            $user_card['card_no']           = $request['card_no'];
                            $user_card['expiry_year']       = $request['expiry_year'];
                            $user_card['expiry_month']      = $request['expiry_month'];
                            $user_card['cvc']               = $request['cvc'];
                            $user_card['card_holder_name']  = $request['card_holder_name'];
                            $user_card['created_at'];
                            $user_card->save();
                            //return response user data
                            $data = array();
                            $data['id'] = $user_card['id'];
                            $data['card_no'] = $user_card['card_no'];
                            $data['expiry_year'] = $user_card['expiry_year'];
                            $data['expiry_month'] = $user_card['expiry_month'];
                            $data['cvc'] = $user_card['cvc'];
                            $data['card_holder_name'] = $user_card['card_holder_name'];
                            $data['created_at'] = date('Y-m-d H:i:s', strtotime($user_card['created_at']));
                            $response = array('message' => Helper::response('debitcard_success'), 'responsecode' => Helper::statusCode('ok'), 'responseStatus' => Helper::response('ok'), 'userInfo' => $data);
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
    public function cardPrimary(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            $makePrimary = Debitcard::where('id', $id)->first();
            if ($makePrimary['is_primary'] == '1') {
                $status   = '0';
            } else {
                $status   = '1';
            }
            Debitcard::where('id', $id)->update(['is_primary' => $status]);
            $response = array('message' => Helper::response('debitcard_makeprimary'), 'responsecode' => Helper::statusCode('ok'), 'responseStatus' => Helper::response('ok'), 'userInfo' => $status);
            return response()->json($response);
        } else {
            return response()->json(['message' => Helper::response('something_went'), 'responsecode' => Helper::response('bed_request'), 'responseStatus' => Helper::response('bed_request')]);
        }
    }
    public function delete($id)
    {
        try {
            $users =  Debitcard::where('id', $id)->first();
            if ($users) {
                $user = Debitcard::where('id', $id)->delete();
                return response()->json(['message' => Helper::response('user_card_delete'), 'responsecode' => Helper::statusCode('ok'), 'responsestatus' => Helper::response('ok'), 'userInfo' => (object)[]]);
            } else {
                return response()->json(['message' => Helper::response('session_expire'), 'responseCode' => Helper::statusCode('session_expire'), 'responseStatus' => Helper::response('unauthorized'), 'data' => (object)[]]);
            }
        } catch (Exception $e) {
            return response()->json(['message' => 'ExpectationFailed', 'responseCode' => Helper::statusCode('expectation_failed'), 'userInfo' => $e]);
        }
    }
}
