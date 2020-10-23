<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Input;
//use Illuminate\Http\Request;
use Request;
use DateTime;
use Response;
use File;


class tenderingController extends Controller
{
    public function getAllBIWorkList($name)
	{
        $BIList=\DB::table('service_work')
        ->join('segment','segment.Segment_ID','=','service_work.Segment_ID')
		->join('sales_lead','sales_lead.Lead_ID','=','service_work.Lead_ID')
		->join('sales_customer', 'sales_lead.Cust_ID','=','sales_customer.Customer_ID')
		->join('location','location.Loc_ID','=','sales_lead.Lead_LocID')
		->join('services','services.Service_ID','=','service_work.Service_ID')
		->join('work_status', 'work_status.Work_Status_ID','=','service_work.WorkStatus')
		->join('work_updation_status','work_updation_status.Update_Status_ID','=','service_work.Update_Status')
	   // ->where('WorkStatus',3)
	   ->where('service_work.Assigned_To',$name)
	   ->orderBy('service_work.Work_ID', 'DESC')
	   ->get();
		$response=array($BIList);
		return $response;	
	}

	public function getAssignees($name)
	{
		$deptID=\DB::table('department')->where('Dept_Name',$name)->pluck('Dept_ID');

		$assignee=\DB::table('logins')->select('User_Login')->where('Dept_ID', $deptID[0])->get();
		$resp=array($assignee);
		return $resp;
	}

	public function updateAssignees(Request $r)
	{
		$values = Request::json()->all();
		$update=\DB::table('service_work')->where('Work_ID', $values['work_ID'])
		->update(array('AssignedDept'=> $values['assignDept'], 'Assigned_To'=> $values['assignTo']));

		$resp=array($update, "Dept"=>$values['assignDept']);
		return $resp;
	}

	public function getCertifyAssocList($id)
	{
		$service_Id=\DB::table('work_service_map')->where('Work_ID', $id)->pluck('Service_ID');
	if(!empty($service_Id))
	{
		$certifyList=\DB::table('associate')
		->join('associate_segment_rate', 'associate_segment_rate.Assoc_ID','=','associate.Assoc_ID')
		
		->join('associate_details', 'associate_details.Assoc_ID', '=','associate.Assoc_ID')
		->leftjoin('location', 'location.Loc_ID', '=','associate_details.Loc_ID')
		->whereIn('associate_segment_rate.Service_ID', $service_Id)
		//->where('associate.Assoc_Status', 4)
		->get();
		$resp=array($certifyList);
		return $resp;
	}
	else{
		$resp=array("Success"=>false);
		return $resp;
	}
		

	}
	public function getAssocList()
	{
		
		$certifyList=\DB::table('associate')
		->where('ServiceFlag',1)->orderBy('Assoc_FirstName','ASC')
		->select('Assoc_ID','Assoc_FirstName', 'Assoc_MiddleName','Assoc_LastName')
		//->where('associate.Assoc_Status', 4)
		->get();
		$resp=array($certifyList);
		return $resp;
	
		

	}
	public function saveAssocList(Request $r)
	{
		/*$values = Request::json()->all();
		$id=$values['param1'];
	$items=$values['param2'];
		$dataset=[];
		$dataset1=[];
		$assocList =[];
	foreach($items as $value)
	{
		$exists=\DB::table('work_tendering')->where('Work_ID', $id)
		->where('Assoc_ID', $value['name'])->get();
		$count=count($exists);
		if($count==0)
		{
		$dataset[]=['Work_ID' => $id, 'Assoc_ID'=> $value['name']];
		}
		else{
			return;
		}
	}
	
	
		$assocList[]=\DB::table('work_tendering')->insert($dataset);

		/*if(!empty($assocList))
		{
$itemID=\DB::table('work_labour_estimation')->where('Work_ID', $id)->pluck('LineItem_ID');

foreach($itemID as $item)
{
	$dataset1[]=['Work_ID'=>$id, 'LineItem_ID'=>$item['LineItem_ID']];
}


			$changeWorkFlag=\DB::table('service_work')->where('Work_ID',$id)
			->update(array('AssocSelectFlag'=>2));

		
		$resp=array("Success"=>true);
		return $resp;*/
		\DB::transaction(function() use ($r) {
		$values = Request::json()->all();
        
		$id=$values['param1'];
    $items=$values['param2'];
    
		$dataset=[];
		$dataset1=[];
        $assocList =[];
        $scope=\DB::table('work_labour_estimation')->where('Work_ID', $id)->where('deleteFlag',0)->select('LE_ID','LineItem_ID', 'Quantity','Comments', 'Priority')->get();
       
	foreach($items as $value)
	{
       // $typeID=$value['typeID'];
		$exists=\DB::table('work_tendering')->where('Work_ID', $id)
		->where('Assoc_ID', $value['name'])->get();
		$count=count($exists);
		if($count==0)
		{
           
                $tenderID=\DB::table('work_tendering')->insertGetID(array('Work_ID'=>$id, 'Assoc_ID'=>$value['name'],'Online_Flag'=>0));
                foreach($scope as $item)
                {
                   
                    $exists=\DB::table('work_tender_details_lab')->where('WorkTender_ID', $tenderID)
            ->where('LineItem_ID', $item->LineItem_ID)->get();
                    $count=count($exists);
                    if($count==0)
                    {
                    $dataset[]=['WorkTender_ID' => $tenderID, 'LineItem_ID'=> $item->LineItem_ID, 'Quantity'=> $item->Quantity,'Comments'=>$item->Comments, 'Priority'=>$item->Priority];
                    }
                   
                    
                }
				
			}
			

		}
		$items_insert=\DB::table('work_tender_details_lab')->insert($dataset);
		$resp=array('Succees'=>true);
		return $resp;
	});
	}
	

	public function getSelectedAssocs($id)
	{
$selectedList=\DB::table('work_tendering')->where('Work_ID', $id)
->where('work_tendering.updateFlag',0)
->join('associate', 'associate.Assoc_ID', '=','work_tendering.Assoc_ID')
//->leftjoin('work_tender_details_mat','work_tender_details_mat.WorkTender_ID', '=','work_tendering.WorkTender_ID')
->get();
$resp=array($selectedList);
return $resp;
	}
	public function getTenderAssocs($id)
	{
$selectedList=\DB::table('work_tendering')->where('Work_ID', $id)
//->where('deleteFlag',1)
//->where('DeleteFlag',0)

->join('associate', 'associate.Assoc_ID', '=','work_tendering.Assoc_ID')
->join('associate_details', 'associate_details.Assoc_ID','=','associate.Assoc_ID')
->join('contacts', 'contacts.Contact_ID', '=','associate.Contact_ID')
//->leftjoin('work_tender_details_mat','work_tender_details_mat.WorkTender_ID', '=','work_tendering.WorkTender_ID')
->get();
$resp=array($selectedList);
return $resp;
	}

	public function getFinalTenderAssoc($id)
	{
		$selectedList=\DB::table('work_tendering')->where('Work_ID', $id)->where('SelectStatus',1)
//->where('deleteFlag',1)
//->where('DeleteFlag',0)

->join('associate', 'associate.Assoc_ID', '=','work_tendering.Assoc_ID')
->join('associate_details', 'associate_details.Assoc_ID','=','associate.Assoc_ID')
->join('contacts', 'contacts.Contact_ID', '=','associate.Contact_ID')
->join('address','address.Address_ID','=','associate.Address_ID')

//->leftjoin('work_tender_details_mat','work_tender_details_mat.WorkTender_ID', '=','work_tendering.WorkTender_ID')
->get();
$resp=array($selectedList);
return $resp;
	}

	public function getSelectedTenderAssocs($id)
	{
		$selectedList=\DB::table('work_tendering')->where('Work_ID', $id)
->where('deleteFlag',1)
//->where('DeleteFlag',0)

->join('associate', 'associate.Assoc_ID', '=','work_tendering.Assoc_ID')
//->leftjoin('work_tender_details_mat','work_tender_details_mat.WorkTender_ID', '=','work_tendering.WorkTender_ID')
->get();
$resp=array($selectedList);
return $resp;
	}

	public function getTenderItemName($id)
	{
		//$itemName=\DB::table('')
	}

	public function saveTenderMatDetails(Request $r)
	{
		$values = Request::json()->all();
		$qty=$values['qty'];
		$rate=$values['rate'];
		$saveDetails=\DB::table('work_tender_details_mat')->insert(array('WorkTender_ID'=>$values['assocList'],'LineItem_ID'=>$values['MEID'], 'Rate'=>$values['rate'],
		 'Quantity'=>$values['qty'], 'Value'=>$qty * $rate, 'Comment'=>$values['comment'], 'updateFlag'=>1));

		 /*if(!empty($saveDetails))
		 {
			 $update=\DB::table('work_tendering')->where('WorkTender_ID', $values['assocList'])->update(array('updateFlag'=>1));
			 
		 }*/
		 if(!empty($saveDetails))
		 {
			 $update=\DB::table('work_labour_estimation')->where('LE_ID', $values['MEID'])->update(array('assocSelFlag'=>1));
			 
		 }
		 $resp=array("Success"=>true);
		 return $resp;
	}

