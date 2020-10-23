<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Input;
//use Illuminate\Http\Request;
use Request;
use DateTime;
use Response;
use File;

class salesModuleController extends Controller
{
    public function getSalesStatus()
	{
		$status=\DB::table('lead_status')->limit(1)->get();
		$response=array($status);
		return $response;	
	}
	public function addCustomer(Request $r)
	{
		\DB::transaction(function() use ($r) {
		$values = Request::json()->all();
		$existsCustomer=\DB::table('sales_customer')->join('contacts','sales_customer.Contact_ID','=','contacts.Contact_ID')->where('sales_customer.Cust_FirstName',$values['FirstName'])
		->where('contacts.Contact_phone',$values['contact'])->get();
		$count=count($existsCustomer);
		if($count>0)
		{
			$resp=array('hai', 'Success' =>false, $existsCustomer);
		return $resp;
		}
		else
		{
		$addressID=\DB::table('address')->insertGetID(array('Address_line1' => $values['addr1'], 'Address_line2' => $values['addr2'],'Address_email' => $values['email'], 'Landmark'=>$values['mark'], 'Address_postalcode'=>$values['pin']));
		$contactID=\DB::table('contacts')-> insertGetID(array('Contact_name' =>$values['FirstName'], 'Contact_whatsapp' =>$values['whatsapp'], 'Contact_phone' =>$values['contact']));
		if(!empty($addressID))
		{ 
		if(!empty($contactID))
		{
		$customer=\DB::table('sales_customer') -> insert(array('Cust_FirstName' =>$values['FirstName'], 'Cust_MidName'=>$values['MidName'], 'Cust_LastName' =>$values['LastName'], 'Address_ID' => $addressID, 'Contact_ID' => $contactID, 'Loc_ID' =>$values['loc'], 'Occupation' =>$values['occupation']));
		}
		}
		$resp=array('Customer added', 'Success' =>true, $existsCustomer);
		return $resp;
		}
	});
		
		//$resp=array('Customer added', 'Success' =>true, $existsCustomer);
		//return $resp;
	}
	public function getAllCustomers()
	{
		$customers=\DB::table('sales_customer')->join('address','address.Address_ID','=','sales_customer.Address_ID')
		->join('contacts','contacts.Contact_ID','=','sales_customer.Contact_ID')
		->select('Customer_ID','Cust_FirstName','Cust_MidName','Cust_LastName','address.*', 'contacts.*')->orderBy('Cust_FirstName','ASC')
		->get();
		$resp=array($customers);
		return $resp;
	}
	public function getOneCustomer($id)
	{
		$customer=\DB::table('sales_customer')->join ('address', 'sales_customer.Address_ID','=','address.Address_ID')
		->join('contacts','sales_customer.Contact_ID','=','contacts.Contact_ID')
		-> join('location','location.Loc_ID','=','sales_customer.Loc_ID')
		->where('sales_customer.Customer_ID',$id)
		->get();
		$resp=array($customer);
		return $resp;
	}
	public function getSources()
	{
		$source=\DB::table('sales_source')->get();
		$resp=array($source);
		return $resp;
	}
	public function getActivity()
	{
		$activity=\DB::table('lead_activity')->get();
		$resp=array($activity);
		return $resp;
	}
	public function addLead(Request $r)
	{
		\DB::transaction(function() use ($r) {
		$values = Request::json()->all();
		if($values['typeFlag']==1)
		{
			$siteAddr=\DB::table('address')->insertGetID(array('Address_line1' => $values['sAddr1'], 'Address_line2' =>$values['sAddr2'], 'Landmark'=>$values['mark'], 'Address_postalcode'=>$values['pin'] ));
			if(!empty($siteAddr))
			{
				$leadID=\DB::table('sales_lead')->insertGetId(array('Cust_ID' => $values['custName'], 'Site_AddressID'=>$siteAddr,'Lead_LocID' => $values['sloc'], 'Lead_StatusID' => '2',  'AssginedTo' => 'PMQA',  'Priority' => 'hot', 
				 'Proj_Details' => $values['projDetails'], 'Proj_Spec' => $values['projSpec'], 'Source_ID'=> $values['source'], 'Comment' => $values['comments']));
			}
			
			if(!empty($leadID))
			{
				$control=\DB::table('lead_access_control') -> insert(array('User_ID' =>'PMQA', 'Lead_ID' => $leadID, 'Action' =>'2'));
				$historyID=\DB::table('lead_history')->insert(array('Site_AddressID'=>$siteAddr,'Lead_StatusID' => '2',  'AssginedTo' => 'PMQA','Priority' => 'hot', 
				'Proj_Details' => $values['projDetails'], 'Proj_Spec' => $values['projSpec'], 'Source_ID'=> $values['source'], 'Comment' => $values['comments'], 'Lead_ID' => $leadID));
				 
			}
			
			$resp=array('Success' =>true,$values);
			return $resp;
		}
		if($values['type']==2)
		{
			$siteAddr=\DB::table('address')->insertGetID(array('Address_line1' => $values['sAddr1'], 'Address_line2' =>$values['sAddr2'], 'Landmark'=>$values['mark'], 'Address_postalcode'=>$values['pin'] ));
			if(!empty($siteAddr))
			{
				$leadID=\DB::table('sales_lead')->insertGetId(array('Cust_ID' => $values['custName'], 'Site_AddressID'=>$siteAddr,'Lead_LocID' => $values['sloc'], 'Lead_StatusID' => '2',  'AssginedTo' => 'PMQA',  'Priority' => 'hot', 
				 'Proj_Details' => $values['projDetails'], 'Proj_Spec' => $values['projSpec'], 'Source_ID'=> $values['source'], 'Comment' => $values['comments'], 'Flag'=>1));
			}
			
			if(!empty($leadID))
			{
				$control=\DB::table('lead_access_control') -> insert(array('User_ID' =>'PMQA', 'Lead_ID' => $leadID, 'Action' =>'2'));
				$historyID=\DB::table('lead_history')->insert(array('Site_AddressID'=>$siteAddr,'Lead_StatusID' => '2',  'AssginedTo' => 'PMQA','Priority' => 'hot', 
				'Proj_Details' => $values['projDetails'], 'Proj_Spec' => $values['projSpec'], 'Source_ID'=> $values['source'], 'Comment' => $values['comments'], 'Lead_ID' => $leadID));
				 
			}
			
			$resp=array('Success' =>true,$values);
			return $resp;
		}
		else{
		
		$newfollow=new DateTime($values['followup']);
		$newexp=new DateTime($values['closeDate']);
		$newfollow->modify('+1 day');
		$newexp->modify('+1 day');
				
		$siteAddr=\DB::table('address')->insertGetID(array('Address_line1' => $values['sAddr1'], 'Address_line2' =>$values['sAddr2'], 'Landmark'=>$values['mark'], 'Address_postalcode'=>$values['pin'] ));
		if(!empty($siteAddr))
		{
			$leadID=\DB::table('sales_lead')->insertGetId(array('Cust_ID' => $values['custName'], 'Site_AddressID'=>$siteAddr,'Lead_LocID' => $values['sloc'], 'Lead_StatusID' => $values['status'], 'NxtFollowupDate' => $newfollow->format('Y-m-d'), 'AssginedTo' => 'PMQA', 'AssignedDept' => $values['assignDept'], 'Priority' => $values['priority'], 
			'Activity' => $values['activity'], 'ExpClosureDate' => $newexp->format('Y-m-d'), 'ExpClosureAmt' => $values['closeAmt'], 'Proj_Details' => $values['projDetails'], 'Proj_Spec' => $values['projSpec'], 'Source_ID'=> $values['source'], 'Comment' => $values['comments']));
		}
		
		if(!empty($leadID))
		{
			$control=\DB::table('lead_access_control') -> insert(array('User_ID' =>$values['assignTo'], 'Lead_ID' => $leadID, 'Action' =>'2'));
			$historyID=\DB::table('lead_history')->insert(array('Site_AddressID'=>$siteAddr,'Lead_StatusID' => $values['status'], 'NxtFollowupDate' => $values['followup'], 'AssginedTo' => 'PMQA', 'AssignedDept' => $values['assignDept'], 'Priority' => $values['priority'], 
			'Activity' => $values['activity'], 'ExpClosureDate' => $values['closeDate'], 'ExpClosureAmt' => $values['closeAmt'], 'Proj_Details' => $values['projDetails'], 'Proj_Spec' => $values['projSpec'], 'Source_ID'=> $values['source'], 'Comment' => $values['comments'], 'Lead_ID' => $leadID));
			 
		}
		if(!empty($values['kms']))
		{
			$TA=\DB::table('project_expense')->insert(array('Lead_ID' => $leadID, 'Activity_ID' =>  $values['activity'], 'Value'=> $values['kms'], 'User_ID' =>'PMQA'));
		}
		$resp=array('Success' =>true,$values);
		return $resp;
			
	}
});
		
	}
	public function getSegCategories(Request $r)
	{
		$values = Request::json()->all();
		$cats=\DB::table('services')
		->join('service_segment_map','service_segment_map.Service_ID','=','services.Service_ID')
		->whereIn('service_segment_map.Segment_ID', $values)
		->where('services.DeleteFlag',1)
		->where('service_segment_map.DeleteFlag',0)
		->select('services.Service_ID','Service_Name','Service_Code')
		->orderby('Segment_ID')->get();
		$resp= array($cats);
		return $resp;
	}
	public function getCategories()
	{  
		$cats=\DB::table('services')->select('Service_ID','Service_Name','Service_Code')->get();
		$resp= array($cats);
		return $resp;
	}
	
	public function saveWork(Request $req)
	{
		$values = Request::json()->all();
		$service_list= $values['categories'];
	
	$comma_separated = implode(",", $values['categories']);
		$work=\DB::table('service_work')->insertGetId(array('Lead_ID'=>$values['leadID'],'WorkStatus' => 2,'Status_ID'=>2,
		'Segment_ID' => $values['services'], 'Service_ID' => $comma_separated,'WorkDetail' => $values['workDetails'], 'WorkSpec' => $values['workSpec'], 'FollowupDate'=>$values['wfollowup'],'Comments'=> $values['workcomments'], 'Assigned_To'=>$values['To'], 'AssignedDept'=>'PMQA', 'Site_Analysis_Date'=>$values['siteDate'], 'QuotationDate'=>$values['qDate']));
		foreach ($service_list as $ser) {
			$serv_map=\DB::table('work_service_map')->insert(array('Work_ID'=> $work, 'Service_ID'=>$ser));
		}
		if(!empty($work))
		{
			$work_history=\DB::table('work_history')-> insert(array('Lead_ID' =>$values['leadID'], 'Status_ID' =>1, 'Segment_ID' => $values['services'], 'Service_ID' => $comma_separated,'WorkDetail' => $values['workDetails'], 'WorkSpec' => $values['workSpec'], 'FollowupDate'=>$values['wfollowup'],'Comments'=> $values['workcomments'], 'Work_ID' => $work)); 
		}
		$resp=array('Success'=>true);
		return $resp;
		
	}

