<?php

namespace App\Http\Controllers;

//use Illuminate\Http\Request;
use Request;
use DateTime;

class dashboardController extends Controller
{
    public function getSession()
	{
		$sessions=\DB::table('session')->get();/*->map(function ($item) {
    return get_object_vars($item);});*/
	$response=array('response'=>'session start','success'=>true,$sessions);
		return $response;
	}
	public function getQAVerify()
	{
		$QaVerify=\DB::table('customer')->join ('associate', 'associate.Assoc_ID','=','customer.Assoc_ID')
		->join('location','location.Loc_ID','=','customer.Loc_ID')
		->join ('associate_project','associate_project.Cust_ID','=','customer.Cust_ID')->select('associate.Assoc_FirstName','customer.Cust_Name','customer.Contact_No','location.Loc_Name', 'associate_project.Work_Detail')->orderby('associate.Assoc_ID')
		-> where('associate.Assoc_Status', '2')
		-> get();
		
		$res=array($QaVerify);
		return $res;
	}
	public function getQACount()
	{
		$qaAssoc=\DB::table('associate')->where('Assoc_Status','2')->get();
		$count=count($qaAssoc);
		$resp=array($count);
		return $resp;
	}
	public function getAllServices()
	{
		$services=\DB::table('services')->get();
		$resp=array($services);
		return $resp;
	}
	public function addLineItem(Request $r)
	{
$data= Request::json()->all();
/*foreach($data['param2'] as $value)
{

$item=\DB::table('work_labour_estimation')->insert(array('Work_ID'=>$data['param1'], 'LineItem_ID'=>$value['name'],'Amend_Flag'=>$data['param3']));
}
*/
$resp=array($data['param3']);
return $resp;
	}
	public function getCountItems($id)
	{
		$items=\DB::table('work_labour_estimation')->where('Work_ID', $id)->where('deleteFlag',0)
	->select('LineItem_ID')->get();
$itemsCount=count($items);
	

	$resp=array($itemsCount);
	return $resp;

	}

	public function getFlagCount($id)
	{
		$flags=\DB::table('work_labour_estimation')->where('Work_ID', $id)
		->where('updateFlag',1)->get();
	
	$flagsCount=count($flags);
		$resp=array($flagsCount);
		return $resp;
	}

	public function getKeys($id)
	{
		/*$keys=\DB::table('key_deliverables')->whereIn('Service_ID',array($id))
		->where('customFlag',0)->get();
		$resp=array($keys);
		return $resp;*/

		$service_Id=\DB::table('work_service_map')->where('Work_ID', $id)->pluck('Service_ID');
	if(!empty($service_Id))
	{
		$keys=\DB::table('key_deliverables')->whereIn('Service_ID',$service_Id)
		->where('DeleteFlag',0)
		->get();
		$resp=array($keys);
		return $resp;
		
			
		
		}
	
	$res=array($keys);
	return $res;
	}
	public function chkKeyDeliExists($id)
	{
		$service_Id=\DB::table('work_service_map')->where('Work_ID', $id)->pluck('Service_ID');
	if(!empty($service_Id))
	{
		$keys=\DB::table('key_deliverables')->whereIn('Service_ID',$service_Id)
		->where('customFlag',0)
		->where('DeleteFlag',0)->get();
		$count=count($keys);

		$resp=array($count);
		return $resp;
		
			
		
		}
	}

	public function saveKeys(Request $r)
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
			$items=\DB::table('wo_key_deliverables')->insert(array('Work_ID' => $id, 'Key_ID'=> $item['name']));
		}
		else if($type==1)
		{
			/*$existsWID=\DB::table('work_amendment')->where('Work_ID',$id)->get();
	$count=count($existsWID);
	if($count==0)
	{
		$amendID=\DB::table('work_amendment')->insert(array('Work_ID'=>$id));
		$paySchedFlag=\DB::table('work_amendment')->where('Work_ID',$id)->pluck('KeyDeliv_Amend_Flag');
	$insertFlag=$paySchedFlag[0]+1;
	$items=\DB::table('wo_key_deliverables')->insert(array('Work_ID' => $id, 'Key_ID'=> $item['name'],'Amend_Flag'=>$insertFlag));
	}
	else{
		$paySchedFlag=\DB::table('work_amendment')->where('Work_ID',$id)->pluck('KeyDeliv_Amend_Flag');
	$insertFlag=$paySchedFlag[0]+1;
	$items=\DB::table('wo_key_deliverables')->insert(array('Work_ID' => $id, 'Key_ID'=> $item['name'],'Amend_Flag'=>$insertFlag));
	}*/
	$items=\DB::table('wo_key_deliverables')->insert(array('Work_ID' => $id, 'Key_ID'=> $item['name'],'Amend_Flag'=>$data['param4']));
		}
		
	}
	
	