	public function getSelectedAssocsUpdated($id)
	{
		$selectedList=\DB::table('work_tendering')->where('Work_ID', $id)
		->where('work_tender_details_mat.updateFlag',1)
		->leftjoin('associate', 'associate.Assoc_ID', '=','work_tendering.Assoc_ID')
		->join('work_tender_details_mat','work_tender_details_mat.WorkTender_ID', '=','work_tendering.WorkTender_ID')
		->get();
		$resp=array($selectedList);
		return $resp;
	}

	public function saveTenderLabDetails(Request $r)
	{
		
		$values = Request::json()->all();
		$qty=$values['qty'];
		$rate=$values['rate'];
		$saveDetails=\DB::table('work_tender_details_lab')->where('WorkTenderLab_ID',$values['MEID'])
		->where('LineItem_ID',$values['itemID'])->update(array('Rate'=>$values['rate'],
		 'Quantity'=>$values['qty'], 'Value'=>$qty * $rate, 'LabNo'=>$values['labNo'],'Days'=>$values['days'],
		 'updateFlag'=>1,'Tender_Comments'=>$values['comment']));

		 if(!empty($saveDetails))
		 {
			$totalQuote=\DB::table('work_tender_details_lab')->where('WorkTender_ID',$values['WTID'])->sum('Value');
			 $update=\DB::table('work_tendering')->where('WorkTender_ID', $values['WTID'])->update(array('TotalQuote'=>$totalQuote));
			/* $updateEst=\DB::table('work_labour_estimation')->where('LE_ID', $values['MEID'])
			 ->where('Work_ID',$values['workID'])->update(array('assocSelFlag'=>1));*/
		 }
		 $resp=array("Success"=>true, $saveDetails);
		 return $resp;
	}
	public function changeUpdateFlag($id)
	{
		$updateFlag=\DB::table('work_material_estimation')->where('Work_ID',$id)->update(array('tenderFlag'=>1));
		$changeAssocSeleFlag=\DB::table('work_tendering')->where('Work_ID', $id)->update(array('updateFlag'=>0));
		$resp=array($updateFlag);
		return $resp;
	}