	public function saveWorkSales(Request $r)
	{
		\DB::transaction(function() use ($r) {
		$now=new DateTime();
	$today=$now->format('Y-m-d');


		$values = Request::json()->all();
		
		
		if($values['type']==1)
		{
			$lastWorkID=\DB::table('service_work')->where('Work_ID','<',10000)->orWhere('Work_ID','>',20000)->orderBy('Work_ID','DESC')->first();
		$insertID=$lastWorkID->Work_ID +1;

		if($values['typeID']==1)
		{
			
		if($values['lead_ID']==0)
		{
			$work=\DB::table('service_work')->insert(array('Work_ID'=>(int)$insertID,'Lead_ID'=>$values['lead'],'Status_ID' => 2,'WorkStatus'=>2,
			//'Segment_ID' => $values['services'], 'Service_ID' => $values['categories'],
			'Category' => $values['category'],
			'WorkDetail' => $values['workDetails'], 'WorkSpec' => $values['workSpec'], 'Comments'=> $values['workcomments'], 'Assigned_To'=>'PMQA', 'AssignedDept'=>'PMQA', 'RemoveFlag'=>1));
			
			if(!empty($work))
			{
				$work_history=\DB::table('work_history')-> insert(array('Lead_ID' =>$values['lead'], 'Status_ID' =>2,'WorkStatus'=>2,
				 'WorkDetail' => $values['workDetails'], 'WorkSpec' => $values['workSpec'],'Comments'=> $values['workcomments'], 'Work_ID' => (int)$insertID)); 
				$access=\DB::table('work_access_table')->insert(array('Work_ID'=> (int)$insertID , 'PMQA'=>'PMQA'));
				$work_create_date=\DB::table('work_timeline')->insert(array('Work_ID'=>(int)$insertID, 'Work_Attrb_ID'=>13, 'Value'=>$today));
				$work_limit=\DB::table('work_timeline')->insert(array('Work_ID'=>(int)$insertID, 'Work_Attrb_ID'=>38, 'Value'=>1));

				
			}
			
		}
		else if(($values['lead_ID']!=0))
		{
			$work=\DB::table('service_work')->insert(array('Work_ID'=>(int)$insertID,'Lead_ID'=>$values['lead_ID'],'Status_ID' => 2,'WorkStatus'=>2,
			//'Segment_ID' => $values['services'], 'Service_ID' => $values['categories'],
			'Category' => $values['category'],
			'WorkDetail' => $values['workDetails'], 'WorkSpec' => $values['workSpec'], 'Comments'=> $values['workcomments'], 'Assigned_To'=>'PMQA', 'AssignedDept'=>'PMQA', 'RemoveFlag'=>1));
			
			if(!empty($work))
			{
				$work_history=\DB::table('work_history')-> insert(array('Lead_ID' =>$values['lead_ID'], 'Status_ID' =>2,'WorkStatus'=>2,
				 'WorkDetail' => $values['workDetails'], 'WorkSpec' => $values['workSpec'],'Comments'=> $values['workcomments'], 'Work_ID' =>(int)$insertID)); 
				$access=\DB::table('work_access_table')->insert(array('Work_ID'=>(int)$insertID , 'PMQA'=>'PMQA'));
				$work_create_date=\DB::table('work_timeline')->insert(array('Work_ID'=>(int)$insertID, 'Work_Attrb_ID'=>13, 'Value'=>$today));
				$work_limit=\DB::table('work_timeline')->insert(array('Work_ID'=>(int)$insertID, 'Work_Attrb_ID'=>38, 'Value'=>1));
			}
		}
		
	
		$resp=array('Success'=>true);
		return $resp;
		}
		else{
		
		$newfollow=new DateTime($values['wfollowup']);
		$newfollow->modify('+1 day');

		$assignee=\DB::table('sales_lead')->where('Lead_ID',$values['leadID'])->pluck('AssginedTo');
		$work=\DB::table('service_work')->insert(array('Work_ID'=>(int)$insertID,'Lead_ID'=>$values['leadID'],'Status_ID' => 1,
		//'Segment_ID' => $values['services'], 'Service_ID' => $values['categories'],
		'Category' => $values['category'],'RemoveFlag'=>1,
		'WorkDetail' => $values['workDetails'], 'WorkSpec' => $values['workSpec'], 'FollowupDate'=>$newfollow->format('Y-m-d'),'Comments'=> $values['workcomments'], 'Assigned_To'=>$assignee[0], 'AssignedDept'=>'MI'));
		
		
		if(!empty($work))
		{
			$work_history=\DB::table('work_history')-> insert(array('Lead_ID' =>$values['leadID'], 'Status_ID' =>1,
			 //'Segment_ID' => $values['services'], 'Service_ID' => $values['categories'],
			 'WorkDetail' => $values['workDetails'], 'WorkSpec' => $values['workSpec'], 'FollowupDate'=>$newfollow->format('Y-m-d'),'Comments'=> $values['workcomments'], 'Work_ID' => (int)$insertID)); 
			$access=\DB::table('work_access_table')->insert(array('Work_ID'=> (int)$insertID , 'MI'=>$assignee['0']));
			$work_create_date=\DB::table('work_timeline')->insert(array('Work_ID'=>(int)$insertID, 'Work_Attrb_ID'=>13, 'Value'=>$today));
		}
		$resp=array('Success'=>true,(int)$insertID);
		return $resp;

		}
	
	}
	else if($values['type']==2)
	{
		$siteDate=new DateTime($values['analysisDate']);
		$siteDate->modify('+1 day');
		$comma_separated = implode(",", $values['seg']);
		$service_list= $values['seg'];
	
	
		
		$workExists=\DB::table('service_work')->where('Work_ID','>=',10000)->get();
		$count=count($workExists);
		if($count==0)
		{
			$insertID=10000;
			$work=\DB::table('service_work')->insert(array('Work_ID'=>(int)$insertID,'Lead_ID'=>$values['lead'],'Status_ID' => 2,'WorkStatus'=>2,
			'Segment_ID' => $values['ser'], 'Service_ID' => $comma_separated,'WorkStatus' => 2,'Status_ID'=>2,'Work_Type'=>$values['worktype'],
			'Category' => $values['category'],
			'WorkDetail' => $values['workDetails'], 'WorkSpec' => $values['workSpec'], 
			'Comments'=> $values['workcomments'], 'Assigned_To'=>'PMQA', 'AssignedDept'=>'PMQA', 'RemoveFlag'=>1,'Wo_No'=>$values['workNo'] ,'Migrate_Flag'=>1));
			foreach ($service_list as $ser) {
				$serv_map=\DB::table('work_service_map')->insert(array('Work_ID'=> (int)$insertID, 'Service_ID'=>$ser));
			}
			
			if(!empty($work))
			{
				//$work_history=\DB::table('work_history')-> insert(array('Lead_ID' =>$values['lead'], 'Status_ID' =>2,'WorkStatus'=>2,
				// 'WorkDetail' => $values['workDetails'], 'WorkSpec' => $values['workSpec'],'Comments'=> $values['workcomments'], 'Work_ID' => (int)$insertID)); 
				$access=\DB::table('work_access_table')->insert(array('Work_ID'=> (int)$insertID , 'PMQA'=>'PMQA'));
				$work_create_date=\DB::table('work_timeline')->insert(array('Work_ID'=>(int)$insertID, 'Work_Attrb_ID'=>13, 'Value'=>$today));
				$work_limit=\DB::table('work_timeline')->insert(array('Work_ID'=>(int)$insertID, 'Work_Attrb_ID'=>38, 'Value'=>1));

				
			}
		}
		else{
			$lastWorkID=\DB::table('service_work')->where('Work_ID','>=',10000)->orderBy('Work_ID','DESC')->first();
		$insertID=$lastWorkID->Work_ID +1;
		$work=\DB::table('service_work')->insert(array('Work_ID'=>(int)$insertID,'Lead_ID'=>$values['lead'],'Status_ID' => 2,'WorkStatus'=>2,
			'Segment_ID' => $values['ser'], 'Service_ID' => $comma_separated,
			'Category' => $values['category'],
			'WorkDetail' => $values['workDetails'], 'WorkSpec' => $values['workSpec'], 
			'Comments'=> $values['workcomments'], 'Assigned_To'=>'PMQA', 'AssignedDept'=>'PMQA', 'RemoveFlag'=>1,'Wo_No'=>$values['workNo'],'Migrate_Flag'=>1 ));
			$service_list= $values['seg'];
foreach ($service_list as $ser) {
				$serv_map=\DB::table('work_service_map')->insert(array('Work_ID'=> (int)$insertID, 'Service_ID'=>$ser));
			}
			
			if(!empty($work))
			{
				//$work_history=\DB::table('work_history')-> insert(array('Lead_ID' =>$values['lead'], 'Status_ID' =>2,'WorkStatus'=>2,
				// 'WorkDetail' => $values['workDetails'], 'WorkSpec' => $values['workSpec'],'Comments'=> $values['workcomments'], 'Work_ID' => (int)$insertID)); 
				$access=\DB::table('work_access_table')->insert(array('Work_ID'=> (int)$insertID , 'PMQA'=>'PMQA'));
				$work_create_date=\DB::table('work_timeline')->insert(array('Work_ID'=>(int)$insertID, 'Work_Attrb_ID'=>13, 'Value'=>$today));
				$work_limit=\DB::table('work_timeline')->insert(array('Work_ID'=>(int)$insertID, 'Work_Attrb_ID'=>38, 'Value'=>1));

				
			}
			$resp=array("Success"=>true,'Work_ID'=>$insertID);
			return $resp;

		}
			
		
	}
	
});

	
	}
	public function getAllTempWork($id)
	{
		$workDetails= \DB::table('temp_work') ->where('Cust_ID', $id) -> get();
		$resp=array($workDetails);
		return $resp;
	}
	//Todisplay leads list by username
	public function getAllLeads($name)
	{
		
			$leadsAll = \DB::table('sales_lead')->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')->join('lead_status','lead_status.Lead_Status_ID','=','sales_lead.Lead_StatusID')
			->join('contacts','contacts.Contact_ID','=','sales_customer.Contact_ID')
			->join('location','location.Loc_ID','=','sales_lead.Lead_LocID')
			//->join('lead_access_control', 'lead_access_control.User_ID','=','sales_lead.AssginedTo')
			->where('sales_lead.AssginedTo', $name)
			->where('sales_lead.Flag','!=',2)
			->orderby('sales_lead.Lead_ID','DESC')
			->get();
			//$leads=\DB::table('lead_acess_control')->join('sales_lead', 'sales_lead.Lead_ID', '=','lead_acess_control.Lead_ID')->get();
			//->where('lead_access_control.User_ID', $id)
			$resp=array($leadsAll);
			return $resp;
		
		/*$leads = \DB::table('sales_lead')->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')->join('lead_status','lead_status.Lead_Status_ID','=','sales_lead.Lead_StatusID')->join('contacts','contacts.Contact_ID','=','sales_customer.Contact_ID')
		->join('location','location.Loc_ID','=','sales_lead.Lead_LocID')
		->join('lead_access_control', 'lead_access_control.User_ID','=','sales_lead.AssginedTo')
		->join('logins', 'logins.User_Login','=','sales_lead.AssginedTo')
		//->where('logins.User_ID',$id)
		//->where('lead_access_control.User_ID', $userLogin)
		->orderby('sales_lead.Lead_ID','DESC')
		->get();
		$resp=array($leads, $userLogin);
		return $resp;*/
		
	
	}