$resp=array("Success"=>true, $items);
	return $resp;
	}

	public function getWOKeys($id)
	{
		$keys=\DB::table('wo_key_deliverables')
		->leftjoin('work_amendment','work_amendment.Work_ID','=','wo_key_deliverables.Work_ID')
		->where('wo_key_deliverables.Work_ID', $id)->where('wo_key_deliverables.Delete_Flag',0)
		->join('key_deliverables','key_deliverables.Key_ID','=','wo_key_deliverables.Key_ID')->get();
		$resp=array($keys);
		return $resp;
	}
	public function finishWO(Request $r)
	{
		$values= Request::json()->all();
		$assignTo=\DB::table('work_access_table')->where('Work_ID', $values['workID'])->pluck('BI');
		$finish=\DB::table('service_work')->where('Work_ID',  $values['workID'])
		->update(array('WorkStatus'=>13,'Assigned_To'=>$assignTo[0],'AssignedDept'=>'BI'));//7
		$resp1=array("Success"=>true, $finish);
		return $resp1;
	}

	public function saveCustKeys(Request $r)
	{
		$values= Request::json()->all();
		$keyID=\DB::table('key_deliverables')->insertGetID(array('Key_Name'=>$values['keyName'], 'Service_ID'=>$values['services'], 'customFlag'=>1));
if(!empty($keyID))
{
	if($values['typeID']==0)
	{
		$woKey=\DB::table('wo_key_deliverables')->insert(array('Work_ID'=>$values['workID'], 'Key_ID'=>$keyID));
	}
	else if($values['typeID']==1)
	{
		/*$existsWID=\DB::table('work_amendment')->where('Work_ID',$values['workID'])->get();
	$count=count($existsWID);
	if($count==0)
	{
		$amendID=\DB::table('work_amendment')->insert(array('Work_ID'=>$values['workID']));
		$paySchedFlag=\DB::table('work_amendment')->where('Work_ID',$values['workID'])->pluck('KeyDeliv_Amend_Flag');
	$insertFlag=$paySchedFlag[0]+1;
	$items=\DB::table('wo_key_deliverables')->insert(array('Work_ID' => $values['workID'], 'Key_ID'=> $keyID,'Amend_Flag'=>$insertFlag));
	}
	else{
		$paySchedFlag=\DB::table('work_amendment')->where('Work_ID',$values['workID'])->pluck('KeyDeliv_Amend_Flag');
	$insertFlag=$paySchedFlag[0]+1;
	$items=\DB::table('wo_key_deliverables')->insert(array('Work_ID' => $values['workID'], 'Key_ID'=>$keyID,'Amend_Flag'=>$insertFlag));
	}*/
	$items=\DB::table('wo_key_deliverables')->insert(array('Work_ID' => $values['workID'], 'Key_ID'=> $keyID,'Amend_Flag'=>$values['amendID']));
	}
	
}
$resp=array("Success"=>true);
return $resp;
	}
	public function saveCustTerms(Request $r)
	{
		$values= Request::json()->all();
		if($values['typeID']==0)
	{
		$woKey=\DB::table('terms_conditions')->insertGetID(array('Term_Name'=>$values['termName'], 'Segment_ID'=>$values['segID'], 'CustomFlag'=>1));
		$woTems=\DB::table('wo_terms_conditions')->insert(array('Work_ID'=>$values['workID'], 'Term_ID'=>$woKey));
	}
	else if($values['typeID']==1)
	{
		$woKey=\DB::table('terms_conditions')->insertGetID(array('Term_Name'=>$values['termName'], 'Segment_ID'=>$values['segID'], 'CustomFlag'=>1));
		$woTems=\DB::table('wo_terms_conditions')->insert(array('Work_ID'=>$values['workID'], 'Term_ID'=>$woKey, 'Amend_Flag'=>$values['amendID']));
	}
	else if($values['typeID']==2)
	{
		$woKey=\DB::table('terms_conditions')->insertGetID(array('Term_Name'=>$values['termName'], 'Segment_ID'=>$values['segID'], 'CustomFlag'=>1));
		$woTems=\DB::table('wo_terms_conditions')->insert(array('Work_ID'=>$values['workID'], 'Term_ID'=>$woKey, 'Tender_Flag'=>1));
	}
$resp=array("Success"=>true);
return $resp;
	}

	public function actualAssocVisit(Request $r)
	{
		$data= Request::json()->all();
		$assocVisit=\DB::table('work_timeline')->insert(array('Work_ID'=> $data['workid'], 'Work_Attrb_ID'=>12, 'Value'=>$data['assocVisit']));
		if(!empty($assocVisit))
		{
			$update=\DB::table('service_work')->where('Work_ID', $data['workid'])->update(array('AssocVisitFlag'=>1));
		}
		$resp=array($assocVisit);
		return $resp;

	}

	public function getEstCount($name)
	{
		if($name=='admin')
		{
			$est=\DB::table('service_work')->where('WorkStatus',10)->get();
		$estcount=count($est);
		$resp=array($estcount);
		return $resp;
		}
		else if($name=='PMQA')
		{
			$est=\DB::table('service_work')->where('WorkStatus',10)->where('AssignedDept','PMQA')->get();
		$estcount=count($est);
		$resp=array($estcount);
		return $resp;
		}
		else
		{
			$est=\DB::table('service_work')->where('WorkStatus',10)->where('Assigned_To',$name)->get();
			$estcount=count($est);
			$resp=array($estcount);
			return $resp;
		}
		
	
	}
	public function getWOCount($name)
	{
		if($name=='admin')
		{
		$est=\DB::table('service_work')->where('WorkStatus',6)->get();
		$wocount=count($est);
		$resp=array($wocount);
		return $resp;
		}
		else{
			$est=\DB::table('service_work')->where('WorkStatus',6)->where('Assigned_To',$name)->get();
		$wocount=count($est);
		$resp=array($wocount);
		return $resp;
		}
	}

	public function getStartProjectCount($name)
	{
		if($name=='admin')
		{
		$est=\DB::table('service_work')->where('WorkStatus',7)->get();
		$wocount=count($est);
		$resp=array($wocount);
		return $resp;
		}
		else{
			$est=\DB::table('service_work')->where('WorkStatus',7)->where('Assigned_To',$name)->get();
		$wocount=count($est);
		$resp=array($wocount);
		return $resp;
		}
	}

	public function getQAPendList()
	{
		$qaAssoc=\DB::table('associate')
		->leftjoin('contacts', 'contacts.Contact_ID','=','associate.Contact_ID')
		->leftjoin('associate_details','associate_details.Assoc_ID','=','associate.Assoc_ID')
		->leftjoin('location', 'location.Loc_ID','=','associate_details.Loc_ID')
		->where('Assoc_Status','2')->get();
		$resp=array($qaAssoc);
		return $resp;
	}

	public function getSiteVisitEnq($name)
	{
		$now=new DateTime();
	$today=$now->format('Y-m-d');
	if($name=="admin")
	{
		$todaySiteVisit=\DB::table('service_work')
		->join('sales_lead','sales_lead.Lead_ID','=','service_work.Lead_ID')
				->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
				->join('work_status','service_work.WorkStatus','=','work_status.Work_Status_ID')
				->where('Site_Analysis_Date', $today)
		->get();
	$countSiteVisit=count($todaySiteVisit);
	
	$resp=array($todaySiteVisit,$countSiteVisit);
	return $resp;
	}
	else if($name=="PMQA")
	{
		$todaySiteVisit=\DB::table('service_work')
		->join('sales_lead','sales_lead.Lead_ID','=','service_work.Lead_ID')
				->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
				->join('work_status','service_work.WorkStatus','=','work_status.Work_Status_ID')
				->where('Site_Analysis_Date', $today)
		->where('service_work.AssignedDept','PMQA')
		->get();
		//$countSiteVisit=count($todaySiteVisit);
		
	$resp=array($todaySiteVisit);
	return $resp;
	}
	else{
		$todaySiteVisit=\DB::table('service_work')
		->join('sales_lead','sales_lead.Lead_ID','=','service_work.Lead_ID')
				->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
				->join('work_status','service_work.WorkStatus','=','work_status.Work_Status_ID')
				->where('Site_Analysis_Date', $today)
		->where('Assigned_To',$name)
		->get();
		//$countSiteVisit=count($todaySiteVisit);
		
	$resp=array($todaySiteVisit);
	return $resp;
	}
	
	
	}

	public function getWOComplEnqs($name)
	{
		if($name=="admin")
	{
		$todaySiteVisit=\DB::table('service_work')
		->join('sales_lead','sales_lead.Lead_ID','=','service_work.Lead_ID')
				->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
				->join('work_status','service_work.WorkStatus','=','work_status.Work_Status_ID')
				->where('service_work.WorkStatus',13)->where('service_work.WOSignUp_Flag',1)
		->get();
	$countSiteVisit=count($todaySiteVisit);
	
	$resp=array($todaySiteVisit,$countSiteVisit);
	return $resp;
	}
	else if($name=="PMQA")
	{
		$todaySiteVisit=\DB::table('service_work')
		->join('sales_lead','sales_lead.Lead_ID','=','service_work.Lead_ID')
				->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
				->join('work_status','service_work.WorkStatus','=','work_status.Work_Status_ID')
				->where('service_work.WorkStatus',13)
		->where('service_work.AssignedDept','PMQA')
		->get();
		//$countSiteVisit=count($todaySiteVisit);
		
	$resp=array($todaySiteVisit);
	return $resp;
	}
	else{
		$todaySiteVisit=\DB::table('service_work')
		->join('sales_lead','sales_lead.Lead_ID','=','service_work.Lead_ID')
				->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
				->join('work_status','service_work.WorkStatus','=','work_status.Work_Status_ID')
				->where('service_work.WorkStatus',13)
		->where('Assigned_To',$name)
		->get();
		//$countSiteVisit=count($todaySiteVisit);
		
	$resp=array($todaySiteVisit);
	return $resp;
	}
	}

	public function getOnGoingProjects($name)
	{
		if($name=="admin")
	{
		$todaySiteVisit=\DB::table('service_work')
		->join('sales_lead','sales_lead.Lead_ID','=','service_work.Lead_ID')
				->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
				->join('work_status','service_work.WorkStatus','=','work_status.Work_Status_ID')
				->where('service_work.WorkStatus',7)
		->get();
	$countSiteVisit=count($todaySiteVisit);
	
	$resp=array($todaySiteVisit,$countSiteVisit);
	return $resp;
	}
	else if($name=="PMQA")
	{
		$todaySiteVisit=\DB::table('service_work')
		->join('sales_lead','sales_lead.Lead_ID','=','service_work.Lead_ID')
				->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
				->join('work_status','service_work.WorkStatus','=','work_status.Work_Status_ID')
				->where('service_work.WorkStatus',7)
		->where('service_work.AssignedDept','PMQA')
		->get();
		//$countSiteVisit=count($todaySiteVisit);
		
	$resp=array($todaySiteVisit);
	return $resp;
	}
	else{
		$todaySiteVisit=\DB::table('service_work')
		->join('sales_lead','sales_lead.Lead_ID','=','service_work.Lead_ID')
				->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
				->join('work_status','service_work.WorkStatus','=','work_status.Work_Status_ID')
				->where('service_work.WorkStatus',7)
		->where('Assigned_To',$name)
		->get();
		//$countSiteVisit=count($todaySiteVisit);
		
	$resp=array($todaySiteVisit);
	return $resp;
	}
	}
	public function getEstComplEnqs($name)
	{
		if($name=="admin")
	{
		$todaySiteVisit=\DB::table('service_work')
		->join('sales_lead','sales_lead.Lead_ID','=','service_work.Lead_ID')
				->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
				->join('work_status','service_work.WorkStatus','=','work_status.Work_Status_ID')
				->where('service_work.WorkStatus',5)
		->get();
	$countSiteVisit=count($todaySiteVisit);
	
	$resp=array($todaySiteVisit,$countSiteVisit);
	return $resp;
	}
	else if($name=="PMQA")
	{
		$todaySiteVisit=\DB::table('service_work')
		->join('sales_lead','sales_lead.Lead_ID','=','service_work.Lead_ID')
				->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
				->join('work_status','service_work.WorkStatus','=','work_status.Work_Status_ID')
				->where('service_work.WorkStatus',5)
		->where('service_work.AssignedDept','PMQA')
		->get();
		//$countSiteVisit=count($todaySiteVisit);
		
	$resp=array($todaySiteVisit);
	return $resp;
	}
	else{
		$todaySiteVisit=\DB::table('service_work')
		->join('sales_lead','sales_lead.Lead_ID','=','service_work.Lead_ID')
				->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
				->join('work_status','service_work.WorkStatus','=','work_status.Work_Status_ID')
				->where('service_work.WorkStatus',5)
		->where('Assigned_To',$name)
		->get();
		//$countSiteVisit=count($todaySiteVisit);
		
	$resp=array($todaySiteVisit);
	return $resp;
	}
	}
	public function getEstComplCount($name)
	{
		if($name=="admin")
	{
		$todaySiteVisit=\DB::table('service_work')
		->join('sales_lead','sales_lead.Lead_ID','=','service_work.Lead_ID')
				
				->where('service_work.WorkStatus',5)
		->get();
	$countSiteVisit=count($todaySiteVisit);
	
	$resp=array($countSiteVisit);
	return $resp;
	}
	else if($name=="PMQA")
	{
		$todaySiteVisit=\DB::table('service_work')
		
				->where('service_work.WorkStatus',5)
		->where('service_work.AssignedDept','PMQA')
		->get();
		$countSiteVisit=count($todaySiteVisit);
		
	$resp=array($countSiteVisit);
	return $resp;
	}
	else{
		$todaySiteVisit=\DB::table('service_work')
		
				->where('service_work.WorkStatus',5)
		->where('Assigned_To',$name)
		->get();
		$countSiteVisit=count($todaySiteVisit);
		
	$resp=array($countSiteVisit);
	return $resp;
	}

	}

	public function getSiteVisitPend($name)
	{
		$now=new DateTime();
	$today=$now->format('Y-m-d');
	if($name=='admin')
	{
		$pendingSiteVisit=\DB::table('service_work')->where('Site_Analysis_Date','<', $today)
		->where('ActualSite_Analysis_Date',null)
		->get();
		//$countPending=count($pendingSiteVisit);
		
	$resp=array($pendingSiteVisit);
	return $resp;

	}
	else{
		$pendingSiteVisit=\DB::table('service_work')->where('Site_Analysis_Date','<', $today)
		->where('ActualSite_Analysis_Date',null)
		->where('Assigned_To',$name)
		->get();
		//$countPending=count($pendingSiteVisit);
		
	$resp=array($pendingSiteVisit);
	return $resp;

	}
		
	}

	public function getSiteVisitCount($name)
	{
		$now=new DateTime();
	$today=$now->format('Y-m-d');
	if($name=='admin')
	{
		$pendingSiteVisit=\DB::table('service_work')->where('Site_Analysis_Date','=', $today)
		->get();
	$countPending=count($pendingSiteVisit);
		
	$resp=array($countPending);
	return $resp;

	}
	else if($name=='PMQA')
	{
		$pendingSiteVisit=\DB::table('service_work')->where('Site_Analysis_Date','=', $today)
		->where('AssignedDept','PMQA')
		->get();
	$countPending=count($pendingSiteVisit);
		
	$resp=array($countPending);
	return $resp;

	}
	else{
		$pendingSiteVisit=\DB::table('service_work')->where('Site_Analysis_Date','=', $today)
		->where('Assigned_To',$name)
		->get();
		$countPending=count($pendingSiteVisit);
		
	$resp=array($countPending);
	return $resp;

	}

		
	}

	public function getSiteVisitPendCount($name)
	{
		$now=new DateTime();
	$today=$now->format('Y-m-d');
	if($name=='admin')
	{
		$pendingSiteVisit=\DB::table('service_work')->where('Site_Analysis_Date','<', $today)
		->where('ActualSite_Analysis_Date',null)
		->get();
	$countPending=count($pendingSiteVisit);
		
	$resp=array($countPending);
	return $resp;

	}
	else if($name=='PMQA')
	{
		$pendingSiteVisit=\DB::table('service_work')->where('Site_Analysis_Date','<', $today)
		->where('ActualSite_Analysis_Date',null)
		->where('AssignedDept','PMQA')
		->get();
	$countPending=count($pendingSiteVisit);
		
	$resp=array($countPending);
	return $resp;

	}
	else{
		$pendingSiteVisit=\DB::table('service_work')->where('Site_Analysis_Date','<', $today)
		->where('ActualSite_Analysis_Date',null)
		->where('Assigned_To',$name)
		->get();
		$countPending=count($pendingSiteVisit);
		
	$resp=array($countPending);
	return $resp;

	}
	
	}
	public function getSiteVisitPendEnq($name)
	{
		$now=new DateTime();
	$today=$now->format('Y-m-d');
	if($name=='admin')
	{
		$pendingSiteVisit=\DB::table('service_work')
		->join('sales_lead','sales_lead.Lead_ID','=','service_work.Lead_ID')
				->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
				->join('work_status','service_work.WorkStatus','=','work_status.Work_Status_ID')
				->where('Site_Analysis_Date','<', $today)
		->where('ActualSite_Analysis_Date',null)
		->get();
	
		
	$resp=array($pendingSiteVisit);
	return $resp;

	}
	else if($name=='PMQA')
	{
		$pendingSiteVisit=\DB::table('service_work')
		->join('sales_lead','sales_lead.Lead_ID','=','service_work.Lead_ID')
				->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
				->join('work_status','service_work.WorkStatus','=','work_status.Work_Status_ID')
				->where('Site_Analysis_Date','<', $today)
		->where('ActualSite_Analysis_Date',null)
		->where('service_work.AssignedDept','PMQA')
		->get();

		
	$resp=array($pendingSiteVisit);
	return $resp;

	}
	else{
		$pendingSiteVisit=\DB::table('service_work')->where('Site_Analysis_Date','<', $today)
		->join('sales_lead','sales_lead.Lead_ID','=','service_work.Lead_ID')
				->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
				->join('work_status','service_work.WorkStatus','=','work_status.Work_Status_ID')

		->where('ActualSite_Analysis_Date',null)
		->where('service_work.Assigned_To',$name)
		->get();
		
		
	$resp=array($pendingSiteVisit);
	return $resp;

	}
	
	}

	public function getEstEnq($name)
	{
		if($name=='admin')
		{
			$est=\DB::table('service_work')
			->join('sales_lead','sales_lead.Lead_ID','=','service_work.Lead_ID')
			->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
			->join('work_status','service_work.WorkStatus','=','work_status.Work_Status_ID')
			->where('WorkStatus',10)->get();
		
		$resp=array($est);
		return $resp;
		}
		else if($name=='PMQA')
		{
			$est=\DB::table('service_work')
			->join('sales_lead','sales_lead.Lead_ID','=','service_work.Lead_ID')
			->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
			->join('work_status','service_work.WorkStatus','=','work_status.Work_Status_ID')
			->where('WorkStatus',10)->where('service_work.AssignedDept','PMQA')->get();
		
		$resp=array($est);
		return $resp;
		}
		else
		{
			$est=\DB::table('service_work')
			->join('sales_lead','sales_lead.Lead_ID','=','service_work.Lead_ID')
			->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
			->join('work_status','service_work.WorkStatus','=','work_status.Work_Status_ID')
			->where('WorkStatus',10)->where('service_work.Assigned_To',$name)->get();
			
			$resp=array($est);
			return $resp;
		}
		
	}

	public function getWOEnq($name)
	{
		if($name=='admin')
		{
		$est=\DB::table('service_work')
		->join('sales_lead','sales_lead.Lead_ID','=','service_work.Lead_ID')
			->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
			->join('work_status','service_work.WorkStatus','=','work_status.Work_Status_ID')
			->where('WorkStatus',6)->get();
			
		
		$resp=array($est);
		return $resp;
		}
		else if($name=='PMQA')
		{
			$est=\DB::table('service_work')
			->join('sales_lead','sales_lead.Lead_ID','=','service_work.Lead_ID')
				->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
				->join('work_status','service_work.WorkStatus','=','work_status.Work_Status_ID')
				->where('WorkStatus',6)->where('service_work.AssignedDept','PMQA')->get();
		$resp=array($est);
		return $resp;
		}
		else
		{
			$est=\DB::table('service_work')
			->join('sales_lead','sales_lead.Lead_ID','=','service_work.Lead_ID')
				->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
				->join('work_status','service_work.WorkStatus','=','work_status.Work_Status_ID')
				->where('WorkStatus',6)->where('service_work.Assigned_To',$name)->get();
		$resp=array($est);
		return $resp;
		}
	}

	public function getRegisteredAssocList()
	{
		$RegAssoc=\DB::table('associate')
		->leftjoin('contacts', 'contacts.Contact_ID','=','associate.Contact_ID')
		//->leftjoin('associate_details','associate_details.Assoc_ID','=','associate.Assoc_ID')
		//->leftjoin('location', 'location.Loc_ID','=','associate_details.Loc_ID')
		->where('Assoc_Status','1')->get();
		
		$resp=array($RegAssoc);
		return $resp;
	}

	public function getVerifiedAssocList()
	{
		$VerfAssoc=\DB::table('associate')
		->leftjoin('contacts', 'contacts.Contact_ID','=','associate.Contact_ID')
		->leftjoin('associate_details','associate_details.Assoc_ID','=','associate.Assoc_ID')
		->leftjoin('location', 'location.Loc_ID','=','associate_details.Loc_ID')
		->where('Assoc_Status','3')->get();
		
		$resp=array($VerfAssoc);
		return $resp;
	}

	public function getCertifiedAssocList()
	{
		
		$CertfAssoc=\DB::table('associate')
		->leftjoin('contacts', 'contacts.Contact_ID','=','associate.Contact_ID')
		->leftjoin('associate_details','associate_details.Assoc_ID','=','associate.Assoc_ID')
		->leftjoin('location', 'location.Loc_ID','=','associate_details.Loc_ID')
		->where('Assoc_Status','4')->get();
		
		$resp=array($CertfAssoc);
		return $resp;
	}

	public function getLostEnqDetails($name)
	{
		if($name=='admin')
		{
			$est=\DB::table('service_work')
			->join('sales_lead','sales_lead.Lead_ID','=','service_work.Lead_ID')
			->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
			->join('work_status','service_work.WorkStatus','=','work_status.Work_Status_ID')
			->where('WorkStatus',11)->get();
		
		$resp=array($est);
		return $resp;
		}
		else if($name=='PMQA')
		{
			$est=\DB::table('service_work')
			->join('sales_lead','sales_lead.Lead_ID','=','service_work.Lead_ID')
			->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
			->join('work_status','service_work.WorkStatus','=','work_status.Work_Status_ID')
			->where('WorkStatus',11)->where('service_work.AssignedDept','MID')->get();
		
		$resp=array($est);
		return $resp;
		}
		else
		{
			$est=\DB::table('service_work')
			->join('sales_lead','sales_lead.Lead_ID','=','service_work.Lead_ID')
			->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
			->join('work_status','service_work.WorkStatus','=','work_status.Work_Status_ID')
			->where('WorkStatus',11)->where('service_work.Assigned_To',$name)->get();
			
			$resp=array($est);
			return $resp;
		}
	}

	public function getLostEnqCount($name)
	{
		if($name=='admin')
		{
			$est=\DB::table('service_work')
				->where('WorkStatus',11)->get();
		$count=count($est);
		$resp=array($count);
		return $resp;
		}
		else if($name=='PMQA')
		{
			$est=\DB::table('service_work')
				->where('WorkStatus',11)->where('service_work.AssignedDept','MID')->get();
				$count=count($est);
				$resp=array($count);
				return $resp;
		}
		else
		{
			$est=\DB::table('service_work')
					->where('WorkStatus',11)->where('service_work.Assigned_To',$name)->get();
					$count=count($est);
					$resp=array($count);
					return $resp;
		}
	}
	public function getHotEnqDetails()
	{
		$hotLeads=\DB::table('sales_lead')
		->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
		-> join('location','location.Loc_ID','=','sales_customer.Loc_ID')
		->where('Priority','=','hot')->get();
		$resp=array($hotLeads);
		return $resp;
	}

	public function getEngineerCount()
	{
		$eng=\DB::table('logins')->where('User_Category', 2)->get();
		$count=count($eng);
		$resp=array($count);
		return $resp;

	}

	/*public function getEngDetails($id)
	{
		$eng=\DB::table('associate_type_rel')
		->join('associate','associate_type_rel.Assoc_ID','=','associate.Assoc_ID')
		->join('contacts', 'contacts.Contact_ID', '=','associate.Contact_ID')
		->join('associate_details', 'associate_details.Assoc_ID','=','associate.Assoc_ID')
		->join('location', 'location.Loc_ID', '=','associate_details.Loc_ID')
		->select('associate.Assoc_ID', 'contacts.Contact_phone', 'associate.Assoc_FirstName', 'associate.Assoc_MiddleName', 'associate.Assoc_LastName', 'location.Loc_Name', 'associate_details.Grade' )
		->where('Type_ID', $id)->get();
		
		$resp=array($eng);
		return $resp;
	}*/

	public function getArchCount()
	{
		$arch=\DB::table('logins')->where('User_Category', 1)->get();
		$count=count($arch);
		$resp=array($count);
		return $resp;
	}
	public function getVastuCount()
	{
		$vaastu=\DB::table('logins')->where('User_Category', 3)->get();
		$count=count($vaastu);
		$resp=array($count);
		return $resp;
	}

	public function getArticleCount()
	{
		$articles=\DB::table('associate_articles')->where('Approval_Status', 0)
		->where('DeleteFlag',0)->get();
		$count=count($articles);
		$resp=array($count);
		return $resp;
	}

	public function getSuppliersCount()
	{
		$suppliers=\DB::table('logins')->where('User_Category', 6)->get();
		$count=count($suppliers);
		$resp=array($count);
		return $resp;
	}

	public function getIntCount()
	{
		$interior=\DB::table('logins')->where('User_Category', 4)->get();
		$count=count($interior);
		$resp=array($count);
		return $resp;
	}

	public function getContractCount()
	{

	$contractor=\DB::table('logins')->where('User_Category', 5)->get();
		$count=count($contractor);
		$resp=array($count);
		return $resp;
	}

	/*public function getAssocTypeList($id)
	{
		$eng=\DB::table('associate_type_rel')
		->join('associate','associate_type_rel.Assoc_ID','=','associate.Assoc_ID')
		->join('contacts', 'contacts.Contact_ID', '=','associate.Contact_ID')
		->join('associate_details', 'associate_details.Assoc_ID','=','associate.Assoc_ID')
		->join('location', 'location.Loc_ID', '=','associate_details.Loc_ID')
		->select('associate.Assoc_ID', 'contacts.Contact_phone', 'associate.Assoc_FirstName', 'associate.Assoc_MiddleName', 'associate.Assoc_LastName', 'location.Loc_Name', 'associate_details.Grade' )
		->where('Type_ID', $id)->get();
		
		$resp=array($eng);
		return $resp;
	}*/

	public function getServiceNames($id)
	{
		$names=[];
		$Services=\DB::table('service_work')->where('Work_ID',$id)->pluck('Service_ID');
		foreach($Services as $serv)
        {
			$ServID=(int)$serv;
			$name=\DB::table('services')->where('Service_ID', $ServID)->pluck('Service_Name');
			array_push($names, $name);
		}
		$resp=array($names);
		return $resp;
		
		
		/*->join('services', 'services.Service_ID','=','service_work.Work_ID')
		->where('service_work.Work_ID',$id)->get();
		$resp=array($Services);
		return $resp;*/
	}

	public function getWorkServices($id)
	{
		$servicesList=\DB::table('work_service_map')->join('services', 'services.Service_ID','=','work_service_map.Service_ID')
		->where('Work_ID', $id)->get();
		$resp=array($servicesList);
		return $resp;
	}
	public function getAmendedLineItems($id, $no)
	{
		$labEstimate=\DB::table('work_labour_estimation')
		->join('serv_line_items', 'serv_line_items.LineItem_ID','=','work_labour_estimation.LineItem_ID')
	->join('units', 'units.Unit_ID','=','serv_line_items.UnitID')
	//->leftjoin('custom_line_items','custom_line_items.LineItem_ID','=','work_labour_estimation.customLineItem_ID')
	->where('work_labour_estimation.Work_ID', $id)->where('deleteFlag',0)->where('FinishAmend_Flag',1)->where('Amend_Flag',$no)->get();
	//$total=\DB::table('work_labour_estimation')->where('Work_ID', $id)->sum('Value');
	$resp=array($labEstimate);
	return $resp;
	}
	public function getAmendLineItems($id, $no)
	{
		$labEstimate=\DB::table('work_labour_estimation')
		->join('serv_line_items', 'serv_line_items.LineItem_ID','=','work_labour_estimation.LineItem_ID')
	->join('units', 'units.Unit_ID','=','serv_line_items.UnitID')
	//->leftjoin('custom_line_items','custom_line_items.LineItem_ID','=','work_labour_estimation.customLineItem_ID')
	->where('work_labour_estimation.Work_ID', $id)->where('deleteFlag',0)->where('FinishAmend_Flag',0)->where('Amend_Flag',$no)
	->orderBy('work_labour_estimation.Quantity','DESC')->get();
	//$total=\DB::table('work_labour_estimation')->where('Work_ID', $id)->sum('Value');
	$resp=array($labEstimate);
	return $resp;
	}
	public function getAmended1LineItems($id)
	{
		$labEstimate=\DB::table('work_labour_estimation')
		->join('serv_line_items', 'serv_line_items.LineItem_ID','=','work_labour_estimation.LineItem_ID')
	->join('units', 'units.Unit_ID','=','serv_line_items.UnitID')
	//->leftjoin('custom_line_items','custom_line_items.LineItem_ID','=','work_labour_estimation.customLineItem_ID')
	->where('work_labour_estimation.Work_ID', $id)->where('deleteFlag',0)->where('Amend_Flag',1)->where('FinishAmend_Flag',1)->get();
	//$total=\DB::table('work_labour_estimation')->where('Work_ID', $id)->sum('Value');
	$resp=array($labEstimate);
	return $resp;
	}
	public function getAmended2LineItems($id)
	{
		$labEstimate=\DB::table('work_labour_estimation')
		->join('serv_line_items', 'serv_line_items.LineItem_ID','=','work_labour_estimation.LineItem_ID')
	->join('units', 'units.Unit_ID','=','serv_line_items.UnitID')
	//->leftjoin('custom_line_items','custom_line_items.LineItem_ID','=','work_labour_estimation.customLineItem_ID')
	->where('work_labour_estimation.Work_ID', $id)->where('deleteFlag',0)->where('Amend_Flag',2)->where('FinishAmend_Flag',1)->get();
	//$total=\DB::table('work_labour_estimation')->where('Work_ID', $id)->sum('Value');
	$resp=array($labEstimate);
	return $resp;
	}
	public function getAmended3LineItems($id)
	{
		$labEstimate=\DB::table('work_labour_estimation')
		->join('serv_line_items', 'serv_line_items.LineItem_ID','=','work_labour_estimation.LineItem_ID')
	->join('units', 'units.Unit_ID','=','serv_line_items.UnitID')
	//->leftjoin('custom_line_items','custom_line_items.LineItem_ID','=','work_labour_estimation.customLineItem_ID')
	->where('work_labour_estimation.Work_ID', $id)->where('deleteFlag',0)->where('Amend_Flag',3)->where('FinishAmend_Flag',1)->get();
	//$total=\DB::table('work_labour_estimation')->where('Work_ID', $id)->sum('Value');
	$resp=array($labEstimate);
	return $resp;
	}

	public function saveExtraServices(Request $r)
	{
		$data= Request::json()->all();
		$items=$data['param2'];
		$id=$data['param1'];
		$serv_ID=\DB::table('service_work')->where('Work_ID', $id)->pluck('Service_ID');
		//$collection=collect($serv_ID);
		
foreach($items as $item)
{

$extra=\DB::table('work_service_map')->insert(array('Work_ID'=> $id, 'Service_ID'=>$item['name']));
//array_merge($serv_ID,$item['name']);

}


$resp=array("Success"=>true);
return $resp;


	}

	public function savePriority(Request $r)
	{
		$data= Request::json()->all();
		$itemID=$data['param2'];
		$priority=$data['param1'];
		$wid=$data['param3'];
		$savePriority=\DB::table('work_labour_estimation')->where('LE_ID',$itemID)
		->update(array('Priority'=>$priority, 'Priority_Flag'=>1));
		$resp=array('Success'=>true);
		return $resp;

	}

	public function editPriority(Request $r)
	{
		$data= Request::json()->all();
		$wID=$data['param1'];
		$savePriority=\DB::table('work_labour_estimation')->where('Work_ID',$wID)
		->update(array('Priority'=>0, 'Priority_Flag'=>0));
		$resp=array('Success'=>true);
		return $resp;

	}

	
}