	/*public function chkDisableQuote($id)
	{
		$chk=\DB::table('work_tender_details_mat')->where('LineItem_ID', $id)->sum('updateFlag');
		$resp=array($chk);
		return $resp;
	}*/
	public function getallMatEstimateTender($id)
{
	$MatEstimates=\DB::table('work_material_estimation')
	->join('products','products.Prod_ID','=','work_material_estimation.Product_ID')
	->where('Work_ID', $id)->where('deleteFlag',1)
	->where('tenderFlag',0)->get();
	$count=count($MatEstimates);
	$resp=array($MatEstimates, $count);
	return $resp;
}

public function getAssocTender($id)
{
	
/*$type=\DB::table('service_work')->where('Work_ID', $id)->pluck('Work_Type');
if($type['0']=="Labour Only")
{*/
	$tender=\DB::table('work_tender_details_lab')->where('work_tendering.WorkTender_ID', $id)
	->join('work_tendering','work_tender_details_lab.WorkTender_ID', '=','work_tendering.WorkTender_ID')
	->leftjoin('serv_line_items', 'serv_line_items.LineItem_ID', '=','work_tender_details_lab.LineItem_ID')
	//->join('work_labour_estimation', 'work_labour_estimation.LineItem_ID','=','serv_line_items.LineItem_ID')
	->leftjoin('units', 'units.Unit_ID','=','serv_line_items.UnitID')
	->leftjoin('associate', 'associate.Assoc_ID','=','work_tendering.Assoc_ID')
	->leftjoin('contacts', 'contacts.Contact_ID','=','associate.Contact_ID')
	
	->leftjoin('associate_details', 'associate_details.Assoc_ID','=', 'associate.Assoc_ID')
	->leftjoin('location', 'location.Loc_ID','=','associate_details.Loc_ID')
	->leftjoin('address', 'address.Address_ID','=','associate.Address_ID')
	//->where('work_labour_estimation.Amend_Flag',0)
	//->where('work_labour_estimation.deleteFlag',0)
	->orderBy('work_tender_details_lab.Priority', 'ASC')
	->select('work_tender_details_lab.*','serv_line_items.LineItem_ID','serv_line_items.LineItem_Name','units.Unit_Code','work_tendering.*')
	->get();

	$resp=array($tender);
	return $resp;
	/*$workid=\DB::table('work_tendering')->where('WorkTender_ID', $id)->pluck('Work_ID');
	$tender=\DB::table('work_tendering')->
	join('work_tender_details_lab', 'work_tendering.WorkTender_ID','=','work_tender_details_lab.WorkTender_ID')
	->join('serv_line_items', 'serv_line_items.LineItem_ID','=','work_tender_details_lab.LineItem_ID')
	->join('work_labour_estimation', 'work_labour_estimation.LineItem_ID','=','serv_line_items.LineItem_ID')
	->join('units', 'units.Unit_ID','=','serv_line_items.UnitID')
	->where('work_tender_details_lab.WorkTender_ID', $id)
	->where('work_labour_estimation.Work_ID', $workid)
	->where('work_labour_estimation.Amend_Flag',0)
	->where('work_labour_estimation.deleteFlag',0)
	->select('work_labour_estimation.Comments','work_labour_estimation.Amend_Flag','work_tender_details_lab.*','serv_line_items.LineItem_ID','serv_line_items.LineItem_Name','units.Unit_Code','work_tendering.*')
	->orderBy('work_tender_details_lab.Quantity','DESC')
	->get();

	$resp=array($tender);
	return $resp;*/
/*}
else if($type['0']=="Material Only")
{
	$tender=\DB::table('work_tendering')->where('work_tendering.Assoc_ID', $value)
	->join('work_tender_details_mat','work_tender_details_mat.WorkTender_ID', '=','work_tendering.WorkTender_ID')
	->get();
	$resp=array($tender, "Type"=>$type);
	return $resp;
}*/

}

public function getAssocTenderMat($value, $id)
{
	$tender=\DB::table('work_tendering')->where('work_tendering.Assoc_ID', $value)
	->join('work_tender_details_mat','work_tender_details_mat.WorkTender_ID', '=','work_tendering.WorkTender_ID')
	->get();
	$resp=array($tender, "Type"=>$type);
	return $resp;
}

public function getWorkType($id)
{
	$type=\DB::table('service_work')->where('Work_ID', $id)->select('Work_Type')->get();
	$resp=array($type);
	return $resp;
}

public function finalizeAssoc($id)
{
	$changeStatus=\DB::table('work_tendering')->where('WorkTender_ID',$id)->update(array('SelectStatus'=> 1));
	$resp=array("Success"=>true, $changeStatus);
	return $resp;
}

public function getCountItemsLab($id)
{
	$items=\DB::table('work_labour_estimation')->where('Work_ID', $id)->where('deleteFlag',0)
	->select('LineItem_ID')->get();
$itemsCount=count($items);
	

	$resp=array($itemsCount);
	return $resp;

}
public function getCountItemsLabTender($id)
{
	$items=\DB::table('work_tender_details_lab')->where('WorkTender_ID', $id)->where('deleteFlag',0)
	->select('LineItem_ID')->get();
$itemsCount=count($items);
	

	$resp=array($itemsCount);
	return $resp;

}
public function getCountFlagsLab($id)
{
	$flags=\DB::table('work_labour_estimation')->where('Work_ID', $id)
	->where('assocSelFlag',1)->get();

$flagsCount=count($flags);
	$resp=array($flagsCount);
	return $resp;
}

public function getCountFlagsLabTender($id)
{
	$flags=\DB::table('work_tender_details_lab')->where('WorkTender_ID', $id)
	->where('updateFlag',1)->get();

$flagsCount=count($flags);
	$resp=array($flagsCount);
	return $resp;
}

public function getTotalQuote($id)
{
$total=\DB::table('work_tendering')->where('WorkTender_ID', $id)
//->select('TotalQuote')
->get();
$resp=array($total);
return $resp;
}

public function getTenderDetails($tid)
{
	$workid=\DB::table('work_tendering')->where('WorkTender_ID', $tid)->pluck('Work_ID');

	/*$tenderDetails=\DB::table('work_tendering')->where('work_tendering.Work_ID', $workid[0])
	->where('work_tendering.WorkTender_ID', $tid)
	->join('work_tender_details_lab', 'work_tender_details_lab.WorkTender_ID','=','work_tendering.WorkTender_ID')
	->join('serv_line_items', 'serv_line_items.LineItem_ID','=','work_tender_details_lab.LineItem_ID')
	//->leftjoin('work_labour_estimation', 'work_labour_estimation.LineItem_ID','=','work_tender_details_lab.LineItem_ID')
	//->join('associate', 'associate.Assoc_ID', '=','work_tendering.Assoc_ID')
	->get();*/
	$tenderDetails=\DB::table('work_tender_details_lab')->join('work_tendering', 'work_tendering.WorkTender_ID','=','work_tender_details_lab.WorkTender_ID')
	->join('serv_line_items', 'serv_line_items.LineItem_ID','=','work_tender_details_lab.LineItem_ID')
	->join('units', 'units.Unit_ID','=','serv_line_items.UnitID')
	//->join('work_labour_estimation', 'work_labour_estimation.LineItem_ID','=','serv_line_items.LineItem_ID')
	->where('work_tendering.Work_ID', $workid[0])
	//->where('work_labour_estimation.Amend_Flag',0)
	//->where('work_labour_estimation.deleteFlag',0)
	->where('work_tender_details_lab.WorkTender_ID', $tid)
	->orderBy('work_tender_details_lab.Priority','asc')
	//->select('work_tender_details_lab.*','work_labour_estimation.Comments','serv_line_items.*')
	->get();
	$resp=array($tenderDetails);
	return $resp;
}
public function getTenderedAssocs($id)
{
	$assocs=\DB::table('work_tendering')->where('work_tendering.Work_ID', $id)
	->join('associate', 'associate.Assoc_ID','=','work_tendering.Assoc_ID')->where('work_tendering.TenderFinish_Flag',1)->get();//->where('work_tendering.updateFlag',1)
	$resp=array($assocs);
	return $resp;
}
public function getWorkTenderDetails($id)
{
	$workid=\DB::table('work_tendering')->where('Work_ID', $id)->pluck('WorkTender_ID');

	/*$tenderDetails=\DB::table('work_tendering')->where('work_tendering.Work_ID', $workid[0])
	->where('work_tendering.WorkTender_ID', $tid)
	->join('work_tender_details_lab', 'work_tender_details_lab.WorkTender_ID','=','work_tendering.WorkTender_ID')
	->join('serv_line_items', 'serv_line_items.LineItem_ID','=','work_tender_details_lab.LineItem_ID')
	//->leftjoin('work_labour_estimation', 'work_labour_estimation.LineItem_ID','=','work_tender_details_lab.LineItem_ID')
	//->join('associate', 'associate.Assoc_ID', '=','work_tendering.Assoc_ID')
	->get();*/
	$tenderDetails=\DB::table('work_tender_details_lab')->join('work_tendering', 'work_tendering.WorkTender_ID','=','work_tender_details_lab.WorkTender_ID')
	->join('serv_line_items', 'serv_line_items.LineItem_ID','=','work_tender_details_lab.LineItem_ID')
	->join('units', 'units.Unit_ID','=','serv_line_items.UnitID')
	->where('work_tendering.Work_ID', $id)
	->where('work_tender_details_lab.WorkTender_ID', $workid[0])
	->get();
	$resp=array($tenderDetails);
	return $resp;
}

public function getAssocDetails($id)
{
	$details=\DB::table('work_tendering')->where('work_tendering.Work_ID', $id)->where('work_tendering.SelectStatus',1)
	->join('associate', 'associate.Assoc_ID','=','work_tendering.Assoc_ID')
	->select('associate.Assoc_FirstName','associate.Assoc_LastName', 'work_tendering.TotalQuote')
	->get();
	$resp=array($details);
	return $resp;
}

public function getDetails($id)
{
	$details=\DB::table('work_tendering')->where('work_tendering.Work_ID', $id)->where('work_tendering.SelectStatus',1)
	->join('service_work', 'service_work.Work_ID','=','work_tendering.Work_ID')
	->join('logins', 'logins.User_Login','=','service_work.Assigned_To')
	->join('associate', 'associate.Assoc_ID','=','work_tendering.Assoc_ID')
	->select('associate.Assoc_FirstName','associate.Assoc_LastName', 'work_tendering.TotalQuote','logins.User_Name')
	->get();
	$resp=array($details);
	return $resp;
}
public function getAssocDetailsWO($id)
{
	$details=\DB::table('work_tendering')->where('work_tendering.Work_ID', $id)->where('work_tendering.SelectStatus',1)
	->join('associate', 'associate.Assoc_ID','=','work_tendering.Assoc_ID')
->join('contacts', 'contacts.Contact_ID','=','associate.Contact_ID')
	 ->join('service_work', 'service_work.Work_ID','=','work_tendering.Work_ID')
	//->join('work_timeline','work_timeline.Work_ID','=','work_tendering.Work_ID')
	//->join('work_attributes','work_attributes.Work_Attrb_ID','=','work_timeline.Work_Attrb_ID')
	->join('associate_details', 'associate_details.Assoc_ID','=', 'associate.Assoc_ID')
	->join('location', 'location.Loc_ID','=','associate_details.Loc_ID')
	->join('address', 'address.Address_ID','=','associate.Address_ID')
	//->select('associate.Assoc_FirstName','associate.Assoc_LastName', 'work_tendering.TotalQuote')
	->get();
	$resp=array($details);
	return $resp;
}

public function getAssocTenderDetails($id)
{
	$details=\DB::table('work_tendering')->where('work_tendering.WorkTender_ID', $id)
	->join('associate', 'associate.Assoc_ID','=','work_tendering.Assoc_ID')
	->join('contacts', 'contacts.Contact_ID','=','associate.Contact_ID')
	
	->join('associate_details', 'associate_details.Assoc_ID','=', 'associate.Assoc_ID')
	->join('location', 'location.Loc_ID','=','associate_details.Loc_ID')
	->join('address', 'address.Address_ID','=','associate.Address_ID')
	
	->select('associate.Assoc_FirstName','associate.Assoc_LastName', 'contacts.Contact_Phone','associate_details.Rating','work_tendering.TotalQuote')
	->get();
	$resp=array($details);
	return $resp;
}

public function finishTender($id)
{
	$now=new DateTime();
	$today=$now->format('Y-m-d');

	$TenderDate=\DB::table('work_timeline')->insert(array('Work_ID'=>$id, 'Work_Attrb_ID'=>17, 'Value'=>$today));
	$assignee=\DB::table('service_work')->where('Work_ID', $id)->pluck('Assigned_To');
	$assignTo=\DB::table('work_access_table')->where('Work_ID', $id)->pluck('MI');
	$finishTender=\DB::table('service_work')->where('Work_ID', $id)->update(array('AssignedDept'=>'MI','Assigned_To'=>$assignTo[0], 'WorkStatus'=>5, 'Generate_Work_Status'=>3));
	if(!empty($finishTender))
	{
		$access=\DB::table('work_access_table')->where('Work_ID', $id)->update(array('BI'=>$assignee[0]));
	}
	$resp=array($assignee, $assignTo);
	return $resp;
}

public function reTender($id)
{
	$assocID=\DB::table('work_tendering')->where('Work_ID', $id)
	->where('SelectStatus',1)->pluck('Assoc_ID');
	if(!empty($assocID))
	{
		$changeAssocFlag=\DB::table('work_tendering')->where('work_ID', $id)->where('Assoc_ID', $assocID['0'])
		->update(array('SelectStatus'=>0));

		$delete=\DB::table('work_tendering')->where('Work_ID', $id)
		->update(array('deleteFlag'=>1));
		$changeWorkFlag=\DB::table('service_work')->where('Work_ID',$id)
		->update(array('AssocSelectFlag'=>1));
		$rateUpdate=\DB::table('work_tendering')->where('Work_ID', $id)
		->update(array('updateFlag'=>0));
		/*$changeQuote=\DB::table('service_work')->where('Work_ID', $id)
		->update(array('assocSelFlag'=>0));*/
		$resp=array("Success"=>true, $delete, $assocID);
	return $resp;
		
	}
	else{
		$resp=array("Success"=>false);
		return $resp;

	}
	
}

public function addPaymentTerms(Request $r)
{
	$values = Request::json()->all();
	if($values['type_ID']==0)
	{
		$terms=\DB::table('work_timeline')->insert(array('Work_ID'=>$values['work_ID'], 'Work_Attrb_ID'=>11,
		'Value'=>$values['comment']));
	}
	else if($values['type_ID']==1)
	{
		$updateTerm=\DB::table('work_tendering')->where('WorkTender_ID', $values['tender_ID'])->update(array('Payment_Terms'=>$values['comment']));
		
	}
	
	$resp=array('Success'=>true);
	return $resp;
}

public function getPaymentTerms($id)
{
	$terms=\DB::table('work_timeline')->where('Work_ID', $id)->where('Work_Attrb_ID',11)->select('Value')->get();
	$resp=array($terms);
	return $resp;
}
public function checkPaymentTermsExists($id)
{
	$exists=\DB::table('work_timeline')->where('Work_ID',$id)->where('Work_Attrb_ID',11)->get();
	$count=count($exists);
	$resp=array($count);
	return $resp;
}
public function chkKeysExists($id)
{
	$keysExists=\DB::table('wo_key_deliverables')->where('Work_ID',$id)->get();
	$count=count($keysExists);
	$resp=array($count);
	return $resp;
}
public function chkAmendKeysExists($id, $no)
{
	$keysExists=\DB::table('wo_key_deliverables')->where('Work_ID',$id)->where('Amend_Flag',$no)->where('FinishAmend_Flag',0)->get();
	$count=count($keysExists);
	$resp=array($count);
	return $resp;
}
public function checkTermsExists($id)
{
	$termsExists=\DB::table('wo_terms_conditions')->where('Work_ID',$id)->get();
	$count=count($termsExists);
	$resp=array($count);
	return $resp;
}
public function checkAmendTermsExists($id, $no)
{
	$termsExists=\DB::table('wo_terms_conditions')->where('Work_ID',$id)->where('Amend_Flag',$no)->where('FinishAmend_Flag',0)->get();
	$count=count($termsExists);
	$resp=array($count);
	return $resp;
}

public function saveWorkSchedule(Request $r)
{
	$values = Request::json()->all();
	$newstart=new DateTime($values['startDate']);
	$newstart->modify('+1 day');
	
$date = strtotime($newstart->format('Y-m-d'));
$period=$values['duration'];
//$holidays = array('2018-12-03');

for($i=1;$i<=$period;$i++){		
	$dt = strtotime("+".$i." day", $date);
	$curr = date('D', $dt);
// substract if Saturday or Sunday
	// if ($curr == 'Sat' || $curr == 'Sun') {
	if ($curr == 'Sun') {
		$period++;
	}
	
}
$date = strtotime("+".$period." day", $date);
$endDate=date('Y/m/d', $date);
if($values['type_ID']==0)
{
	$terms=\DB::table('work_schedule')->insert(array('Work_ID'=>$values['work_ID'], 'Work_Stage'=>$values['workStage'],
	'Start_Date'=>$newstart->format('Y-m-d'),'Duration'=>$values['duration'],'End_Date'=>$endDate,'Remarks'=>$values['comment']));
}
else if($values['type_ID']==1)
{
	/*$existsWID=\DB::table('work_amendment')->where('Work_ID',$values['work_ID'])->get();
	$count=count($existsWID);
	if($count==0)
	{
	$amendID=\DB::table('work_amendment')->insert(array('Work_ID'=>$values['work_ID']));
	$workSchedFlag=\DB::table('work_amendment')->where('Work_ID',$values['work_ID'])->pluck('WorkSched_Amend_Flag');
	$insertFlag=$workSchedFlag[0]+1;
	$terms=\DB::table('work_schedule')->insert(array('Work_ID'=>$values['work_ID'], 'Work_Stage'=>$values['workStage'],
	'Start_Date'=>$newstart->format('Y-m-d'),'Duration'=>$values['duration'],'End_Date'=>$endDate, 'Remarks'=>$values['comment'],'Amend_Flag'=>$insertFlag));
	}
	else
	{
		$workSchedFlag=\DB::table('work_amendment')->where('Work_ID',$values['work_ID'])->pluck('WorkSched_Amend_Flag');
		$insertFlag=$workSchedFlag[0]+1;
		$terms=\DB::table('work_schedule')->insert(array('Work_ID'=>$values['work_ID'], 'Work_Stage'=>$values['workStage'],
		'Start_Date'=>$newstart->format('Y-m-d'),'Duration'=>$values['duration'],'End_Date'=>$endDate, 'Remarks'=>$values['comment'],'Amend_Flag'=>$insertFlag));
	}*/
	$terms=\DB::table('work_schedule')->insert(array('Work_ID'=>$values['work_ID'], 'Work_Stage'=>$values['workStage'],
	'Start_Date'=>$newstart->format('Y-m-d'),'Duration'=>$values['duration'],'End_Date'=>$endDate, 'Remarks'=>$values['comment'],'Amend_Flag'=>$values['amend_ID']));

}
	$resp=array("Success"=>true);
	return $resp;
}
public function editWorkSchDetails(Request $r)
{
	$values = Request::json()->all();
	$newstart=new DateTime($values['startDate']);
	$newstart->modify('+1 day');
	
$date = strtotime($newstart->format('Y-m-d'));
$period=$values['duration'];
//$holidays = array('2018-12-03');

for($i=1;$i<=$period;$i++){		
	$dt = strtotime("+".$i." day", $date);
	$curr = date('D', $dt);
// substract if Saturday or Sunday
	// if ($curr == 'Sat' || $curr == 'Sun') {
	if ($curr == 'Sun') {
		$period++;
	}
	/*elseif (in_array(date('Y-m-d', $dt), $holidays)) {
		$period++;
	}*/
}
$date = strtotime("+".$period." day", $date);
$endDate=date('Y/m/d', $date);
if($values['type_ID']==2)
{
	$terms=\DB::table('work_schedule')->where('Work_Schedule_ID',$values['sched_ID'])->update(array('Work_Stage'=>$values['workStage'],
	'Start_Date'=>$newstart->format('Y-m-d'),'Duration'=>$values['duration'],'End_Date'=>$endDate, 'Remarks'=>$values['comment']));
}
$resp=array("Success"=>true);
return $resp;
}

public function savePaymentSchedule(Request $r)
{
	$values = Request::json()->all();
	$newPaydate=new DateTime($values['payDate']);
	$newPaydate->modify('+1 day');
	if($values['type_ID']==0)
	{
		
	$payment=\DB::table('payment_schedule')->insert(array('Work_ID'=>$values['work_ID'], 'Payment_Phase'=>$values['payStage'],
	'Amount'=>$values['amount'],'Payment_Date'=>$newPaydate->format('Y-m-d'), 'Remarks'=>$values['cmnts']));
	}
	else if($values['type_ID']==1)
	{
		/*$existsWID=\DB::table('work_amendment')->where('Work_ID',$values['work_ID'])->get();
	$count=count($existsWID);
	if($count==0)
	{
		$amendID=\DB::table('work_amendment')->insert(array('Work_ID'=>$values['work_ID']));
		$paySchedFlag=\DB::table('work_amendment')->where('Work_ID',$values['work_ID'])->pluck('PaySched_Amend_Flag');
	$insertFlag=$paySchedFlag[0]+1;

		$payment=\DB::table('payment_schedule')->insert(array('Work_ID'=>$values['work_ID'], 'Payment_Phase'=>$values['payStage'],
	'Amount'=>$values['amount'],'Payment_Date'=>$newPaydate->format('Y-m-d'), 'Remarks'=>$values['cmnts'],'Amend_Flag'=>$insertFlag));
	}
	else{
		$paySchedFlag=\DB::table('work_amendment')->where('Work_ID',$values['work_ID'])->pluck('PaySched_Amend_Flag');
	$insertFlag=$paySchedFlag[0]+1;

		$payment=\DB::table('payment_schedule')->insert(array('Work_ID'=>$values['work_ID'], 'Payment_Phase'=>$values['payStage'],
	'Amount'=>$values['amount'],'Payment_Date'=>$newPaydate->format('Y-m-d'), 'Remarks'=>$values['cmnts'],'Amend_Flag'=>$insertFlag));

	}*/
	$payment=\DB::table('payment_schedule')->insert(array('Work_ID'=>$values['work_ID'], 'Payment_Phase'=>$values['payStage'],
	'Amount'=>$values['amount'],'Payment_Date'=>$newPaydate->format('Y-m-d'), 'Remarks'=>$values['cmnts'],'Amend_Flag'=>$values['amend_ID']));

}
	$resp=array("Success"=>true);
	return $resp;
}
public function editPaySchDetails(Request $r)
{
	$values = Request::json()->all();
	$newPaydate=new DateTime($values['payDate']);
	$newPaydate->modify('+1 day');
	if($values['type_ID']==2)
	{
		
	$payment=\DB::table('payment_schedule')->where('Pay_Schedule_ID',$values['sched_ID'])->update(array('Payment_Phase'=>$values['payStage'],
	'Amount'=>$values['amount'],'Payment_Date'=>$newPaydate->format('Y-m-d'), 'Remarks'=>$values['cmnts']));
	}
	$resp=array("Success"=>true);
	return $resp;
}
public function deletePaySched(Request $r)
{
	$values = Request::json()->all();
	$delete=\DB::table('payment_schedule')->where('Pay_Schedule_ID',$values['sched_ID'])->update(array('DeleteFlag'=>1));
	$resp=array("Success"=>true);
	return $resp;
}

public function getWorkSchedule($id)
{
	$work=\DB::table('work_schedule')
	->leftjoin('work_amendment','work_schedule.Work_ID','=','work_amendment.Work_ID')
	//->where('work_amendment.WorkSched_Amend_Flag',1)
	->where('work_schedule.Work_ID', $id)->where('DeleteFlag',0)->orderBy('work_schedule.Start_Date')->get();
	$resp=array($work);
	return $resp;
}
public function chkWorkSchedExists($id)
{
	$work=\DB::table('work_schedule')
	->where('work_schedule.Work_ID', $id)->get();
	$count=count($work);
	$resp=array($count);
	return $resp;
}
public function chkAmendWorkSchedExists($id, $no)
{
	$work=\DB::table('work_schedule')
	->where('work_schedule.Work_ID', $id)->where('Amend_Flag',$no)->where('FinishAmend_Flag',0)->get();
	$count=count($work);
	$resp=array($count);
	return $resp;
}
public function chkAmendLineItemsExists($id, $no)
{
	$items=\DB::table('work_labour_estimation')->where('Work_ID', $id)->where('Amend_Flag',$no)->where('FinishAmend_Flag',0)->get();
	$count=count($items);
	$resp=array($count);
	return $resp;
}
public function getPaySchedule($id)
{
	$pay=\DB::table('payment_schedule')->leftjoin('work_amendment','payment_schedule.Work_ID','=','work_amendment.Work_ID')
	//->leftjoin('actual_payments', 'actual_payments.PaySched_ID','=','payment_schedule.Pay_Schedule_ID')
	->where('payment_schedule.Work_ID', $id)->where('payment_schedule.DeleteFlag',0)->orderBy('payment_schedule.Payment_Date')->get();
	$resp=array($pay);
	return $resp;
}
public function chkPaySchedExists($id)
{
	$pay=\DB::table('payment_schedule')->where('payment_schedule.Work_ID', $id)->get();
	$count=count($pay);
	$resp=array($count);
	return $resp;
}
public function chkAmendPaySchedExists($id, $no)
{
	$pay=\DB::table('payment_schedule')->where('payment_schedule.Work_ID', $id)->where('Amend_Flag',$no)->get();
	$count=count($pay);
	$resp=array($count);
	return $resp;
}

public function getTerms()
{
	$terms=\DB::table('terms_conditions')
	->where('AllWorkFlag',1)
	->where('DeleteFlag',0)//->where('Segment_ID',8)
	//->where('Segment_ID',11)
	->get();
	$resp=array($terms);
	return $resp;
}

public function saveTerms(Request $r)
	{
		$data= Request::json()->all();
	$id=$data['param1'];
	$items=$data['param2'];
	$type=$data['param3'];
	//$dataset=[];
	foreach($items as $item)
	{
		if($type==0)
		{
			$items=\DB::table('wo_terms_conditions')->insert(array('Work_ID' => $id, 'Term_ID'=> $item['name']));
		}
		else if($type==1)
		{
			/*$existsWID=\DB::table('work_amendment')->where('Work_ID',$id)->get();
	$count=count($existsWID);
	if($count==0)
	{
		$amendID=\DB::table('work_amendment')->insert(array('Work_ID'=>$id));
		$paySchedFlag=\DB::table('work_amendment')->where('Work_ID',$id)->pluck('Terms_Amend_Flag');
	$insertFlag=$paySchedFlag[0]+1;
	$items=\DB::table('wo_terms_conditions')->insert(array('Work_ID' => $id, 'Term_ID'=> $item['name'],'Amend_Flag'=>$insertFlag));
	}
	else{
		
		$paySchedFlag=\DB::table('work_amendment')->where('Work_ID',$id)->pluck('Terms_Amend_Flag');
	$insertFlag=$paySchedFlag[0]+1;
	$items=\DB::table('wo_terms_conditions')->insert(array('Work_ID' => $id, 'Term_ID'=> $item['name'],'Amend_Flag'=>$insertFlag));

	}*/
	$items=\DB::table('wo_terms_conditions')->insert(array('Work_ID' => $id, 'Term_ID'=> $item['name'],'Amend_Flag'=>$data['param4']));
		}
		else if($type==2)
		{
			$items=\DB::table('wo_terms_conditions')->insert(array('Work_ID' => $id, 'Term_ID'=> $item['name'],'Tender_Flag'=>1));
		}
		
	}
	
	

$resp=array("Success"=>true, $items);
	return $resp;
	}
	public function getTermsConditionWO($id)
	{
		$terms=\DB::table('wo_terms_conditions')->leftjoin('work_amendment','work_amendment.Work_ID','=','wo_terms_conditions.Work_ID')
		->join('terms_conditions','terms_conditions.Term_ID','=','wo_terms_conditions.Term_ID')
		->where('wo_terms_conditions.Work_ID', $id)
		->where('wo_terms_conditions.Delete_Flag',0)
		->where('wo_terms_conditions.Tender_Flag',0)->get();
		$cterms=\DB::table('wo_terms_conditions')->leftjoin('work_amendment','work_amendment.Work_ID','=','wo_terms_conditions.Work_ID')
		->join('terms_conditions','terms_conditions.Term_ID','=','wo_terms_conditions.Term_ID')
		->where('wo_terms_conditions.Work_ID', $id)
		->where('wo_terms_conditions.Delete_Flag',0)
		->where('wo_terms_conditions.Tender_Flag',0)
		->where('terms_conditions.CustomFlag',1)->get();
		
		
		$resp=array($terms);
		return $resp;
	}