	public function getFullLeads()
	{
		$leadsAll = \DB::table('sales_lead')->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')->join('lead_status','lead_status.Lead_Status_ID','=','sales_lead.Lead_StatusID')
			->join('contacts','contacts.Contact_ID','=','sales_customer.Contact_ID')
			->join('location','location.Loc_ID','=','sales_lead.Lead_LocID')
			//->join('lead_access_control', 'lead_access_control.User_ID','=','sales_lead.AssginedTo')
			//->where('sales_lead.AssginedTo', $name)
			->where('sales_lead.Flag',0)
			->orderby('sales_lead.Lead_ID','DESC')
			->get();
			//$leads=\DB::table('lead_acess_control')->join('sales_lead', 'sales_lead.Lead_ID', '=','lead_acess_control.Lead_ID')->get();
			//->where('lead_access_control.User_ID', $id)
			$resp=array($leadsAll);
			return $resp;
	}
	public function getOneLead($id)
	{
		$oneLead= \DB::table('sales_lead')
		->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
		->join('lead_status','lead_status.Lead_Status_ID','=','sales_lead.Lead_StatusID')
		->join('contacts','contacts.Contact_ID','=','sales_customer.Contact_ID')
		->leftjoin('address','address.Address_ID','=','sales_lead.Site_AddressID')
		->leftjoin ('sales_source','sales_source.Source_ID','=','sales_lead.Source_ID')
		->leftjoin('location','location.Loc_ID','=','sales_lead.Lead_LocID')
		->leftjoin ('lead_activity','lead_activity.Activity_ID','=','sales_lead.Activity')
		->where('sales_lead.Lead_ID',$id)
		->get();
		$resp=array($oneLead);
		return $resp;
	}
	public function getAllWork($id)
	{
		$allWork=\DB::table('service_work')
		//->join('segment','segment.Segment_ID','=','service_work.Segment_ID')
		//->join('services','services.Service_ID','=','service_work.Service_ID')
		->join('work_color_status', 'work_color_status.Color_StatusID','=','service_work.Generate_Work_Status')
		->join('work_updation_status','work_updation_status.Update_Status_ID','=','service_work.Update_Status')->join('work_status','work_status.Work_Status_ID','=','service_work.WorkStatus')
		->join('sales_status','sales_status.sales_statusID','=','service_work.WorkStatus')
		->where('Lead_ID', $id)->get();
		$resp=array($allWork);
		return $resp;
	}
	public function getWorkStatus()
	{
		$status = \DB::table('work_status')->limit(2)->get();
		$resp=array($status);
		return $resp;
	}
	public function getOneWork($id)
	{
		$oneWork=\DB::table('service_work')
		->leftjoin('segment','segment.Segment_ID','=','service_work.Segment_ID')
		->leftjoin('services','services.Service_ID','=','service_work.Service_ID')
		//->join('work_color_status', 'work_color_status.Color_StatusID','=','service_work.Generate_Work_Status')
		->join ('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')
		->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
		->join('address', 'address.Address_ID','=','sales_customer.Address_ID')
		->join('contacts','contacts.Contact_ID','=','sales_customer.Contact_ID')
		->join('location','location.Loc_ID','=','sales_lead.Lead_LocID')
		->join('work_status', 'work_status.Work_Status_ID','=','service_work.WorkStatus')
		//->join('work_status','work_status.Work_Status_ID','=','service_work.WorkStatus')
		//->join('department', 'department.Dept_Name','=','service_work.AssignedDept')
		//->join('logins', 'logins.User_Login', '=','service_work.Assigned_To')
		->where('Work_ID', $id)->get();
		$resp=array($oneWork);
		return $resp;
	}
	public function getOneWork1($id)
	{
		$oneWork=\DB::table('service_work')
		->join('segment','segment.Segment_ID','=','service_work.Segment_ID')
		
		//->join('work_service_map', 'work_service_map.Work_ID','=','service_work.Work_ID')
		//->join('services','services.Service_ID','=','work_service_map.Service_ID')
		->join ('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')
		->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
		->join('contacts','contacts.Contact_ID','=','sales_customer.Contact_ID')
		->join('location','location.Loc_ID','=','sales_lead.Lead_LocID')
		->join('work_status', 'work_status.Work_Status_ID','=','service_work.WorkStatus')
		//->join('work_status','work_status.Work_Status_ID','=','service_work.WorkStatus')
		//->join('department', 'department.Dept_Name','=','service_work.AssignedDept')
		->join('logins', 'logins.User_Login', '=','service_work.Assigned_To')
		->where('Work_ID', $id)->get();
		$resp=array($oneWork);
		return $resp;
	}
	public function updateLead(Request $r)
	{
		\DB::transaction(function() use ($r) {
		$new=array();
		
		$values = Request::json()->all();
		$newfollow=new DateTime($values['followup']);
		$newfollow->modify('+1 day');

		$original=\DB::table('sales_lead')->where('Lead_ID', $values['lead'])->get();
		$update_lead=\DB::table('sales_lead')->where('Lead_ID' , $values['lead'])
		->update(array('Activity' => $values['activity'],'Comment' => $values['comments']));

	
		
		if(!($original[0]->NxtFollowupDate == $newfollow->format('Y-m-d')))
		{
			$update_followup=\DB::table('sales_lead')->where('Lead_ID' , $values['lead'])
			->update(array('NxtFollowupDate' => $newfollow->format('Y-m-d')));
		}
		else if(!($original[0]->AssginedTo == $values['assignTo']))
		{
			$update_assign=\DB::table('sales_lead')->where('Lead_ID' , $values['lead'])
		->update(array('AssginedTo' => $values['assignTo']));
		if(!empty($update_assign))
		{
			$userID=\DB::table('lead_access_control')->where('User_ID', $values['assignTo'])->where('Lead_ID', $values['lead'])->get();
			$count=count($userID);
			if($count==0)
			{
			
			$control=\DB::table('lead_access_control') -> insert(array('User_ID' =>$values['assignTo'], 'Lead_ID' => $values['lead'], 'Action' =>'2'));
			}
			
		
		}
		}
		else if(!($original[0]->Priority == $values['pr']))
		{
			$update_priority=\DB::table('sales_lead')->where('Lead_ID' , $values['lead'])
		->update(array('Priority' => $values['pr']));
		}

		/*$update_lead=\DB::table('sales_lead')->where('Lead_ID' , $values['lead'])
		->update(array('Activity' => $values['activity'],'Comment' => $values['comments']));
		if(!empty($values['followup']))
		{
			
		}
		if(!empty($values['pr']))
		{
			$update_priority=\DB::table('sales_lead')->where('Lead_ID' , $values['lead'])
		->update(array('Priority' => $values['pr']));
		}
		if(!empty($values['assignTo']))
		{
			$update_assign=\DB::table('sales_lead')->where('Lead_ID' , $values['lead'])
		->update(array('AssginedTo' => $values['assignTo']));
		}
		if(!empty($update_lead))
		{
			$userID=\DB::table('lead_access_control')->where('User_ID', $values['assignTo'])->where('Lead_ID', $values['lead'])->get();
			$count=count($userID);
			if($count>0)
			{
			
			$control=\DB::table('lead_access_control') -> insert(array('User_ID' =>$values['assignTo'], 'Lead_ID' => $values['lead'], 'Action' =>'2'));
			}
			
		
		}*/
		
		
		if(!empty($values['kms']))
		{
			$TA=\DB::table('project_expense')->insert(array('Lead_ID' => $values['lead'], 'Activity_ID' =>  $values['activity'], 'Value'=> $values['kms'], 'User_ID' =>$values['assignTo']));
		}
		if(!empty($value['assignTo']))
		{
			$userID=\DB::table('lead_access_control')->where('User_ID', $values['assignTo'])->where('Lead_ID', $values['lead'])->get();
			$count=count($userID);
			if($count==0)
			{
			
			$control=\DB::table('lead_access_control') -> insert(array('User_ID' =>$values['assignTo'], 'Lead_ID' => $values['lead'], 'Action' =>'2'));
			}
			
		
		}
		$resp=array('Success' =>true);
		return $resp;
	});
		
	}
	
