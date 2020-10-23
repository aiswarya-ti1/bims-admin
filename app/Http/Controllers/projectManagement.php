<?php

namespace App\Http\Controllers;
use Illuminate\Support\Arr;
//use Illuminate\Http\Request;
use Request;
use DateTime;

class projectManagement extends Controller
{
    public function workStart(Request $r)
    {
        $values = Request::json()->all();
        $start=\DB::table('work_timeline')->insert(array('Work_ID'=>$values['work_ID'], 'Work_Attrb_ID'=>24, 'Value'=>$values['startDate']));
        $statusChange=\DB::table('service_work')->where('Work_ID',$values['work_ID'])->update(array('WorkStatus'=>7));
        $resp=array("Success"=>true);
        return $resp;
    }

    public function saveWorkDate(Request $r)
    {
        $data = Request::json()->all();
        
      // $work=json_decode($values['param1'], true);
      
       // $data=json_decode($values['param2'], true);
        $newStartDate=new DateTime($data['ActualStart']);
        $newStartDate->modify('+1 day');
        $newCloseDate=new DateTime($data['ActualClose']);
        $newCloseDate->modify('+1 day');
if($data['ActualStart']!=null)
{
        $editWorkSched=\DB::table('work_schedule')->where('Work_Schedule_ID',$data['schd_ID'])
        ->update(array('ActualStart_Date'=> $newStartDate->format('Y-m-d'),'WorkSched_Flag'=>1));
}
if($data['ActualClose']!=null)
{
    $editWorkSched=\DB::table('work_schedule')->where('Work_Schedule_ID',$data['schd_ID'])
    ->update(array('ActualEnd_Date'=>$newCloseDate->format('Y-m-d')));
}
       
        $resp=array("Success"=>true);
        return $resp;
    }

public function savePayDate(Request $r)
{
    $values = Request::json()->all();
        
       $work=json_decode($values['param1'], true);
      
        $data=json_decode($values['param2'], true);
        $newPayDate=new DateTime($data['ActualPayDate']);
        $newPayDate->modify('+1 day');
if($data['ActualAmount']!=null)
{
   /* $mangmtFeePerc=\DB::table('service_work')->where('Work_ID',$work['Work_ID'])->pluck('Management_Fee_Perc');
    $mangmtFee=$data['ActualAmount']*$mangmtFeePerc[0]/100;
    $assocPayable=$data['ActualAmount']-$mangmtFee;

    
        $editPaySched=\DB::table('payment_schedule')->where('Pay_Schedule_ID',$work['Pay_Schedule_ID'])
        ->update(array('Actual_Amount'=> $data['ActualAmount'], 'Amount_Rec_Flag'=>1,'PayStatus_ID'=>1, 'Management_Fee'=>$mangmtFee,'Assoc_Payable_Amt'=>$assocPayable));*/
        $editPaySched=\DB::table('payment_schedule')->where('Pay_Schedule_ID',$work['Pay_Schedule_ID'])
        ->update(array('Actual_Amount'=> $data['ActualAmount'], 'Amount_Rec_Flag'=>1,'PayStatus_ID'=>1)); 
}
if($data['ActualPayDate']!=null)
{
    $editPaySched=\DB::table('payment_schedule')->where('Pay_Schedule_ID',$work['Pay_Schedule_ID'])
    ->update(array('Actual_Pay_Date'=>$newPayDate->format('Y-m-d')));
}
       
        $resp=array($editPaySched);
        return $resp;
}

public function savePRItemDetails(Request $r)
{
    $values = Request::json()->all();
        $pr_ID=\DB::table('purchase_request')->insertGetID(array('Work_ID'=>$values[0]['work_ID'] ));
        if(!empty($pr_ID))
        {
            foreach($values as $v)
            {
                $itemName=$v['item1'];
                $quantity=$v['qty1'];
                $unit=$v['unitID1'];
                $brand=$v['brand'];
                $spec=$v['spec'];
                $size=$v['size'];
                $delDate=$v['delivDate'];
                $items=\DB::table('purchase_request_items')->insert(array('Item_Name'=>$itemName, 
            'Quantity'=>$quantity, 'Unit_ID'=>$unit, 'PR_ID'=>$pr_ID, 'Brand'=>$brand, 
            'Size'=>$size, 'ItemSpec'=>$spec, 'Req_Deli_Date'=>$delDate));

            // $itemsList = explode('"', $v);
             //$itemName[$i]=(string)$itemsList[i];
            
             /*$segID=\DB::table('services')->where('Service_ID',$serID[$i])->pluck('Segment_ID');
             $seg=\DB::table('associate_segment_rate')->insert(array(
             'Assoc_ID' =>$assocID[0] ,
             'Segment_ID' => $segID[0],
             'Service_ID' =>  $serID[$i]));
              $items=\DB::table('purchase_request_items')->insertGetID(array('Item_Name'=>$values['item1'], 
            'Quantity'=>$values['qty1'], 'Unit_ID'=>$values['unitID1']));
             $i++;*/
            
     
            }
           
        }

   /* $itemID=\DB::table('purchase_request_items')->insertGetID(array('Item_Name'=>$values['item1'], 
    'Quantity'=>$values['qty1'], 'Unit_ID'=>$values['unitID1']));
    if(!empty($itemID))
    {
        $pr=\DB::table('purchase_request')->insert  (array('Work_ID'=>$values['work_ID'],'Item_ID'=>$itemID));
    }*/
    $resp=array("Success"=>true);
    return $resp;

}

public function getPRDetails($id)
{
    $pr=\DB::table('purchase_request')
    ->join('purchase_request_items', 'purchase_request_items.PR_ID','=','purchase_request.PR_ID')
    ->join('units', 'units.Unit_ID','=','purchase_request_items.Unit_ID')
    
    ->where('purchase_request.Work_ID', $id)->where('purchase_request_items.DeleteFlag',0)
    ->orderBy('purchase_request.PR_Date', 'Desc')->get();
    $resp=array($pr);
    return $resp;
}

public function getPRCount($id)
{
    $prDetails=\DB::table('purchase_request')->where('Work_ID', $id)->select('PR_ID')->get();
    $count=count($prDetails);
    $resp=array($count, $prDetails);
    return $resp;
}

public function getPRDetailsForPO()
{
    $pr=\DB::table('purchase_request')
    ->join('purchase_request_items', 'purchase_request_items.PR_ID','=','purchase_request.PR_ID')
    ->join('units', 'units.Unit_ID','=','purchase_request_items.Unit_ID')
    ->join('service_work','service_work.Work_ID','=','purchase_request.Work_ID')
    ->join('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')
    ->join('sales_customer', 'sales_customer.Customer_ID','=','sales_lead.Cust_ID')
   ->where('purchase_request_items.PO_Flag',0)->where('purchase_request_items.DeleteFlag',0)
    ->orderBy('purchase_request.PR_Date', 'Desc')->get();
    $resp=array($pr);
    return $resp;
}

public function getSuppliers()
{
    $suppliers=\DB::table('associate')->where('MaterialFlag', 1)->select('Assoc_ID', 'Assoc_FirstName', 'Assoc_MiddleName', 'Assoc_LastName')->get();
    $resp=array($suppliers);
    return $resp;
}

public function savePO(Request $r)
{
    $values = Request::json()->all();
    $data=$values['param1'];
    $items=$values['param2'];
    $data=json_decode($values['param1'], true);
      
    $items=json_decode($values['param2'], true);
   $PO_ID=\DB::table('purchase_order')->insertGetID(array('Work_ID'=>$data['work'], 'Assoc_ID'=>$data['supplier']));
    if(!empty($PO_ID))
    {
        foreach($items as $item)
        {
            $itemID=(int)$item;
            $updateItem=\DB::table('purchase_request_items')->where('Item_ID', $itemID)->update(array('PO_ID'=>$PO_ID, 'PO_Flag'=>1));

        }
        
    }

    $resp=array("Success"=>true);
    return $resp;

}

public function addSupplier(Request $r)
{
    $values = Request::json()->all();
    $aadressID=\DB::table('address')->insertGetID(array('Address_line1'=>$values['Address1'], 'Address_line2'=>$values['Address2'], 'Address_town'=>$values['City']));
    $contactID=\DB::table('contacts')->insertGetID(array('Contact_name'=>$values['Contact_Person'], 'Contact_whatsapp'=>$values['Whatsapp_Number'],'Contact_phone'=>$values['Contact_Number']));
    if(!empty($aadressID)&& !empty($contactID))
    {
        $supplier=\DB::table('associate')->insert(array('Assoc_FirstName'=>$values['FirstName'], 'Assoc_MiddleName'=>$values['MidName'], 'Assoc_LastName'=>$values['LastName'], 'Address_ID'=>$aadressID, 'Contact_ID'=>$contactID, 'MaterialFlag'=>1));

    }

    $resp=array($supplier);
    return $resp;
}

public function getPRDetailsForGoods($id)
{
    $pr=\DB::table('purchase_request')
    ->join('purchase_request_items', 'purchase_request_items.PR_ID','=','purchase_request.PR_ID')
    ->join('purchase_order','purchase_order.PO_ID','=','purchase_request_items.PO_ID' )
    ->join('units', 'units.Unit_ID','=','purchase_request_items.Unit_ID')
    ->join('service_work','service_work.Work_ID','=','purchase_request.Work_ID')
    ->join('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')
    ->join('sales_customer', 'sales_customer.Customer_ID','=','sales_lead.Cust_ID')
   ->where('purchase_request_items.PO_Flag',1)
   ->where('purchase_request_items.Goods_Flag',0)->where('purchase_request_items.DeleteFlag',0)
    ->orderBy('purchase_request.PR_Date', 'Desc')->get();
    $resp=array($pr);
    return $resp;
}

public function updateGoodsDate(Request $r)
{
    $values = Request::json()->all();
    $items=$values['itemid'];

    foreach($items as $item)
    {
        $goodsDate=\DB::table('purchase_request_items')->where('Item_ID', $item)
    ->update(array('Goods_Flag'=>1));

    }
    /*$perc=\DB::table('purchase_order')->where('PO_ID', $values['poid'])->pluck('Management_Fee_Perc');
    $managmentFee=$values['billAmt']*$perc[0]/100;
    $suppAmpount=$values['billAmt']-$managmentFee;*/
        $paymentHistory=\DB::table('supplier_payment_history')->insert(array('PO_ID'=>$values['poid'], 'Bill_No'=>$values['billNo'], 'Bill_Amount'=>$values['billAmt'],
        'Bill_Date'=>$values['goodsDate'],'PayStatus_ID'=>1));
    $resp=array($goodsDate);
    return $resp;
}

public function getItemDetails($id)
{
    $itemdetails=\DB::table('purchase_request_items')
    ->join('purchase_request', 'purchase_request.PR_ID','=','purchase_request_items.PR_ID')
    //->join('purchase_order', 'purchase_order.PO_ID', '=','purchase_request_items.PO_ID')
    ->join('units', 'units.Unit_ID','=','purchase_request_items.Unit_ID')
    ->where('Item_ID', $id)->get();
    $resp=array($itemdetails);
    return $resp;
}

public function editPRItemDetails(Request $r)
{
    $values = Request::json()->all();
    $amount=$values['qty1']*$values['rate'];
    $editItems=\DB::table('purchase_request_items')->where('Item_ID', $values['item_ID'])->update(array('Item_Name'=>$values['item1'],
    'Quantity'=>$values['qty1'],'Unit_ID'=>$values['unitID'], 'Rate'=>$values['rate'], 'Amount'=>$amount,'Brand'=>$values['brand'],
    'Size'=>$values['size'], 'ItemSpec'=>$values['spec'], 'Delivery_Loc'=>$values['deliLoc'], 'Remarks'=>$values['remarks']));
    $resp=array("Success"=>true);
    return $resp;
}

public function deletePR($id)
{
    $delete=\DB::table('purchase_request_items')->where('Item_ID', $id)->update(array('DeleteFlag'=>1));
    $resp=array("Success"=>true);
    return $resp;

}
public function getBIPRDetails()
{
    $pr=\DB::table('purchase_request')
    ->join('purchase_request_items', 'purchase_request_items.PR_ID','=','purchase_request.PR_ID')
    ->join('units', 'units.Unit_ID','=','purchase_request_items.Unit_ID')
    ->join('service_work','service_work.Work_ID','=','purchase_request.Work_ID')
    ->join('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')
    ->join('sales_customer', 'sales_customer.Customer_ID','=','sales_lead.Cust_ID')
    //->where('purchase_request_items.POFlag',0)
    ->where('purchase_request_items.DeleteFlag',0)
    ->orderBy('purchase_request.PR_Date', 'Desc')->get();
    $resp=array($pr);
    return $resp;
}

public function getOnGoingProjectsBI()
{
    $onGoingProjects=\DB::table('service_work')
		->join('sales_lead','sales_lead.Lead_ID','=','service_work.Lead_ID')
				->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
				->join('work_status','service_work.WorkStatus','=','work_status.Work_Status_ID')
				->where('service_work.WorkStatus',7)
		->get();
	
	
	$resp=array($onGoingProjects);
	return $resp;
}
public function getOnePRDetailsForPO($id)
{
    $pr=\DB::table('purchase_request')
    ->join('purchase_request_items', 'purchase_request_items.PR_ID','=','purchase_request.PR_ID')
    ->join('units', 'units.Unit_ID','=','purchase_request_items.Unit_ID')
    ->join('service_work','service_work.Work_ID','=','purchase_request.Work_ID')
    ->join('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')
    ->join('sales_customer', 'sales_customer.Customer_ID','=','sales_lead.Cust_ID')
    ->where('service_work.Work_ID', $id)
   ->where('purchase_request_items.PO_Flag',0)->where('purchase_request_items.DeleteFlag',0)
    ->orderBy('purchase_request.PR_Date', 'Desc')->get();
    $resp=array($pr);
    return $resp;
}

public function getContractorPayDetails()
{
 $contractorPayDetails=\DB::table('payment_schedule')
 ->join('service_work','service_work.Work_ID','=','payment_schedule.Work_ID')
    ->join('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')
    ->join('sales_customer', 'sales_customer.Customer_ID','=','sales_lead.Cust_ID')
    ->join('work_tendering','work_tendering.Work_ID','=','service_work.Work_ID')
    ->join('associate', 'associate.Assoc_ID','=','work_tendering.Assoc_ID')
    ->join('payment_status', 'payment_status.Pay_Status_ID','=','payment_schedule.PayStatus_ID')
    ->leftjoin('contractor_payment_history', 'contractor_payment_history.PaySched_ID','=','payment_schedule.Pay_Schedule_ID')
    ->where('Amount_Rec_Flag',1)
    ->orderBy('payment_schedule.Pay_Schedule_ID')->get();
 $resp=array($contractorPayDetails);
 return $resp;
}
public function approvePayment($id)
{
    $approval=\DB::table('payment_schedule')->where('Pay_Schedule_ID',$id)
    ->update(array('PayStatus_ID'=>2));
    $resp=array($approval);
    return $resp;
}
public function getApprovedPayDetails()
{
    $contractorPayDetails=\DB::table('payment_schedule')
 ->join('service_work','service_work.Work_ID','=','payment_schedule.Work_ID')
    ->join('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')
    ->join('sales_customer', 'sales_customer.Customer_ID','=','sales_lead.Cust_ID')
    ->join('work_tendering','work_tendering.Work_ID','=','service_work.Work_ID')
    ->join('associate', 'associate.Assoc_ID','=','work_tendering.Assoc_ID')
    ->join('payment_status', 'payment_status.Pay_Status_ID','=','payment_schedule.PayStatus_ID')
    ->leftjoin('contractor_payment_history', 'contractor_payment_history.PaySched_ID','=','payment_schedule.Pay_Schedule_ID')
    ->where('Amount_Rec_Flag',1)
    ->where('payment_schedule.PayStatus_ID','!=',1)
    ->orderBy('payment_schedule.Pay_Schedule_ID')->get();
 $resp=array($contractorPayDetails);
 return $resp;
}

public function getOneContractorPayDetails($id)
{
    $contractorPayDetails=\DB::table('payment_schedule')
    ->join('service_work','service_work.Work_ID','=','payment_schedule.Work_ID')
       ->join('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')
       ->join('sales_customer', 'sales_customer.Customer_ID','=','sales_lead.Cust_ID')
       ->join('work_tendering','work_tendering.Work_ID','=','service_work.Work_ID')
       ->join('associate', 'associate.Assoc_ID','=','work_tendering.Assoc_ID')
       ->join('payment_status', 'payment_status.Pay_Status_ID','=','payment_schedule.PayStatus_ID')
       ->leftjoin('contractor_payment_history', 'contractor_payment_history.PaySched_ID','=','payment_schedule.Pay_Schedule_ID')
       ->where('payment_schedule.Pay_Schedule_ID',$id)
      
       ->orderBy('payment_schedule.Pay_Schedule_ID')->get();
    $resp=array($contractorPayDetails);
    return $resp;
}

public function updateContractorPaymentDetails(Request $r)
{
    $values = Request::json()->all();
    $paidDate=new DateTime($values['paidDate']);
    $paidDate->modify('+1 day');
   if($values['typeID']==1)
   {
    $payment=\DB::table('contractor_payment_history') ->insert(array('PaySched_ID'=>$values['payID'],'Type'=>$values['type'],
    'Paid_Amt'=>$values['paidAmt'],'Transaction_Type'=>$values['tranType'],'Paid_Date'=>$paidDate->format('Y-m-d'),
    'Trans_ID'=>$values['tranID'], 'MFee_Flag'=>1));
    


    
   }
   else if($values['typeID']==2)
   {
    $payment=\DB::table('contractor_payment_history') ->insert(array('PaySched_ID'=>$values['payID'],'Type'=>$values['type'],
    'Paid_Amt'=>$values['paidAmt'],'Transaction_Type'=>$values['tranType'],'Paid_Date'=>$paidDate->format('Y-m-d'),
    'Trans_ID'=>$values['tranID'], 'AssocFee_Flag'=>1));
    $update=\DB::table('payment_schedule')->where('Pay_Schedule_ID',$values['payID'])->update(array('PayStatus_ID'=>3));
   }
   //$chkMFeePaidStatus=\DB::table('initiate_payment')->where('PaySched_ID',$values['payID'])
   
    $resp=array($payment);
    return $resp;
}

public function getWorkPOList($id)
{
    $poid=\DB::table('purchase_order')->where('Work_ID', $id)->get();
    $resp=array($poid);
    return $resp;
}
public function getPOItemList($id)
{
    $items=\DB::table('purchase_request_items')
    ->join('purchase_order','purchase_order.PO_ID','=','purchase_request_items.PO_ID')
    ->join('units', 'units.Unit_ID','=','purchase_request_items.Unit_ID')
    ->join('associate', 'associate.Assoc_ID','=','purchase_order.Assoc_ID')
    ->where('purchase_request_items.Goods_Flag',0)
    ->where('purchase_order.PO_ID', $id)->get();
    $resp=array($items);
    return $resp;
}

public function getSupplierPayDetails()
{
    $supplierPayDetails=\DB::table('supplier_payment_history')
    ->join('purchase_order', 'purchase_order.PO_ID','=','supplier_payment_history.PO_ID')
    ->join('service_work', 'service_work.Work_ID','=','purchase_order.Work_ID')
     ->join('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')
    ->join('sales_customer', 'sales_customer.Customer_ID','=','sales_lead.Cust_ID')
    
    ->join('associate', 'associate.Assoc_ID','=','purchase_order.Assoc_ID')
    ->join('payment_status', 'payment_status.Pay_Status_ID','=','supplier_payment_history.PayStatus_ID')
    
    ->where('supplier_payment_history.Approve_Flag',0)
   // ->orderBy('payment_schedule.Pay_Schedule_ID')
    ->get();
 $resp=array($supplierPayDetails);
 return $resp;
}

public function approveSuppPayment($id)
{
    $approval=\DB::table('supplier_payment_history')->where('PayHistory_ID', $id)
    ->update(array('Approve_Flag'=>1, 'PayStatus_ID'=>2));
    $resp=array($approval);
    return $resp;
}
public function getApprovedSuppPayDetails()
{
    $supplierPayDetails=\DB::table('supplier_payment_history')
    ->join('purchase_order', 'purchase_order.PO_ID','=','supplier_payment_history.PO_ID')
    ->join('service_work', 'service_work.Work_ID','=','purchase_order.Work_ID')
     ->join('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')
    ->join('sales_customer', 'sales_customer.Customer_ID','=','sales_lead.Cust_ID')
    
    ->join('associate', 'associate.Assoc_ID','=','purchase_order.Assoc_ID')
    ->join('payment_status', 'payment_status.Pay_Status_ID','=','supplier_payment_history.PayStatus_ID')
    
    //->where('supplier_payment_history.Approve_Flag',1)
   // ->where('supplier_payment_history.PayStatus_ID','!=',1)
    
   // ->orderBy('payment_schedule.Pay_Schedule_ID')
    ->get();
 $resp=array($supplierPayDetails);
 return $resp;
}

public function updateSupplierPaymentDetails(Request $r)
{
    $values = Request::json()->all();
   if($values['typeID']==1)
   {
    $payment=\DB::table('supplier_payment_history')->where('PayHistory_ID',$values['payID']) ->update(array('Type'=>$values['type'],
    'Paid_Supp_Amount'=>$values['paidAmt'],'Trans_Type'=>$values['tranType'],'Paid_Date'=>$values['paidDate'],
    'Trans_ID'=>$values['tranID'], 'MagmntFee_Flag'=>1));
    


    
   }
   else if($values['typeID']==2)
   {
    $payment=\DB::table('supplier_payment_history')->where('PayHistory_ID',$values['payID'])->update(array('Type'=>$values['type'],
    'Paid_Supp_Amount'=>$values['paidAmt'],'Trans_Type'=>$values['tranType'],'Paid_Date'=>$values['paidDate'],
    'Trans_ID'=>$values['tranID'], 'AssocFee_Flag'=>1,'PayStatus_ID'=>3));
    //$update=\DB::table('payment_schedule')->where('Pay_Schedule_ID',$values['payID'])->update(array('PayStatus_ID'=>3));
   }
   
    $resp=array($payment);
    return $resp;
}
//-----Payment Initiation-------
public function initiatePayment(Request $r)
{
    $values=Request::json()->all();
    $reqDate=new DateTime($values['reqDate']);
    $reqDate->modify('+1 day');
    $chkLimit=\DB::table('work_timeline')->where('Work_ID',$values['workID'])->where('Work_Attrb_ID',38)->pluck('Value');
    if($chkLimit->count()!=0)
    {

    if($chkLimit[0] ==1)
    {
    $totalRec=\DB::table('payment_schedule')
    ->join('split_payments','payment_schedule.Pay_Schedule_ID','=','split_payments.Pay_ID' )
    ->where('Work_ID', $values['workID'])
  ->sum('split_payments.Split_Amount');
  
   $totalInit=\DB::table('initiate_payment')->where('Work_ID', $values['workID'])->where('DeleteFlag',0)->sum('ReqAmount');
   $existsPayment=$totalRec-$totalInit;
   if($values['reqAmt'] > $existsPayment)
   {
       $resp=array("Error"=>true, "Amount"=>$existsPayment);
       return $resp;
   }
   else{

    if($values['typeID']==1)
    {
    $initiate=\DB::table('initiate_payment')->insert(array('Work_ID'=>$values['workID'],
     'ReqAmount'=>$values['reqAmt'], 'ReqDate'=>$reqDate->format('Y-m-d'),
    'PayStatus_ID'=>1,'Comments'=>$values['comment'], 'Trans_Type'=>$values['type']));
    $resp=array($initiate);
    return $resp;
    }
    else if($values['typeID']==2)
    {
        $edit=\DB::table('initiate_payment')
        ->where('Work_ID', $values['workID'])
        ->where('InitPay_ID', $values['payID'])
        ->update(array('ReqAmount'=>$values['reqAmt'], 'ReqDate'=>$reqDate->format('Y-m-d'),'Trans_Type'=>$values['type'],'Comments'=>$values['comment']));
        $resp=array($edit);
    return $resp;
    }
}
}
    
else if($chkLimit[0]==0)
{
    if($values['typeID']==1)
    {
    $initiate=\DB::table('initiate_payment')->insert(array('Work_ID'=>$values['workID'],
     'ReqAmount'=>$values['reqAmt'], 'ReqDate'=>$reqDate->format('Y-m-d'),
    'PayStatus_ID'=>1,'Comments'=>$values['comment'], 'Trans_Type'=>$values['type']));
    $resp=array($initiate);
    return $resp;
    }
    else if($values['typeID']==2)
    {
        $edit=\DB::table('initiate_payment')
        ->where('Work_ID', $values['workID'])
        ->where('InitPay_ID', $values['payID'])
        ->update(array('ReqAmount'=>$values['reqAmt'], 'ReqDate'=>$reqDate->format('Y-m-d'),'Trans_Type'=>$values['type'],'Comments'=>$values['comment']));
        $resp=array($edit);
    return $resp;
    }
}
    }
    else{
        if($values['typeID']==1)
    {
    $initiate=\DB::table('initiate_payment')->insert(array('Work_ID'=>$values['workID'],
     'ReqAmount'=>$values['reqAmt'], 'ReqDate'=>$reqDate->format('Y-m-d'),
    'PayStatus_ID'=>1,'Comments'=>$values['comment'], 'Trans_Type'=>$values['type']));
    $resp=array($initiate);
    return $resp;
    }
    else if($values['typeID']==2)
    {
        $edit=\DB::table('initiate_payment')
        ->where('Work_ID', $values['workID'])
        ->where('InitPay_ID', $values['payID'])
        ->update(array('ReqAmount'=>$values['reqAmt'], 'ReqDate'=>$reqDate->format('Y-m-d'),'Trans_Type'=>$values['type'],'Comments'=>$values['comment']));
        $resp=array($edit);
    return $resp;
    }

$resp=array("Empty Chk");
    return $resp;
    }
}

public function getInitiatePayDetails($id)
{
$details=\DB::table('initiate_payment')
->join('payment_status','payment_status.Pay_Status_ID','=','initiate_payment.PayStatus_ID')
->where('Work_ID', $id)
  ->where('DeleteFlag',0)
->orderBy('initiate_payment.ReqDate','DESC')->get();
$resp=array($details);
return $resp;
}
public function getOneInitiatePay($id)
{
    $onePayment=\DB::table('initiate_payment')
    ->where('InitPay_ID', $id)->get();
    $resp=array($onePayment);
    return $resp;
}

public function getAllInitiatePayDetails()
{
    $details=\DB::table('initiate_payment')
    ->join('payment_status','payment_status.Pay_Status_ID','=','initiate_payment.PayStatus_ID')
    ->join('service_work', 'service_work.Work_ID','=','initiate_payment.Work_ID')
    ->join('work_tendering','work_tendering.Work_ID','=','initiate_payment.Work_ID')
     ->join('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')
    ->join('sales_customer', 'sales_customer.Customer_ID','=','sales_lead.Cust_ID') 
    ->join('associate', 'associate.Assoc_ID','=','work_tendering.Assoc_ID')
   ->join('address','address.Address_ID','=','associate.Address_ID')
   ->join('contacts','contacts.Contact_ID','=','associate.Contact_ID')
   
    ->where('work_tendering.SelectStatus',1)
    ->where('initiate_payment.PayStatus_ID','!=',3)
    ->where('initiate_payment.DeleteFlag',0)
    ->orderBy('initiate_payment.ReqDate', 'DESC')
    ->select('initiate_payment.*','sales_customer.*', 'payment_status.*',  'associate.*', 'address.*','contacts.Contact_phone')
    ->get();
   /* $values=array();
    $newArray=json_decode($details);
    foreach($newArray as $detail=>$d)
    {
        $t=$d;
        $Flag=$t->MFee_Flag;
        if($Flag==0)
        {
           $t->NewStatus= 'Unpaid';
           $newArray['detail']= $t;
        }
        else if($Flag==1)
        {
            $t->NewStatus= 'Paid';
            $newArray['detail']= $t;
        }
        
   

    }
   

    $encodedSku = json_encode($newArray);*/
    
       

    
$resp=array($details);
return $resp;
}

public function updateMFee(Request $r)
{ 
    $values=Request::json()->all();
    $mFee= $values['amount']*$values['perc']/100;
   $assocPay=$values['amount']-$mFee;
    if($values['type']==1)
    {
    $mfee=\DB::table('initiate_payment')->where('InitPay_ID', $values['payID'])
    ->update(array('MFee_Perc'=>$values['perc'], 'MFee'=>$mFee, 'AssocPay'=>$assocPay, 'PayStatus_ID'=>4));
    }
    else if($values['type']==2)
    {
        $mfee=\DB::table('supplier_payment_history')->where('PayHistory_ID', $values['payID'])
        ->update(array('MFee_Perc'=>$values['perc'], 'Management_Fee'=>$mFee, 'Supplier_Amount'=>$values['amount'], 'PayStatus_ID'=>4));
    }
    $resp=array($mfee);
    return $resp;
}

public function approveInitPay(Request $r)
{
    $values=Request::json()->all();
    $editStatus=\DB::table('initiate_payment')->where('InitPay_ID', $values[0])
    ->update(array('PayStatus_ID'=>2));
    $MFee=\DB::table('initiate_payment')->where('InitPay_ID', $values[0])->pluck('MFee');
    $AssocPay=\DB::table('initiate_payment')->where('InitPay_ID', $values[0])->pluck('AssocPay');
    if($MFee[0]==0)
    {
$update=\DB::table('initiate_payment')->where('InitPay_ID', $values[0])
->update(array('MFee_Flag'=>1));
    }
    if($AssocPay[0]==0)
    {
        $updateAssoc=\DB::table('initiate_payment')->where('InitPay_ID', $values[0])
        ->update(array('AssocPay_Flag'=>1));  
    }
    $resp=array($editStatus);
    return $resp;
}
public function getOneContractorInitPayDetails($id)
{
    $contractorPayDetails=\DB::table('initiate_payment')
    ->join('service_work','service_work.Work_ID','=','initiate_payment.Work_ID')
       ->join('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')
       ->join('sales_customer', 'sales_customer.Customer_ID','=','sales_lead.Cust_ID')
       ->join('work_tendering','work_tendering.Work_ID','=','service_work.Work_ID')
       ->join('associate', 'associate.Assoc_ID','=','work_tendering.Assoc_ID')
       ->join('payment_status', 'payment_status.Pay_Status_ID','=','initiate_payment.PayStatus_ID')
       ->where('work_tendering.SelectStatus',1)
       ->where('initiate_payment.InitPay_ID',$id)
       ->where('initiate_payment.DeleteFlag',0)
       ->orderBy('initiate_payment.InitPay_ID')->get();
    $resp=array($contractorPayDetails);
    return $resp;
}
public function updateContractorInitPaymentDetails(Request $r)
{
    $values = Request::json()->all();
    

   if($values['typeID']==1)
   {
    $paidDate=new DateTime($values['paidDate']);
    $paidDate->modify('+1 day');
    $payment=\DB::table('initiate_payment')->where('InitPay_ID', $values['payID']) ->update(array('Paid_MFee'=>$values['paidAmt'],'Trans_Type'=>$values['tranType'],'M_PaidDate'=>$paidDate->format('Y-m-d'),
    'Trans_ID'=>$values['tranID'], 'MFee_Flag'=>1, 'MFee_Comments'=>$values['comments']));
    


    
   }
   else if($values['typeID']==2)
   {
    $paidDate=new DateTime($values['paidDate']);
    $paidDate->modify('+1 day');
    $payment=\DB::table('initiate_payment')->where('InitPay_ID', $values['payID']) ->update(array('Paid_AssocPay'=>$values['paidAmt'],'Assoc_Trans_Type'=>$values['tranType'],
    'AssocPay_Date'=>$paidDate->format('Y-m-d'),
    'Assoc_Trans_ID'=>$values['tranID'], 'AssocPay_Flag'=>1, 'Assoc_Comments'=>$values['comments']));
   
   }
   //$chkPaidStatus=\DB::table('initiate_payment')->where('InitPay_ID', $values['payID'])
   //->select('MFee_Flag','AssocPay_Flag')->get();
   $chkPaidStatus=\DB::table('initiate_payment')->where('InitPay_ID', $values['payID'])
   ->pluck('MFee_Flag');
   $chkAssocStatus=\DB::table('initiate_payment')->where('InitPay_ID', $values['payID'])->pluck('AssocPay_Flag');
   if($chkPaidStatus[0]==1 && $chkAssocStatus[0]==1)
   {
       $update=\DB::table('initiate_payment')->where('InitPay_ID', $values['payID'])->update(array('PayStatus_ID'=>3));
      
   }
   $resp=array($update);
   return $resp;
  
}
public function chkPaidStatus($id)
{
   
}

//Material Payment
      
public function getAllSupplPayDetails()
{
    $supplierPayDetails=\DB::table('supplier_payment_history')
    ->join('purchase_order', 'purchase_order.PO_ID','=','supplier_payment_history.PO_ID')
    ->join('service_work', 'service_work.Work_ID','=','purchase_order.Work_ID')
     ->join('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')
    ->join('sales_customer', 'sales_customer.Customer_ID','=','sales_lead.Cust_ID')
    
    ->join('associate', 'associate.Assoc_ID','=','purchase_order.Assoc_ID')
    ->join('payment_status', 'payment_status.Pay_Status_ID','=','supplier_payment_history.PayStatus_ID')
  ->orderBy('supplier_payment_history.PayHistory_ID', 'desc')
    ->get();
 $resp=array($supplierPayDetails);
 return $resp;
}
public function getOneSupplierPayDetails($id)
{
    $onePayment=\DB::table('supplier_payment_history')
    ->join('purchase_order', 'purchase_order.PO_ID','=','supplier_payment_history.PO_ID')
    ->join('service_work', 'service_work.Work_ID','=','purchase_order.Work_ID')
     ->join('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')
    ->join('sales_customer', 'sales_customer.Customer_ID','=','sales_lead.Cust_ID')
    
    ->join('associate', 'associate.Assoc_ID','=','purchase_order.Assoc_ID')
    ->join('payment_status', 'payment_status.Pay_Status_ID','=','supplier_payment_history.PayStatus_ID')
    ->where('PayHistory_ID', $id)->get();
    $resp=array($onePayment);
    return $resp;
}
public function getTotalAmendItems($id, $no)
{
    
        $total=\DB::table('work_labour_estimation')->where('Work_ID', $id)->where('deleteFlag',0)->where('FinishAmend_Flag',1)
        ->where('Amend_Flag',$no)->sum('value');
    
    $resp=array($total);
    return $resp;
}
public function getTotalAmend1Items($id, $no)
{
    
        $total=\DB::table('work_labour_estimation')->where('Work_ID', $id)->where('deleteFlag',0)->where('FinishAmend_Flag',1)
        ->where('Amend_Flag',1)->sum('value');
    
    $resp=array($total);
    return $resp;
}
public function getReTotalAmend1Items($id, $no)
{
    $reTotal=\DB::table('work_labour_estimation')->where('Work_ID', $id)->where('deleteFlag',0)->where('FinishAmend_Flag',1)
    ->where('Amend_Flag',1)->where('reMeasure_Flag',1)->sum('ReMeasure_Value');
    
        $total=\DB::table('work_labour_estimation')->where('Work_ID', $id)->where('deleteFlag',0)->where('FinishAmend_Flag',1)
        ->where('Amend_Flag',1)->where('reMeasure_Flag',0)->sum('Value');
        $GTotal=$reTotal+$total;

    
    $resp=array($GTotal);
    return $resp;
}
public function getTotalAmend2Items($id, $no)
{
    
        $total=\DB::table('work_labour_estimation')->where('Work_ID', $id)->where('deleteFlag',0)->where('FinishAmend_Flag',1)
        ->where('Amend_Flag',2)->sum('value');
    
    $resp=array($total);
    return $resp;
}
public function getTotalAmendedItems($id, $no)
{
    
        $total=\DB::table('work_labour_estimation')->where('Work_ID', $id)->where('deleteFlag',0)->where('FinishAmend_Flag',1)
        ->where('Amend_Flag',$no)->sum('value');
    
    $resp=array($total);
    return $resp;
}

public function getReTotalAmend2Items($id, $no)
{
    
    $reTotal=\DB::table('work_labour_estimation')->where('Work_ID', $id)->where('deleteFlag',0)->where('FinishAmend_Flag',1)
    ->where('Amend_Flag',2)->where('reMeasure_Flag',1)->sum('ReMeasure_Value');
    
        $total=\DB::table('work_labour_estimation')->where('Work_ID', $id)->where('deleteFlag',0)->where('FinishAmend_Flag',1)
        ->where('Amend_Flag',2)->where('reMeasure_Flag',0)->sum('Value');
        $GTotal=$reTotal+$total;

    
    $resp=array($GTotal);
    return $resp;
}
public function getReTotalAmendItems($id, $no)
{
    
    $reTotal=\DB::table('work_labour_estimation')->where('Work_ID', $id)->where('deleteFlag',0)->where('FinishAmend_Flag',1)
    ->where('Amend_Flag',$no)->where('reMeasure_Flag',1)->sum('ReMeasure_Value');
    
        $total=\DB::table('work_labour_estimation')->where('Work_ID', $id)->where('deleteFlag',0)->where('FinishAmend_Flag',1)
        ->where('Amend_Flag',$no)->where('reMeasure_Flag',0)->sum('Value');
        $GTotal=$reTotal+$total;

    
    $resp=array($GTotal);
    return $resp;
}

public function getTotalAmend3Items($id, $no)
{
    
        $total=\DB::table('work_labour_estimation')->where('Work_ID', $id)->where('deleteFlag',0)->where('FinishAmend_Flag',1)
        ->where('Amend_Flag',3)->sum('value');
    
    $resp=array($total);
    return $resp;
}
public function getReTotalAmend3Items($id, $no)
{
    
    $reTotal=\DB::table('work_labour_estimation')->where('Work_ID', $id)->where('deleteFlag',0)->where('FinishAmend_Flag',1)
    ->where('Amend_Flag',3)->where('reMeasure_Flag',1)->sum('ReMeasure_Value');
    
        $total=\DB::table('work_labour_estimation')->where('Work_ID', $id)->where('deleteFlag',0)->where('FinishAmend_Flag',1)
        ->where('Amend_Flag',3)->where('reMeasure_Flag',0)->sum('Value');
        $GTotal=$reTotal+$total;

    
    $resp=array($GTotal);
    return $resp;
}


public function getAmendWorkSchedule($id, $no)
{
    $amendWSched=\DB::table('work_schedule')->where('Work_ID',$id)->where('Amend_Flag',$no)->where('DeleteFlag',0)->where('FinishAmend_Flag',0)->get();
    $resp=array($amendWSched);
    return $resp;
}
public function getAmendedWorkSchedule($id, $no)
{
    $amendWSched=\DB::table('work_schedule')->where('Work_ID',$id)->where('Amend_Flag',$no)->where('DeleteFlag',0)->where('FinishAmend_Flag',1)->get();
    $resp=array($amendWSched);
    return $resp;
}
public function getAmendPaySchedule($id, $no)
{
    $amendPSched=\DB::table('payment_schedule')->where('Work_ID', $id)->where('Amend_Flag', $no)->where('DeleteFlag',0)->where('FinishAmend_Flag',0)->get();
    $resp=array($amendPSched);
    return $resp;
}
public function getAmendedPaySchedule($id, $no)
{
    $amendPSched=\DB::table('payment_schedule')->where('Work_ID', $id)->where('Amend_Flag', $no)->where('DeleteFlag',0)->where('FinishAmend_Flag',1)->get();
    $resp=array($amendPSched);
    return $resp;
}
public function getAmendKeyDeliverables($id, $no)
{
    $amendKeys=\DB::table('wo_key_deliverables')->where('Work_ID', $id)->where('Amend_Flag',$no)->where('FinishAmend_Flag',0)
    ->where('Delete_Flag',0)
    ->join('key_deliverables','key_deliverables.Key_ID','=','wo_key_deliverables.Key_ID')->get();
    $resp=array($amendKeys);
    return $resp;
}
public function getAmendedKeyDeliverables($id, $no)
{
    $amendKeys=\DB::table('wo_key_deliverables')->where('Work_ID', $id)->where('Amend_Flag',$no)
    ->where('FinishAmend_Flag',1)->where('Delete_Flag',0)
    ->join('key_deliverables','key_deliverables.Key_ID','=','wo_key_deliverables.Key_ID')->get();
    $resp=array($amendKeys);
    return $resp;
}
public function getAmendTerms($id, $no)
{
    $amendTerms=\DB::table('wo_terms_conditions')
		->join('terms_conditions','terms_conditions.Term_ID','=','wo_terms_conditions.Term_ID')
        ->where('Work_ID', $id)->where('Amend_Flag',$no)->where('Delete_Flag',0)
        ->where('wo_terms_conditions.Tender_Flag',0)
        ->where('FinishAmend_Flag',0)->get();
		$resp=array($amendTerms);
		return $resp;
}
public function getAmendedTerms($id, $no)
{
    $amendTerms=\DB::table('wo_terms_conditions')
		->join('terms_conditions','terms_conditions.Term_ID','=','wo_terms_conditions.Term_ID')
        ->where('Work_ID', $id)
        ->where('Amend_Flag',$no)->where('Delete_Flag',0)
       ->where('wo_terms_conditions.Tender_Flag',0)
       ->where('FinishAmend_Flag',1)
        ->get();
		$resp=array($amendTerms);
		return $resp;
}
public function getAmendedTermsNew($id, $no)
{
    $terms=\DB::table('wo_terms_conditions')->where('Work_ID', $id)->get();
    $resp=array($terms);
    return $resp;
}

public function finishAmend(Request $r)
{
    \DB::transaction(function() use ($r) {
    $values = Request::json()->all();
    $amendNo=$values['amend_Flag'];
    
    if($values['est_Flag']==1)
    {
        $estAmend=\DB::table('work_amendment')->where('Work_ID',$values['work_ID'])->pluck('Estimation_Amend_Flag');
        $estFlag=$estAmend[0]+1;
        $insert=\DB::table('work_amendment')->where('Work_ID',$values['work_ID'])->insert(array('Estimation_Amend_Flag'=>$estFlag, 'Est_Amend_Total'=>$values['totalEst']));
        $finishEst=\DB::table('work_labour_estimation')->where('Work_ID',$values['work_ID'])->where('Amend_Flag',$amendNo)->update(array('FinishAmend_Flag'=>1));
        $timeline=\DB::table('work_timeline')->insert(array('Work_ID'=>$values['work_ID'],'Work_Attrb_ID'=>28, 'Value'=>$values['totalEst']));
        $workAmend=\DB::table('service_work')->where('Work_ID',$values['work_ID'])->update(array('Amend_Flag'=>$estFlag));
    }
    if($values['work_Flag']==1)
    {
        $workAmend=\DB::table('work_amendment')->where('Work_ID',$values['work_ID'])->pluck('WorkSched_Amend_Flag');
        $workFlag=$workAmend[0]+1;
        $insertW=\DB::table('work_amendment')->where('Work_ID',$values['work_ID'])->update(array('WorkSched_Amend_Flag'=>$workFlag));
        $finishWSched=\DB::table('work_schedule')->where('Work_ID',$values['work_ID'])->where('Amend_Flag',$amendNo)->update(array('FinishAmend_Flag'=>1));
    }
    if($values['pay_Flag']==1)
    {
        $payAmend=\DB::table('work_amendment')->where('Work_ID',$values['work_ID'])->pluck('PaySched_Amend_Flag');
        $payFlag=$payAmend[0]+1;
        $insertP=\DB::table('work_amendment')->where('Work_ID',$values['work_ID'])->update(array('PaySched_Amend_Flag'=>$payFlag));
        $finishPSched=\DB::table('payment_schedule')->where('Work_ID',$values['work_ID'])->where('Amend_Flag',$amendNo)->update(array('FinishAmend_Flag'=>1));
    }
    if($values['key_Flag']==1)
    {
        $keyAmend=\DB::table('work_amendment')->where('Work_ID',$values['work_ID'])->pluck('KeyDeliv_Amend_Flag');
        $keyFlag=$keyAmend[0]+1;
        $insertK=\DB::table('work_amendment')->where('Work_ID',$values['work_ID'])->update(array('KeyDeliv_Amend_Flag'=>$keyFlag));
        $finishKDel=\DB::table('wo_key_deliverables')->where('Work_ID',$values['work_ID'])->where('Amend_Flag',$amendNo)->update(array('FinishAmend_Flag'=>1));
    }
    if($values['terms_Flag']==1)
    {
        $termsAmend=\DB::table('work_amendment')->where('Work_ID',$values['work_ID'])->pluck('Terms_Amend_Flag');
        $termFlag=$termsAmend[0]+1;
        $insertT=\DB::table('work_amendment')->where('Work_ID',$values['work_ID'])->update(array('Terms_Amend_Flag'=>$termFlag));
        $finishTerms=\DB::table('wo_terms_conditions')->where('Work_ID',$values['work_ID'])->where('Amend_Flag',$amendNo)->update(array('FinishAmend_Flag'=>1));
    }
   
    $amendValue=\DB::table('work_timeline')->where('Work_ID', $values['work_ID'])->where('Work_Attrb_ID', 28)->pluck('Value');
    $IntAmend=(int)$amendValue[0];
    $tenderValue=\DB::table('work_tendering')->where('Work_ID',$values['work_ID'])->pluck('TotalQuote');
    $GrandTotal=$IntAmend+$tenderValue[0];
    $totalInsert=\DB::table('work_timeline')->insert(array('Work_ID'=>$values['work_ID'], 'Work_Attrb_ID'=>29, 'Value'=>$GrandTotal));
    $resp=array("Success"=>true);
    return $resp;
});
}
public function finishAmendment($wid, $aid)
{
    
    $finishEst=\DB::table('work_labour_estimation')->where('Work_ID',$wid)->where('Amend_Flag',$aid)->update(array('FinishAmend_Flag'=>1));
    $workAmend=\DB::table('service_work')->where('Work_ID',$wid)->update(array('Amend_Flag'=>$aid));
    $finishWSched=\DB::table('work_schedule')->where('Work_ID',$wid)->where('Amend_Flag',$aid)->update(array('FinishAmend_Flag'=>1));
    $finishPSched=\DB::table('payment_schedule')->where('Work_ID',$wid)->where('Amend_Flag',$aid)->update(array('FinishAmend_Flag'=>1));
     $finishKDel=\DB::table('wo_key_deliverables')->where('Work_ID',$wid)->where('Amend_Flag',$aid)->update(array('FinishAmend_Flag'=>1));
     $finishTerms=\DB::table('wo_terms_conditions')->where('Work_ID',$wid)->where('Amend_Flag',$aid)->update(array('FinishAmend_Flag'=>1));
$totalEst=\DB::table('work_labour_estimation')->where('Work_ID',$wid)->where('Amend_Flag',$aid)->where('FinishAmend_Flag',1)->where('deleteFlag',0)
->select(\DB::raw('SUM(Value) as Sum'))->get();
    $insert=\DB::table('work_amendment')->where('Work_ID',$wid)
     ->update(array('Estimation_Amend_Flag'=>$aid,'WorkSched_Amend_Flag'=>$aid,'PaySched_Amend_Flag'=>$aid,'KeyDeliv_Amend_Flag'=>$aid,'Terms_Amend_Flag'=>$aid, 'Est_Amend_Total'=>$totalEst[0]->Sum));
     $totalInsert=\DB::table('work_amendment_issuedate')->where('Work_ID', $wid)->where('Amend_No',$aid)->update(array('Total'=>$totalEst[0]->Sum));

    $resp=array("Success"=>true, $totalInsert);
    return $resp;
    
}

public function getGrandTotal($id)
{
    $amendTotal=\DB::table('work_labour_estimation')->where('Work_ID', $id)->where('Amend_Flag','!=',0)->where('deleteFlag',0)->sum('Value');
    $IntAmend=(int)$amendTotal[0];
        $tenderTotal=\DB::table('work_tendering')->where('Work_ID', $id)->where('SelectStatus',1)->pluck('TotalQuote');
        $total=$IntAmend+$tenderTotal[0];
        //$grandTotal=\DB::table('work_timeline')->inse;
        $resp=array($total);
        return $resp;
}
public function saveReMeasureDetails(Request $r)
{
    \DB::transaction(function() use ($r) {
    $values = Request::json()->all();
   // $exists=\DB::table('work_remeasure')->where('Work_ID',$values['work_ID'])->where('Item_ID',)
    //$amendFlag=\DB::table('work_labour_estimation')->where('LE_ID',$values['LE_ID'])->pluck('Amend_Flag');
    if($values['type_ID']==1)
    {
        //$TID=\DB::table('work_tendering')->where('Work_ID',$values['work_ID'])->pluck('WorkTender_ID');
        if($values['rate_Flag']==0)
        {
            $rate=\DB::table('work_tender_details_lab')->where('WorkTenderLab_ID',$values['T_ID'])
            ->pluck('Rate');
            $reValue=$rate[0]*$values['qty'];
            $reMeasure=\DB::table('work_tender_details_lab')->where('WorkTenderLab_ID',$values['T_ID'])
            ->update(array('reMeasure_Flag'=>1,'reMeasure_Qty'=>$values['qty'],'reMeasure_Rate'=>$rate[0]
, 'reMeasure_Value'=>$reValue,'Re_Comments'=>$values['cmnts']));
        }
        else if($values['rate_Flag']==1)
        {
            $newRate=$values['rate'];
            $reValue=$newRate*$values['qty'];
            $reMeasure=\DB::table('work_tender_details_lab')->where('WorkTenderLab_ID',$values['T_ID'])
            ->update(array('reMeasure_Flag'=>1,'reMeasure_Qty'=>$values['qty'],'reMeasure_Rate'=>$newRate
, 'reMeasure_Value'=>$reValue,'Re_RateFlag'=>1,'Re_Comments'=>$values['cmnts']));
        }
        $TID=\DB::table('work_tendering')->where('Work_ID',$values['work_ID'])->where('SelectStatus',1)->pluck('WorkTender_ID');
        $ReItems=\DB::table('work_tender_details_lab')->where('WorkTender_ID', $TID[0])->where('reMeasure_Flag',1)->get();
        $ReItemTotal=\DB::table('work_tender_details_lab')->where('WorkTender_ID', $TID[0])->where('reMeasure_Flag',1)
        ->sum('reMeasure_Value');
        $ReNotItemTotal=\DB::table('work_tender_details_lab')->where('WorkTender_ID', $TID[0])->where('reMeasure_Flag',0)
        ->where('deleteFlag',0)->sum('Value');
        if($ReItemTotal!=0)
        {
            $TotalAfterRe=$ReNotItemTotal+$ReItemTotal;
        }
       
        $resp=array("Success"=>true, 'Total'=>$TotalAfterRe);
        return $resp;
    }
    else if($values['type_ID']==2)
    {
        if($values['rate_Flag']==0)
        {
            $rate=\DB::table('work_labour_estimation')
            //->where('Amend_Flag',1)
            ->where('LE_ID',$values['LE_ID'])
            ->pluck('Rate');
            $reValue=$rate[0]*$values['qty'];
            $reMeasure=\DB::table('work_labour_estimation')
            //->where('Amend_Flag',1)
            ->where('LE_ID',$values['LE_ID'])
            ->update(array('reMeasure_Flag'=>1,'reMeasure_Qty'=>$values['qty'],'reMeasure_Rate'=>$rate[0]
, 'reMeasure_Value'=>$reValue,'Re_Comments'=>$values['cmnts']));
        }
        else if($values['rate_Flag']==1)
        {
            $newRate=$values['rate'];
            $reValue=$newRate*$values['qty'];
            $reMeasure=\DB::table('work_labour_estimation')
            //->where('Amend_Flag',1)
            ->where('LE_ID',$values['LE_ID'])->update(array('reMeasure_Flag'=>1,'reMeasure_Qty'=>$values['qty'],'reMeasure_Rate'=>$newRate,
            'reMeasure_Value'=>$reValue,'Re_RateFlag'=>1,'Re_Comments'=>$values['cmnts']));
        }
        $resp=array("Success"=>true);
        return $resp;
       
    }
});
   
}

public function getTenderItemDetails($id)
{
    $tenderDetails=\DB::table('work_tender_details_lab')
    ->join('serv_line_items','serv_line_items.LineItem_ID','=','work_tender_details_lab.LineItem_ID')
    ->join('units','units.Unit_ID','=','serv_line_items.UnitID')
    ->where('WorkTenderLab_ID', $id)->get();
    $resp=array($tenderDetails);
    return $resp;
}

public function getReMeasuredTenderDetails($id)
{
    $reDetails=\DB::table('work_tendering')->join('work_tender_details_lab','work_tender_details_lab.WorkTender_ID','=','work_tendering.WorkTender_ID')
    ->join('serv_line_items','serv_line_items.LineItem_ID','=','work_tender_details_lab.LineItem_ID')->where('work_tendering.Work_ID',$id)->where('work_tender_details_lab.reMeasure_Flag',1)->get();
    $resp=array($reDetails);
    return $resp;
}

public function getReMeasuredAmendDetails($id)
{
    $reAmendDetails=\DB::table('work_labour_estimation')
    ->join('serv_line_items','serv_line_items.LineItem_ID','=','work_labour_estimation.LineItem_ID')
    ->where('Work_ID',$id)->where('Amend_Flag',1)
    ->where('reMeasure_Flag',1)->get();
    $resp=array($reAmendDetails);
    return $resp;

}
/*public function finishReMeasure(Request $r)
{
    $values = Request::json()->all();
    $remeasureTTotal=\DB::table('work_tendering')->join('work_tender_details_lab','work_tender_details_lab.WorkTender_ID','=','work_tendering.WorkTender_ID')
    ->where('work_tendering.Work_ID',$values['work_ID'])->where('work_tender_details_lab.reMeasure_Flag',1)->sum('work_tender_details_lab.reMeasure_Value');
    $reMeasureAmndTotal=\DB::table('work_labour_estimation')->where('Work_ID',$values['work_ID'])->where('Amend_Flag',1)
    ->where('reMeasure_Flag',1)->sum('work_labour_estimation.reMeasure_Value');
    $work=\DB::table('service_work')->where('Work_ID',$values['work_ID'])->update(array('Re_Flag'=>1));
    if(!empty($remeasureTTotal))
    {
        $remeasureTAttrb=\DB::table('work_timeline')->insert(array('Work_ID'=>$values['work_ID'],'Work_Attrb_ID'=>30, 'Value'=>$remeasureTTotal));
    }
    if(!empty($reMeasureAmndTotal))
    {
        $remeasureAAttrb=\DB::table('work_timeline')->insert(array('Work_ID'=>$values['work_ID'],'Work_Attrb_ID'=>31, 'Value'=>$reMeasureAmndTotal));

    }
    $grandTotal=\DB::table('work_tendering')->where('Work_ID',$values['work_ID'])->pluck('TotalQuote');
    $reMeasureGTotal=$grandTotal[0]+$remeasureTTotal+$reMeasureAmndTotal;
    if(!empty($reMeasureGTotal))
    {
        $insertGTotal=\DB::table('work_timeline')->insert(array('Work_ID'=>$values['work_ID'],'Work_Attrb_ID'=>32, 'Value'=>$reMeasureGTotal));
    }
    $resp=array($remeasureTTotal, $reMeasureAmndTotal);  
    return $resp;
}*/
public function finishReMeasure($workid, $type)
{
    
    if($type==0)
    {
        $tid=\DB::table('work_tendering')->where('Work_ID', $workid)->where('SelectStatus',1)->pluck('WorkTender_ID');
       $reMeasureInsert=\DB::table('work_remeasure') ->insert(array('Work_ID'=> $workid, 'ScopeID'=>$tid[0],'ReFlag'=>1));
       
           $reFlag=\DB::table('work_tender_details_lab')->where('WorkTender_ID',$tid[0])->where('reMeasure_Flag',1)
           ->update(array('finishRe_Flag'=>1));
          

      
    }
    if($type!=0)
    {
        $reMeasureInsert=\DB::table('work_remeasure') ->insert(array('Work_ID'=> $workid, 'ScopeID'=>$type,'ReFlag'=>1));
        
            $reFlag=\DB::table('work_labour_estimation')->where('Work_ID',$workid)->where('reMeasure_Flag',1)
            ->update(array('finishRe_Flag'=>1));
           
 
       
    }
    $resp=array("Success"=>true);
    return $resp;
}

public function getReMeasureSummary($id, $no)
{
    /*$remeasureTTotal=\DB::table('work_timeline')->where('Work_ID',$id)->where('Work_Attrb_ID',30)->pluck('Value');
    $remeasureATotal=\DB::table('work_timeline')->where('Work_ID',$id)->where('Work_Attrb_ID',31)->pluck('Value');
    $remeasureGrandTotal=\DB::table('work_timeline')->where('Work_ID',$id)->where('Work_Attrb_ID',32)->pluck('Value');
    if(!empty($remeasureATotal[0]))
    {
        $resp=array("Tender"=>$remeasureTTotal[0],"Amend"=>$remeasureATotal[0],"Grand"=>$remeasureGrandTotal[0]);
        return $resp;
    }
    else{
        $resp=array("Tender"=>$remeasureTTotal[0],"Grand"=>$remeasureGrandTotal[0]);
        return $resp; 
    }*/
    /*$tenderTotal=\DB::table('work_tendering')->join('work_tender_details_lab','work_tender_details_lab.WorkTender_ID','=','work_tendering.WorkTender_ID')
    ->where('work_tendering.Work_ID',$id)->where('work_tender_details_lab.reMeasure_Flag',0)->sum('work_tender_details_lab.reMeasure_Value');
    $remeasureTTotal=\DB::table('work_tendering')->join('work_tender_details_lab','work_tender_details_lab.WorkTender_ID','=','work_tendering.WorkTender_ID')
    ->where('work_tendering.Work_ID',$id)->where('work_tender_details_lab.reMeasure_Flag',1)->sum('work_tender_details_lab.reMeasure_Value');
    $Amnd1Total=\DB::table('work_labour_estimation')->where('Work_ID',$id)->where('Amend_Flag',1)
    ->where('reMeasure_Flag',0)->sum('work_labour_estimation.reMeasure_Value');
    $reMeasureAmnd1Total=\DB::table('work_labour_estimation')->where('Work_ID',$id)->where('Amend_Flag',1)
    ->where('reMeasure_Flag',1)->sum('work_labour_estimation.reMeasure_Value');
    $Amnd2Total=\DB::table('work_labour_estimation')->where('Work_ID',$id)->where('Amend_Flag',2)
    ->where('reMeasure_Flag',0)->sum('work_labour_estimation.reMeasure_Value');
    $reMeasureAmnd2Total=\DB::table('work_labour_estimation')->where('Work_ID',$id)->where('Amend_Flag',2)
    ->where('reMeasure_Flag',1)->sum('work_labour_estimation.reMeasure_Value');
    $Amnd3Total=\DB::table('work_labour_estimation')->where('Work_ID',$id)->where('Amend_Flag',3)
    ->where('reMeasure_Flag',0)->sum('work_labour_estimation.reMeasure_Value');
    $reMeasureAmnd3Total=\DB::table('work_labour_estimation')->where('Work_ID',$id)->where('Amend_Flag',3)
    ->where('reMeasure_Flag',1)->sum('work_labour_estimation.reMeasure_Value');
    $remeasureGrandTotal=$tenderTotal+$remeasureTTotal+$Amnd1Total+$reMeasureAmnd1Total+$Amnd2Total+$reMeasureAmnd2Total+
    $Amnd3Total+$reMeasureAmnd3Total;
    $resp=array("GrandTotal"=>$remeasureGrandTotal,"ReAmend1"=>$reMeasureAmnd1Total,"ReAmend2"=>$reMeasureAmnd2Total,"ReAmend3"=>$reMeasureAmnd3Total);
    return $resp;*/
    if($no==0)
    {
        $tid=\DB::table('work_tendering')->where('Work_ID', $id)->where('SelectStatus',1)->pluck('WorkTender_ID');
        $ReItems=\DB::table('work_tender_details_lab')->where('WorkTender_ID', $tid[0])->where('reMeasure_Flag',1)->where('finishRe_Flag',1)->get();
        $ReItemTotal=\DB::table('work_tender_details_lab')->where('WorkTender_ID', $tid[0])->where('reMeasure_Flag',1)->where('finishRe_Flag',1)
        ->sum('reMeasure_Value');
        $ReNotItemTotal=\DB::table('work_tender_details_lab')->where('WorkTender_ID', $tid[0])->where('reMeasure_Flag',0)
        ->where('deleteFlag',0)->sum('Value');
        if($ReItemTotal!=0)
        {
            $TotalAfterRe=$ReNotItemTotal+$ReItemTotal;
        }
        else
        {
            $TotalAfterRe=0;
        }
        
        $resp=array('Items'=>$ReItems,'TotalAfterRe'=>$TotalAfterRe,'ScopReFlag'=>0);
        return $resp;
    }
    else if($no!=0)
    {
        $ReItems=\DB::table('work_labour_estimation')->where('Work_ID',$id)->where('deleteFlag',0)->where('Amend_Flag',$no)
        ->where('reMeasure_Flag',1)->get();
        $reTotal=\DB::table('work_labour_estimation')->where('Work_ID',$id)->where('deleteFlag',0)->where('Amend_Flag',$no)
        ->where('reMeasure_Flag',1)->sum('reMeasure_Value');
        $reNotItemTotal=\DB::table('work_labour_estimation')->where('Work_ID',$id)->where('deleteFlag',0)->where('Amend_Flag',$no)
        ->where('reMeasure_Flag',0)->sum('Value');
        if($reTotal!=0)
        {
        $TotalAfterRe=$reTotal+$reNotItemTotal;
        }
        else
        {
            $TotalAfterRe=0;  
        }
        $resp=array('Items'=>$ReItems,'TotalAfterRe'=>$TotalAfterRe,'ScopeReFlag'=>$no);
        return $resp;
    }
   
}
public function getTenderTotal($id)
{
    $tenderTotal=\DB::table('work_tendering')->where('Work_ID', $id)->where('SelectStatus',1)->pluck('TotalQuote');
    $resp=array($tenderTotal);
    return $resp;
}
public function getTenderQuote($id)
{
    $tenderTotal=\DB::table('work_tendering')->where('WorkTender_ID', $id)->pluck('TotalQuote');
    $resp=array($tenderTotal);
    return $resp;
}
public function finishWork(Request $r)
{
    $values = Request::json()->all();
    $finishWork=\DB::table('service_work')->where('Work_ID',$values['work_ID'])->update(array('ActualClosureDate'=>$values['startDate'],'Compl_Flag'=>1,
'WorkStatus'=>8, 'Update_Status'=>8));
    $resp=array("Success"=>true);
    return $resp;
}

public function getAmendNo($id)
{
  /*  $amendNo=\DB::table('work_labour_estimation')->where('Work_ID', $id)->where('deleteFlag',0)
    ->select(\DB::raw('MAX(Amend_Flag) AS Max'))->get();
   // $max=MAX($amendNo);
    $resp=array($amendNo);
    return $resp;*/
    $amendNo=\DB::table('work_amendment')->where('Work_ID', $id)->pluck('Estimation_Amend_Flag');
    $resp=array($amendNo[0]);
    return $resp;
}
public function updateAmendIssueDate(Request $r)
{
    $values = Request::json()->all();
   /* if($values['amendno']==1)
    {
        $amend1Issue=\DB::table('work_timeline')->insert(array('Work_ID'=>$values['workid'], 'Work_Attrb_ID'=>33,'Value'=>$values['woIssueDate']));
        $resp=array("Success"=>true);
        return $resp;
    }
    else if($values['amendno']==2)
    {
        $amend1Issue=\DB::table('work_timeline')->insert(array('Work_ID'=>$values['workid'], 'Work_Attrb_ID'=>34,'Value'=>$values['woIssueDate']));
        $resp=array("Success"=>true);
        return $resp;
    }
    else if($values['amendno']==3)
    {
        $amend1Issue=\DB::table('work_timeline')->insert(array('Work_ID'=>$values['workid'], 'Work_Attrb_ID'=>35,'Value'=>$values['woIssueDate']));
        $resp=array("Success"=>true);
        return $resp;
    }*/

    $amend1Issue=\DB::table('work_amendment_issuedate')->where('Work_ID',$values['workid'])->where('Amend_No',$values['amendno'])
    ->update(array('IssueDate'=>$values['woIssueDate']));
        $resp=array("Success"=>true);
        return $resp;
    

}
public function updateRemeasureIssueDate(Request $r)
{
    $values = Request::json()->all();
    $newDate=new DateTime($values['woIssueDate']);
        $newDate->modify('+1 day');

    if($values['type']==0)
    {
        $tid=\DB::table('work_tendering')->where('Work_ID', $values['workid'])->pluck('WorkTender_ID');
        $insertDate=\DB::table('work_remeasure')->where('Work_ID',$values['workid'])->where('ScopeID',$tid[0])->update(array('IssueDate'=>$newDate->format('Y-m-d')));
        $resp=array("Success"=>true);
        return $resp;
    }
    else if($values['type']!=0)
    {
        $insertDate=\DB::table('work_remeasure')->where('Work_ID',$values['workid'])->where('ScopeID',$values['type'])->update(array('IssueDate'=>$newDate->format('Y-m-d')));
        $resp=array("Success"=>true);
        return $resp;
    }
        
        
    
}
public function chkReMeasureIssueDateExists($id, $no)
{
    if($no==0)
    {
        $tid=\DB::table('work_tendering')->where('Work_ID', $id)->pluck('WorkTender_ID');
        $issueDate=\DB::table('work_remeasure')->where('Work_ID', $id)->where('ScopeID',$tid[0])->select('IssueDate')->get();
                $resp=array($issueDate);
        return $resp;
    }
    if($no!=0)
    {
        $issueDate=\DB::table('work_remeasure')->where('Work_ID', $id)->where('ScopeID',$no)->select('IssueDate')->get();
                $resp=array($issueDate);
        return $resp;
    }
   
}
public function chkAmendIssueDateExists($id, $no)
{
    $issueDate=\DB::table('work_amendment_issuedate')->where('Work_ID', $id)->where('Amend_No',$no)->pluck('IssueDate');
    //$chkExists=count($issueDate);
    if($issueDate[0]=='')
    {
        $chkExists=0;
    }
    else{
        $chkExists=1;
    }
    $resp=array($chkExists);
    return $resp;
}
public function chkAmend1IssueDateExists($id)
{
    $issueDate=\DB::table('work_timeline')->where('Work_ID', $id)->where('Work_Attrb_ID',33)->get();
    $chkExists=count($issueDate);
    $resp=array($chkExists);
    return $resp;
}
public function chkAmend2IssueDateExists($id)
{
    $issueDate=\DB::table('work_timeline')->where('Work_ID', $id)->where('Work_Attrb_ID',34)->get();
    $chkExists=count($issueDate);
    $resp=array($chkExists);
    return $resp;
}
public function chkAmend3IssueDateExists($id)
{
    $issueDate=\DB::table('work_timeline')->where('Work_ID', $id)->where('Work_Attrb_ID',35)->get();
    $chkExists=count($issueDate);
    $resp=array($chkExists);
    return $resp;
}
public function chkAmend4IssueDateExists($id)
{
    $issueDate=\DB::table('work_timeline')->where('Work_ID', $id)->where('Work_Attrb_ID',37)->get();
    $chkExists=count($issueDate);
    $resp=array($chkExists);
    return $resp;
}
public function getAmendIssueDate($wid,$no)
{
   /* if($no==1)
    {
        $issueDate=\DB::table('work_timeline')->where('Work_ID', $wid)->where('Work_Attrb_ID',33)->pluck('Value');
        $resp=array($issueDate);
        return $resp;
    }
    else if($no==2)
    {
        $issueDate=\DB::table('work_timeline')->where('Work_ID', $wid)->where('Work_Attrb_ID',34)->pluck('Value');
        $resp=array($issueDate);
        return $resp;
    }
    else if($no==3)
    {
        $issueDate=\DB::table('work_timeline')->where('Work_ID', $wid)->where('Work_Attrb_ID',35)->pluck('Value');
        $resp=array($issueDate);
        return $resp;
    }*/
    
    $issueDate=\DB::table('work_amendment_issuedate')->where('Work_ID', $wid)->where('Amend_No',$no)->pluck('IssueDate');
    $resp=array($issueDate);
    return $resp;

  
}
public function delKeys($WID, $KID)
{
    $del=\DB::table('wo_key_deliverables')->where('Work_ID', $WID)->where('Key_ID',$KID)->update(array('Delete_Flag'=>1));
    $resp=array($del);
    return $resp;
}
public function delTerms($WID, $TID)
{
    $del=\DB::table('wo_terms_conditions')->where('Work_ID', $WID)->where('Term_ID',$TID)->update(array('Delete_Flag'=>1));
    $resp=array($del);
    return $resp;
}
public function saveExtraPayment(Request $r)
{
    $values = Request::json()->all();
    $newPayDate=new DateTime($values['payDate']);
        $newPayDate->modify('+1 day');
    if($values['type_ID']==1)
    {
      
   /* $extraPay=\DB::table('actual_payments')->insert(array('PaySched_ID'=>$values['pay_ID'],
    'Actual_Amount'=>$values['recAmt'],'Actual_Date'=>$newPayDate->format('Y-m-d'),'Type'=>$values['type'],'R_Comments'=>$values['Comment']));*/
    $extraPay=\DB::table('received_payments')->insert(array('Work_ID'=>$values['work_ID'],
    'Rec_Amount'=>$values['recAmt'],'Rec_Date'=>$newPayDate->format('Y-m-d'),'Type'=>$values['type'],'Receipt_Comment'=>$values['Comment']));
    }
    else if($values['type_ID']==2)
    {
   
       /* $extraPay=\DB::table('actual_payments')->insert(array('PaySched_ID'=>$values['pay_ID'],
        'Split_Amount'=>$values['recAmt'],'Actual_Date'=>$newPayDate->format('Y-m-d'),'Type'=>$values['type'],'R_Comments'=>$values['Comment']));*/
        $extraPay=\DB::table('split_payments')->insert(array('Pay_ID'=>$values['pay_ID'],'Rec_ID'=>$values['recipts'],
        'Split_Amount'=>$values['recAmt'],'Date'=>$newPayDate->format('Y-m-d'),'Type'=>$values['type'],'Split_Comment'=>$values['Comment'])); 
    }
    
    $resp=array('Success'=>true);
    return $resp;
}
public function getPayments($id)
{
    /*$payments=\DB::table('actual_payments')
    ->join('payment_schedule','actual_payments.PaySched_ID','=','payment_schedule.Pay_Schedule_ID')
    ->where('payment_schedule.Work_ID',$id)->get();*/
    $payments=\DB::table('split_payments')
    ->join('payment_schedule','split_payments.Pay_ID','=','payment_schedule.Pay_Schedule_ID')
    ->where('payment_schedule.Work_ID',$id)->get();
    $resp=array($payments);
    return $resp;
}
public function getEstimateTotal($id)
{
    $Total=\DB::table('work_tendering')->where('Work_ID', $id)->where('SelectStatus',1)->pluck('TotalQuote');
    $resp=array($Total);
    return $resp;
}
public function getAmendSubTotals($id, $no)
{
    if($no==0)
    {
        $estTotal=\DB::table('work_tendering')->where('Work_ID', $id)->where('SelectStatus',1)->pluck('TotalQuote');
        
        /*$estSubTotal=\DB::table('payment_schedule')->where('Work_ID', $id)->where('deleteFlag',0)
        ->where('Amend_Flag',$no)->join('actual_payments','payment_schedule.Pay_Schedule_ID','=','actual_payments.PaySched_ID' )
        ->sum('actual_payments.Actual_Amount')
        //->get()
        ;*/
        $recPayment=\DB::table('received_payments')->where('Work_ID', $id)->sum('Rec_Amount');
        $splitTotal=\DB::table('split_payments')
        ->join('payment_schedule','payment_schedule.Pay_Schedule_ID','=','split_payments.Pay_ID' )
        ->where('payment_schedule.Work_ID', $id)->where('deleteFlag',0)
        ->where('Amend_Flag',$no)
        ->sum('split_payments.split_Amount');
        $balRec=$estTotal[0]- $recPayment;
        $balSplit=$estTotal[0]-$splitTotal;

        $tid=\DB::table('work_tendering')->where('Work_ID', $id)->where('SelectStatus',1)->pluck('WorkTender_ID');
        $ReItems=\DB::table('work_tender_details_lab')->where('WorkTender_ID', $tid[0])->where('reMeasure_Flag',1)->where('finishRe_Flag',1)->get();
        $ReItemTotal=\DB::table('work_tender_details_lab')->where('WorkTender_ID', $tid[0])->where('reMeasure_Flag',1)->where('finishRe_Flag',1)
        ->sum('reMeasure_Value');
        $ReNotItemTotal=\DB::table('work_tender_details_lab')->where('WorkTender_ID', $tid[0])->where('reMeasure_Flag',0)
        ->where('deleteFlag',0)->sum('Value');
        if($ReItemTotal!=0)
        {
            $TotalAfterRe=$ReNotItemTotal+$ReItemTotal;
            $ReEstBal=$TotalAfterRe-$splitTotal;
        }
        else
        {
            $TotalAfterRe=0;
            $ReEstBal=0;
        }
        
        


        //$estBal=$estTotal[0]-$estSubTotal;
        
        $resp=array(
            'Total'=>$estTotal[0], 'RecTotal'=>$recPayment,'SplitTotal'=>$splitTotal,
           
           'BalanceRec'=>$balRec, 'BalanceSplit'=>$balSplit,'ReBal'=>$ReEstBal,'TotalAfterRe'=>$TotalAfterRe
        );
        return $resp;
    }
    else if($no!=0)
    {
        $amendTotal=\DB::table('work_labour_estimation')->where('Work_ID', $id)->where('deleteFlag',0)->where('FinishAmend_Flag',1)
        ->where('Amend_Flag',$no)->sum('Value');
       /* $amendSubTotal=\DB::table('payment_schedule')->where('Work_ID', $id)->where('deleteFlag',0)->where('FinishAmend_Flag',1)
        ->where('Amend_Flag',$no)->join('actual_payments','payment_schedule.Pay_Schedule_ID','=','actual_payments.PaySched_ID' )
        ->sum('actual_payments.Actual_Amount');*/

        //$amendBal=$amendTotal - $amendSubTotal;
        $recPayment=\DB::table('received_payments')->where('Work_ID', $id)->sum('Rec_Amount');
        $splitTotal=\DB::table('split_payments')
        ->join('payment_schedule','payment_schedule.Pay_Schedule_ID','=','split_payments.Pay_ID' )
        ->where('payment_schedule.Work_ID', $id)->where('deleteFlag',0)
        ->where('Amend_Flag',$no)
        ->sum('split_payments.split_Amount');
        //$estBal=$estTotal[0]-$estSubTotal;
        $balRec=$amendTotal- $recPayment;
        $balSplit=$amendTotal-$splitTotal;


        $ReItems=\DB::table('work_labour_estimation')->where('Work_ID',$id)->where('deleteFlag',0)->where('Amend_Flag',$no)
        ->where('reMeasure_Flag',1)->get();
        $reTotal=\DB::table('work_labour_estimation')->where('Work_ID',$id)->where('deleteFlag',0)->where('Amend_Flag',$no)
        ->where('reMeasure_Flag',1)->sum('reMeasure_Value');
        $reNotItemTotal=\DB::table('work_labour_estimation')->where('Work_ID',$id)->where('deleteFlag',0)->where('Amend_Flag',$no)
        ->where('reMeasure_Flag',0)->sum('Value');
        if($reTotal!=0)
        {
        $TotalAfterRe=$reTotal+$reNotItemTotal;
        $ReEstBal=$TotalAfterRe-$splitTotal;
        }
        else
        {
            $TotalAfterRe=0;  
            $ReEstBal=0;
        }


        $resp=array(
            'Total'=>$amendTotal, 'RecTotal'=>$recPayment,'SplitTotal'=>$splitTotal,'BalanceRec'=>$balRec, 'BalanceSplit'=>$balSplit,
            'ReBal'=>$ReEstBal,'TotalAfterRe'=>$TotalAfterRe
        );
        return $resp;
    }
   /*$total=\DB::table('payment_schedule')->join('actual_payments','payment_schedule.Pay_Schedule_ID','=','actual_payments.Pay_Schedule_ID' )->where('Work_ID', $id)->where('deleteFlag',0)->where('FinishAmend_Flag',1)
        ->where('Amend_Flag',$no)->sum('actual_payments.Actual_Amount');
        $resp=array($total);
        return $resp;*/
}

public function chkAmendPayExists($id, $no)
{
    $amendPayExists=\DB::table('payment_schedule')->where('Work_ID', $id)->where('deleteFlag',0)->where('FinishAmend_Flag',1)
    ->where('Amend_Flag',$no)->get();
    $count=count($amendPayExists);
    $resp=array($count);
    return $resp;
}
public function chkAmendSchedExists($id, $no)
{
    $amendSchdExists=\DB::table('work_schedule')->where('Work_ID', $id)->where('deleteFlag',0)->where('FinishAmend_Flag',1)
    ->where('Amend_Flag',$no)->get();
    $count=count($amendSchdExists);
    $resp=array($count);
    return $resp;
}
public function getReFlag($id, $no)
{
    if($no==0)
    {
        $tid=\DB::table('work_tendering')->where('Work_ID', $id)->where('SelectStatus',1)->pluck('WorkTender_ID');
        $ReFlags=\DB::table('work_remeasure')->where('Work_ID', $id)->where('ScopeID',$tid[0])->get();
        $resp=array($ReFlags);
        return $resp; 
    }
    else if($no!==0)
    {
        $ReFlags=\DB::table('work_remeasure')->where('Work_ID', $id)->where('ScopeID',$no)->get();
        $resp=array($ReFlags);
        return $resp;
    }
    
}
public function getReItems($id, $no)
{
    if($no==0)
    {
        $tid=\DB::table('work_tendering')->where('Work_ID', $id)->where('SelectStatus',1)->pluck('WorkTender_ID');
        $reItems=\DB::table('work_tendering')->
        join('work_tender_details_lab', 'work_tendering.WorkTender_ID','=','work_tender_details_lab.WorkTender_ID')
        ->join('serv_line_items', 'serv_line_items.LineItem_ID','=','work_tender_details_lab.LineItem_ID')
        ->join('units', 'units.Unit_ID','=','serv_line_items.UnitID')
        ->where('work_tender_details_lab.WorkTender_ID', $tid[0])
        //->where('work_tender_details_lab.Quantity','!=',0)
        ->where('work_tender_details_lab.reMeasure_Flag',1)
        ->get();
        $resp=array($reItems);
        return $resp;
    }
    if($no!=0)
    {
        $reItems=\DB::table('work_labour_estimation')    
        ->join('serv_line_items', 'serv_line_items.LineItem_ID','=','work_labour_estimation.LineItem_ID')
        ->join('units', 'units.Unit_ID','=','serv_line_items.UnitID')
        ->where('Work_ID', $id)
        ->where('Amend_Flag',$no)->where('deleteFlag',0)
        //->where('Quantity','!=',0)
        ->where('reMeasure_Flag',1)
        ->get();
        $resp=array($reItems);
        return $resp;
    }
   
}
public function getRemeasureIssueDate($id, $no)
{
    if($no==0)
    {
        $tid=\DB::table('work_tendering')->where('Work_ID', $id)->pluck('WorkTender_ID');
        $issueDate=\DB::table('work_remeasure')->where('ScopeID',$tid[0])->where('Work_ID',$id)
        ->select('IssueDate')->get();
        $resp=array($issueDate);
        return $resp;
    }
    if($no!=0)
    {
        $issueDate=\DB::table('work_remeasure')->where('ScopeID',$no)->where('Work_ID',$id)
        ->select('IssueDate')->get();
        $resp=array($issueDate);
        return $resp;
    }
}
public function getPrintPaySchedule($id, $no)
{
    if($no==0)
    {
        $pay=\DB::table('payment_schedule')
       // ->leftjoin('work_amendment','payment_schedule.Work_ID','=','work_amendment.Work_ID')
       // ->leftjoin('actual_payments', 'actual_payments.PaySched_ID','=','payment_schedule.Pay_Schedule_ID')
        ->where('payment_schedule.Work_ID', $id)
        ->where('payment_schedule.DeleteFlag',0)
        ->where('payment_schedule.Amend_Flag',$no)->orderBy('payment_schedule.Payment_Date')
        ->get();
        $resp=array($pay);
        return $resp;
    
    }
    else{
        $pay=\DB::table('payment_schedule')->leftjoin('work_amendment','payment_schedule.Work_ID','=','work_amendment.Work_ID')
        //->leftjoin('actual_payments', 'actual_payments.PaySched_ID','=','payment_schedule.Pay_Schedule_ID')
        ->where('payment_schedule.Work_ID', $id)->where('payment_schedule.DeleteFlag',0)
        ->where('payment_schedule.Amend_Flag',$no)->where('payment_schedule.FinishAmend_Flag',1)->orderBy('payment_schedule.Payment_Date')->get();
        $resp=array($pay);
        return $resp;

    }
   

}

public function closePaySched($id, $no)
{
    $closeItems=\DB::table('payment_schedule')
	//->leftjoin('actual_payments', 'actual_payments.PaySched_ID','=','payment_schedule.Pay_Schedule_ID')
    ->where('payment_schedule.Work_ID', $id)->where('payment_schedule.DeleteFlag',0)
    ->where('payment_schedule.Amend_Flag',$no)->where('payment_schedule.FinishAmend_Flag',1)
    ->update(array('Sched_CloseFlag'=>1));
	$resp=array($closeItems);
	return $resp;
}
public function getAmendLineItemsNew($id)
{
    $labEstimate=\DB::table('work_labour_estimation')
		->join('serv_line_items', 'serv_line_items.LineItem_ID','=','work_labour_estimation.LineItem_ID')
	->join('units', 'units.Unit_ID','=','serv_line_items.UnitID')
	
    ->where('work_labour_estimation.Work_ID', $id)->where('deleteFlag',0)->where('FinishAmend_Flag',0)
    ->get();
	//$total=\DB::table('work_labour_estimation')->where('Work_ID', $id)->sum('Value');
	$resp=array($labEstimate);
	return $resp;
}
public function getAmendedLineItemsNew($id)
{
    $labEstimate=\DB::table('work_labour_estimation')
		->join('serv_line_items', 'serv_line_items.LineItem_ID','=','work_labour_estimation.LineItem_ID')
	->join('units', 'units.Unit_ID','=','serv_line_items.UnitID')
	
    ->where('work_labour_estimation.Work_ID', $id)->where('deleteFlag',0)->where('FinishAmend_Flag',1)
    ->get();
	//$total=\DB::table('work_labour_estimation')->where('Work_ID', $id)->sum('Value');
	$resp=array($labEstimate);
	return $resp;
}
public function getWorkingAmends($id)
{
    $amends=\DB::table('work_labour_estimation')->where('Work_ID', $id)->where('deleteFlag',0)
    ->where('Amend_Flag','!=',0)->where('FinishAmend_Flag',0)->select('Amend_Flag')->distinct()->get();
    $resp=array($amends);
    return $resp;

}
public function getViewWorkSchedule($id, $view)
{
    if($view==1)
    {
        $work=\DB::table('work_schedule')
        ->leftjoin('work_amendment','work_schedule.Work_ID','=','work_amendment.Work_ID')
        //->where('work_amendment.WorkSched_Amend_Flag',1)
        ->where('work_schedule.Work_ID', $id)->where('DeleteFlag',0)
        ->where('Amend_Flag',0)->orderBy('work_schedule.Start_Date')->get();
        $resp=array($work);
        return $resp;
    }
    else if($view==2)
    {
        $work=\DB::table('work_schedule')
        ->leftjoin('work_amendment','work_schedule.Work_ID','=','work_amendment.Work_ID')
        //->where('work_amendment.WorkSched_Amend_Flag',1)
        ->where('work_schedule.Work_ID', $id)->where('DeleteFlag',0)
        ->where('Amend_Flag','!=',0)->orderBy('work_schedule.Start_Date')->get();
        $resp=array($work);
        return $resp;
    }
  
}
public function getPayAmounts($id)
{
    $InitAmount=\DB::table('initiate_payment')->where('Work_ID', $id)->where('DeleteFlag',0)//->where('PayStatus_ID',1)
    ->sum('ReqAmount');
    $PaidAmount=\DB::table('initiate_payment')->where('Work_ID', $id)->where('DeleteFlag',0)->where('PayStatus_ID',3)
    ->sum('ReqAmount');
    $BalAmount=$InitAmount-$PaidAmount;
    $resp=array('Init'=>$InitAmount, 'Paid'=>$PaidAmount, 'Balance'=>$BalAmount);
    return $resp;
}
public function getAllSubTotals($id)
{
   
    $i=0;
    $result=[];
    $amendNo=\DB::table('work_amendment')->where('Work_ID',$id)->pluck('Estimation_Amend_Flag');
    while($i<=$amendNo[0])
    {
        if($i==0)
        {
            $estSubTotal=\DB::table('payment_schedule')->where('Work_ID', $id)->where('deleteFlag',0)
            ->where('Amend_Flag',0)->join('split_payments','payment_schedule.Pay_Schedule_ID','=','split_payments.Pay_ID' )
            ->sum('split_payments.Split_Amount')
            
            ;
            array_push($result,$estSubTotal);

        }
        else if($i!=0)
        {
            $amendSubTotal=\DB::table('payment_schedule')->where('Work_ID', $id)->where('deleteFlag',0)->where('FinishAmend_Flag',1)
            ->where('Amend_Flag',$i)->join('split_payments','payment_schedule.Pay_Schedule_ID','=','split_payments.Pay_ID' )
            ->sum('split_payments.Split_Amount');
            array_push($result,$amendSubTotal);
        }
        $i++;

    }
    $resp=array($result);
    return $resp;
}
public function getAllRecievedPayments($id)
{
   /* $payments=\DB::table('actual_payments')->join('payment_schedule','actual_payments.PaySched_ID','=','payment_schedule.Pay_Schedule_ID')
    ->where('payment_schedule.Work_ID',$id)
    ->orderBy('actual_payments.Actual_Date','DESC')->get();*/
    $payments=\DB::table('split_payments')->join('payment_schedule','split_payments.Pay_ID','=','payment_schedule.Pay_Schedule_ID')
    ->where('payment_schedule.Work_ID',$id)
    ->orderBy('split_payments.Date','DESC')->get();
    $resp=array($payments);
    return $resp;
}

public function getAllTotals($id)
{
    //grandTotal
    $tid=\DB::table('work_tendering')->where('Work_ID', $id)->where('SelectStatus',1)->pluck('WorkTender_ID');
    $WTotal=\DB::table('work_tendering')->where('Work_ID', $id)->where('SelectStatus',1)->pluck('TotalQuote');
    $ATotal=\DB::table('work_labour_estimation')->where('Work_ID',$id)->where('deleteFlag',0)->where('Amend_Flag','!=',0)->where('FinishAmend_Flag',1)->sum('Value');
    $totalWork=$WTotal[0]+$ATotal;
    $recPayment=\DB::table('received_payments')->where('Work_ID', $id)->sum('Rec_Amount');
        $ReItemTotal=\DB::table('work_tender_details_lab')->where('WorkTender_ID', $tid[0])->where('reMeasure_Flag',1)->where('finishRe_Flag',1)
        ->sum('reMeasure_Value');
        $ReNotItemTotal=\DB::table('work_tender_details_lab')->where('WorkTender_ID', $tid[0])->where('reMeasure_Flag',0)
        ->where('deleteFlag',0)->sum('Value');
       
            $reATotal=\DB::table('work_labour_estimation')->where('Work_ID',$id)->where('deleteFlag',0)->where('Amend_Flag','!=',0)
            ->where('reMeasure_Flag',1)->sum('reMeasure_Value');
            $reNotItemATotal=\DB::table('work_labour_estimation')->where('Work_ID',$id)->where('deleteFlag',0)->where('Amend_Flag','!=',0)
            ->where('reMeasure_Flag',0)->sum('Value');
                        $GrandTotal=$reATotal+$reNotItemATotal+$ReNotItemTotal+$ReItemTotal;
        //InitTotal
        $InitTotal=\DB::table('initiate_payment')->where('Work_ID', $id)->where('DeleteFlag',0)//->where('PayStatus_ID',1)
    ->sum('ReqAmount');
        //RecTotal(split amount)
        $RecTotal=\DB::table('split_payments')->join('payment_schedule','split_payments.Pay_ID','=','payment_schedule.Pay_Schedule_ID')
    ->where('payment_schedule.Work_ID',$id)->sum('Split_Amount');
    //Paid Total
   /* $PaidTotal=\DB::table('initiate_payment')->where('Work_ID', $id)->where('PayStatus_ID',3)
    ->sum('ReqAmount');*/
    $paidMfee=\DB::table('initiate_payment')->where('Work_ID', $id)->where('MFee_Flag',1)->where('DeleteFlag',0)
    ->sum('Paid_MFee');
    $paidAssocFee=\DB::table('initiate_payment')->where('Work_ID', $id)->where('AssocPay_Flag',1)->where('DeleteFlag',0)
    ->sum('Paid_AssocPay');
    $PaidTotal=$paidMfee + $paidAssocFee;
    //CustBal
    $CustBal=$GrandTotal-$RecTotal;
    //AcBal
    $AcBal=$RecTotal-$InitTotal;
   

    $resp=array('GrandTotal'=>$GrandTotal,'WOTotal'=>$WTotal[0], 'AmendTotal'=>$ATotal,'TotalWorkAmt'=>$totalWork,'RecAmount'=>$recPayment,'RecAmountSplit'=>$RecTotal, 'Init'=>$InitTotal,'Paid'=>$PaidTotal,'CustBal'=>$CustBal,'AcBal'=>$AcBal,
'Mfee'=>$paidMfee,'AssocFee'=>$paidAssocFee,'ReWOTotal'=>$ReItemTotal,'NoReWOTotal'=>$ReNotItemTotal,'ReAmendTotal'=>$reATotal, 'ReNotATotal'=>$reNotItemATotal);
    return $resp;
}

public function getAllPaidPayments($id)
{
    $details=\DB::table('initiate_payment')
->join('payment_status','payment_status.Pay_Status_ID','=','initiate_payment.PayStatus_ID')
->where('Work_ID', $id)->where('PayStatus_ID',3)->where('DeleteFlag',0)
->orderBy('initiate_payment.AssocPay_Date','DESC')->get();
$resp=array($details);
return $resp;
}

public function getAllReceipts()
{
   /* $receipts=\DB::table('actual_payments')
    ->join('payment_schedule','payment_schedule.Pay_Schedule_ID','=','actual_payments.PaySched_ID')
    ->join('service_work','service_work.Work_ID','=','payment_schedule.Work_ID')
   ->join('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')
   ->join('sales_customer', 'sales_customer.Customer_ID','=','sales_lead.Cust_ID')
   //->join('work_tendering','work_tendering.Work_ID','=','service_work.Work_ID')
   //->join('associate', 'associate.Assoc_ID','=','work_tendering.Assoc_ID')
   ->select('actual_payments.*','payment_schedule.*','sales_customer.*')
    ->orderBy('actual_payments.Actual_Date', 'DESC')->get();*/
    $receipts=\DB::table('received_payments')
    
    ->join('service_work','service_work.Work_ID','=','received_payments.Work_ID')
   ->join('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')
   ->join('sales_customer', 'sales_customer.Customer_ID','=','sales_lead.Cust_ID')
   //->join('work_tendering','work_tendering.Work_ID','=','service_work.Work_ID')
   //->join('associate', 'associate.Assoc_ID','=','work_tendering.Assoc_ID')
   ->select('received_payments.*','sales_customer.*')
    ->orderBy('received_payments.Rec_Date', 'DESC')->get();
$resp=array($receipts);
return $resp;
/*$Amounts=\DB::table('actual_payments')
->join('payment_schedule','payment_schedule.Pay_Schedule_ID','=','actual_payments.PaySched_ID')
->join('service_work','service_work.Work_ID','=','payment_schedule.Work_ID')
->join('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')
->join('sales_customer', 'sales_customer.Customer_ID','=','sales_lead.Cust_ID')
->groupBy('Work_ID')
->groupBy('Actual_Date')
->select('payment_schedule.Work_ID','Actual_Date',\DB::raw('sum(Actual_Amount) as total'),'actual_payments.*','sales_customer.*','payment_schedule.*')
->orderBy('Actual_Date','DESC')->get();


$resp=array($Amounts);
return $resp;*/
}
public function updateReceivedPayment(Request $r)
{
    $values = Request::json()->all();

    /*$updation=\DB::table('actual_payments')->where('ActPay_ID',$values['payID'])
    ->update(array('Receipt_Status'=>1,'TransactionNo'=>$values['transID']));*/
    $updation=\DB::table('received_payments')->where('Rec_ID',$values['payID'])
    ->update(array('Receipt_Status'=>1,'Transaction_No'=>$values['transID']));
    
    $resp=array($updation);
    return $resp;
    
}
public function getAllWorkID()
{
    $ID=\DB::table('service_work')->select('Work_ID')->orderBy('Work_ID','ASC')->get();
    $resp=array($ID);
    return $resp;
}
public function getFilteredRecPay($WID, $CID, $TID)
{
    
if($TID==2)
{
    if($WID!=0)

    {
       /* $receipts=\DB::table('payment_schedule')
        ->join('actual_payments','payment_schedule.Pay_Schedule_ID','=','actual_payments.PaySched_ID')
        ->join('service_work','service_work.Work_ID','=','payment_schedule.Work_ID')
       ->join('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')
       ->join('sales_customer', 'sales_customer.Customer_ID','=','sales_lead.Cust_ID')
       //->join('work_tendering','work_tendering.Work_ID','=','service_work.Work_ID')
       //->join('associate', 'associate.Assoc_ID','=','work_tendering.Assoc_ID')
       ->select('actual_payments.*','payment_schedule.*','sales_customer.*')
       ->where('payment_schedule.Work_ID',$WID)
        ->orderBy('actual_payments.Actual_Date', 'DESC')->get();*/
        $receipts=\DB::table('received_payments')->join('service_work','service_work.Work_ID','=','received_payments.Work_ID')
        ->join('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')
        ->join('sales_customer', 'sales_customer.Customer_ID','=','sales_lead.Cust_ID')->where('received_payments.Work_ID', $WID)
        ->select('received_payments.*', 'sales_customer.*')
        ->orderBy('Rec_Date','DESC')->get();
    $resp=array($receipts);
    return $resp;
    }
    else if($CID!=0)
    {
        /*$receipts=\DB::table('actual_payments')
        ->join('payment_schedule','payment_schedule.Pay_Schedule_ID','=','actual_payments.PaySched_ID')
        ->join('service_work','service_work.Work_ID','=','payment_schedule.Work_ID')
       ->join('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')
       ->join('sales_customer', 'sales_customer.Customer_ID','=','sales_lead.Cust_ID')
       //->join('work_tendering','work_tendering.Work_ID','=','service_work.Work_ID')
       //->join('associate', 'associate.Assoc_ID','=','work_tendering.Assoc_ID')
       ->select('actual_payments.*','payment_schedule.*','sales_customer.*')
       ->where('sales_lead.Cust_ID',$CID)
        ->orderBy('actual_payments.Actual_Date', 'DESC')->get();*/
        $receipts=\DB::table('received_payments')->join('service_work','service_work.Work_ID','=','received_payments.Work_ID')
        ->join('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')
        ->join('sales_customer', 'sales_customer.Customer_ID','=','sales_lead.Cust_ID')
        ->where('sales_customer.Customer_ID', $CID)
        ->select('received_payments.*', 'sales_customer.*')
        ->orderBy('Rec_Date','DESC')->get();
    $resp=array($receipts);
    return $resp;
    }
}
    
}
public function getAllAssocs()
{
    $assocs=\DB::table('work_tendering')
    ->join('associate', 'associate.Assoc_ID','=','work_tendering.Assoc_ID')
    ->join('address','address.Address_ID','=','associate.Address_ID')
    ->join('contacts','contacts.Contact_ID','=','associate.Contact_ID')
    ->select('associate.*','address.*','contacts.*')->orderBy('associate.Assoc_FirstName','ASC')->distinct()->get();
    $resp=array($assocs);
    return $resp;
}
public function getFilteredPayments($wid,$cid,$tid,$aid,$init,$auth,$app,$mfee,$afee)
{
    $id_array=array();
    if($tid==1)
{
    if($wid!=0)

    {
    $details=\DB::table('initiate_payment')
    ->join('payment_status','payment_status.Pay_Status_ID','=','initiate_payment.PayStatus_ID')
    ->join('service_work', 'service_work.Work_ID','=','initiate_payment.Work_ID')
    ->join('work_tendering','work_tendering.Work_ID','=','initiate_payment.Work_ID')
     ->join('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')
    ->join('sales_customer', 'sales_customer.Customer_ID','=','sales_lead.Cust_ID') 
    ->join('associate', 'associate.Assoc_ID','=','work_tendering.Assoc_ID')
    ->where('work_tendering.SelectStatus',1)->where('initiate_payment.Work_ID',$wid)->where('initiate_payment.DeleteFlag',0)
    ->orderBy('initiate_payment.ReqDate', 'DESC')
    ->select('initiate_payment.*','sales_customer.*', 'payment_status.*',  'associate.*')
    ->get();
$resp=array($details);
return $resp;
}
else if($cid!=0)
{
    $details=\DB::table('initiate_payment')
    ->join('payment_status','payment_status.Pay_Status_ID','=','initiate_payment.PayStatus_ID')
    ->join('service_work', 'service_work.Work_ID','=','initiate_payment.Work_ID')
    ->join('work_tendering','work_tendering.Work_ID','=','initiate_payment.Work_ID')
     ->join('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')
    ->join('sales_customer', 'sales_customer.Customer_ID','=','sales_lead.Cust_ID') 
    ->join('associate', 'associate.Assoc_ID','=','work_tendering.Assoc_ID')
    ->where('work_tendering.SelectStatus',1)->where('sales_lead.Cust_ID',$cid)->where('initiate_payment.DeleteFlag',0)
    ->orderBy('initiate_payment.ReqDate', 'DESC')
    ->select('initiate_payment.*','sales_customer.*', 'payment_status.*',  'associate.*')
    ->get();
$resp=array($details);
return $resp;
}
else if($aid!=0)
{
    $details=\DB::table('initiate_payment')
    ->join('payment_status','payment_status.Pay_Status_ID','=','initiate_payment.PayStatus_ID')
    ->join('service_work', 'service_work.Work_ID','=','initiate_payment.Work_ID')
    ->join('work_tendering','work_tendering.Work_ID','=','initiate_payment.Work_ID')
     ->join('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')
    ->join('sales_customer', 'sales_customer.Customer_ID','=','sales_lead.Cust_ID') 
    ->join('associate', 'associate.Assoc_ID','=','work_tendering.Assoc_ID')
    ->where('work_tendering.SelectStatus',1)->where('work_tendering.Assoc_ID',$aid)
    ->where('initiate_payment.DeleteFlag',0)
    ->orderBy('initiate_payment.ReqDate', 'DESC')
    ->select('initiate_payment.*','sales_customer.*', 'payment_status.*',  'associate.*')
    ->get();
$resp=array($details);
return $resp;
}
else
{
    if($init!=0)
    {
        array_push($id_array, 1); 
    }
    else if($auth!=0)
    {
        array_push($id_array, 4); 
    }
    else if($app!=0)
    {
        array_push($id_array, 2); 
    }
    else if($mfee!=0)
    {
        array_push($id_array, 3); 
    }
    $details=\DB::table('initiate_payment')
    ->join('payment_status','payment_status.Pay_Status_ID','=','initiate_payment.PayStatus_ID')
    ->join('service_work', 'service_work.Work_ID','=','initiate_payment.Work_ID')
    ->join('work_tendering','work_tendering.Work_ID','=','initiate_payment.Work_ID')
     ->join('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')
    ->join('sales_customer', 'sales_customer.Customer_ID','=','sales_lead.Cust_ID') 
    ->join('associate', 'associate.Assoc_ID','=','work_tendering.Assoc_ID')
    ->where('work_tendering.SelectStatus',1)->whereIn('initiate_payment.PayStatus_ID', $id_array)
    ->where('initiate_payment.DeleteFlag',0)
    ->orderBy('initiate_payment.ReqDate', 'DESC')
    ->select('initiate_payment.*','sales_customer.*', 'payment_status.*',  'associate.*')
    ->get();
$resp=array($details,$id_array);
return $resp;
    
} /*if($init==1)
{
    $details=\DB::table('initiate_payment')
    ->join('payment_status','payment_status.Pay_Status_ID','=','initiate_payment.PayStatus_ID')
    ->join('service_work', 'service_work.Work_ID','=','initiate_payment.Work_ID')
    ->join('work_tendering','work_tendering.Work_ID','=','initiate_payment.Work_ID')
     ->join('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')
    ->join('sales_customer', 'sales_customer.Customer_ID','=','sales_lead.Cust_ID') 
    ->join('associate', 'associate.Assoc_ID','=','work_tendering.Assoc_ID')
    ->where('work_tendering.SelectStatus',1)->where('initiate_payment.PayStatus_ID',1)
    ->orderBy('initiate_payment.ReqDate', 'DESC')
    ->select('initiate_payment.*','sales_customer.*', 'payment_status.*',  'associate.*')
    ->get();
$resp=array($details);
return $resp;
}
else if($auth==1)
{
    $details=\DB::table('initiate_payment')
    ->join('payment_status','payment_status.Pay_Status_ID','=','initiate_payment.PayStatus_ID')
    ->join('service_work', 'service_work.Work_ID','=','initiate_payment.Work_ID')
    ->join('work_tendering','work_tendering.Work_ID','=','initiate_payment.Work_ID')
     ->join('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')
    ->join('sales_customer', 'sales_customer.Customer_ID','=','sales_lead.Cust_ID') 
    ->join('associate', 'associate.Assoc_ID','=','work_tendering.Assoc_ID')
    ->where('work_tendering.SelectStatus',1)->where('initiate_payment.PayStatus_ID',4)
    ->orderBy('initiate_payment.ReqDate', 'DESC')
    ->select('initiate_payment.*','sales_customer.*', 'payment_status.*',  'associate.*')
    ->get();
$resp=array($details);
return $resp;
}
else if($app==1)
{
    $details=\DB::table('initiate_payment')
    ->join('payment_status','payment_status.Pay_Status_ID','=','initiate_payment.PayStatus_ID')
    ->join('service_work', 'service_work.Work_ID','=','initiate_payment.Work_ID')
    ->join('work_tendering','work_tendering.Work_ID','=','initiate_payment.Work_ID')
     ->join('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')
    ->join('sales_customer', 'sales_customer.Customer_ID','=','sales_lead.Cust_ID') 
    ->join('associate', 'associate.Assoc_ID','=','work_tendering.Assoc_ID')
    ->where('work_tendering.SelectStatus',1)->where('initiate_payment.PayStatus_ID',2)
    ->orderBy('initiate_payment.ReqDate', 'DESC')
    ->select('initiate_payment.*','sales_customer.*', 'payment_status.*',  'associate.*')
    ->get();
$resp=array($details);
return $resp;
}
else if($mfee==1)
{
    $details=\DB::table('initiate_payment')
    ->join('payment_status','payment_status.Pay_Status_ID','=','initiate_payment.PayStatus_ID')
    ->join('service_work', 'service_work.Work_ID','=','initiate_payment.Work_ID')
    ->join('work_tendering','work_tendering.Work_ID','=','initiate_payment.Work_ID')
     ->join('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')
    ->join('sales_customer', 'sales_customer.Customer_ID','=','sales_lead.Cust_ID') 
    ->join('associate', 'associate.Assoc_ID','=','work_tendering.Assoc_ID')
    ->where('work_tendering.SelectStatus',1)->where('initiate_payment.MFee_Flag',1)
    ->orderBy('initiate_payment.ReqDate', 'DESC')
    ->select('initiate_payment.*','sales_customer.*', 'payment_status.*',  'associate.*')
    ->get();
$resp=array($details);
return $resp;
}
else if($afee==1)
{
    $details=\DB::table('initiate_payment')
    ->join('payment_status','payment_status.Pay_Status_ID','=','initiate_payment.PayStatus_ID')
    ->join('service_work', 'service_work.Work_ID','=','initiate_payment.Work_ID')
    ->join('work_tendering','work_tendering.Work_ID','=','initiate_payment.Work_ID')
     ->join('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')
    ->join('sales_customer', 'sales_customer.Customer_ID','=','sales_lead.Cust_ID') 
    ->join('associate', 'associate.Assoc_ID','=','work_tendering.Assoc_ID')
    ->where('work_tendering.SelectStatus',1)->where('initiate_payment.AssocPay_Flag',1)
    ->orderBy('initiate_payment.ReqDate', 'DESC')
    ->select('initiate_payment.*','sales_customer.*', 'payment_status.*',  'associate.*')
    ->get();
$resp=array($details);
return $resp;
}
}*/
}

}
public function getFilPayments($FAarray)
{

        $resp=array($FAarray->WorkID);
        return $resp;
    

}
public function checkPaymentExists($id)
{
    $totalRec=\DB::table('payment_schedule')
    ->join('actual_payments','payment_schedule.Pay_Schedule_ID','=','actual_payments.PaySched_ID' )
    ->where('Work_ID', $id)
  ->sum('actual_payments.Actual_Amount');
  
   $totalInit=\DB::table('initiate_payment')->where('Work_ID', $id)->where('DeleteFlag',0)->sum('ReqAmount');
   $existsPayment=$totalRec-$totalInit;
   $resp=array('Rec'=>$totalRec,'Init'=>$totalInit,'Bal'=>$existsPayment);
   return $resp;
}
 
public function getAssociateServices($id)
{
    $segments=\DB::table('associate_segment_rate')
    //->join('ser_assoc_services','ser_assoc_services.SerSev_ID','=','associate_segment_rate.Service_ID')
    ->join('segment','Segment.Segment_ID','=','associate_segment_rate.Segment_ID')
    ->join('services','services.Service_ID','=','associate_segment_rate.Service_ID')
    ->where('associate_segment_rate.Assoc_ID',$id)
    ->select('Segment_Name')->distinct()->get();//,'Service_Name')->orderBy('associate_segment_rate.Segment_ID')

    $services=\DB::table('associate_segment_rate')->join('services','services.Service_ID','=','associate_segment_rate.Service_ID')
    ->where('associate_segment_rate.Assoc_ID',$id)
    ->select('Service_Name')->distinct()->get();
    $resp=array('Segments'=>$segments, 'Services'=>$services);
    return $resp;
}
public function getServiceAssociates($id)
{
    $certifyList=\DB::table('associate_segment_rate')
    ->join('associate','associate.Assoc_ID','=','associate_segment_rate.Assoc_ID')
        ->leftjoin('contacts','contacts.Contact_ID','=','associate.Assoc_ID')
		
		//->join('associate_details', 'associate_details.Assoc_ID', '=','associate.Assoc_ID')
		//->join('location', 'location.Loc_ID', '=','associate_details.Loc_ID')
		->where('associate_segment_rate.Service_ID', $id)
        //->where('associate.Assoc_Status', 4)
        ->select('associate.*','contacts.Contact_phone')
		->get();
		$resp=array($certifyList);
		return $resp;
}
public function getRecAmountByDate($id)
{
    /*$Amounts=\DB::table('actual_payments')->join('payment_schedule','payment_schedule.Pay_Schedule_ID','=','actual_payments.PaySched_ID')
    ->where('Work_ID',$id)
    ->groupBy('Actual_Date')
    ->select('Actual_Date',\DB::raw('sum(Actual_Amount) as total'))
    ->orderBy('Actual_Date','DESC')->get();*/
    $Amounts=\DB::table('received_payments')
    ->where('Work_ID',$id)
    
    
    ->orderBy('Rec_Date','DESC')->get();


    $resp=array($Amounts);
    return $resp;
}

public function chkReasonExists($id)
{
    $AmendFlag=\DB::table('work_labour_estimation')->where('Work_ID',$id)->select(\DB::raw('MAX(Amend_Flag) AS Max'))->get();
$AmendNo=$AmendFlag[0]->Max +1;

$chkReason=\DB::table('work_amendment_issuedate')->where('Work_ID',$id)->where('Amend_No',$AmendNo)->get();
$resp=array($chkReason);
return $resp;
}
public function updateAmendReason(Request $r)
{ 
    $values = Request::json()->all();
    $AmendFlag=\DB::table('work_labour_estimation')->where('Work_ID',$values['workid'])->select(\DB::raw('MAX(Amend_Flag) AS Max'))->get();
    $AmendNo=$AmendFlag[0]->Max +1;
   
    $insertAmendReason=\DB::table('work_amendment_issuedate')->insert(array('Work_ID'=>$values['workid'],'Amend_No'=>$AmendNo, 'Reason'=>$values['reason'], 'Comment'=>$values['comment']));
    $resp=array('Success'=>true);
    return $resp;
}
public function getAmendReasons()
{
    $reasons=\DB::table('amendment_reasons')->get();
    $resp=array($reasons);
    return $resp;
}
public function chkStartDateExists($id)
{
    $exists=\DB::table('work_schedule')->where('Work_Schedule_ID',$id)->pluck('ActualStart_Date');
    $resp=array($exists);
    return $resp;
}
public function getAllMFeeDetails()
{
    $details=\DB::table('initiate_payment')
    ->join('payment_status','payment_status.Pay_Status_ID','=','initiate_payment.PayStatus_ID')
    ->join('service_work', 'service_work.Work_ID','=','initiate_payment.Work_ID')
    ->join('work_tendering','work_tendering.Work_ID','=','initiate_payment.Work_ID')
     ->join('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')
    ->join('sales_customer', 'sales_customer.Customer_ID','=','sales_lead.Cust_ID') 
    ->join('associate', 'associate.Assoc_ID','=','work_tendering.Assoc_ID')
   ->join('address','address.Address_ID','=','associate.Address_ID')
   ->join('contacts','contacts.Contact_ID','=','associate.Contact_ID')
   
    ->where('work_tendering.SelectStatus',1)
    ->where('MFee','!=',0)
    ->where('initiate_payment.DeleteFlag',0)
    ->orderBy('initiate_payment.ReqDate', 'DESC')
    ->select('initiate_payment.*','sales_customer.*', 'payment_status.*',  'associate.*', 'address.*','contacts.Contact_phone')
    ->get();
    $resp=array($details);
    return $resp;
}
public function getAllPaidDetails()
{
    $details=\DB::table('initiate_payment')
    ->join('payment_status','payment_status.Pay_Status_ID','=','initiate_payment.PayStatus_ID')
    ->join('service_work', 'service_work.Work_ID','=','initiate_payment.Work_ID')
    ->join('work_tendering','work_tendering.Work_ID','=','initiate_payment.Work_ID')
     ->join('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')
    ->join('sales_customer', 'sales_customer.Customer_ID','=','sales_lead.Cust_ID') 
    ->join('associate', 'associate.Assoc_ID','=','work_tendering.Assoc_ID')
   ->join('address','address.Address_ID','=','associate.Address_ID')
   ->join('contacts','contacts.Contact_ID','=','associate.Contact_ID')
   
    ->where('work_tendering.SelectStatus',1)
    ->where('PayStatus_ID',3)
    ->where('initiate_payment.DeleteFlag',0)
    ->orderBy('initiate_payment.ReqDate', 'DESC')
    ->select('initiate_payment.*','sales_customer.*', 'payment_status.*',  'associate.*', 'address.*','contacts.Contact_phone')
    ->get();
    $resp=array($details);
    return $resp;
}
public function getPayDetails($id)
{
    $details=\DB::table('initiate_payment')
    ->join('payment_status','payment_status.Pay_Status_ID','=','initiate_payment.PayStatus_ID')
    ->join('service_work', 'service_work.Work_ID','=','initiate_payment.Work_ID')
    ->join('work_tendering','work_tendering.Work_ID','=','initiate_payment.Work_ID')
     ->join('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')
    ->join('sales_customer', 'sales_customer.Customer_ID','=','sales_lead.Cust_ID') 
    ->join('associate', 'associate.Assoc_ID','=','work_tendering.Assoc_ID')
   ->join('address','address.Address_ID','=','associate.Address_ID')
   ->join('contacts','contacts.Contact_ID','=','associate.Contact_ID')
   
    ->where('work_tendering.SelectStatus',1)->where('initiate_payment.DeleteFlag',0)
    ->where('InitPay_ID',$id)
    
    ->select('initiate_payment.*','sales_customer.*', 'payment_status.*',  'associate.*', 'address.*','contacts.*')
    ->get();
    $resp=array($details);
    return $resp;
}
public function workLost(Request $r)
{
    $values = Request::json()->all();
    $reason=\DB::table('work_timeline')->insert(array('Work_ID'=>$values['work_ID'], 'Work_Attrb_ID'=>39, 'Value'=>$values['reason']));
    if(!empty($reason))
    {
        $statusChange=\DB::table('service_work')->where('Work_ID', $values['work_ID'])->update(array('WorkStatus'=>11));
    }
   
    $resp=array($statusChange);
    return $resp;
}
public function getWorkLostReason($id)
{
    $reason=\DB::table('work_timeline')->where('Work_ID',$id)->where('Work_Attrb_ID',39)->get();
    $resp=array($reason);
    return $resp;
}
//function to display balnce of received amount while select receipts
public function getBalReceipt($id)
{
    $recAmt=\DB::table('received_payments')->where('Rec_ID',$id)->pluck('Rec_Amount');
    $splitAmt=\DB::table('split_payments')->where('Rec_ID',$id)->sum('Split_Amount');
    $balance=$recAmt[0]-$splitAmt;
    $resp=array($balance);
    return $resp;
}
//function to get receipt date and type
public function getReciptDetails($id)
{
    $details=\DB::table('received_payments')->where('Rec_ID',$id)->select('Rec_Date','Type')->get();
    $resp=array($details);
    return $resp;
}

//function to generate report
public function generateReport(Request $r)
{
    $values = Request::json()->all();
    $search=collect($values);
   $newArray=[];
    $filterArray=[];
    $finalArray=[];
    $sortArray=[];
    $receipts=\DB::table('received_payments')
    ->join('service_work', 'service_work.Work_ID','=','received_payments.Work_ID')
    ->join('work_tendering','work_tendering.Work_ID','=','received_payments.Work_ID')
     ->join('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')
    ->join('sales_customer', 'sales_customer.Customer_ID','=','sales_lead.Cust_ID') 
    ->join('associate', 'associate.Assoc_ID','=','work_tendering.Assoc_ID')
  // ->join('address','address.Address_ID','=','associate.Address_ID')
  // ->join('contacts','contacts.Contact_ID','=','associate.Contact_ID')
   
    ->where('work_tendering.SelectStatus',1)
    ->select('received_payments.Work_ID as Work_ID','Rec_Amount as Amount','Rec_Date AS PayDate',
    'received_payments.Type as Type','Transaction_No as TransNo','sales_customer.Customer_ID','sales_customer.Cust_FirstName','sales_customer.Cust_MidName',
    'sales_customer.Cust_LastName', 'associate.Assoc_FirstName as assoc_FirstName','associate.Assoc_MiddleName','associate.Assoc_LastName as assoc_LastName','associate.Assoc_ID', \DB::raw("'0' as Flag"))
    //->orderBy('Rec_Date', 'DESC')
    ->get();
    $assocPays=\DB::table('initiate_payment')
    ->join('service_work', 'service_work.Work_ID','=','initiate_payment.Work_ID')
    ->join('work_tendering','work_tendering.Work_ID','=','initiate_payment.Work_ID')
     ->join('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')
    ->join('sales_customer', 'sales_customer.Customer_ID','=','sales_lead.Cust_ID') 
    ->join('associate', 'associate.Assoc_ID','=','work_tendering.Assoc_ID')
  
   
    ->where('work_tendering.SelectStatus',1)
    ->where('initiate_payment.DeleteFlag',0)
    ->select('initiate_payment.Work_ID as Work_ID','AssocPay as Amount','AssocPay_Date AS PayDate','Assoc_Trans_Type as Type','Assoc_Trans_ID as TransNo','sales_customer.Customer_ID','sales_customer.Cust_FirstName','sales_customer.Cust_MidName','sales_customer.Cust_LastName', 'associate.Assoc_FirstName as assoc_FirstName','associate.Assoc_MiddleName','associate.Assoc_LastName as assoc_LastName','associate.Assoc_ID', \DB::raw("'1' as Flag"))
    ->where('AssocPay_Flag',1)->where('AssocPay', '!=',0)
    
    ->get();
    $mfee=\DB::table('initiate_payment')
    ->join('service_work', 'service_work.Work_ID','=','initiate_payment.Work_ID')
    ->join('work_tendering','work_tendering.Work_ID','=','initiate_payment.Work_ID')
     ->join('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')
    ->join('sales_customer', 'sales_customer.Customer_ID','=','sales_lead.Cust_ID') 
    ->join('associate', 'associate.Assoc_ID','=','work_tendering.Assoc_ID')
    ->where('initiate_payment.DeleteFlag',0)
    ->where('work_tendering.SelectStatus',1)
    ->select('initiate_payment.Work_ID as Work_ID','MFee as Amount','M_PaidDate AS PayDate','Trans_Type as Type','Trans_ID as TransNo','sales_customer.Customer_ID','sales_customer.Cust_FirstName','sales_customer.Cust_MidName','sales_customer.Cust_LastName', 'associate.Assoc_FirstName as assoc_FirstName','associate.Assoc_MiddleName','associate.Assoc_LastName as assoc_LastName','associate.Assoc_ID', \DB::raw("'2' as Flag"))
    
    ->where('MFee_Flag',1)->where('MFee','!=',0)->get();
    if($receipts)
    {
        foreach ($receipts as $rec)
        {
            array_push($newArray, $rec);
        }
    }
    if($assocPays)
    {
        foreach($assocPays as $assoc)
        {
            array_push($newArray, $assoc);
        }
    }
    if($mfee)
    {
        foreach($mfee as $fee)
        {
            array_push($newArray, $fee);
        }
    }
  array_multisort( array_column($newArray, "PayDate"), SORT_ASC, $newArray );
  foreach($newArray as $a)
  {
    if(( $values['workID'] == null || ($values['workID']&& $a->Work_ID==$values['workID'])) &&
    ($values['custName'] == null  || ($values['custName'] && $a->Customer_ID==$values['custName'])) &&
    ($values['assocName'] == null  || ($values['assocName']&& $a->Assoc_ID==$values['assocName'] && $a->Flag!=0)) &&
    ($values['startDate'] == null  || ($values['startDate']&& $a->PayDate>=$values['startDate'])) &&
    ($values['endDate'] == null  || ($values['endDate']&& $a->PayDate<=$values['endDate'])))
    {
        array_push($filterArray,$a);
       

    }
   
  }
 
     
    

  
  
    

$sum=collect($filterArray)->where('Flag',0)->sum('Amount');
$assocSum=collect($filterArray)->where('Flag',1)->sum('Amount');
$mfeeSum=collect($filterArray)->where('Flag',2)->sum('Amount');
$paysum=$assocSum+$mfeeSum;
$bal=$sum - $paysum;
$resp=array($filterArray, 'RecSum'=>$sum, 'PaySum'=>$paysum, 'Balance'=>$bal);
return $resp;

}
}
  