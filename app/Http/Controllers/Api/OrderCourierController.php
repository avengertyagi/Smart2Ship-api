<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Cart;
use App\Models\User;
use App\Helpers\Helper;
use App\Models\SearchQuote;
use App\Models\ParcelDetail;
use Illuminate\Http\Request;
use App\Models\DeliveryAddress;
use App\Models\CollectionAddress;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class OrderCourierController extends Controller
{
    public function getQuotes(Request $request)
    {
        date_default_timezone_set('Asia/Kolkata');
        if ($request->isMethod('post')) {
            $data = $request->all();
            $rules = [
                'weight' => ['required', 'numeric', 'between:1,30']
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return response()->json([
                    'message' => $validator->errors()->first(), 'responseCode' => Helper::statusCode('bed_request'), 'responseStatus' => Helper::response('bed_request'), 'userInfo' => (object)[]
                ], 200);
            } else {
                try {
                    $users =  User::where('remember_token', $request->header('token'))->first();
                    if ($users) {
                        $createparcelid = 'STS-' . rand();
                        if (isset($users['id'])) {
                            $insert_data = new SearchQuote();
                            $insert_data['user_id']              = $users['id'];
                            $insert_data['parcel_id']            = $createparcelid;
                            $insert_data['source_id']            = $request['source_id'];
                            $insert_data['destination_id']       = $request['destination_id'];
                            $insert_data['source_postcode']      = $request['source_postcode'];
                            $insert_data['destination_postcode'] = $request['destination_postcode'];
                            $insert_data['weight']               = $request['weight'];
                            $insert_data['created_at'];
                            $insert_data->save();
                            //return response user data
                            $data = array();
                            $data['id']                   = $insert_data['id'];
                            $data['parcel_id']            = $insert_data['parcel_id'];
                            $data['source_id']            = $insert_data['source_id'];
                            $data['destination_id']       = $insert_data['destination_id'];
                            $data['source_postcode']      = $insert_data['source_postcode'];
                            $data['destination_postcode'] = $insert_data['destination_postcode'];
                            $data['weight']               = $insert_data['weight'];
                            $data['created_at']           = date('Y-m-d H:i:s', strtotime($insert_data['created_at']));
                            $response = array('message' => Helper::response('quote_success'), 'responsecode' => Helper::statusCode('ok'), 'responseStatus' => Helper::response('ok'), 'userInfo' => $data);
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
            return response()->json(['message' => Helper::response('something_went'), 'responsecode' => Helper::response('bed_request'), 'responseStatus' => Helper::statusCode('bed_request')]);
        }
    }
    public function getBuyer(Request $request)
    {
        try {
            $users =  User::where('remember_token', $request->header('token'))->first();
            if ($users) {
                if ($users['status'] == 0) {
                    return response()->json(['message' => Helper::response('block_admin'), 'responseCode' => Helper::statusCode('unauthorized'), 'responseStatus' => Helper::response('unauthorized'), 'userInfo' => (object)[]]);
                }
                $searchQuote =  SearchQuote::where('user_id', $users['id'])->with('getQuoteInfo')->get();
                //return response user data
                foreach ($searchQuote as $key => $value) {
                    $data['id']                     = $value['id'];
                    $data['service_type']           = $value->getQuoteInfo->service_type;
                    $data['service_info']           = $value->getQuoteInfo->service_info;
                    $data['service_rating']         = $value->getQuoteInfo->service_rating;
                    $data['delivery_duration']      = $value->getQuoteInfo->delivery_duration;
                    $data['delivery_notes']         = $value->getQuoteInfo->delivery_notes;
                    $data['company_image']          = url('/') . '/public/assets/uploads/user/' . $value->getQuoteInfo->company_image;
                    $data['created_at']             = date('Y-m-d H:i:s', strtotime($value->getQuoteInfo->created_at));
                    $datalist[] = $data;
                }
                $response = array('message' => Helper::response('quick_list'), 'responsecode' => Helper::statusCode('ok'), 'responseStatus' => Helper::response('ok'), 'userInfo' => $datalist);
                return response()->json($response);
            } else {
                return response()->json(['message' => Helper::response('session_expire'), 'responseCode' => Helper::statusCode('session_expire'), 'responseStatus' => Helper::response('unauthorized'), 'data' => (object)[]]);
            }
        } catch (Exception $e) {
            return response()->json(['message' => 'ExpectationFailed', 'responseCode' => Helper::statusCode('expectation_failed'), 'userInfo' => $e]);
        }
    }
    public function saveOrderDetail(Request $request)
    {
        date_default_timezone_set('Asia/Kolkata');
        if ($request->isMethod('post')) {
            $data = $request->all();
            $rules  = [
                'sender_name'        => ['required', 'alpha', 'max:60'],
                'sender_contactNo'   => ['required', 'min:10', 'max:10'],
                'sender_address'     => ['required'],
                'sender_pincode'     => ['required', 'numeric', 'min:6'],
                'receiver_name'      => ['required', 'alpha', 'max:60'],
                'receiver_contactNo' => ['required', 'min:10', 'max:10'],
                'receiver_address'   => ['required'],
                'receiver_pincode'   => ['required', 'numeric', 'min:6'],
                'parcel_content'     => ['required'],
            ];
            $message = [
                'sender_name'        => 'Please enter your sender name',
                'sender_contactNo'   => 'Please enter your sender contact no.',
                'sender_address'     => 'Please enter your sender address',
                'sender_pincode'     => 'Please enter your post code',
                'receiver_name'      => 'Please enter your receiver name',
                'receiver_contactNo' => 'Please enter your receiver contact no.',
                'receiver_address'   => 'Please enter your receiver address',
                'receiver_pincode'   => 'Please enter your post code',
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
                        if ($users['status'] == 0) {
                            return response()->json(['message' => Helper::response('block_admin'), 'responseCode' => Helper::statusCode('unauthorized'), 'responseStatus' => Helper::response('unauthorized'), 'userInfo' => (object)[]]);
                        }
                        $randomNo = "QSP" . rand(0, 9999);
                        //save collectionaddress details
                        $saveOrderCollection = new CollectionAddress();
                        $saveOrderCollection['user_id']          = $users['id'];
                        $saveOrderCollection['name']             = $request['sender_name'];
                        $saveOrderCollection['email']            = $request['sender_email'];
                        $saveOrderCollection['mobile_no']        = $request['sender_contactNo'];
                        $saveOrderCollection['company']          = $request['sender_company'];
                        $saveOrderCollection['address']          = $request['sender_address'];
                        $saveOrderCollection['postcode']         = $request['sender_pincode'];
                        $saveOrderCollection['sender_country']   = $request['sender_country'];
                        $saveOrderCollection['sender_mobile_no'] = $request['sender_mobileNo'];
                        $saveOrderCollection['sender_unit_no']   = $request['sender_unitNo'];
                        $saveOrderCollection['sender_area']      = $request['sender_area'];
                        $saveOrderCollection['postcode']         = $request['sender_pincode'];
                        $saveOrderCollection['sender_region']    = $request['sender_city'];
                        $saveOrderCollection['tracking_no']      = $randomNo;
                        $saveOrderCollection->save();
                        // save receiveraddress details
                        $saveReceiverAddress = new DeliveryAddress();
                        $saveReceiverAddress['user_id']            = $users['id'];
                        $saveReceiverAddress['name']               = $request['receiver_name'];
                        $saveReceiverAddress['email']              = $request['receiver_email'];
                        $saveReceiverAddress['mobile_no']          = $request['receiver_contactNo'];
                        $saveReceiverAddress['receiver_company']   = $request['receiver_company'];
                        $saveReceiverAddress['address']            = $request['receiver_address'];
                        $saveReceiverAddress['receiver_country']   = $request['receiver_country'];
                        $saveReceiverAddress['receiver_mobile_no'] = $request['receiver_mobileNo'];
                        $saveReceiverAddress['receiver_unit_no']   = $request['receiver_unitNo'];
                        $saveReceiverAddress['receiver_area']      = $request['receiver_area'];
                        $saveReceiverAddress['receiver_region']    = $request['receiver_city'];
                        $saveReceiverAddress['postcode']           = $request['receiver_pincode'];
                        $saveReceiverAddress['tracking_no']        = $randomNo;
                        $saveReceiverAddress->save();
                        //save parcel details
                        $saveParcel = new ParcelDetail();
                        $saveParcel['user_id']     = $users['id'];
                        $saveParcel['parcel_id']   = "PRL-" . rand(0, 9999);
                        $saveParcel['remark']      = $request['remark'];
                        $saveParcel['content']     = $request['parcel_content'];
                        $saveParcel['value']       = $request['parcel_price'];
                        $saveParcel['weight']      = $request['weight'];
                        $saveParcel['pickup_date'] = date('Y-m-d', strtotime($request['parcel_date']));
                        $saveParcel['tracking_no'] = $randomNo;
                        $saveParcel->save();
                        //my cart list
                        $search_quote =  SearchQuote::where('user_id', $users['id'])->with('getQuoteInfo:id,amount')->first();
                        $saveCart = new Cart();
                        $saveCart['user_id']                = $users['id'];
                        $saveCart['reference_number']       = $search_quote['parcel_id'];
                        $saveCart['parcel_id']              = $saveParcel['id'];
                        $saveCart['quote_id']               = $search_quote['quote_id'];
                        $saveCart['courier_from_detail_id'] = $saveOrderCollection['id'];
                        $saveCart['courier_to_detail_id']   = $saveReceiverAddress['id'];
                        $saveCart['price']                  = $search_quote['getQuoteInfo']['amount'];
                        $saveCart['weight']                 = $search_quote['weight'];
                        $saveCart['original_price']         = $search_quote['getQuoteInfo']['amount'];
                        $saveCart['created_at'];
                        $saveCart->save();
                        //mycart list
                        $orderList = Cart::where('user_id', $users['id'])->with('getCollectionAddress', 'getDeliveryAddress', 'getQuoteInfo')->get();
                        //return response data
                        foreach ($orderList as $value) {
                            $data['id']                = $value->id;
                            $data['courier_company']   = $value->getQuoteInfo->courier_company;
                            $data['service_type']      = $value->getQuoteInfo->service_type;
                            $data['sender_name']       = $value->getCollectionAddress->name;
                            $data['receiver_name']     = $value->getDeliveryAddress->name;
                            $data['price']             = $value->getQuoteInfo->price;
                            $data['created_at']        = date('Y-m-d', strtotime($value->created_at));
                        }
                        //$datalist[] = $data;
                        $response = array('message' => Helper::response('cartlist_success'), 'responsecode' => Helper::statusCode('ok'), 'responseStatus' => Helper::response('ok'), 'userInfo' => $data);
                        return response()->json($response);
                    } else {
                        return response()->json(['message' => Helper::response('session_expire'), 'responseCode' => Helper::statusCode('session_expire'), 'responseStatus' => Helper::response('unauthorized'), 'data' => (object)[]]);
                    }
                } catch (Exception $e) {
                    return response()->json(['message' => 'ExpectationFailed', 'responseCode' => Helper::statusCode('expectation_failed'), 'userInfo' => $e]);
                }
            }
        } else {
            return response()->json(['message' => Helper::response('something_went'), 'responsecode' => Helper::response('bed_request'), 'responseStatus' => Helper::statusCode('bed_request')]);
        }
    }
}