	public function updateWork(Request $r)
	{
		\DB::transaction(function() use ($r) {
		$now=new DateTime();
	$today=$now->format('Y-m-d');
		$values = Request::json()->all();

$newsite=new DateTime($values['siteDate']);
$newsite->modify('+1 day');
$newquote=new DateTime($values['qDate']);
$newquote->modify('+1 day');

		if($values['status']==1)
		{
			

$resp=array("Success"=>false);
return $resp;
		}


	if($values['status']==2)
		{
			/*$mobileNo=\DB::table('service_work')->join('sales_lead','sales_lead.Lead_ID', '=','service_work.Lead_ID')
			->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
			->join('contacts','contacts.Contact_ID','=','sales_customer.Contact_ID')->pluck('contacts.Contact_phone');*/

			$assignee=\DB::table('service_work')->where('Work_ID', $values['workID'])->pluck('Assigned_To');
			$work=\DB::table('service_work')->where('Work_ID', $values['workID'])
						->update(array('Site_Analysis_Date' =>$newsite->format('Y-m-d'),'QuotationDate'=>$newquote->format('Y-m-d'),
						'WorkStatus'=>2, 'Generate_Work_Status'=>2, 'Status_ID'=>2, 'Assigned_To'=>$values['assignedToPMQA'], 'AssignedDept'=>'PMQA'));
						if(!empty($work))
						{
						
							$changeActivity=\DB::table('sales_lead')->where('Lead_ID', $values['leadID'])->update(array('Lead_StatusID'=> '2'));
							$access=\DB::table('work_access_table')->where('Work_ID', $values['workID'])->update(array( 'MI'=>$assignee['0']));
							$work_generate_date=\DB::table('work_timeline')->insert(array('Work_ID'=>$values['workID'], 'Work_Attrb_ID'=>14, 'Value'=>$today));
						}
//code for sms process
/*$message="Hello, this is a test message from Inframall..";
$encodeMessage=urlencode($message);
$sender = "API Test"; // This is who the message appears to be from.
	$numbers = "919961901065";
	//$numbers = implode(',', $numbers);
 
	$hash="WyR4O60Dx90-devAMUmOd8XPc7Xvxa1zB6SoTMhBSd";
	$data = array('apikey' => $hash, 'numbers' => $numbers, "sender" => $sender, "message" => $encodeMessage);
	$ch = curl_init('http://api.txtlocal.com/send/');
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec($ch);
	curl_close($ch);*/
			

		$resp=array('Success'=>true, $work);//, $response
		return $resp;
		}
		if($values['status']==11)
		{
			
			$work=\DB::table('service_work')->where('Work_ID', $values['workID'])
						->update(array('WorkStatus'=>11,'Generate_Work_Status'=>4,'Status_ID'=>11));
						if(!empty($work))
						{
						$reson=\DB::table('work_timeline')->insert(array('Work_ID'=>$values['workID'], 'Work_Attrb_ID'=>15, 'Value'=>$today));
							
						}
			

		$resp=array('Success'=>true);
		return $resp;
		}
		
	});
		
		
		
	}
	public function getPMQALeadList()
	{
		$PMQAList=\DB::table('sales_lead')
		->join('sales_customer', 'sales_lead.Cust_ID','=','sales_customer.Customer_ID')
		->join('location','location.Loc_ID','=','sales_lead.Lead_LocID')
		->join('lead_status','lead_status.Lead_Status_ID','=','sales_lead.Lead_StatusID')
		->where('sales_lead.Lead_StatusID','2')->get();
		$resp=array($PMQAList);
		return $resp;
	}
	public function getPMQAWorks()
	{
		$PMQAWork=\DB::table('service_work')->join('segment','segment.Segment_ID','=','service_work.Segment_ID')
		->join('sales_lead','sales_lead.Lead_ID','=','service_work.Lead_ID')
		->join('sales_customer', 'sales_lead.Cust_ID','=','sales_customer.Customer_ID')
		->join('location','location.Loc_ID','=','sales_lead.Lead_LocID')
		->join('services','services.Service_ID','=','service_work.Service_ID')
		->join('work_status', 'work_status.Work_Status_ID','=','service_work.Status_ID')
		->join('sales_status','sales_status.sales_statusID','=','service_work.WorkStatus')
		->where('service_work.Status_ID', '2')->get();
		$resp=array($PMQAWork);
		return $resp;
	}
	public function getOnePMQAWorks($id)
	{
		$PMQAWork=\DB::table('service_work')->join('segment','segment.Segment_ID','=','service_work.Segment_ID')
		->join('sales_lead','sales_lead.Lead_ID','=','service_work.Lead_ID')
		->join('sales_customer', 'sales_lead.Cust_ID','=','sales_customer.Customer_ID')
		->join('location','location.Loc_ID','=','sales_lead.Lead_LocID')
		->join('services','services.Service_ID','=','service_work.Service_ID')
		->join('work_status', 'work_status.Work_Status_ID','=','service_work.WorkStatus')
		->join('work_updation_status','work_updation_status.Update_Status_ID','=','service_work.Update_Status')
		->join('sales_status','sales_status.sales_statusID','=','service_work.WorkStatus')
		->where('service_work.Status_ID', '2')
		->where('sales_lead.Lead_ID',$id)->get();
		$resp=array($PMQAWork);
		return $resp;
	}
	public function design_upload(Request $req)
{
	
	
$value= Request::json()->all();
	$filename = Request::file('fileKey')->getClientOriginalName();
	$name=Input::get('name');
	$id=Input::get('id');
	$docExists=\DB::table('work_details')->where ('Work_ID',$id)->select('Design_Doc')->get();
	$count=count($docExists);
	if($count>0)
	{
		if(Request::file('fileKey')){
		$file=Request::file('fileKey');
		$file->move('resources/assets/uploads/ProjectDesigns',$filename);
		$doc=\DB::table('work_details')->where('Work_ID', $id)->update(array('Design_Doc' =>$filename));
		$response=array('response'=>'Design updated','success'=>true, $filename);
		return $response;
		}
	}
	//$file2=$value->file['fileKey'];
	//$attachment = Request::file(a);
	//$response=array('response'=>'file not uploaded','success'=>true, Request::all(),$filename);
		//return $response;
		else
		{
		
	if(Request::file('fileKey')){
		$file=Request::file('fileKey');
		$file->move('resources/assets/uploads/ProjectDesigns',$filename);
	$doc=\DB::table('work_details')->insert(array('Work_ID'=>$id,'Design_Doc' =>$filename));
	//echo '<img src = 'uploads/'.$file->getClientOriginalName()>';
	$response=array('response'=>'Design uploaded','success'=>true, $filename);
		return $response;
	}
		}

	
}

public function designExists($id)
{
	$docExists=\DB::table('work_details')->where('Work_ID',$id)->select('Design_Doc')->get();
	$count=count($docExists);
	$resp=array($count);
	return $resp;
}
public function addMatEstimation(Request $req)
{
	$value= Request::json()->all();
	$matEstimate=\DB::table('work_material_estimation')->insert(array('Work_ID' => $value['work_ID'],'Product_ID' => $value['prod_ID'],'Unit' => $value['unitM'],'Quantity' => $value['qty'],'Rate' =>  $value['rate'], 'Value' => $value['value'],'Comments' => $value['cmnts']));
	
	$resp=array($matEstimate);
	return $resp;
}
public function getallMatEstimate($id)
{
	$MatEstimates=\DB::table('work_material_estimation')
	->join('products','products.Prod_ID','=','work_material_estimation.Product_ID')
	->where('Work_ID', $id)->where('deleteFlag',1)->get();
	$count=count($MatEstimates);
	$resp=array($MatEstimates, $count);
	return $resp;
}
public function updatePMQAWork(Request $r)
{
	\DB::transaction(function() use ($r) {
	$value= Request::json()->all();
if($value['typeID']==1)
{
	$service_list= $value['seg'];
	foreach ($service_list as $ser) {
		$serv_map=\DB::table('work_service_map')->insert(array('Work_ID'=> $value['work'], 'Service_ID'=>$ser));
	}
	$comma_separated = implode(",", $value['seg']);
	$leadID=\DB::table('service_work')->where('Work_ID', $value['work'])->pluck('Lead_ID');
	
	$update3=\DB::table('service_work')-> where('Work_ID', $value['work'])->update(array('Segment_ID'=>$value['ser'],'Service_ID'=>$comma_separated,'Work_Days'=> $value['duration'], 'Work_Type' => $value['type'],'Comments'=>$value['comments'],'ActualSite_Analysis_Date'=>$value['analysisDate'],'Update_Status' =>'2', 'WorkStatus'=>10,'SiteAnalysis_Flag'=>1));
	if(!empty($value['area']))
		{
			$Work_Area=\DB::table('work_timeline')->insert(array('Work_ID'=> $value['work'], 'Work_Attrb_ID'=>21, 'Value'=>$value['area']));
		}

		$resp=array("Success" => true);
	return $resp;
}
else{
	


	$service_list= $value['seg'];
	foreach ($service_list as $ser) {
		$serv_map=\DB::table('work_service_map')->insert(array('Work_ID'=> $value['work'], 'Service_ID'=>$ser));
	}
	$comma_separated = implode(",", $value['seg']);
	$leadID=\DB::table('service_work')->where('Work_ID', $value['work'])->pluck('Lead_ID');
	if(!empty($value['analysisDate']))
	{
	$update3=\DB::table('service_work')-> where('Work_ID', $value['work'])->update(array('Segment_ID'=>$value['ser'],'Service_ID'=>$comma_separated,'Work_Days'=> $value['duration'], 'Work_Type' => $value['type'],'Comments'=>$value['comments'],'ActualSite_Analysis_Date'=>$value['analysisDate'],'Update_Status' =>'2', 'WorkStatus'=>10));
	}
	else{
		$update3=\DB::table('service_work')-> where('Work_ID', $value['work'])->update(array('Segment_ID'=>$value['ser'],'Service_ID'=>$comma_separated,'Work_Days'=> $value['duration'], 'Work_Type' => $value['type'],'Comments'=>$value['comments'],'ActualSite_Analysis_Date'=>$value['analysisDate'],'Update_Status' =>'1','WorkStatus'=>2));
	}
	if(!empty($value['kms']))
		{
			$TA=\DB::table('project_expense')->insert(array('Lead_ID' => $leadID[0], 'Activity_ID' =>  2, 'Value'=> $value['kms'], 'User_ID' =>$value['assignedToPMQA']));
		}
		if(!empty($value['area']))
		{
			$Work_Area=\DB::table('work_timeline')->insert(array('Work_ID'=> $value['work'], 'Work_Attrb_ID'=>21, 'Value'=>$value['area']));
		}
		
	$resp=array("Success" => true, $update3, "Assignee"=>$value['assignedToPMQA']);
	return $resp;
	
}
	});
	
}
public function checkWorkUpdate($id)
{
	$update=\DB::table('service_work')->where('Update_Status',2)->get();
	$resp=array($update);
	return $resp;
}
public function checkWorkType($id)
{
	$type=\DB::table('service_work')->where('Work_ID',$id)->select('Work_Type')->get();
	$resp=array($type);
	return $resp;
}
public function checkDesign($id)
{
	$design=\DB::table('work_details')->where('Work_ID', $id) ->select('Design_Doc')->get();
	$resp=array($design);
	return $resp;
}
public function downloadDesign($id)
{
	$Doc=\DB::table('work_details')->where('Work_ID',$id)->pluck('Design_Doc');
	//$split=explode('-',$aadhar);
	//$filename=$split[1];
	//$url= Storage::url($aadhar);
	$url='http://inframall.net/bims/public/resources/assets/uploads/ProjectDesigns/'.$Doc[0];
	$resp=array($url);
	return $resp;
}
public function getProductsList($id)
{
	$products=\DB::table('work_labour_estimation')->join('serv_line_items', 

'serv_line_items.LineItem_ID','=','work_labour_estimation.LineItem_ID')->join

('service_group_map','serv_line_items.Service_ID','=','service_group_map.Service_ID')
	->join('prod_groups','prod_groups.Group_ID','=','service_group_map.Group_ID')
	->join('products','products.Group_ID','=','service_group_map.Group_ID')->where('work_labour_estimation.LE_ID',$id)
->select('products.*')->get();
	$resp=array($products);
	return $resp;
}

public function getAllLineItems($id)
{
	
	$service_Id=\DB::table('work_service_map')->where('Work_ID', $id)->pluck('Service_ID');
	if(!empty($service_Id))
	{
		
			$lineItems=\DB::table('serv_line_items')
			->join('units','units.Unit_ID','=','serv_line_items.UnitID')
			->join('service_servlineitem_rel', 'service_servlineitem_rel.LineItem_ID','=','serv_line_items.LineItem_ID')
			//->join('services', 'services.Service_ID', '=','service_servlineitem_rel.Service_ID')
			//->where('customFlag', 0)
			->whereIn('service_servlineitem_rel.Service_ID',$service_Id)
			->get();
			
		
		}
	
	$res=array($lineItems);
	return $res;
}
public function chkLineItemsExists($id)
{
	$service_Id=\DB::table('work_service_map')->where('Work_ID', $id)->pluck('Service_ID');
	if(!empty($service_Id))
	{
		
			$lineItems=\DB::table('serv_line_items')
			->join('units','units.Unit_ID','=','serv_line_items.UnitID')
			->join('service_servlineitem_rel', 'service_servlineitem_rel.LineItem_ID','=','serv_line_items.LineItem_ID')
			//->join('services', 'services.Service_ID', '=','service_servlineitem_rel.Service_ID')
			//->where('customFlag', 0)
			->whereIn('service_servlineitem_rel.Service_ID',$service_Id)
			->get();
			
		
		}
		$count=count($lineItems);
	
	$res=array($count);
	return $res;
}
public function getUnitofProduct($id)
{
	$unit=\DB::table('products')
	->where('Prod_ID', $id)->get();
	$res=array($unit);
	return $res;
}
public function getTotalMatEst($id)
{
	$totalMat=\DB::table('work_details') ->where('Work_ID', $id)->select('Mat_Estimate_Total') ->get();
	$resp=array($totalMat);
	return $resp;
}
public function getTotalLabEst($id)
{
	$totalLab=\DB::table('work_details') ->where('Work_ID', $id)->select('Lab_Estimate_Total') ->get();
	$resp=array($totalLab);
	return $resp;
}
public function getTotalMatLabEst($id)
{
	$totalMatLab=\DB::table('work_details') ->where('Work_ID', $id)->select('LabMat_Est_Total') ->get();
	$resp=array($totalMatLab);
	return $resp;
}
public function addLabEstimation(Request $r)
{
	$value= Request::json()->all();
	$labEstimate=\DB::table('work_labour_estimation')->insert(array('Work_ID' => $value['work_ID'],'LineItem_ID' => $value['item_ID'],'Unit' => $value['unit_name'],'Quantity' => $value['qty'],'Rate' =>  $value['rate'], 'Value' => $value['value'],'Comments' => $value['cmnts'], 'WorkDays' => $value['days'], 'LabourNo' => $value['labours']));
	if(!empty($labEstimate))
	{
		$total=\DB::table('work_labour_estimation')->where('Work_ID', $value['work_ID'])->sum('Value');
	$totalExists=\DB::table('work_details')->where('Work_ID',$value['work_ID'])->get();
	$count=count($totalExists);
	if($count>0)
	{
		$update=\DB::table('work_details')->where('Work_ID', $value['work_ID'])-> update(array('Lab_Estimate_Total' => $total));
		$resp=array($update);
		return $resp;
	}
	else
	{
	
	$insert=\DB::table('work_details')->insert(array('Work_ID' => $value['work_ID'], 'Lab_Estimate_Total' => $total));
	$resp=array($insert);
	return $resp;
	}
	}
	$resp=array($labEstimate);
	return $resp;
}

public function getAllLabEst($id)
{
	$labEstimate=\DB::table('work_labour_estimation')->join('serv_line_items', 'serv_line_items.LineItem_ID','=','work_labour_estimation.LineItem_ID')
	->join('units', 'units.Unit_ID','=','serv_line_items.UnitID')
	//->leftjoin('custom_line_items','custom_line_items.LineItem_ID','=','work_labour_estimation.customLineItem_ID')
	->where('work_labour_estimation.Work_ID', $id)->where('deleteFlag',0)
	//->orderBy('work_labour_estimation.Quantity','DESC')
->orderBy('work_labour_estimation.Priority', 'ASC')
	->get();
	//$total=\DB::table('work_labour_estimation')->where('Work_ID', $id)->sum('Value');
	$resp=array($labEstimate);
	return $resp;
}
public function getAllWorkLineItems($id)
{
	$labEstimate=\DB::table('work_labour_estimation')->join('serv_line_items', 'serv_line_items.LineItem_ID','=','work_labour_estimation.LineItem_ID')
	->join('units', 'units.Unit_ID','=','serv_line_items.UnitID')
	//->leftjoin('custom_line_items','custom_line_items.LineItem_ID','=','work_labour_estimation.customLineItem_ID')
	->where('work_labour_estimation.Work_ID', $id)->where('deleteFlag',0)
	//->orderBy('work_labour_estimation.Quantity','DESC')
->orderBy('work_labour_estimation.Priority', 'ASC')
	->get();
	//$total=\DB::table('work_labour_estimation')->where('Work_ID', $id)->sum('Value');
	$resp=array($labEstimate);
	return $resp;
}
public function getAllMatLabEst($id)
{
	$matLabEstimate=\DB::table('work_matlabor_estimation')->join('serv_line_items', 'serv_line_items.LineItem_ID','=','work_matlabor_estimation.LineItem_ID')
	//->leftjoin('custom_line_items','custom_line_items.LineItem_ID','=','work_labour_estimation.customLineItem_ID')
	->join('units', 'units.Unit_ID','=','serv_line_items.UnitID')
	->where('work_matlabor_estimation.Work_ID', $id)->where('deleteFlag',1)->get();
	$total=\DB::table('work_matlabor_estimation')->where('Work_ID', $id)->sum('Value');
	$resp=array($matLabEstimate, $total);
	return $resp;
}
public function getLeadDetails($id)
{
	$details=\DB::table('sales_lead')-> join('sales_customer','sales_lead.Cust_ID','=','sales_customer.Customer_ID')
	->join('address','address.Address_ID','=','sales_lead.Site_AddressID')
	->join('lead_status', 'lead_status.Lead_Status_ID','=','sales_lead.Lead_StatusID')
	-> join('location', 'location.Loc_ID','=','sales_lead.Lead_LocID')
	->join('contacts','contacts.Contact_ID','=','sales_customer.Contact_ID')
	-> join('sales_source','sales_source.Source_ID','=','sales_lead.Source_ID')
	->where('Lead_ID', $id)->get();
	$resp=array($details);
	return $resp;
}
public function getLeadHistory($id)
{
	$leadHistoryList=\DB::table('lead_history')->join('lead_activity','lead_activity.Activity_ID','=','lead_history.Activity')
	->distinct()->where('Lead_ID',$id)->orderby('TimeStamp')->get();
	$resp=array($leadHistoryList);
	return $resp;
}
public function saveLineItem(Request $r)
{

	$data= Request::json()->all();
	$id=$data['param1'];
	$items=$data['param2'];
	$dataset=[];
	foreach($items as $item)
	{
		$dataset[]=['Work_ID' => $id, 'LineItem_ID'=> $item['name']];
	}
	
	
$items=\DB::table('work_labour_estimation')->insert($dataset);

$resp=array("Success"=>true, $items);
	return $resp;
}
public function getWorkItems($id)
{
	$items=\DB::table('work_labour_estimation')->join('serv_line_items', 'serv_line_items.LineItem_ID', '=','work_labour_estimation.LineItem_ID')
	->join('units', 'units.Unit_ID', '=','serv_line_items.UnitID')
	//->join('custom_line_items', 'custom_line_items.Work_ID', '=','work_labour_estimation.Work_ID')
	
	->where('work_labour_estimation.Work_ID', $id)->get();

	$resp=array($items);
	return $resp;
}
public function getWorkItemsCount($id)
{
	$labEstimate=\DB::table('work_labour_estimation')->join('serv_line_items', 'serv_line_items.LineItem_ID','=','work_labour_estimation.LineItem_ID')
	->join('units', 'units.Unit_ID','=','serv_line_items.UnitID')
	//->leftjoin('custom_line_items','custom_line_items.LineItem_ID','=','work_labour_estimation.customLineItem_ID')
	->where('work_labour_estimation.Work_ID', $id)->where('work_labour_estimation.deleteFlag',0)->get();
	$countItems=count($labEstimate);
	$resp=array($countItems);
	return $resp;
}
public function getAssignee($id)
{
	$assignee=\DB::table('sales_lead')->select('AssginedTo','ExpClosureDate','ExpClosureAmt')->where('Lead_ID', $id)->get();
	$resp=array($assignee);
	return $resp;
}

public function getAllWorkList($name)
{
	if($name=='admin')
	{
		$allWork=\DB::table('service_work')
		//->join('segment','segment.Segment_ID','=','service_work.Segment_ID')
		//->join('services','services.Service_ID','=','service_work.Service_ID')
		//->join('work_color_status', 'work_color_status.Color_StatusID','=','service_work.Generate_Work_Status')
		->join ('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')->join('contacts','contacts.Contact_ID','=','sales_customer.Contact_ID')
		->join('location','location.Loc_ID','=','sales_lead.Lead_LocID')
		->join('work_status', 'work_status.Work_Status_ID','=','service_work.WorkStatus')
		->join('work_timeline','work_timeline.Work_ID','=','service_work.Work_ID' )
		//->join('sales_status','sales_status.sales_statusID','=','service_work.WorkStatus')
		->join('work_updation_status','work_updation_status.Update_Status_ID','=','service_work.Update_Status')
		->where('work_timeline.Work_Attrb_ID', 14)
		//->where('work_status.Work_Status_ID', 10)
		
		->orderBy('service_work.Work_ID', 'desc')
		->get();
		$resp=array($allWork);
		return $resp;
	}
	else
	{

	$allWork=\DB::table('service_work')
		//->join('segment','segment.Segment_ID','=','service_work.Segment_ID')
		//->join('services','services.Service_ID','=','service_work.Service_ID')
		//->join('work_color_status', 'work_color_status.Color_StatusID','=','service_work.Generate_Work_Status')
		->join ('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')->join('contacts','contacts.Contact_ID','=','sales_customer.Contact_ID')
		->join('location','location.Loc_ID','=','sales_lead.Lead_LocID')
		->join('work_status', 'work_status.Work_Status_ID','=','service_work.WorkStatus')
		//->join('sales_status','sales_status.sales_statusID','=','service_work.WorkStatus')
		->join('work_updation_status','work_updation_status.Update_Status_ID','=','service_work.Update_Status')
		->join('work_timeline','work_timeline.Work_ID','=','service_work.Work_ID' )
		//->where('work_status.Work_Status_ID', 2)
		//->where('work_status.Work_Status_ID', 10)
		->where('work_timeline.Work_Attrb_ID', 14)
		->where('service_work.Assigned_To',$name)
		->orderBy('service_work.Work_ID', 'desc')
		->get();
		$resp=array($allWork);
		return $resp;
	}
}
public function saveCustLabItems(Request $r)
	{
		//old function
		/*$data= Request::json()->all();
		$custom=\DB::table('serv_line_items')->insertGetID(array('Service_ID'=> $data['servID'], 'LineItem_Name'=> $data['custItemName'],
		'LineItem_Desc'=> $data['desc'],'UnitID'=> $data['unit'], 'customFlag'=>1));

	if(!empty($custom))
		{
			$item=\DB::table('work_labour_estimation')->insert(array('Work_ID'=>$data['workID'],  'LineItem_ID'=>$custom, 'customFlag'=>'1'));
		}
		$resp=array($custom);
		return $resp;*/
		//-------------------------------
		//new function

		$data= Request::json()->all();
		$customID=\DB::table('serv_line_items')->insertGetID(array('LineItem_Name'=> $data['custItemName'],
		'LineItem_Desc'=> $data['desc'],'UnitID'=> $data['unit'], 'customFlag'=>1));
		$EstFlag=\DB::table('work_labour_estimation')->where('Work_ID',$data['workID'])->select(\DB::raw('MAX(Amend_Flag) AS Max'))->get();
$insertFlag=$EstFlag[0]->Max +1;

	if(!empty($customID))
		{
$map=\DB::table('service_servlineitem_rel')->insert(array('Service_ID'=>$data['services'], 'LineItem_ID'=>$customID));
if($data['typeID']==0)
{
	$item=\DB::table('work_labour_estimation')->insert(array('Work_ID'=>$data['workID'],  'LineItem_ID'=>$customID, 'customFlag'=>'1'));
}
else if($data['typeID']==1)
{
	if($data['newFlag']==0)
	{
		$existsWID=\DB::table('work_amendment')->where('Work_ID',$data['workID'])->get();
		$count=count($existsWID);
		if($count==0)
		{
			$amendID=\DB::table('work_amendment')->insert(array('Work_ID'=>$data['workID']));
		//	$EstFlag=\DB::table('work_amendment')->where('Work_ID',$data['workID'])->pluck('Estimation_Amend_Flag');
		//$insertFlag=$EstFlag[0]+1;

		$item=\DB::table('work_labour_estimation')->insert(array('Work_ID'=>$data['workID'],  'LineItem_ID'=>$customID, 'customFlag'=>'1','Amend_Flag'=>0));
		}
		else
		{

		$item=\DB::table('work_labour_estimation')->insert(array('Work_ID'=>$data['workID'],  'LineItem_ID'=>$customID, 'customFlag'=>'1','Amend_Flag'=>$insertFlag));
		}
	}
	else if($data['newFlag']!=0)
	{$existsWID=\DB::table('work_amendment')->where('Work_ID',$data['workID'])->get();
		$count=count($existsWID);
		if($count==0)
		{
			$amendID=\DB::table('work_amendment')->insert(array('Work_ID'=>$data['workID']));
		//	$EstFlag=\DB::table('work_amendment')->where('Work_ID',$data['workID'])->pluck('Estimation_Amend_Flag');
		//$insertFlag=$EstFlag[0]+1;

		$item=\DB::table('work_labour_estimation')->insert(array('Work_ID'=>$data['workID'],  'LineItem_ID'=>$customID, 'customFlag'=>'1','Amend_Flag'=>$data['newFlag']));
		}
		else
		{

		$item=\DB::table('work_labour_estimation')->insert(array('Work_ID'=>$data['workID'],  'LineItem_ID'=>$customID, 'customFlag'=>'1','Amend_Flag'=>$data['newFlag']));
		}

	}
	
	

}

			
		}
		$resp=array("Success"=>true);
		return $resp;



	}