	public function getTermsConditions($id)
	{
	/*	$terms=\DB::table('wo_terms_conditions')->leftjoin('work_amendment','work_amendment.Work_ID','=','wo_terms_conditions.Work_ID')
		->join('terms_conditions','terms_conditions.Term_ID','=','wo_terms_conditions.Term_ID')
		->where('wo_terms_conditions.Work_ID', $id)
		->where('wo_terms_conditions.Delete_Flag',0)
		->where('wo_terms_conditions.Tender_Flag',0)->get();
		$cterms=\DB::table('wo_terms_conditions')->leftjoin('work_amendment','work_amendment.Work_ID','=','wo_terms_conditions.Work_ID')
		->join('terms_conditions','terms_conditions.Term_ID','=','wo_terms_conditions.Term_ID')
		->where('wo_terms_conditions.Work_ID', $id)
		->where('wo_terms_conditions.Delete_Flag',0)
		->where('wo_terms_conditions.Tender_Flag',0)
		->where('terms_conditions.CustomFlag',1)->get();
		
		
		$resp=array($terms);
		return $resp;*/
	$gterms=\DB::table('wo_terms_conditions')->leftjoin('work_amendment','work_amendment.Work_ID','=','wo_terms_conditions.Work_ID')
		->join('terms_conditions','terms_conditions.Term_ID','=','wo_terms_conditions.Term_ID')
		->where('wo_terms_conditions.Work_ID', $id)
		->where('wo_terms_conditions.Delete_Flag',0)
		->where('wo_terms_conditions.Tender_Flag',0)->where('terms_conditions.CustomFlag',0)->get();
		$cterms=\DB::table('wo_terms_conditions')->leftjoin('work_amendment','work_amendment.Work_ID','=','wo_terms_conditions.Work_ID')
		->join('terms_conditions','terms_conditions.Term_ID','=','wo_terms_conditions.Term_ID')
		->where('wo_terms_conditions.Work_ID', $id)
		->where('wo_terms_conditions.Delete_Flag',0)
		->where('wo_terms_conditions.Tender_Flag',0)
		->where('terms_conditions.CustomFlag',1)->get();
		$no=11;
		$newArray1=[];
		$newArray2=[];
		$finalArray=[];
		foreach($gterms as $g)
		{
$newArray1['ID']=$no;
$newArray1['Name']=$g->Term_Name;
$no++;
array_push($finalArray,$newArray1);
		}
		$x = array_slice($finalArray, -1)[0];
		$lastID=$x['ID']+1;
		foreach($cterms as $c)
		{
$newArray2['ID']=$lastID;
$newArray2['Name']=$c->Term_Name;
$lastID++;
array_push($finalArray,$newArray2);
		}
		
		$resp=array($finalArray);
		return $resp;
	}

