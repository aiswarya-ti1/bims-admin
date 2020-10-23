<?php

namespace App\Http\Controllers;
use Razorpay\Api\Api;
use Request;
use Illuminate\Support\Str;

//use Illuminate\Http\Request;

class paymentController extends Controller
{
    public $RazorPay_ID="rzp_test_58HZBwOLc2Uj8v" ;
    public $RazorPay_Key="TLkGTaJeLrEKcewBoyjg3YIb";

    public function initializePayment(Request $r)
    {
    $values = Request::json()->all();
    $amount=$values['amount']*100;
    $wid = $values['workid'];
    $userID=$values['userid'];
        $api = new Api("rzp_test_58HZBwOLc2Uj8v", "TLkGTaJeLrEKcewBoyjg3YIb");
$receiptID=Str::random(20);
        $order = $api->order->create(array(
            'receipt' => $receiptID,
            'amount' => $amount,
            'currency' => 'INR'
            )
          );
$insertPayment=\DB::table('payment_history')->insertGetID(array('Work_ID'=>$wid,'Payment_Type_ID'=>1,
'Payment_Flag_Status'=>1,'Payment_Amount'=>$amount,'User_ID'=>$userID));
          $response=[
              'orderId'=>$order['id'],
              'amount' => $amount,
              'payId'=>$insertPayment
          ];
          return $response;
    }
    public function completePayment(Request $r)
    {
        $values = Request::json()->all();
        $orderID=$values['o_id'];
        $payID=$values['p_id'];
        $sign=$values['sign'];
        $historyID=$values['h_id'];
        $workID=$values['w_id'];
        $paymentStatus=$this->confirmSignature($orderID,$payID,$sign, $historyID);
        if($paymentStatus==true)
        {
            $updateHistory=\DB::table('payment_history')->where('Pay_ID',$historyID)->update(array('Payment_Flag_Status'=>3));
            $updateWork=\DB::table('service_work')->where('Work_ID',$workID)->update(array('Payment_Flag'=>1));
            $resp=array('Success'=>true);
            return $resp;
        }
        else{
            $updateHistory=\DB::table('payment_history')->where('Pay_ID',$historyID)->update(array('Payment_Flag_Status'=>5));
            $resp=array('Success'=>false);
            return $resp;
        }
    }

    public function confirmSignature($oid, $pid,$sign, $hid)
    {
        
        try{
            $attrbs_pay=\DB::table('payment_attr_value')->insert(array('History_ID'=>$hid,'Payment_Attr_ID'=>1,'Value'=>$pid));
            $attrbs_pay=\DB::table('payment_attr_value')->insert(array('History_ID'=>$hid,'Payment_Attr_ID'=>2,'Value'=>$sign));
            $attrbs_pay=\DB::table('payment_attr_value')->insert(array('History_ID'=>$hid,'Payment_Attr_ID'=>3,'Value'=>$oid));
            $updateHistory=\DB::table('payment_history')->where('Pay_ID',$hid)->update(array('Payment_Flag_Status'=>2));
            $api = new Api("rzp_test_58HZBwOLc2Uj8v", "TLkGTaJeLrEKcewBoyjg3YIb");
            $attributes  = array('razorpay_signature'  => $sign,  'razorpay_payment_id'  => $pid ,  'razorpay_order_id' => $oid);
$order  = $api->utility->verifyPaymentSignature($attributes);
return true;
        }
        catch(\Exception $e)
        {
return false;
        }
    }
}