	public function saveCustLabMatItems(Request $r)
	{
		$data= Request::json()->all();
		$custom=\DB::table('serv_line_items')->insertGetID(array('Service_ID'=> $data['servID'], 'LineItem_Name'=> $data['custItemName'],
		'LineItem_Desc'=> $data['desc'],'UnitID'=> $data['unit'], 'customFlag'=>1));

	if(!empty($custom))
		{
			$item=\DB::table('work_matlabor_estimation')->insert(array('Work_ID'=>$data['workID'],  'LineItem_ID'=>$custom, 'customFlag'=>'1'));
		}
		$resp=array($custom);
		return $resp;

	}

public function addProductList(Request $r)
{
	$data= Request::json()->all();
	$id=$data['param1'];
	$items=$data['param2'];
	$dataset=[];
	foreach($items as $item)
	{
		$dataset[]=['Work_ID' => $id, 'Product_ID'=> $item['name']];
	}
	
	
$items=\DB::table('work_material_estimation')->insert($dataset);

$resp=array("Success"=>true, $items);
	return $resp;

}

public function getItemName($id)
{
	$items=\DB::table('work_material_estimation')->join('products', 'products.Prod_ID', '=','work_material_estimation.Product_ID')
	//->select('products.Prod_Name', 'products.UnitofMeasure')
	->where('ME_ID', $id)
	->get();
	$resp=array($items);
	return $resp;
}

public function saveProdDetails(Request $r)
{
	$data= Request::json()->all();
	
	$matEstimate=\DB::table('work_material_estimation')->where('ME_ID',$data['ME_ID'])->update(array('Quantity'=>$data['qty'], 'Rate'=>$data['rate'], 'Value'=>$data['qty'] * $data['rate'], 'Comments'=>$data['cmnts'],'updateFlag'=>2));
	if(!empty($matEstimate))
	{
		$total=\DB::table('work_material_estimation')->where('Work_ID', $data['work_ID'])->where('deleteFlag',1)->sum('Value');
	$totalExists=\DB::table('work_details')->where('Work_ID',$data['work_ID'])->get();
	$count=count($totalExists);
	if($count>0)
	{
		$update=\DB::table('work_details')->where('Work_ID', $data['work_ID'])-> update(array('Mat_Estimate_Total' => $total));
		$resp=array($update);
		return $resp;
	}
	else
	{
	
	$insert=\DB::table('work_details')->insert(array('Work_ID' => $data['work_ID'], 'Lab_Estimate_Total' => $total));
	$resp=array($insert);
	return $resp;
	}
	
	$resp=array("Success"=>true, $matEstimate, $total);
	return $resp;

	}
}

public function removeMatItem($id)
{
	$workid=\DB::table('work_material_estimation')->where('ME_ID', $id)->pluck('Work_ID');
	$delete=\DB::table('work_material_estimation')->where('ME_ID', $id)->update(array('deleteFlag'=>2));
	if(!empty($delete))
	{
		$total=\DB::table('work_material_estimation')->where('Work_ID', $workid["0"])->where('deleteFlag',1)->sum('Value');
	
		$update=\DB::table('work_details')->where('Work_ID', $workid["0"])-> update(array('Mat_Estimate_Total' => $total));
		$resp=array($update);
		return $resp;
	
}
	$resp=array("Success"=>true, $delete);
	return $resp;
}

public function removeLabMatItem($id)
{
	$workid=\DB::table('work_matlabor_estimation')->where('MLE_ID', $id)->pluck('Work_ID');
	$delete=\DB::table('work_matlabor_estimation')->where('MLE_ID', $id)->update(array('deleteFlag'=>2));
	if(!empty($delete))
	{
		$total=\DB::table('work_matlabor_estimation')->where('Work_ID', $workid["0"])->where('deleteFlag',1)->sum('Value');
	
		$update=\DB::table('work_details')->where('Work_ID', $workid["0"])-> update(array('LabMat_Est_Total' => $total));
		$resp=array($update, $total);
		return $resp;
	
}
	$resp=array("Success"=>true, $delete);
	return $resp;
}

public function getLineItemName($id)
{
	$items=\DB::table('work_labour_estimation')->join('serv_line_items', 'serv_line_items.LineItem_ID', '=','work_labour_estimation.LineItem_ID')
	->join('units', 'units.Unit_ID', '=','serv_line_items.UnitID')
	->where('LE_ID', $id)
	->get();
	$resp=array($items);
	return $resp;
}

public function saveLabDetails(Request $r)
{
	$data= Request::json()->all();
	
	$matEstimate=\DB::table('work_labour_estimation')->where('LE_ID',$data['LE_ID'])->update(array('Quantity'=>$data['qty'], 'Rate'=>$data['rate'], 'Value'=>$data['qty'] * $data['rate'],'WorkDays'=>$data['days'], 'LabourNo'=>$data['number'], 'Comments'=>$data['cmnts'],'updateFlag'=>1));
	if(!empty($matEstimate))
	{
		$total=\DB::table('work_labour_estimation')->where('Work_ID', $data['work_ID'])->where('deleteFlag',0)->sum('Value');
	$totalExists=\DB::table('work_details')->where('Work_ID',$data['work_ID'])->get();
	$count=count($totalExists);
	if($count>0)
	{
		$update=\DB::table('work_details')->where('Work_ID', $data['work_ID'])-> update(array('Lab_Estimate_Total' => $total));
		$resp=array($update);
		return $resp;
	}
	else
	{
	
	$insert=\DB::table('work_details')->insert(array('Work_ID' => $data['work_ID'], 'Lab_Estimate_Total' => $total));
	$resp=array($insert);
	return $resp;
	}
	
	$resp=array("Success"=>true, $matEstimate, $total);
	return $resp;

	}
}
public function saveMatLabDetails(Request $r)
{
	$data= Request::json()->all();
	
	$matEstimate=\DB::table('work_matlabor_estimation')->where('MLE_ID',$data['MLE_ID'])->update(array('Quantity'=>$data['qty'], 'Rate'=>$data['rate'], 'Value'=>$data['qty'] * $data['rate'],'WorkDays'=>$data['days'], 'LaborNo'=>$data['number'], 'Comments'=>$data['cmnts'],'updateFlag'=>1));
	if(!empty($matEstimate))
	{
		$total=\DB::table('work_matlabor_estimation')->where('Work_ID', $data['work_ID'])->where('deleteFlag',1)->sum('Value');
	$totalExists=\DB::table('work_details')->where('Work_ID',$data['work_ID'])->get();
	$count=count($totalExists);
	if($count>0)
	{
		$update=\DB::table('work_details')->where('Work_ID', $data['work_ID'])-> update(array('LabMat_Est_Total' => $total));
		$resp=array("Success"=>true, $update);
		return $resp;
	}
	else
	{
	
	$insert=\DB::table('work_details')->insert(array('Work_ID' => $data['work_ID'], 'LabMat_Est_Total' => $total));
	$resp=array($insert);
	return $resp;
	}
	
	$resp=array("Success"=>true, $matEstimate, $total);
	return $resp;

	}
}
public function removeLabItem($id)
{
	$workid=\DB::table('work_labour_estimation')->where('LE_ID', $id)->pluck('Work_ID');
	$delete=\DB::table('work_labour_estimation')->where('LE_ID', $id)->update(array('deleteFlag'=>1));
	if(!empty($delete))
	{
		$total=\DB::table('work_labour_estimation')->where('Work_ID', $workid["0"])->where('deleteFlag',0)->sum('Value');
	
		$update=\DB::table('work_details')->where('Work_ID', $workid["0"])-> update(array('Lab_Estimate_Total' => $total));
		$resp=array($update);
		return $resp;
	
}
	$resp=array("Success"=>true, $delete);
	return $resp;
}

public function getDeptAssignee($id)
{
	$assignees=\DB::table('logins')->select('User_Login')->where('Dept_ID', $id)->get();
	$resp=array($assignees);
	return $resp;
}
public function getDepts()
{
	$depts=\DB::table('department')->get();
	$resp=array($depts);
	return $resp;
}
public function addMatLabLineItem(Request $r)
	{
$data= Request::json()->all();
foreach($data['param2'] as $value)
{

$item=\DB::table('work_matlabor_estimation')->insert(array('Work_ID'=>$data['param1'], 'LineItem_ID'=>$value['name']));
}

$resp=array($item);
return $resp;
	}
	public function addLabLineItem(Request $r)
	{
		/* 
$data= Request::json()->all();
foreach($data['param2'] as $value)
{
	
if($data['param3']==0)
{
	$item=\DB::table('work_labour_estimation')->insert(array('Work_ID'=>$data['param1'], 
'LineItem_ID'=>$value['name']));
}
else if($data['param3']==1)
{

	$existsWID=\DB::table('work_amendment')->where('Work_ID',$data['param1'])->get();
	$count=count($existsWID);
	if($count==0)
	{
		$amendID=\DB::table('work_amendment')->insert(array('Work_ID'=>$data['param1']));
		$EstFlag=\DB::table('work_amendment')->where('Work_ID',$data['param1'])->pluck('Estimation_Amend_Flag');
	$insertFlag=$EstFlag[0]+1;
	$item=\DB::table('work_labour_estimation')->insert(array('Work_ID'=>$data['param1'], 
'LineItem_ID'=>$value['name'], 'Amend_Flag'=>$insertFlag));
	}
	else
	{
		$EstFlag=\DB::table('work_amendment')->where('Work_ID',$data['param1'])->pluck('Estimation_Amend_Flag');
	$insertFlag=$EstFlag[0]+1;
	$item=\DB::table('work_labour_estimation')->insert(array('Work_ID'=>$data['param1'], 
'LineItem_ID'=>$value['name'], 'Amend_Flag'=>$insertFlag));
	}
	
}

}

$resp=array($item);
return $resp;
	*/
$data= Request::json()->all();
$EstFlag=\DB::table('work_labour_estimation')->where('Work_ID',$data['param1'])->select(\DB::raw('MAX(Amend_Flag) AS Max'))->get();
$insertFlag=$EstFlag[0]->Max +1;
foreach($data['param2'] as $value)
{
	
if($data['param3']==0)
{
	$chkItems=\DB::table('work_labour_estimation')->where('Work_ID',$data['param1'])->get();
	$count=count($chkItems);
	/*if($count==0)
	{
		$leadID=\DB::table('service_work')->where('Work_ID',$data['param1'])->pluck('Lead_ID');
		$updateLead=\DB::table('sales_lead')->where('Lead_ID',$leadID[0])->update(array('Cust_Status_ID'=>4));
	}*/
	$item=\DB::table('work_labour_estimation')->insert(array('Work_ID'=>$data['param1'], 
'LineItem_ID'=>$value['name']));
}
else if($data['param3']==1)
{
	if($data['param4']==0)
	{
		$existsWID=\DB::table('work_amendment')->where('Work_ID',$data['param1'])->get();
	$count=count($existsWID);
	if($count==0)
	{
		$amendID=\DB::table('work_amendment')->insert(array('Work_ID'=>$data['param1']));
		//$EstFlag=\DB::table('work_amendment')->where('Work_ID',$data['param1'])->pluck('Estimation_Amend_Flag');
	//$insertFlag=$EstFlag[0]+1;

	$item=\DB::table('work_labour_estimation')->insert(array('Work_ID'=>$data['param1'], 
'LineItem_ID'=>$value['name'], 'Amend_Flag'=>$insertFlag));
	}
	else
	{
	$item=\DB::table('work_labour_estimation')->insert(array('Work_ID'=>$data['param1'], 
	'LineItem_ID'=>$value['name'], 'Amend_Flag'=>$insertFlag));
	}
	

	}
	else if($data['param4']!=0)
	{
	
	//$EstFlag=\DB::table('work_labour_estimation')->where('Work_ID',$data['param1'])->select(\DB::raw('MAX(Amend_Flag) AS Max'))->get();
	//$insertFlag=$EstFlag[0]->Max +1;
	$item=\DB::table('work_labour_estimation')->insert(array('Work_ID'=>$data['param1'], 
	'LineItem_ID'=>$value['name'], 'Amend_Flag'=>$data['param4']));
	}

}
}

$resp=array("Success"=>true);
return $resp;

}

	