	public function getAssocName($id)
	{
		$assocName=\DB::table('work_tendering')->join('associate', 'associate.Assoc_ID','=','work_tendering.Assoc_ID')
		->where('work_tendering.WorkTender_ID',$id)->select('Assoc_FirstName', 'Assoc_LastName')->get();
		$resp=array($assocName);
		return $resp;
	}

	public function removeAssocName($id)
	{
		
		$remove=\DB::table('work_tendering')->where('WorkTender_ID', $id)->update(array('deleteFlag'=>1));
//$changeLineItemFlag=\DB::table('work_labour_estimation')->where('Work_ID', $id)->update(array('updateFlag'=>0, 'assocSelFlag'=>0));

		$resp=array("Success"=>true);
		return $resp;
	}

	public function getLineItemID($id)
	{
		$items=\DB::table('work_labour_estimation')->where('Work_ID', $id)->where('deleteFlag',0)->select('LE_ID','LineItem_ID', 'Quantity','Comments', 'Priority')->get();
		$resp=array($items);
		return $resp;
	}
	public function getMatSpec($id)
	{
		$items=\DB::table('work_material_estimation')->where('Work_ID', $id)->where('deleteFlag',1)->select('Product_ID', 'Quantity')->get();
		$resp=array($items);
		return $resp;
	}
	public function getMatLabLineItemID($id)
	{
		$items=\DB::table('work_matlabor_estimation')->where('Work_ID', $id)
		->where('deleteFlag',1)
		->select('LineItem_ID', 'Quantity')->get();
		$resp=array($items);
		return $resp;
	}