	public function getLabMatLineItemName($id)
	{
		$items=\DB::table('work_matlabor_estimation')->join('serv_line_items', 'serv_line_items.LineItem_ID', '=','work_matlabor_estimation.LineItem_ID')
	->join('units', 'units.Unit_ID', '=','serv_line_items.UnitID')
	->where('MLE_ID', $id)
	->get();
	$resp=array($items);
	return $resp;
	}
public function finishEstimate($id)
{
 $detailsExists=\DB::table('work_details')->where('work_details.Work_ID', $id)
 //->join('service_work', 'service_work.Work_ID','=','work_details.Work_ID')
 ->get();

 $resp=array($detailsExists);
 return $resp;

  
}
public function changeStatusEst($id)
{
	$now=new DateTime();
	$today=$now->format('Y-m-d');

	$estimateDate=\DB::table('work_timeline')->insert(array('Work_ID'=>$id, 'Work_Attrb_ID'=>16, 'Value'=>$today));
	$assignee=\DB::table('service_work')->where('Work_ID', $id)->pluck('Assigned_To');
	$status=\DB::table('service_work')->where('Work_ID', $id)
	->update(array('AssignedDept'=> 'BI', 'Assigned_To'=>'BID', 'WorkStatus'=>'3', 'Update_Status'=>2,'Est_Flag'=>1));
	/*$leadID=\DB::table('service_work')->where('Work_ID',$id)->pluck('Lead_ID');
		$updateLead=\DB::table('sales_lead')->where('Lead_ID',$leadID[0])->update(array('Cust_Status_ID'=>5));*/
	if(!empty($assignee))
	{
		/*$exists=\DB::table('work_access_table')->where('Work_ID', $id)->where('PMQA',$assignee['0'])->get();
		$count=count($exists);
		if($count==0)
		{
		$access=\DB::table('work_access_table')->insert(array('Work_ID'=> $id, 'PMQA'=>$assignee['0']));
	}
	else{*/
		$access=\DB::table('work_access_table')->where('Work_ID',$id)->update(array('PMQA'=>$assignee['0']));
	
	}
	

	$resp=array("Success" => true);
	return $resp;

}

	public function getAllSalesWorkList()
	{
		$allWork=\DB::table('service_work')
		//->join('segment','segment.Segment_ID','=','service_work.Segment_ID')
		//->join('services','services.Service_ID','=','service_work.Service_ID')
		//->join('work_color_status', 'work_color_status.Color_StatusID','=','service_work.Generate_Work_Status')
		->join ('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')
		->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
		->join('contacts','contacts.Contact_ID','=','sales_customer.Contact_ID')
		//->join('location','location.Loc_ID','=','sales_lead.Lead_LocID')
		->join('work_status', 'work_status.Work_Status_ID','=','service_work.WorkStatus')
		->join('sales_status','sales_status.sales_statusID','=','service_work.WorkStatus')
		->join('work_updation_status','work_updation_status.Update_Status_ID','=','service_work.Generate_Work_Status')
		->leftjoin('work_tendering', function ($join) {
			$join->on('work_tendering.Work_ID','=','service_work.Work_ID')
			->where('work_tendering.SelectStatus',1);
		})
		->leftjoin('associate','associate.Assoc_ID','=','work_tendering.Assoc_ID')
		//->where('work_status.Work_Status_ID', 2)
		//->where('work_status.Work_Status_ID', 10)
		->where('service_work.RemoveFlag',1)
		->where('sales_lead.Flag','!=',2)
		->where('service_work.Work_ID','<',10000)
		//->where('work_tendering.SelectStatus',1)
		//->where('service_work.Assigned_To',$name)
		->orderBy('service_work.Work_ID', 'desc')
		->select('service_work.Work_ID','service_work.WorkDetail','service_work.WorkStatus',
		'Cust_FirstName','Cust_MidName','Cust_LastName','Work_StatusName','SelectStatus',
		
		\DB::raw('(CASE WHEN SelectStatus=1  THEN Assoc_FirstName ELSE "NULL" END) AS Assoc_FirstName'),
		\DB::raw('(CASE WHEN SelectStatus=1 THEN Assoc_MiddleName ELSE "NULL" END) AS Assoc_MiddleName'),
		\DB::raw('(CASE WHEN SelectStatus=1 THEN Assoc_LastName ELSE "NULL" END) AS Assoc_LastName'),
		\DB::raw('(CASE WHEN SelectStatus=1 THEN work_tendering.Assoc_ID ELSE "NULL" END) AS Assoc_ID'))
		->get();
		$resp=array($allWork);
		return $resp;
		/*$newArray=[];
		$allWork=\DB::table('service_work')
		//->join('segment','segment.Segment_ID','=','service_work.Segment_ID')
		//->join('services','services.Service_ID','=','service_work.Service_ID')
		//->join('work_color_status', 'work_color_status.Color_StatusID','=','service_work.Generate_Work_Status')
		->join ('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')
		->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
		->join('contacts','contacts.Contact_ID','=','sales_customer.Contact_ID')
		//->join('location','location.Loc_ID','=','sales_lead.Lead_LocID')
		->join('work_status', 'work_status.Work_Status_ID','=','service_work.WorkStatus')
		->join('sales_status','sales_status.sales_statusID','=','service_work.WorkStatus')
		->join('work_updation_status','work_updation_status.Update_Status_ID','=','service_work.Generate_Work_Status')
		->leftjoin('work_tendering','work_tendering.Work_ID','=','service_work.Work_ID')
		->leftjoin('associate','associate.Assoc_ID','=','work_tendering.Assoc_ID')
		//->where('work_status.Work_Status_ID', 2)
		//->where('work_status.Work_Status_ID', 10)
		->where('service_work.RemoveFlag',1)
		->where('sales_lead.Flag','!=',2)
		->where('service_work.Work_ID','<',10000)
		->where('work_tendering.SelectStatus',1)
		//->where('service_work.Assigned_To',$name)
		->orderBy('service_work.Work_ID', 'desc')
		->select('service_work.Work_ID','service_work.WorkDetail','service_work.WorkStatus',
		'Cust_FirstName','Cust_MidName','Cust_LastName','Work_StatusName',
		'Assoc_FirstName','Assoc_MiddleName','Assoc_LastName', 'work_tendering.Assoc_ID', 'SelectStatus')
		->get();

//retrive work-tender selectstatus==1 only
foreach($allWork as $item)
                {
					if($item->WorkStatus ==1 || $item->WorkStatus ==2 ||$item->WorkStatus ==10 || $item->WorkStatus ==12)
					{
						array_push($newArray,$item);
					}
					else if($item->WorkStatus==3)
						{
$existsWork_ID=array_search($item->Work_ID,$newArray,TRUE);

if($existsWork_ID==false)
{

	array_push($newArray,$item);
}
						}
						else{
							if($item->SelectStatus ==1)
						{
							array_push($newArray,$item);
						}

						}
						

					
						
						
						
					
				}
                  

		$resp=array($newArray);
		return $resp;

		
		/*$newArray=[];
		$allWork=\DB::table('service_work')
		//->join('segment','segment.Segment_ID','=','service_work.Segment_ID')
		//->join('services','services.Service_ID','=','service_work.Service_ID')
		//->join('work_color_status', 'work_color_status.Color_StatusID','=','service_work.Generate_Work_Status')
		->join ('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')
		->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
		->join('contacts','contacts.Contact_ID','=','sales_customer.Contact_ID')
		//->join('location','location.Loc_ID','=','sales_lead.Lead_LocID')
		->leftjoin('work_status', 'work_status.Work_Status_ID','=','service_work.WorkStatus')
		->leftjoin('sales_status','sales_status.sales_statusID','=','service_work.WorkStatus')
		->leftjoin('work_updation_status','work_updation_status.Update_Status_ID','=','service_work.Generate_Work_Status')
		->leftjoin('work_tendering','work_tendering.Work_ID','=','service_work.Work_ID')
		->leftjoin('associate','associate.Assoc_ID','=','work_tendering.Assoc_ID')
		//->where('work_status.Work_Status_ID', 2)
		//->where('work_status.Work_Status_ID', 10)
		->where('service_work.RemoveFlag',1)
		->where('sales_lead.Flag','!=',2)
		->where('service_work.Work_ID','<',10000)
		//->where('work_tendering.SelectStatus',1)
		//->where('service_work.Assigned_To',$name)
		->orderBy('service_work.Work_ID', 'desc')
		->select('service_work.Work_ID','service_work.WorkDetail','service_work.WorkStatus',
		'Cust_FirstName','Cust_MidName','Cust_LastName','Work_StatusName',
		'Assoc_FirstName','Assoc_MiddleName','Assoc_LastName', 'work_tendering.Assoc_ID', 'SelectStatus')
		->get();

//retrive work-tender selectstatus==1 only
foreach($allWork as $item)
                {
					if($item->WorkStatus ==1 || $item->WorkStatus ==2 ||$item->WorkStatus ==10 || $item->WorkStatus ==12)
					{
						array_push($newArray,$item);
					}
					else if($item->WorkStatus==3)
						{
$existsWork_ID=array_search($item->Work_ID,$newArray,TRUE);

if($existsWork_ID==false)
{

	array_push($newArray,$item);
}
						}
						else{
							if($item->SelectStatus ==1)
						{
							array_push($newArray,$item);
						}

						}
						

					
						
						
						
					
				}
                  

		$resp=array($newArray);
		return $resp;*/
		
	}
	public function updateAssocVisit(Request $r)
	{
		$data= Request::json()->all();
		$assocVisit=\DB::table('work_timeline')->insert(array('Work_ID'=> $data['workid'], 'Work_Attrb_ID'=>9, 'Value'=>$data['assocVisit']));
		/*if(!empty($assocVisit))
		{
			$update=\DB::table('service_work')->where('Work_ID', $data['workid'])->update(array('AssocVisitFlag'=>1));
		}*/
		$resp=array($assocVisit);
		return $resp;

	}

	public function updateWorkStatus(Request $r)
	{
		\DB::transaction(function() use ($r) {
		$data= Request::json()->all();
		$now=new DateTime();
	$today=$now->format('Y-m-d');

		
		if($data['status']==12 )
		{
			$ReEstDate=\DB::table('work_timeline')->insert(array('Work_ID'=>$data['workid'], 'Work_Attrb_ID'=>20, 'Value'=>$today));
			$assignToPMQA=\DB::table('work_access_table')->where('Work_ID', $data['workid'])->pluck('PMQA');
			$updateStatus=\DB::table('service_work')->where('Work_ID', $data['workid'])->update(array('Assigned_To'=>$assignToPMQA[0],'AssignedDept'=>'PMQA', 'WorkStatus'=>$data['status'], 'Update_Status'=>10, 'Comments'=>$data['assocComments']));
		}
		else if($data['status']==11)
		{
			$LostDateAfterCustAppr=\DB::table('work_timeline')->insert(array('Work_ID'=>$data['workid'], 'Work_Attrb_ID'=>19, 'Value'=>$today));
$lostREason=\DB::table('work_timeline')->insert(array('Work_ID'=>$data['workid'], 'Work_Attrb_ID'=>15, 'Value'=>$data['assocComments']));
			$assignToMI=\DB::table('work_access_table')->where('Work_ID', $data['workid'])->pluck('MI');
			$updateStatus=\DB::table('service_work')->where('Work_ID', $data['workid'])->update(array('Assigned_To'=>$assignToMI[0],'AssignedDept'=>'MI', 'WorkStatus'=>$data['status'], 'Update_Status'=>11, 'Comments'=>$data['assocComments']));
		}
		else if($data['status']==6)
		{
			$customerApprDate=\DB::table('work_timeline')->insert(array('Work_ID'=>$data['workid'], 'Work_Attrb_ID'=>18, 'Value'=>$today));
			$assignToPMQA=\DB::table('work_access_table')->where('Work_ID', $data['workid'])->pluck('PMQA');
			$updateStatus=\DB::table('service_work')->where('Work_ID', $data['workid'])->update(array('Assigned_To'=>$assignToPMQA[0],'AssignedDept'=>'PMQA', 'WorkStatus'=>$data['status'], 'Update_Status'=>7, 'Comments'=>$data['assocComments']));
		}
		
		
		
		$resp=array("Success"=>true, $assignToMI, $assignToPMQA);
		return $resp;
	});
}


	public function getAssocVisitDate($id)
	{
		$assocDate=\DB::table('work_timeline')->where('Work_ID', $id)->where('Work_Attrb_ID', 9)->get();
		$resp=array($assocDate);
		return $resp;
	}

	public function getFullWorkList()
	{
		$fullList=\DB::table('service_work')
		->join('segment','segment.Segment_ID','=','service_work.Segment_ID')
		->join('services','services.Service_ID','=','service_work.Service_ID')
		//->join('work_color_status', 'work_color_status.Color_StatusID','=','service_work.Generate_Work_Status')
		->join ('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')->join('contacts','contacts.Contact_ID','=','sales_customer.Contact_ID')
		//->join('location','location.Loc_ID','=','sales_lead.Lead_LocID')
		->join('work_status', 'work_status.Work_Status_ID','=','service_work.WorkStatus')
		//->join('sales_status','sales_status.sales_statusID','=','service_work.WorkStatus')
//->join('work_updation_status','work_updation_status.Update_Status_ID','=','service_work.Generate_Work_Status')
		->leftjoin('work_access_table', 'work_access_table.Work_ID','=','service_work.Work_ID')
		->orderby('service_work.Work_ID', 'DESC')
		->get();
		$resp=array($fullList);
		return $resp;
	}