	public function addTenderLabItems(Request $r)
	{
		$data= Request::json()->all();
	$id=$data['param1'];
	$items=$data['param2'];
	$dataset=[];
	foreach($items as $item)
	{
		$exists=\DB::table('work_tender_details_lab')->where('WorkTender_ID', $id)->where('LineItem_ID', $item['name'])->get();
		$count=count($exists);
		if($count==0)
		{
		$dataset[]=['WorkTender_ID' => $id, 'LineItem_ID'=> $item['name'], 'Quantity'=> $item['qty'],'Comments'=>$item['comment'], 'Priority'=>$item['priority']];
		}
		
	}
	
	
$items=\DB::table('work_tender_details_lab')->insert($dataset);

$resp=array("Success"=>true, $items);
	return $resp;
	
	}

	public function addTenderMatLabItems(Request $r)
	{
		$data= Request::json()->all();
	$id=$data['param1'];
	$items=$data['param2'];
	$dataset=[];
	foreach($items as $item)
	{
		$exists=\DB::table('work_tender_details_lab')->where('WorkTender_ID', $id)->where('LineItem_ID', $item['name'])->get();
		$count=count($exists);
		if($count==0)
		{
		$dataset[]=['WorkTender_ID' => $id, 'LineItem_ID'=> $item['name'], 'Quantity'=> $item['qty']];
		}
		
	}
	
	
$items=\DB::table('work_tender_details_lab')->insert($dataset);

$resp=array("Success"=>true, $items);
	return $resp;
	
	}

public function chkAssocDelFlag($id)
{
$flagNo=\DB::table('work_tendering')->where('Work_ID', $id)
->where('deleteFlag',1)->get();
$count=count($flagNo);
/*if($count>=1)
{
	$resp=array("Success"=>true);
	return $resp;
}
else{
	$resp=array("Success"=>false);
	return $resp;
}*/
$resp=array($count);
	return $resp;
}

public function getFinalLabTenderDetails($id)
{
	$workTid=\DB::table('work_tendering')->where('Work_ID', $id)->where('SelectStatus',1)->pluck('WorkTender_ID');

	$tenderDetails=\DB::table('work_tendering')->
	join('work_tender_details_lab', 'work_tendering.WorkTender_ID','=','work_tender_details_lab.WorkTender_ID')
	->join('serv_line_items', 'serv_line_items.LineItem_ID','=','work_tender_details_lab.LineItem_ID')
	->join('units', 'units.Unit_ID','=','serv_line_items.UnitID')
	->where('work_tender_details_lab.WorkTender_ID', $workTid[0])
	->get();
	$resp=array($tenderDetails);
	return $resp;
}

public function getFinalTender($id)
{
	$workTid=\DB::table('work_tendering')->where('Work_ID', $id)->where('SelectStatus',1)->pluck('WorkTender_ID');
if(!empty($workTid[0]))
{
	$tenderDetails=\DB::table('work_tendering')->
	join('work_tender_details_lab', 'work_tendering.WorkTender_ID','=','work_tender_details_lab.WorkTender_ID')
	->join('serv_line_items', 'serv_line_items.LineItem_ID','=','work_tender_details_lab.LineItem_ID')
	//->join('work_labour_estimation', 'work_labour_estimation.LineItem_ID','=','serv_line_items.LineItem_ID')
	->join('units', 'units.Unit_ID','=','serv_line_items.UnitID')
	->where('work_tender_details_lab.WorkTender_ID', $workTid[0])
	->where('work_tendering.Work_ID', $id)
//->where('work_labour_estimation.Amend_Flag',0)
	//->where('work_labour_estimation.deleteFlag',0)
	//->select('work_labour_estimation.Comments','work_labour_estimation.Amend_Flag','work_tender_details_lab.*','serv_line_items.LineItem_ID','serv_line_items.LineItem_Name','units.Unit_Code','work_tendering.*')
	//->orderBy('work_tender_details_lab.Quantity','DESC')
	->orderBy('work_tender_details_lab.Priority','ASC')
	//->distinct()
	->get();
	$resp=array($tenderDetails); 
	return $resp;
}
else{
	$resp=array("Success"=>false);
	return $resp;
}
}
public function reEstimate($id)
{
	$assignTo=\DB::table('service_work')->where('Work_ID', $id)->pluck('Assigned_To');
	if(!empty($assignTo))
	{
		$access=\DB::table('work_access_table')->where('Work_ID', $id)->update(array('BI'=>$assignTo[0]));
	}
	$assignee=\DB::table('work_access_table')->where('Work_ID', $id)->pluck('PMQA');
	$updateWork=\DB::table('service_work')->where('Work_ID',$id)
	->update(array('Assigned_To'=>$assignee['0'], 'AssignedDept'=>'PMQA','Update_Status'=>10,'WorkStatus'=>12));
	$resp=array($assignee);
	return $resp;

}

public function getCustomerDetails($id)
{
	$custDetails=\DB::table('service_work')
	->join('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')
	//->join('location', 'location.Loc_ID','=','sales_lead.Lead_Loc_ID')
	->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
	->join('contacts', 'contacts.Contact_ID','=','sales_customer.Contact_ID')
	->join('location', 'location.Loc_ID','=','sales_customer.Loc_ID')
	->join('address', 'address.Address_ID','=','sales_customer.Address_ID')
	->where('service_work.Work_ID', $id)
	->get();
	$resp=array($custDetails);
	return $resp;
}
public function getCustomer($id)
{
	$custDetails=\DB::table('service_work')
	->join('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')
	//->join('location', 'location.Loc_ID','=','sales_lead.Lead_Loc_ID')
	->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
	->join('contacts', 'contacts.Contact_ID','=','sales_customer.Contact_ID')
	->join('location', 'location.Loc_ID','=','sales_customer.Loc_ID')
	->join('address', 'address.Address_ID','=','sales_customer.Address_ID')
	->where('sales_customer.Customer_ID', $id)
	->get();
	$resp=array($custDetails);
	return $resp;
}

public function getAssocFlag($id)
{
	$assocFlag=\DB::table('work_tendering')->where('Work_ID', $id)->where('SelectStatus',1)->get();
	$count=count($assocFlag);
	$resp=array($count);
	return $resp;
}

public function getWorkID($id)
{
	$workID=\DB::table('work_tendering')->where('WorkTender_ID', $id)->pluck('Work_ID');
	$resp=array($workID);
	return $resp;
}

public function getTotalTenderPend($name)
{
	if($name=='admin')
	{
		$Tender=\DB::table('service_work')->where('WorkStatus', 3)
	->get();
	$count=count($Tender);
	$resp=array($count);
	return $resp;
	}
	else if($name=='BID')
	{
		$Tender=\DB::table('service_work')->where('WorkStatus', 3)
	->where('AssignedDept',BI)->get();
	$count=count($Tender);
	$resp=array($count);
	return $resp;
	}
	else
	{
		$Tender=\DB::table('service_work')->where('WorkStatus', 3)
		->where('Assigned_To',$name)->get();
		$count=count($Tender);
		$resp=array($count);
		return $resp;
	}
	
}

public function getTotalTenderReq($name)
{
	
	if($name=='admin')
	{
		$Tender=\DB::table('service_work')
		->join('sales_lead','sales_lead.Lead_ID','=','service_work.Lead_ID')
				->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
				->join('work_status','service_work.WorkStatus','=','work_status.Work_Status_ID')
				->where('WorkStatus', 3)
		->get();
		
		$resp=array($Tender);
		return $resp;
	}
	else if($name=='BID')
	{
		$Tender=\DB::table('service_work')
		->join('sales_lead','sales_lead.Lead_ID','=','service_work.Lead_ID')
				->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
				->join('work_status','service_work.WorkStatus','=','work_status.Work_Status_ID')
				//->where('service_work.AssignedDept','BI')
				->where('WorkStatus', 3)
		->get();
		
		$resp=array($Tender);
		return $resp;
	}
	else{
		$Tender=\DB::table('service_work')
		->join('sales_lead','sales_lead.Lead_ID','=','service_work.Lead_ID')
				->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
				->join('work_status','service_work.WorkStatus','=','work_status.Work_Status_ID')
				//->where('service_work.Assigned_To',$name)
				->where('WorkStatus', 3)
		->get();
		
		$resp=array($Tender);
		return $resp;
	}
}
public function getTotalWO($name)
{
	if($name=='admin')
	{
		$Tender=\DB::table('service_work')->where('WorkStatus', 6)->get();
	$count=count($Tender);
	$resp=array($count);
	return $resp;
	}
	else if($name=='BID')
	{
		$Tender=\DB::table('service_work')->where('WorkStatus', 6)->where('AssignedDept','BI')->get();
	$count=count($Tender);
	$resp=array($count);
	return $resp;
	}
	else
	{
		$Tender=\DB::table('service_work')->where('WorkStatus', 6)->where('Assigned_To',$name)->get();
		$count=count($Tender);
		$resp=array($count);
		return $resp;
	}
	
}
public function getTotalWOEnq($name)
{
	if($name=='admin')
	{
		$Tender=\DB::table('service_work')
		->join('sales_lead','sales_lead.Lead_ID','=','service_work.Lead_ID')
				->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
				->join('work_status','service_work.WorkStatus','=','work_status.Work_Status_ID')
				->where('WorkStatus', 6)->get();
	
	$resp=array($Tender);
	return $resp;
	}
	else if($name=='BID')
	{
		$Tender=\DB::table('service_work')
		->join('sales_lead','sales_lead.Lead_ID','=','service_work.Lead_ID')
				->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
				->join('work_status','service_work.WorkStatus','=','work_status.Work_Status_ID')
				->where('WorkStatus', 6)->where('service_work.AssignedDept','BI')->get();
	
	$resp=array($Tender);
	return $resp;
	}
	else
	{
		$Tender=\DB::table('service_work')
		->join('sales_lead','sales_lead.Lead_ID','=','service_work.Lead_ID')
				->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
				->join('work_status','service_work.WorkStatus','=','work_status.Work_Status_ID')
				->where('WorkStatus', 6)->where('service_work.Assigned_To',$name)->get();
		
		$resp=array($Tender);
		return $resp;
	}
}

public function getPaymentReminderCount($name)
{
	$now=new DateTime();
	$today=$now->format('Y-m-d');
	if($name=='admin')
	{
	
		$Tender=\DB::table('service_work')
		//->join('sales_lead','sales_lead.Lead_ID','=','service_work.Lead_ID')
				//->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
				//->join('work_status','service_work.WorkStatus','=','work_status.Work_Status_ID')
				->join('payment_schedule','payment_schedule.Work_ID','=','service_work.Work_ID')
				->where('Payment_Date','=',$today)
				->get();
				$count=count($Tender);

	
	$resp=array($count);
	return $resp;
	}
	else if($name=='BID')
	{
		$Tender=\DB::table('service_work')->join('payment_schedule','payment_schedule.Work_ID','=','service_work.Work_ID')
		->where('Payment_Date','=',$today)
		->where('service_work.AssignedDept','BI')->get();
	
		$count=count($Tender);

	
		$resp=array($count);
		return $resp;
	}
	else
	{
		$Tender=\DB::table('service_work')->join('payment_schedule','payment_schedule.Work_ID','=','service_work.Work_ID')
		->where('Payment_Date','=',$today)->where('service_work.Assigned_To',$name)->get();
		
		$count=count($Tender);

	
	$resp=array($count);
	return $resp;
	}
}

public function getPaymentReminderEnq($name)
{
	$now=new DateTime();
	$today=$now->format('Y-m-d');
	if($name=='admin')
	{
		
		$Tender=\DB::table('service_work')
		->join('sales_lead','sales_lead.Lead_ID','=','service_work.Lead_ID')
				->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
				->join('work_status','service_work.WorkStatus','=','work_status.Work_Status_ID')
				->join('payment_schedule','payment_schedule.Work_ID','=','service_work.Work_ID')
				->where('Payment_Date','=',$today)
				->get();
				
	$resp=array($Tender);
	return $resp;
	}
	else if($name=='BID')
	{
		$Tender=\DB::table('service_work')
		->join('sales_lead','sales_lead.Lead_ID','=','service_work.Lead_ID')
				->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
				->join('work_status','service_work.WorkStatus','=','work_status.Work_Status_ID')
				->join('payment_schedule','payment_schedule.Work_ID','=','service_work.Work_ID')
		->where('Payment_Date','=',$today)
		->where('service_work.AssignedDept','BI')->get();
	
		

	
		$resp=array($Tender);
		return $resp;
	}
	else
	{
		$Tender=\DB::table('service_work')
		->join('sales_lead','sales_lead.Lead_ID','=','service_work.Lead_ID')
				->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
				->join('work_status','service_work.WorkStatus','=','work_status.Work_Status_ID')
				->join('payment_schedule','payment_schedule.Work_ID','=','service_work.Work_ID')
		->where('Payment_Date','=',$today)->where('service_work.Assigned_To',$name)->get();
		
	

	
	$resp=array($Tender);
	return $resp;
}
}

public function updateWOIssueDate(Request $r)
{
	$data= Request::json()->all();
	$IssueDate=new DateTime($data['woIssueDate']);
	$IssueDate->modify('+1 day');
	$updation=\DB::table('work_timeline')->insert(array('Work_ID'=>$data['workid'], 'Work_Attrb_ID'=>27, 'Value'=>$IssueDate->format('Y-m-d')));
	$resp=array("Success"=>true);
	return $resp;
}
public function updateAmendIssueDate(Request $r)
{
	$data= Request::json()->all();
	$IssueDate=new DateTime($data['woIssueDate']);
	$IssueDate->modify('+1 day');
	$updation=\DB::table('work_timeline')->insert(array('Work_ID'=>$data['workid'], 'Work_Attrb_ID'=>27, 'Value'=>$IssueDate->format('Y-m-d')));
	$resp=array("Success"=>true);
	return $resp;
}

public function getIssueDate($id)
{
	$WO_Date=\DB::table('work_timeline')->where('Work_ID', $id)->where('Work_Attrb_ID',27)->pluck('Value');
	$resp=array($WO_Date);
	return $resp;
}
public function signedWO(Request $r)
{
	$data= Request::json()->all();
	$signedWO=\DB::table('service_work')->where('Work_ID', $data['param1'])->update(array('WOSignUp_Flag'=>1,'Update_Status'=>13));
	$resp=array($signedWO);
	return $resp;
}
public function testFunc()
{
	$paySchedFlag=\DB::table('work_amendment')->where('Work_ID',24)->pluck('PaySched_Amend_Flag');
	$resp=array($paySchedFlag);
	return $resp;
}
   
public function checkAssocSelected($id)
{
	$assocExists=\DB::table('work_tendering')->where('Work_ID', $id)->get();
	$count=count($assocExists);
	$resp=array($count);
	return $resp;
}
public function initiateWO(Request $r)
{
	$values = Request::json()->all();
	//FinalizeAssoc
	$finalize=\DB::table('work_tendering')->where('WorkTender_ID',$values['selectedAssoc'])->update(array('SelectStatus'=> 1));
	//To finish Tender
	$now=new DateTime();
	$today=$now->format('Y-m-d');
	$TenderDate=\DB::table('work_timeline')->insert(array('Work_ID'=>$values['workID'], 'Work_Attrb_ID'=>17, 'Value'=>$today));
	$customerApprDate=\DB::table('work_timeline')->insert(array('Work_ID'=>$values['workID'], 'Work_Attrb_ID'=>18, 'Value'=>$today));
	$assignee=\DB::table('service_work')->where('Work_ID', $values['workID'])->pluck('Assigned_To');
	$assignTo=\DB::table('work_access_table')->where('Work_ID', $values['workID'])->pluck('PMQA');
	$finishTender=\DB::table('service_work')->where('Work_ID', $values['workID'])->update(array('AssignedDept'=>'PMQA','Assigned_To'=>$assignTo[0], 'WorkStatus'=>6, 'Generate_Work_Status'=>3,'InitWO_Flag'=>1));
	if(!empty($finishTender))
	{
		$access=\DB::table('work_access_table')->where('Work_ID', $values['workID'])->update(array('BI'=>$assignee[0]));
	}
	$resp=array("Success"=>true);
	return $resp;


}
public function getOneWorkSchedDetails($id)
{
	$details=\DB::table('work_schedule')->where('Work_Schedule_ID', $id)->get();
	$resp=array($details);
	return $resp;
}
public function getOnePaySchedDetails($id)
{
	$details=\DB::table('payment_schedule')->where('Pay_Schedule_ID', $id)->get();
	$resp=array($details);
	return $resp;
}
public function deleteWorkSch(Request $r)
{
	$values = Request::json()->all();
	$delete=\DB::table('work_schedule')->where('Work_Schedule_ID', $values['sched_ID'])->update(array('DeleteFlag'=>1));
	$resp=array("Success"=>true);
	return $resp;
}
public function woSignUp(Request $r)
{
	$values = Request::json()->all();
	
		$finish=\DB::table('service_work')->where('Work_ID',  $values['param1'])
		->update(array('WorkStatus'=>13,'WOSignUp_Flag'=>1));//7
		$resp1=array("Success"=>true);
		return $resp1;
}
public function woSignedUp(Request $r)
{
	$values = Request::json()->all();
	
	$finish=\DB::table('service_work')->where('Work_ID',  $values['param1'])
	->update(array('WorkStatus'=>14,'WOSignedUp_Flag'=>1));//7
	$resp1=array("Success"=>true);
	return $resp1;
}
public function reEstimateTender(Request $r)
{
	$values=Request::json()->all();
	$reestimate=\DB::table('service_work')->where('Work_ID', $values['param1'])
	->update(array('WorkStatus'=>12,'AssocSelectFlag'=>0,'Est_Flag'=>0,'InitWO_Flag'=>0));
	$resp=array("Success"=>true);
	return $resp;
}
public function chkServiceAssocExists($id)
{
	$exists=\DB::table('associate_segment_rate')->whereIn('Service_ID', $id)->get();
	$count=count($exists);
	return $count;

}
public function getPaySubTotal($id)
{
	$subTotal=\DB::table('payment_schedule')->where('Work_ID', $id)->select(\DB::raw("sum(Amount) as Sum"))//
	->get();
	$resp=array($subTotal);
	return $resp;
}

public function getPendingTotal($id)
{
	$Total=\DB::table('work_tendering')->where('Work_ID', $id)->where('SelectStatus',1)->pluck('TotalQuote');
	$subTotal=\DB::table('payment_schedule')->where('Work_ID', $id)->pluck(\DB::raw("sum(Amount) as Sum"));
	
	$pending=$Total[0]-$subTotal[0];
	$resp=array($pending);
	return $resp;
}
public function getTenderTerms($id)
{
	$tenderTerms=\DB::table('wo_terms_conditions')->join('terms_conditions','terms_conditions.Term_ID','=','wo_terms_conditions.Term_ID')
	->where('wo_terms_conditions.Tender_Flag',1)
	->where('wo_terms_conditions.Delete_Flag',0)->where('wo_terms_conditions.Work_ID',$id)
	->get();
	$resp=array($tenderTerms);
	return $resp;
}
public function getEndDate(Request $r)
{
	$values=Request::json()->all();
	$newstart=new DateTime($values['param2']);
	$newstart->modify('+1 day');
	
$date = strtotime($newstart->format('Y-m-d'));
$period=$values['param1'];
//$holidays = array('2018-12-03');

for($i=1;$i<=$period;$i++){		
	$dt = strtotime("+".$i." day", $date);
	$curr = date('D', $dt);
// substract if Saturday or Sunday
	// if ($curr == 'Sat' || $curr == 'Sun') {
	if ($curr == 'Sun') {
		$period++;
	}
	/*elseif (in_array(date('Y-m-d', $dt), $holidays)) {
		$period++;
	}*/
}
$date = strtotime("+".$period." day", $date);
$endDate=date('m/d/Y', $date);
$resp=array($endDate);
return $resp;
}
public function getLineItemRate($id)
{
	$rates=\DB::table('work_tender_details_lab')
	->join('work_tendering', 'work_tendering.WorkTender_ID','=','work_tender_details_lab.WorkTender_ID')
	->join('associate','associate.Assoc_ID','=','work_tendering.Assoc_ID')
		->where('LineItem_ID', $id)->select('Work_ID','Assoc_FirstName','Assoc_LastName','Rate')->get();
		$resp=array($rates);
		return $rates;

}
public function getTenderTotals($id)
{
	$totals=\DB::table('work_tendering')->where('Work_ID',$id)->select('Work_ID', 'WorkTender_ID','TotalQuote')->get();
	$resp=array($totals);
	return $resp;
}
public function getLineItemByService($id)
{
	$lineItems=\DB::table('serv_line_items')
			->join('units','units.Unit_ID','=','serv_line_items.UnitID')
			->join('service_servlineitem_rel', 'service_servlineitem_rel.LineItem_ID','=','serv_line_items.LineItem_ID')
			//->join('services', 'services.Service_ID', '=','service_servlineitem_rel.Service_ID')
			//->where('customFlag', 0)
			->where('service_servlineitem_rel.Service_ID',$id)
			->get();
			$resp=array($lineItems);
			return $resp;
}
public function getAssocByService($id)
{
	$assocs=\DB::table('associate')
	->leftjoin('associate_segment_rate','associate_segment_rate.Assoc_ID','=','associate.Assoc_ID')
	->leftjoin('address','address.Address_ID','=','associate.Address_ID')
	->leftjoin('contacts','contacts.Contact_ID','=','associate.Contact_ID')
	->select('associate.Assoc_ID','associate.Assoc_FirstName','associate.Assoc_LastName','address.Address_line1','address.Address_line2','address.Address_town','Contact_name','Contact_phone')
	->where('Service_ID',$id)->get();
	$resp=array($assocs);
	return $resp;
}
public function addTemplateEst(Request $r)
{
	$data= Request::json()->all();
	$wid=$data['param1'];
	$tid=$data['param2'];
	$dataset=[];
	$items=\DB::table('work_labour_estimation')->where('Work_ID', $tid)->where('deleteFlag',0)
	->select('LineItem_ID','Comments')->get();
	foreach($items as $item)
	{
		
		$dataset[]=['Work_ID'=>$wid,'LineItem_ID'=> $item->LineItem_ID, 'Comments'=>$item->Comments];
		
		
	}
	
	
$insert_items=\DB::table('work_labour_estimation')->insert($dataset);
$chkTemp=\DB::table('work_template')->where('Work_ID',$wid)->get();
$count=count($chkTemp);
if($count==0)
{
  $temp=\DB::table('work_template')->insert(array('Work_ID'=>$wid, 'Temp_Work_ID'=>$tid));  
}
else
{
    $temp=\DB::table('work_template')->where('Work_ID',$wid)
    ->update(array('Temp_Work_ID'=>$tid)); 
}


$resp=array("Success"=>true, $insert_items);
	return $resp;
}
public function chkTemplateWorkID($id)
{
	$tempID=\DB::table('work_template')->where('Work_ID',$id)->pluck('Temp_Work_ID');
	$resp=array($tempID);
	return $resp;
}
public function addTempSchedules(Request $r)
{
	$data= Request::json()->all();
	$wid=$data['param1'];
	$tid=$data['param2'];
	$dataset_work=[];
	$dataset_pay=[];
	$work_sch=\DB::table('work_schedule')->where('Work_ID', $tid)->where('deleteFlag',0)->where('Amend_Flag',0)
	->select('Work_Stage')->get();
	foreach($work_sch as $w)
	{
		
		$dataset_work[]=['Work_ID'=>$wid,'Work_Stage'=> $w->Work_Stage];
		
		
	}
	$pay_sch=\DB::table('payment_schedule')->where('Work_ID', $tid)->where('deleteFlag',0)->where('Amend_Flag',0)
	->select('Payment_Phase')->get();
	foreach($pay_sch as $p)
	{
		
		$dataset_pay[]=['Work_ID'=>$wid,'Payment_Phase'=> $p->Payment_Phase];
		
		
	}
	
$insert_work=\DB::table('work_schedule')->insert($dataset_work);
$insert_pay=\DB::table('payment_schedule')->insert($dataset_pay);

$resp=array("Success"=>true, $insert_work,$insert_pay);
	return $resp;
}
}