	public function getAccessData($id)
	{
		$accessData=\DB::table('work_access_table')->where('Work_ID', $id)->limit(1)->get();
		$resp=array($accessData);
		return $resp;
	}

	public function findLeadID($id)
	{
		$leadID=\DB::table('service_work')->where('Work_ID', $id)->select('Lead_ID')->get();
		$resp=array($leadID);
		return $resp;
	}
public function getLeadFollowups()
{
	$now=new DateTime();
	$today=$now->format('Y-m-d');
	$followups=\DB::table('sales_lead')->where('NxtFollowupDate', $today)
	->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
	->join('location','location.Loc_ID','=','sales_lead.Lead_LocID')->get();
	$resp=array($followups);
	return $resp;
}
public function getLeadPendings()
{
	$now=new DateTime();
	$today=$now->format('Y-m-d');
	$insert=\DB::table('sales_lead')->update(array('todays_date'=>$now->format('Y-m-d')));


	$followups=\DB::table('sales_lead')->where('NxtFollowupDate','<', $today)
	->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
	->join('location','location.Loc_ID','=','sales_lead.Lead_LocID')
	->select('sales_lead.Lead_ID','sales_lead.Cust_ID','sales_lead.NxtFollowupDate','sales_customer.Cust_FirstName','sales_customer.Cust_MidName',
	'sales_customer.Cust_LastName','location.Loc_Name',\DB::raw("DATEDIFF(todays_date,NxtFollowupDate)AS Days"))
	->get();


	$resp=array($followups, $insert);
	return $resp;
}
public function getPendingAnalysis()
{
	$now=new DateTime();
	$today=$now->format('Y-m-d');
	$insert=\DB::table('service_work')->update(array('todays_date'=>$now->format('Y-m-d')));

	$followups=\DB::table('service_work')->where('Site_Analysis_Date','<',$today)
	->join('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')
	->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
	->join('location','location.Loc_ID','=','sales_lead.Lead_LocID')
	->join('services','services.Service_ID','=','service_work.Service_ID')
	->select('sales_lead.Lead_ID','sales_lead.Cust_ID','service_work.FollowupDate','sales_customer.Cust_FirstName','sales_customer.Cust_MidName',
	'sales_customer.Cust_LastName','location.Loc_Name','services.Service_ID','services.Service_Name',
	\DB::raw("DATEDIFF(service_work.todays_date,service_work.Site_Analysis_Date)AS Days"))->get();


	$resp=array($followups);
	return $resp;
}
public function getReqAnalysisRemind()
{
	$now=new DateTime();
	$today=$now->format('Y-m-d');
	
	$followups=\DB::table('service_work')->where('Site_Analysis_Date',$today)
	->join('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')
	->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
	->join('location','location.Loc_ID','=','sales_lead.Lead_LocID')
	->join('services','services.Service_ID','=','service_work.Service_ID')->get();
	$resp=array($followups);
	return $resp;
}
public function getReqQuoteRemind()
{
	$now=new DateTime();
	$today=$now->format('Y-m-d');
	$followups=\DB::table('service_work')->where('QuotationDate', $today)
	->join('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')
	->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
	->join('location','location.Loc_ID','=','sales_lead.Lead_LocID')
	->join('services','services.Service_ID','=','service_work.Service_ID')->get();
	$resp=array($followups);
	return $resp;
}
public function getPendingQuote()
{
	$now=new DateTime();
	$today=$now->format('Y-m-d');
	$insert=\DB::table('service_work')->update(array('todays_date'=>$now->format('Y-m-d')));


	$followups=\DB::table('service_work')->where('QuotationDate','<', $today)
	->join('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')
	->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
	->join('location','location.Loc_ID','=','sales_lead.Lead_LocID')
	->join('services','services.Service_ID','=','service_work.Service_ID')
	->select('sales_lead.Lead_ID','sales_lead.Cust_ID','service_work.FollowupDate','sales_customer.Cust_FirstName','sales_customer.Cust_MidName',
	'sales_customer.Cust_LastName','location.Loc_Name','services.Service_ID','services.Service_Name',
	\DB::raw("DATEDIFF(service_work.todays_date,service_work.QuotationDate)AS Days"))->get();
	$resp=array($followups);
	return $resp;
}

public function getTotalPendingNo()
{
	$now=new DateTime();
	$today=$now->format('Y-m-d');
	$todayFollowup=\DB::table('sales_lead')->where('NxtFollowupDate', $today)
	->get();
	$countToday=count($todayFollowup);
	$pending=\DB::table('sales_lead')->where('NxtFollowupDate','<', $today)
	->get();
	$countPending=count($pending);
	$total=$countPending+$countToday;
	$resp=array($total);
	return $resp;
}
public function getServiceList($id)
{
	$newArray;
$services=\DB::table('service_work')->where('Work_ID', $id)
->pluck('Service_ID');
/*$services=\DB::table('work_service_map')->where('Work_ID', $id)
->pluck('Service_ID');*/
//$comma_separated = explode(",", $services);
foreach($services as $value) {
	$newArray= $value;
	//$names=\DB::table('services')->whereIn('Service_ID',$value)->select('service_name')->get();
 }
 $names=\DB::table('services')->whereIn('Service_ID',explode(",", $newArray))->select('service_name')->get();
$resp=array($names);
return $resp;
}

public function getServiceIDs($id)
{
	$newArray;
$services=\DB::table('service_work')->where('Work_ID', $id)
->pluck('Service_ID');
//$comma_separated = explode(",", $services);
foreach($services as $value) {
	$newArray= $value;
	//$names=\DB::table('services')->whereIn('Service_ID',$value)->select('service_name')->get();
 }
 $ids=\DB::table('services')->whereIn('Service_ID',explode(",", $newArray))->select('service_ID')->get();
$resp=array($ids);
return $resp;
}

public function getTotalEnqNo($name)
{
	if($name=='admin'|| $name=='MID')
	{
	$TotalEnqNo=\DB::table('service_work')->get();
	$count=count($TotalEnqNo);
	$resp=array($count);
	return $resp;
	}
	
	else{
		$TotalEnqNo=\DB::table('service_work')->where('service_work.Assigned_To',$name)
		->get();
		$count=count($TotalEnqNo);
		$resp=array($count);
		return $resp;
	}

}

public function getTotalEnqDetails($name)
{
	if($name=='admin'|| $name=='MID')
	{
	$TotalEnq=\DB::table('service_work')
	->join('sales_lead','sales_lead.Lead_ID','=','service_work.Lead_ID')
				->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
				->join('work_status','service_work.WorkStatus','=','work_status.Work_Status_ID')
				->orderBy('service_work.Work_ID', 'DESC')
				->get();
	
	$resp=array($TotalEnq);
	return $resp;
	}
	
	else{
		$TotalEnq=\DB::table('service_work')
		->join('sales_lead','sales_lead.Lead_ID','=','service_work.Lead_ID')
				->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
				->join('work_status','service_work.WorkStatus','=','work_status.Work_Status_ID')
				->where('service_work.Assigned_To',$name)
				->orderBy('service_work.Work_ID', 'DESC')
		->get();
		
		$resp=array($TotalEnq);
		return $resp;
	}
}

	public function getTotalHotEnq($name)
	{
		if($name=='admin'|| $name=='MID')
		{
		$hotEnqNo=\DB::table('sales_lead')
		->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
		-> join('location','location.Loc_ID','=','sales_customer.Loc_ID')
		->where('sales_lead.Priority','hot')->get();
		$count=count($hotEnqNo);
		$resp=array($count);
		return $resp;
		}
	else{
		$hotEnqNo=\DB::table('sales_lead')
		->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
		-> join('location','location.Loc_ID','=','sales_customer.Loc_ID')
		->where('sales_lead.Priority','hot')
		->where('sales_lead.AssginedTo',$name)->get();
		$count=count($hotEnqNo);
		$resp=array($count);
		return $resp;
	}

	}

	public function getCustomerAppEnqNo($name)
	{
		if($name=='admin'|| $name=='MID')
	{
	$TotalEnqNo=\DB::table('service_work')->where('service_work.WorkStatus',5)->get();
	$count=count($TotalEnqNo);
	$resp=array($count);
	return $resp;
	}
	
	else{
		$TotalEnqNo=\DB::table('service_work')->where('service_work.Assigned_To',$name)
		->where('service_work.WorkStatus',5)
		->get();
		$count=count($TotalEnqNo);
		$resp=array($count);
		return $resp;
	}
	}
	
	public function getCustomerAppEnqDetails($name)
	{
		if($name=='admin'|| $name=='MID')
	{
	$TotalEnqNo=\DB::table('service_work')
	->join('sales_lead','sales_lead.Lead_ID','=','service_work.Lead_ID')
				->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
				->join('work_status','service_work.WorkStatus','=','work_status.Work_Status_ID')
				->orderBy('service_work.Work_ID', 'DESC')
				->where('service_work.WorkStatus',5)->get();
	
	$resp=array($TotalEnqNo);
	return $resp;
	}
	
	else{
		$TotalEnqNo=\DB::table('service_work')
		->join('sales_lead','sales_lead.Lead_ID','=','service_work.Lead_ID')
				->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
				->join('work_status','service_work.WorkStatus','=','work_status.Work_Status_ID')
				->orderBy('service_work.Work_ID', 'DESC')
				->where('service_work.Assigned_To',$name)
		->where('service_work.WorkStatus',5)
		->get();
		
		$resp=array($TotalEnqNo);
		return $resp;
	}
	}

	public function changeLeadStatus(Request $r)
	{
		$data= Request::json()->all();
		$changeStatus=\DB::table('sales_lead')->where('Lead_ID',$data['lead_ID'])
		->update(array('Lead_StatusID'=>$data['changeStatus'], 'Comment'=>$data['comments']));
		$resp=array("Success"=>true);
		return $resp;
	}
	public function getData($id)
	{
		$data=\DB::table('sales_lead')->where('Lead_ID', $id)->get();
		$resp=array($data);
		return $resp;
	}
	//To display migrated dat
	public function getAllMigrateData()
	{
		$allWork=\DB::table('service_work')
		//->join('segment','segment.Segment_ID','=','service_work.Segment_ID')
		//->join('services','services.Service_ID','=','service_work.Service_ID')
		//->join('work_color_status', 'work_color_status.Color_StatusID','=','service_work.Generate_Work_Status')
		->join ('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')
		->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
		->join('contacts','contacts.Contact_ID','=','sales_customer.Contact_ID')
		//->join('location','location.Loc_ID','=','sales_lead.Lead_LocID')
		->join('work_status', 'work_status.Work_Status_ID','=','service_work.WorkStatus')
		->join('sales_status','sales_status.sales_statusID','=','service_work.WorkStatus')
		->join('work_updation_status','work_updation_status.Update_Status_ID','=','service_work.Generate_Work_Status')
		//->where('work_status.Work_Status_ID', 2)
		//->where('work_status.Work_Status_ID', 10)
		->leftjoin('work_tendering','work_tendering.Work_ID','=','service_work.Work_ID')
		->leftjoin('associate','associate.Assoc_ID','=','work_tendering.Assoc_ID')

->select('service_work.Work_ID','service_work.WorkDetail','service_work.WorkStatus','Cust_FirstName','Wo_No','Cust_MidName','Cust_LastName','Work_StatusName','Assoc_FirstName','Assoc_MiddleName','Assoc_LastName', 'work_tendering.Assoc_ID')
		->where('service_work.RemoveFlag',1)
		->where('service_work.Work_ID','>=',10000)
		//->where('service_work.Assigned_To',$name)
		->orderBy('service_work.Work_ID', 'desc')
		->get();
		$resp=array($allWork);
		return $resp;
	}
	public function getUsersList()
{
	$list=\DB::table('users')->join('roles','roles.Role_ID','=','users.Role_ID')
	->select('ID','User_Name','username','ActiveFlag','Role_Name')->orderBy('ActiveFlag','ASC')->get();
	$resp=array($list);
	return $resp;
}
}
