<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Input;
//use Illuminate\Http\Request;
use Request;
use File;
use Illuminate\Support\Facades\Crypt;
use Hash;
use Illuminate\Support\Facades\Storage;
use DateTime;

class certificationController extends Controller
{
	
    public function getAssociate($type)
	{
		if($type==1)
		{
			$assoc_details=\DB::table('associate')
			->join ('status','associate.Assoc_Status','=','status.Assoc_Status')
				->leftjoin ('associate_details', 'associate.Assoc_ID', '=','associate_details.Assoc_ID')
				->leftjoin ('location','associate_details.Loc_ID','=','location.Loc_ID')
				->leftjoin ('contacts','contacts.Contact_ID','=','associate.Contact_ID')
				->leftjoin('address', 'address.Address_ID','=','associate.Address_ID')
				//->join ('services','associate_details.service_ID','=','services.service_ID')
				//->join ('units','associate_details.Unit_ID','=','units.Unit_ID')
				
				->select('associate.Assoc_ID','associate.Assoc_code','associate.Assoc_FirstName','associate.Assoc_MiddleName','associate.Assoc_LastName','associate_details.Loc_ID','associate.Assoc_Status','associate.Assoc_Type','location.Loc_Name','status.Status_ColorCode','status.Status_Code','status.Status_Action','associate_details.No_Projects','associate_details.Total_Amount', 'contacts.Contact_phone', 'address.*')//'associate_segment_rate.Pattern','associate_segment_rate.StdRateLabour','associate_segment_rate.StdRateMatLabour')
				->orderby('associate.Assoc_ID','DESC')
				//->select('associate.Assoc_code','associate.Assoc_FirstName','associate.Assoc_MiddleName','associate.Assoc_LastName','associate.Loc_ID','associate.Assoc_Status','associate.Assoc_Type','location.Loc_Name')'associate_details.bill_pattern','associate_details.Segment_ID','segment.segment_Name','associate_details.service_ID','services.service_Name','associate_details.stdRate','units.Unit_Code',
				->where('associate.ServiceFlag','1')
				->get()->map(function ($item) {
		return get_object_vars($item);});
		//echo $assoc_details;
		$response=array('response'=>'session start','success'=>true,$assoc_details);
			return $response;
		}
		else if($type==2)
		{
			$assoc_details=\DB::table('associate')
		->join ('status','associate.Assoc_Status','=','status.Assoc_Status')
			->leftjoin ('associate_details', 'associate.Assoc_ID', '=','associate_details.Assoc_ID')
			->leftjoin ('location','associate_details.Loc_ID','=','location.Loc_ID')
			->leftjoin ('contacts','contacts.Contact_ID','=','associate.Contact_ID')
			->leftjoin('address', 'address.Address_ID','=','associate.Address_ID')
			//->join ('services','associate_details.service_ID','=','services.service_ID')
			//->join ('units','associate_details.Unit_ID','=','units.Unit_ID')
			
			->select('associate.Assoc_ID','associate.Assoc_code','associate.Assoc_FirstName','associate.Assoc_MiddleName',
			'associate.Assoc_LastName','associate.Assoc_Status','associate.Assoc_Type',
			'status.Status_Code','status.Status_Action','associate_details.No_Projects','associate_details.Total_Amount', 
			'contacts.Contact_phone', 'address.*')//'associate_segment_rate.Pattern','associate_segment_rate.StdRateLabour','associate_segment_rate.StdRateMatLabour')
			->orderby('associate.Assoc_ID','DESC')
			//->select('associate.Assoc_code','associate.Assoc_FirstName','associate.Assoc_MiddleName','associate.Assoc_LastName','associate.Loc_ID','associate.Assoc_Status','associate.Assoc_Type','location.Loc_Name')'associate_details.bill_pattern','associate_details.Segment_ID','segment.segment_Name','associate_details.service_ID','services.service_Name','associate_details.stdRate','units.Unit_Code',
			->where('associate.MaterialFlag','1')
			->get()->map(function ($item) {
    return get_object_vars($item);});
	//echo $assoc_details;
	$response=array('response'=>'session start','success'=>true,$assoc_details);
		return $response;
		}
		
		
	}
	 public function getAssociateRole($name)
	{
		$perms=\DB::table('logins')->join('userrole_privillage','userrole_privillage.Role_ID','=','logins.Role_ID')
	->join('menu_previllage','menu_previllage.Priv_ID','=','userrole_privillage.Priv_ID')
	->where('logins.User_Name',$name)
	//->where('userrole_privillage.IsActive',0)
	->select('menu_previllage.Priv_Name','userrole_privillage.IsActive')->get();
	
		$assocs=\DB::table('associate')
		->join ('status','associate.Assoc_Status','=','status.Assoc_Status')
			->join ('associate_details', 'associate.Assoc_ID', '=','associate_details.Assoc_ID')
			->join ('location','associate_details.Loc_ID','=','location.Loc_ID')			
			->select('associate.Assoc_ID','associate.Assoc_code','associate.Assoc_FirstName','associate.Assoc_MiddleName','associate.Assoc_LastName','associate_details.Loc_ID','associate.Assoc_Status','associate.Assoc_Type','location.Loc_Name','status.Status_ColorCode','status.Status_Code','status.Status_Action','associate_details.No_Projects','associate_details.Total_Amount')
			->orderby('associate.Assoc_ID','DESC')->get();
			
			foreach($assocs as $assoc)
			{
				echo nl2br("\n");
				echo $assoc->Assoc_ID;echo nl2br("\n");
				echo $assoc->Status_ColorCode;echo nl2br("\n");
				foreach( $perms as $perm ) 
				{
					$Status=$perm->Priv_Name;
					//echo $Status;
					//echo 'hai';
					//echo $assoc->Status_ColorCode;
					if($assoc->Status_ColorCode== $Status)
					{
						echo 'matched';echo nl2br("\n");
					if($perm->IsActive =='1')
						{
							echo $perm->IsActive;
							echo nl2br("\n");
					
    $val='No';
	return $val;

					
						}
						else if($perm->IsActive =='0')
						{
							//echo 'hai';
							echo $perm->IsActive;echo nl2br("\n");
							$val='Yes';
	return $val;
	
						}
					}
				//	else echo 'hai';
				$assocs->map(function ($assoc) {
    $assoc->Dis = $val;
    return $assoc;
	});
				}
				
			}
	$resp=array($assocs);
	return $resp;
	
		/*$assoc_details=\DB::table('associate')
		->join ('status','associate.Assoc_Status','=','status.Assoc_Status')
			->join ('associate_details', 'associate.Assoc_ID', '=','associate_details.Assoc_ID')
			->join ('location','associate_details.Loc_ID','=','location.Loc_ID')
			
			//->join ('associate_segment_rate','associate_segment_rate.Assoc_ID','=','associate.Assoc_ID')
			//->join ('services','associate_details.service_ID','=','services.service_ID')
			//->join ('units','associate_details.Unit_ID','=','units.Unit_ID')
			
			->select('associate.Assoc_ID','associate.Assoc_code','associate.Assoc_FirstName','associate.Assoc_MiddleName','associate.Assoc_LastName','associate_details.Loc_ID','associate.Assoc_Status','associate.Assoc_Type','location.Loc_Name','status.Status_ColorCode','status.Status_Code','status.Status_Action','associate_details.No_Projects','associate_details.Total_Amount')//'associate_segment_rate.Pattern','associate_segment_rate.StdRateLabour','associate_segment_rate.StdRateMatLabour')
			->orderby('associate.Assoc_ID','DESC')
			//->select('associate.Assoc_code','associate.Assoc_FirstName','associate.Assoc_MiddleName','associate.Assoc_LastName','associate.Loc_ID','associate.Assoc_Status','associate.Assoc_Type','location.Loc_Name')'associate_details.bill_pattern','associate_details.Segment_ID','segment.segment_Name','associate_details.service_ID','services.service_Name','associate_details.stdRate','units.Unit_Code',
			//->where('associate.Assoc_ID','1534')
			->get()->map(function ($item) {
    return get_object_vars($item);});
	//echo $assoc_details;
	$response=array('response'=>'session start','success'=>true,$assoc_details);
		return $response;
		*/
	}
	public function getOneAssociate($id, $type)
	{
		if($type==1)
		{
			$assoc_details=\DB::table('associate')
			->leftjoin ('status','associate.Assoc_Status','=','status.Assoc_Status')
			->leftjoin ('contacts','associate.Contact_ID','=','contacts.Contact_ID')
				->leftjoin ('associate_details', 'associate.Assoc_ID', '=','associate_details.Assoc_ID')
				->leftjoin ('location','associate_details.Loc_ID','=','location.Loc_ID')
				->leftjoin ('address','associate.Address_ID','=','address.Address_ID')
				
				->where('associate.Assoc_ID',$id)
				
				->get()->map(function ($item) {
		return get_object_vars($item);});
		$segments=\DB::table('associate_segment_rate')
		->leftjoin ('segment','associate_segment_rate.segment_ID','=','segment.segment_ID')
		->join ('services','associate_segment_rate.service_ID','=','services.service_ID')
		//->groupBy('associate_segment_rate.Segment_ID')
		->where('associate_segment_rate.Assoc_ID',$id)->get();
		//echo $assoc_details;
		$response=array('response'=>'One Associate details','success'=>true,$assoc_details, 'Segments'=>$segments);
			return $response;
		}
		else if($type==2)
		{
			$assoc_details=\DB::table('associate')
		->leftjoin ('status','associate.Assoc_Status','=','status.Assoc_Status')
		->leftjoin ('contacts','associate.Contact_ID','=','contacts.Contact_ID')
			->leftjoin ('associate_details', 'associate.Assoc_ID', '=','associate_details.Assoc_ID')
			->leftjoin ('location','associate_details.Loc_ID','=','location.Loc_ID')
			->leftjoin ('address','associate.Address_ID','=','address.Address_ID')
			
			->where('associate.Assoc_ID',$id)
			
			
			->get()->map(function ($item) {
	return get_object_vars($item);});
	$segments=\DB::table('prod_assoc_segment')
	->leftjoin ('prod_segment','prod_assoc_segment.Segment_ID','=','prod_segment.Seg_ID')
	->join ('prod_groups','prod_assoc_segment.Group_ID','=','prod_groups.Group_ID')
	//->groupBy('associate_segment_rate.Segment_ID')
	->where('prod_assoc_segment.Assoc_ID',$id)->get();
	//echo $assoc_details;
	$response=array('response'=>'One Associate details','success'=>true,$assoc_details, 'Segments'=>$segments);
		return $response;
		}
		
		
	}
	public function getProject($id)
	{
		$project_detail=\DB::table('associate_project')
		->join('customer','associate_project.Cust_ID','=','customer.Cust_ID')
		//->join('Location', 'customer.Loc_ID','=','Location.Loc_ID')
	->join('address', 'address.Address_ID','=','customer.Address_ID')
	->leftjoin('associate_rating', 'associate_rating.Cust_ID','=','customer.Cust_ID')
->leftjoin('associate_qarating', 'associate_qarating.Cust_ID','=','customer.Cust_ID')
		->where ('associate_project.Assoc_ID',$id)
		->select('customer.Cust_ID','customer.Cust_Name','customer.Contact_No','associate_rating.Rating', 'associate_project.*', 'address.*','associate_qarating.QARating')
	
		->get()->map(function ($item) {
    return get_object_vars($item);});
	//echo $project_details;
	$project_count=count($project_detail);
	$response=array('response'=>'project details retreived','success'=>true,$project_detail,'count'=>$project_count);
		return $response;
		
	}
	public function getOneProject($id)
	{
		$project_details=\DB::table('associate_project')
		
		->join('customer', 'associate_project.Cust_ID','=','customer.Cust_ID')
	->where ('associate_project.Assoc_ID',$id)
	->where('associate_project.QAStatus',0)
		
		->select('associate_project.Assoc_ID','associate_project.Cust_ID','customer.Cust_Name','customer.Contact_No','associate_project.Work_Detail','associate_project.QAStatus')
		
		->get()->map(function ($item) {
    return get_object_vars($item);});
	//echo $project_details;
	$project_count=count($project_details);
	$response=array('response'=>'project details retreived','success'=>true,$project_details,'count'=>$project_count);
		return $response;
		
	}
	public function getOneProjectFeed($id)
	{
		$project_details=\DB::table('associate_project')
		
		->join('customer', 'associate_project.Cust_ID','=','customer.Cust_ID')
	
		
		->select('associate_project.Assoc_ID','customer.Cust_ID','customer.Cust_Name','customer.Contact_No','associate_project.Work_Detail')
		->where ('associate_project.Assoc_ID',$id)
	->where('associate_project.FeedStatus','0')
		->get()->map(function ($item) {
    return get_object_vars($item);});
	//echo $project_details;
	$project_count=count($project_details);
	$response=array('response'=>'project details retreived','success'=>true,$project_details,'count'=>$project_count);
		return $response;
		
	}
	public function getOneCustomer1($id)
	{
		$values=\DB::table('customer')->join('associate_project', 'associate_project.Cust_ID','=','customer.Cust_ID')
		->where('customer.Cust_ID', $id)->get();
		
		//
		//->join('associate_rating','associate_rating.Cust_ID','=','associate_project.Cust_ID')
	
		
		//->select('associate_project.Assoc_ID','associate_project.Work_Detail','customer.Cust_ID','customer.Cust_Name','customer.Contact_No','associate_project.Work_Detail','associate_project.OrderValue','associate_project.Rate_Unit')
		//->where ('customer.Assoc_ID',$id)
		//->where('customer.Cust_ID',$id)
		
	
		//->get();
	//$customer_count=count($customer);
	$response=array('response'=>'customer details retreived','success'=>true,$values);
		return $response;
		
	}
	public function getOneCustomerqa($id)
	{
		$customer=\DB::table('customer')
		
		->join('associate_project', 'associate_project.Cust_ID','=','customer.Cust_ID')
		->leftjoin('associate_rating','associate_rating.Cust_ID','=','associate_project.Cust_ID')
	//->join('location', 'customer.Loc_ID','=','location.Loc_ID')
	->join('address','address.Address_ID','=','customer.Address_ID')
		
		->select('associate_project.Assoc_ID','associate_project.Work_Detail','customer.Cust_ID','customer.Cust_Name','customer.Contact_No','associate_project.Work_Detail','associate_project.OrderValue','associate_project.Rate_Unit','associate_rating.Rating',  'address.*')
		//->where ('customer.Assoc_ID',$id)
		->where('customer.Cust_ID',$id)
		
	
		->get()->map(function ($item) {
    return get_object_vars($item);});
	$customer_count=count($customer);
	$response=array('response'=>'customer details retreived','success'=>true,$customer,'count'=>$customer_count);
		return $response;
		
	}
	public function getSegments()
	{
		$assoc_segments=\DB::table('segment')->get();
		$segments=array('response'=>'segments retrieved','success'=>true,$assoc_segments);
		return $segments;
	}
	public function getCategories($id)
	{
		 $id = explode(',', str_replace("[", "", str_replace("]", "", $id)));
		 $assoc_category=\DB::table('services')->select(\DB::raw('concat(Segment_ID, "_", Service_ID) as Service_ID'), 'Service_Name')->whereIn('Segment_ID',$id)
		 ->where('DeleteFlag', 1)->get();	
		 $categories=array('response'=>'segments retrieved','success'=>true,$assoc_category);
		 return $categories;
		
	}
	public function getCategory($id)
	{
		
		 $assoc_category=\DB::table('services')->select('Service_ID','Service_Name')->where('Segment_ID',$id)->get();	
		 $categories=array('response'=>'segments retrieved','success'=>true,$assoc_category);
		 return $categories;
		
	}
	public function getBranches()
	{
		$branch=\DB::table('branch')->select('Branch_ID','Branch_Name')->get();
		$branches=array('response'=>'branches retrieved','success'=>true,$branch);
		return $branches;
		
	}
	public function getOneCategory($id)
	{
		$category=\DB::table('services')->select('Service_ID','Service_Name','Service_Code')->where('Segment_ID',$id)->get();
		$cat=array('response'=>'category retrieved','success'=>true,$category);
		return $cat;
		
	}
	public function getUnits()
	{
		$units=\DB::table('units')->select('Unit_ID','Unit_Code')->get();
		$unit=array('response'=>'units retrieved','success'=>true,$units);
		return $unit;
		
	}
	public function getLocations()
	{
		$locations=\DB::table('location')->select('Loc_ID','Loc_Name')->orderby('Loc_Name')->get();
		$location=array('response'=>'locations retrieved','success'=>true,$locations);
		return $location;		
	}
	public function addAssociate(Request $req)
	{
		
		/*$FirstName=$req->input('FirstName');
		$MidName=$req->input('MidName');
		$LastName=$req->input('LastName');
		$Type=$req->input('Type');
		$City=$req->input('City');*/
		/*$Keralite_Workers= $req->input('Keralite_Workers');
		$Non_Keralite_Workers= $req->input('Non_Keralite_Workers');
		$Total_Workers= $req->input('Total_Workers');
		$Qualifi= $req->input('Qualifi');
		$ProfQuali= $req->input('ProfQuali');
		$Years= $req->input('Years');
		$Proj_Nos= $req->input('Proj_Nos');
		$Total_Value= $req->input('Total_Value');
		$Territory= $req->input('Territory');
		$billing= $req->input('billing');
		$willing= $req->input('willing');
		$services= $req->input('services');
		$categories= $req->input('categories');
		$Quality= $req->input('Quality');
		$StdRate= $req->input('StdRate');
		$Plans= $req->input('Plans');
		$Unit= $req->input('Unit');
		$Address1 = $req ->input('Address1');
		$Address2 = $req ->input('Address2');
		$City=$req ->input('City');
		$Contact_Person=$req -> input('Contact_Person');
		$Contact_Number=$req -> input('Contact_Number');
		$Whatsapp_Number=$req -> input('Whatsapp_Number');*/
			
		
		
		
		/*$add=\DB::table('associate')->insertGetID(array(
            'Assoc_FirstName'     =>   $FirstName, 
            'Assoc_MiddleName'   =>   $MidName,            
			'Assoc_LastName'   =>   $LastName,         
		     
			'Assoc_Type'   =>   $Type,          
			'Loc_ID'   =>   $City));  
			$response =array('response'=>'assoc registration completed', $add);
		return $response;*/
		/*if(!empty($add))
   		{//add contain assocID
			$details=\DB::table('associate_details')->insert(array(
			'Assoc_ID' => $add,
			'Keral_WKRS' => $Keralite_Workers,
			'NonKerala_WKRS' =>$Non_Keralite_Workers,
			'Total_WRKS' => $Total_Workers,
			'Qualification' =>$Qualifi,
			'Prof_Qualification' => $ProfQuali,
			'Experiece' =>$Years,
			'No_Projects' =>$Proj_Nos,
			'Total_Amount' =>$Total_Value,
			'Loc_ID' =>$Territory,
			'Bill_Pattern' =>$billing,
			'Willing' =>$willing,
			'Segment_ID' =>$services,
			'Service_ID' =>$categories,
			'Quality' =>$Quality,
			'StdRate' =>$StdRate,
			'Future_Plans' =>$Plans,
			'Unit_ID' =>$Unit,
												
			
			
			));
			$address=\DB::table('address')->insertGetID(array(
			'Address_line1' => $Address1,
			'Address_line2' => $Address2,
			'Address_town' => $City,
			
			
			));
			if(!empty($address))//address contain addressID
			{$contact=\DB::table('contacts')->insertGetID(array(
			'Contact_name' => $Contact_Person,
			'Contact_phone' => $Contact_Number,
			'Contact_whatsapp' => $Whatsapp_Number));
			if(!empty($contact))//contains contactID
			{
				
				$update_assoc=\DB::table('associate')
				->where('Assoc_ID',$add)
				->update(array('Assoc_Code' => 'A00'.$add, 'Address_ID' => $address, 'Contact_ID' =>$contact));
			}
				
			}
			$response =array('response'=>'assoc registration completed', $update_assoc);
		return $response;*/
		\DB::transaction(function() use ($req) {
		$now=new DateTime();
		
		$associate = Request::json()->all();
		if($associate['type_ID']==1)
		{
		$checkExists=\DB::table('associate')->join('contacts','associate.Contact_ID','=','contacts.Contact_ID')->where('associate.Assoc_FirstName' ,$associate['FirstName'])
		->where('contacts.Contact_phone', $associate['Contact_Number'])
		->get();
		$count=count($checkExists);
		if($count>0)
		{
			$resp=array('success'=>false, $checkExists);
			return $resp;
		}
		else
		{
		
		
		$add=\DB::table('associate')->insertGetID(array(
		'Branch_ID' => 'Kolenchery',//$associate['Branch']
            'Assoc_FirstName'     =>  $associate['FirstName'], 
            'Assoc_MiddleName'   =>   $associate['MidName'],  
			          
			'Assoc_LastName'   =>   $associate['LastName'], 
			'ServiceFlag'=>1 ,
			'Assoc_AccountNo'=>$associate['accountNo'],        
		     
			'Assoc_Type'   =>   $associate['Type'],   
			//'Source_ID' => $associate['Source']     
			
			 
			  
	 ));
	 $address=\DB::table('address')->insertGetID(array(
		'Address_line1' => $associate['Address1'],
		'Address_line2' => $associate['Address2'],
		'Address_town' => $associate['City']
		
		
		));
		$contact=\DB::table('contacts')->insertGetID(array(
			'Contact_name' => $associate['Contact_Person'],
			'Contact_phone' => $associate['Contact_Number'],
			'Alt_phone'=>$associate['Alt_Number'],
			'Contact_whatsapp' => $associate['Whatsapp_Number']));
			if(!empty($contact))//contains contactID
			{
				$update_assoc=\DB::table('associate')
				->where('Assoc_ID',$add)
				->update(array('Assoc_Code' => 'A00'.$add, 'Address_ID' => $address, 'Contact_ID' =>$contact,'Assoc_Status'=>'5'));
			}
	 //$response =array('response'=>'associate added', $add);
		//return $response;
	//$response =array('response'=>'associtae tabledata added',$add);
		//return $response;
	if(!empty($add))
   		{//add contain assocID
		$details=\DB::table('associate_details')->insert (array('Assoc_ID' => $add,
		//'Keral_WKRS' => $associate['Keralite_Workers'],'NonKerala_WKRS' =>$associate['Non_Keralite_Workers'],
			//'Total_WRKS' => $associate['Total_Workers'],
			//'Qualification' =>$associate['Qualifi'],
			//'Prof_Qualification' => $associate['ProfQuali'],
			'Experiece' =>$associate['Years'],
			//'No_Projects' =>$associate['Project_Nos'],
			//'Total_Amount' =>$associate['Total_Value'],
			'Loc_ID' =>$associate['Territory'],
			'Reference'=>$associate['Ref'],
			//'Bill_Pattern' =>$associate['billing'],
			//'Willing' =>$associate['willing'],
			//'Segment_ID' =>$associate['services'],
			//'Service_ID' =>$associate['categories'],
			//'Quality' =>$associate['Quality'],
			//'StdRate' =>$associate['StdRate'],
			//'Future_Plans' =>$associate['Plans'],
			//'Unit_ID' =>$associate['Unit']
			//'Radius' => $associate['Radius'],
			'User'=>$associate['user_ID'],
			'Assoc_CreatedDate'=>$now,
			));
			if(!empty($details))
			{
				$cat=$associate['categories'];
				$segment=$associate['services'];
				$segID;
				$i=0;
				//$id = explode(',', str_replace("[", "", str_replace("]", "", $cat)));

				foreach ($cat as $c) {
					//$id=explode('_',str_replace("\"", "", $c));
					
					//print $id;
					//$segID[$i]=(int)$id[0];
					//$findSeg=\DB::table('services')->where('Service_ID',(int)$c)->pluck('Segment_ID');
					$seg=\DB::table('associate_segment_rate')->insert(array(
					'Assoc_ID' => $add,
					//'Segment_ID' => $segID[$i],
					'Service_ID' => (int)$c));
					//$i++;
				}

				//$id = explode(',', str_replace("[", "", str_replace("]", "", $segment)));
			/*	$diff=array_diff($segID, $segment);
				
				foreach ($diff as $c) {
					
					$seg=\DB::table('associate_segment_rate')->insert(array(
					'Assoc_ID' => $add,
					'Segment_ID' => $c,
					'Service_ID' => 0));
				}*/
			

				
					$response =array('Success'=>true);
		return $response;
				
			
		
			}

		}
	}
			
			
		
			}
			else if($associate['type_ID']==2)
{
	$assocIDs=\DB::table('associate')->where('Assoc_ID',$associate['assoc_ID'])->select('Address_ID','Contact_ID')->get();
	$address=\DB::table('address')->where('Address_ID',$assocIDs[0]->Address_ID)->update(array(
		'Address_line1' => $associate['Address1'],
		'Address_line2' => $associate['Address2'],
		'Address_town' => $associate['City']
		
		
		));
		$updateAssoc=\DB::table('associate')->where('Assoc_ID',$associate['assoc_ID'])->update
		(array('Assoc_FirstName'     =>  $associate['FirstName'], 
		'Assoc_MiddleName'   =>   $associate['MidName'],  
				  
		'Assoc_LastName'   =>   $associate['LastName']));
		$updateUser=\DB::table('associate_details')->where('Assoc_ID',$associate['assoc_ID'])
		->update (array('Updated_User'=>$associate['user_ID'],
		'Updated_Date'=>$now));
		$contact=\DB::table('contacts')->where('Contact_ID',$assocIDs[0]->Contact_ID)->update(array(
			'Contact_name' => $associate['Contact_Person'],
			'Contact_phone' => $associate['Contact_Number'],
			'Contact_whatsapp' => $associate['Whatsapp_Number']));
			
			$resp=array('Success'=>true);
			return $resp;

}			
else if($associate['type_ID']==3)
{
	$type=\DB::table('associate')->where('Assoc_ID',$associate['assoc_ID'])
	->update(array('Assoc_Type'   =>   $associate['Type'], 'Assoc_AccountNo'=>$associate['accountNo']));
	$details=\DB::table('associate_details')->where('Assoc_ID',$associate['assoc_ID'])->update (array(
		'Keral_WKRS' => $associate['Keralite_Workers'],
		'NonKerala_WKRS' =>$associate['Non_Keralite_Workers'],
			'Total_WRKS' => $associate['Total_Workers'],
			'Qualification' =>$associate['Qualifi'],
			'Prof_Qualification' => $associate['ProfQuali'],
			'Experiece' =>$associate['Years'],
			'No_Projects' =>$associate['Project_Nos'],
			'Total_Amount' =>$associate['Total_Value'],
			'Loc_ID' =>$associate['Territory'],
			'Reference'=>$associate['Ref'],
			'Updated_User'=>$associate['user_ID'],
			'Updated_Date'=>$now
			//'Bill_Pattern' =>$associate['billing'],
			//'Willing' =>$associate['willing'],
			//'Segment_ID' =>$associate['services'],
			//'Service_ID' =>$associate['categories'],
			//'Quality' =>$associate['Quality'],
			//'StdRate' =>$associate['StdRate'],
			//'Future_Plans' =>$associate['Plans'],
			//'Unit_ID' =>$associate['Unit']
			//'Radius' => $associate['Radius']
			));
			/*if(!empty($details))
			{
				$cat=$associate['categories'];
				$segment=$associate['services'];
				$segID;
				$i=0;
				//$id = explode(',', str_replace("[", "", str_replace("]", "", $cat)));

				foreach ($cat as $c) {
					//$id=explode('_',str_replace("\"", "", $c));
					//print $id;
					//$segID[$i]=(int)$id[0];
					$seg=\DB::table('associate_segment_rate')->insert(array(
					'Assoc_ID' => $associate['assoc_ID'],
					'Segment_ID' => (int)$segment,
					'Service_ID' => (int)$c));
					//$i++;
				}

				//$id = explode(',', str_replace("[", "", str_replace("]", "", $segment)));
			/*	$diff=array_diff($segID, $segment);
				
				foreach ($diff as $c) {
					
					$seg=\DB::table('associate_segment_rate')->insert(array(
					'Assoc_ID' => $add,
					'Segment_ID' => $c,
					'Service_ID' => 0));
				}*/
			

				
					$response =array('Success'=>true);
		return $response;
				

//}
	
}
			
		});		
		
		
		
		
		
	}
	public function updateAssociate(Request $r)
	{
		\DB::transaction(function() use ($r) {
		$addressID=\DB::table('associate')->pluck('Address_ID');
		$contact_ID=\DB::table('contacts')->pluck('Contact_ID');
		$associate = Request::json()->all();
		if(!empty($associate['FirstName']))
		{
		$add=\DB::table('associate')->where('Assoc_ID',$associate['assoc_ID'])->update(array(
		            'Assoc_FirstName'     =>  $associate['FirstName']));
		}
		if(!empty($associate['MidName']))
		{
		$add=\DB::table('associate')->where('Assoc_ID',$associate['assoc_ID'])->update(array(
		           'Assoc_MiddleName'     =>  $associate['MidName']));
		}
		if(!empty($associate['LastName']))
		{
		$add=\DB::table('associate')->where('Assoc_ID',$associate['assoc_ID'])->update(array(
		           'Assoc_LastName'   =>   $associate['LastName']));
		}
		if(!empty($associate['Keralite_Workers']))
		    {
				$details=\DB::table('associate_details')->where('Assoc_ID',$associate['assoc_ID'])->update (array('Keral_WKRS' => $associate['Keralite_Workers']));
			}
			if(!empty($associate['Non_Keralite_Workers']))
		    {
				$details=\DB::table('associate_details')->where('Assoc_ID',$associate['assoc_ID'])->update (array('NonKerala_WKRS' => $associate['Non_Keralite_Workers']));
			}
			if(!empty($associate['Total_Workers']))
		    {
				$details=\DB::table('associate_details')->where('Assoc_ID',$associate['assoc_ID'])->update (array('Total_WRKS' => $associate['Total_Workers']));
			}
			if(!empty($associate['Qualifi']))
		    {
				$details=\DB::table('associate_details')->where('Assoc_ID',$associate['assoc_ID'])->update (array('Qualification' =>$associate['Qualifi']));
			}
			if(!empty($associate['ProfQuali']))
		    {
				$details=\DB::table('associate_details')->where('Assoc_ID',$associate['assoc_ID'])->update (array('Prof_Qualification' => $associate['ProfQuali']));
			}
			if(!empty($associate['Years']))
		    {
				$details=\DB::table('associate_details')->where('Assoc_ID',$associate['assoc_ID'])->update (array('Experiece' => $associate['Years']));
			}
			if(!empty($associate['Project_Nos']))
		    {
				$details=\DB::table('associate_details')->where('Assoc_ID',$associate['assoc_ID'])->update (array('No_Projects' =>$associate['Project_Nos']));
			}
			
			if(!empty($associate['Total_Value']))
		    {
				$details=\DB::table('associate_details')->where('Assoc_ID',$associate['assoc_ID'])->update (array('Total_Amount' =>$associate['Total_Value']));
			}
			if(!empty($associate['Address1']))
		    {
				$address=\DB::table('address')->where('Address_ID',$addressID)->update(array(
			'Address_line1' => $associate['Address1']));
			}if(!empty($associate['Address2']))
		    {
				$address=\DB::table('address')->where('Address_ID',$addressID)->update(array(
			'Address_line2' => $associate['Address2']));
			}
			if(!empty($associate['City']))
		    {
				$address=\DB::table('address')->where('Address_ID',$addressID)->update(array(
			'Address_town' => $associate['City']));
			}
			if(!empty($associate['Contact_Person']))
		    {
				
				$contact=\DB::table('contacts')->where('Contact_ID',$contact_ID)->update(array(
			'Contact_name' => $associate['Contact_Person']));
			}
			if(!empty($associate['Contact_Number']))
		    {
				
				$contact=\DB::table('contacts')->where('Contact_ID',$contact_ID)->update(array(
			'Contact_phone' => $associate['Contact_Number']));
			}			
			if(!empty($associate['Whatsapp_Number']))
		    {
				
				$contact=\DB::table('contacts')->where('Contact_ID',$contact_ID)->update(array(
			'Contact_whatsapp' => $associate['Whatsapp_Number']));
			}
			
		
							
				$response =array('response'=>'associate updated','success'=>true);
		return $response;
				
		
	});
			
			
		}
	
		
		
		
	

		
		
public function addProject(Request $r)
{
	\DB::transaction(function() use ($r) {
	$input = Request::json()->all();
	
	

	if($input['typeID']==1)
	{
		$addrID =\DB::table('address')->insertGetID(array('Address_line1'=>$input['Addr1'], 
	'Address_line2'=>$input['Addr2'],'Address_town'=>$input['Location']));
		$cust_ID = \DB::table('customer')->insertGetID(array(
			'Assoc_ID' => $input['assocID'],
			'Cust_Name' => $input['custName'],
			'Contact_No'   =>  $input['Contact'], 
			//'Loc_ID' => $input['Location'],
			'TypeFlag'=>1,
			'Address_ID'=>$addrID
			));
			if(!empty($cust_ID))
			{
				$work=\DB::table('associate_project')->insert(array(
			'Assoc_ID' => $input['assocID'],
			'Cust_ID' => $cust_ID,
			'Work_Detail' =>$input['WorkDetails']
			));
	}
}
	else if($input['typeID']==2)
	{
		$addrID=\DB::table('customer')->where('Cust_ID',$input['custID'])->pluck('Address_ID');
	$addrUpdate =\DB::table('address')->where('Address_ID',$addrID[0])->update(array('Address_line1'=>$input['Addr1'], 
	'Address_line2'=>$input['Addr2'],'Address_town'=>$input['Location']));

		$cust_ID = \DB::table('customer')->where('Cust_ID',$input['custID'])->update(array(
			
			'Cust_Name' => $input['custName'],
			'Contact_No'   =>  $input['Contact'], 
			//'Loc_ID' => $input['Location'],
			
			));
			if(!empty($cust_ID))
			{
				$work=\DB::table('associate_project')->where('Cust_ID',$input['custID'])
				->where('Assoc_ID',$input['assocID'])->update(array(
			
			'Work_Detail' =>$input['WorkDetails']
			));
			}
		}
	

	
	$response =array('Success'=>true, $addrID[0]);
		return $response;
					
	});	
			
}

public function addFeedback(Request $req)
{
	
	/*$a=$req->input('assoc_ID');
	$b=$req->input('custName');
	$c=$req->input('contact');
	$d=$req->input('orderValue');
	$e=$req->input('Quality');
	$f=$req->input('workDetails');
	$g=$req->input('Behaviour');
	$h=$req->input('Knowledge');
	$i=$req->input('WorkLevel');
	$j=$req->input('Time');
	$k=$req->input('Payment');
	$l=$req->input('Pricing');
	$m=$req->input('Service');
	$n=$req->input('rate');
	$o=$req->input('unit');
	$rateUnit=$n.$o;
	$rating=($g+$h+$e+$i+$j+$k+$l+$m)/8;
	
	$params=\DB::table('associate_rating')->insert(array(
				
				'Cust_ID' => $b,
			'Param1' => $g,
			'Param2' => $h,
			'Param3' => $e,
			'Param4' => $i,
			'Param5' => $j,
			'Param6' => $k,
			'Param7' => $l,
			'Param8' => $m,
			'Rating' => $rating	
			
			));
			//$rating_count=count($params);
			if(!empty($params))
			{
				$other=\DB::table('associate_project')
	->where('associate_project.Assoc_ID',$a)
	->where('associate_project.Cust_ID',$b)
	->update(array(								
			'OrderValue' => $d,			
			'Rate_Unit' => $rateUnit,
			));		
			
		
			if(!empty($other))
			{
				
			
			$tableData=\DB::table('associate_project')	
	->join('customer', 'customer.Cust_ID','=','associate_project.Cust_ID')
	->join('associate_rating', 'associate_project.Cust_ID','=','associate_rating.Cust_ID')
	->where('associate_project.Assoc_ID',$a)
	//->where('associate_project.Cust_ID',$b)
	->get()->map(function ($item) {
    return get_object_vars($item);});
	$data_count=count($tableData);
	
	
			}
			
		
			}
			$response =array('success'=>true, 'Tabledata'=>$tableData,'datacount'=>$data_count);
		return $response;*/
	
	
	
	
$feedback = Request::json()->all();
$id=$feedback['assocID'];

	//$rateUnit=$feedback['rate'].''.$feedback['unit'];
	$rateUnit=$feedback['rate'];
	$rating=floatval(($feedback['Behaviour']+$feedback['Knowledge']+$feedback['Quality']+$feedback['WorkLevel']+$feedback['Time']+$feedback['Payment']+$feedback['Pricing']+$feedback['Service'])/8);
	
	
	$params=\DB::table('associate_rating')->insert(array(
				
				'Cust_ID' => $feedback['custID'],
			'Param1' => $feedback['Behaviour'],
			'Param2' => $feedback['Knowledge'],
			'Param3' => $feedback['Quality'],
			'Param4' => $feedback['WorkLevel'],
			'Param5' => $feedback['Time'],
			'Param6' => $feedback['Payment'],
			'Param7' => $feedback['Pricing'],
			'Param8' => $feedback['Service'],
			'Rating' => $rating	
			
			));
			//$rating_count=count($params);
			if(!empty($params))
			{
				$other=\DB::table('associate_project')
	->where('associate_project.Assoc_ID',$feedback['assocID'])
	->where('associate_project.Cust_ID',$feedback['custID'])
	->update(array(								
			'OrderValue' => $feedback['orderValue'],			
			'Rate_Unit' => $rateUnit,
			'FeedStatus' => '1'
			));		
			
		
			/*if(!empty($other))
			{
				
			
			$tableData=\DB::table('associate_project')	
	->join('customer', 'customer.Cust_ID','=','associate_project.Cust_ID')
	->join('associate_rating', 'associate_project.Cust_ID','=','associate_rating.Cust_ID')
	->where('associate_project.Assoc_ID',$feedback['assoc_ID'])
	->where('associate_project.Cust_ID',$feedback['custName'])
	->get()->map(function ($item) {
    return get_object_vars($item);});
	$data_count=count($tableData);
	
	
			}*/
			$response =array('Success'=>true,'id'=>$id);
		return $response;
		
			}
			

}
public function getFeedback($aid)
{
	$tableData=\DB::table('associate_project')	
	->join('customer', 'customer.Cust_ID','=','associate_project.Cust_ID')
	->join('associate_rating', 'associate_project.Cust_ID','=','associate_rating.Cust_ID')
	->where('associate_project.Assoc_ID',$aid)
	->where('associate_project.FeedStatus','1')
	//->where('associate_project.Cust_ID',$feedback['custName'])
	->get()->map(function ($item) {
    return get_object_vars($item);});
	$data_count=count($tableData);
	
	$response =array('success'=>true, $tableData);
		return $response;
}
public function addQARating(Request $request)
{
	/*$qaRating = Request::json()->all();
	$rating=floatval(($qaRating['QAP1']+$qaRating['QAP2']+$qaRating['QAP3']+$qaRating['QAP4'])/4);
	
	$QAP=\DB::table('associate_qarating')
	//->where('associate_qarating.Cust_ID',$qaRating['CustName'])
	->join('associate_Project','associate_project.Cust_ID','=','associate_qarating.Cust_ID')
	->where('associate_Project.Assoc_ID',$qaRating['assoc_ID'])
	->distinct('associate_qarating.Cust_ID')
	->get()->map(function ($item) {
    return get_object_vars($item);});
	$QAP_Count=count($QAP);
	if($QAP_Count<5)
	{
	
	$params=\DB::table('associate_qarating')->insert(array(
				
				'Cust_ID' => $qaRating['CustName'],
			'QAParam1' => $qaRating['QAP1'],
			'QAParam2' => $qaRating['QAP2'],
			'QAParam3' => $qaRating['QAP3'],
			'QAParam4' => $qaRating['QAP4'],
			'Rating' => $rating	
			
			));
			if(!empty($params))
			{
				$projRate=\DB::table('associate_rating')->where('Assoc_ID',$qaRating['assoc_ID'])->pluck('Rating');
				$totalRate=($projRate[0]+$rating)/2;
				$addrating=\DB::table('associate_details')->where('Assoc_ID',$qaRating['assoc_ID'])
				->update(array('Rating' =>$totalRate));
				$update=\DB::table('associate_project')
				->where('Cust_ID',$qaRating['CustName'])
				->update(array('QAStatus'=>'1') );
			
			}
			$response =array('response'=>'Data inserted','success'=>true, $QAP, $QAP_Count, $rating);
		return $response;
	}
	else if($QAP_Count=5)
	{		
	$response =array('response'=>'Last feedback', 'success'=>false,$QAP_Count);
		return $response;
	}*/
	$qaRating = Request::json()->all();
	$rating=floatval(($qaRating['QAP1']+$qaRating['QAP2']+$qaRating['QAP3']+$qaRating['QAP4'])/4);
	$projRate=\DB::table('associate_rating')->where('Cust_ID',$qaRating['custID'])->pluck('Rating');
		$totalRate=($projRate[0]+$rating)/2;
		$addrating=\DB::table('associate_details')->where('Assoc_ID',$qaRating['assocID'])
		->update(array('TotalRating' =>$totalRate));
		$update=\DB::table('associate_project')
		->where('Cust_ID',$qaRating['custID'])
		->update(array('QAStatus'=>'1') );
	$params=\DB::table('associate_qarating')->insert(array(
				
		'Cust_ID' => $qaRating['custID'],
	'QAParam1' => $qaRating['QAP1'],
	'QAParam2' => $qaRating['QAP2'],
	'QAParam3' => $qaRating['QAP3'],
	'QAParam4' => $qaRating['QAP4'],
	'QARating' => $rating	
	
	));
	/*if(!empty($params))
	{
		
	
	}*/
	$response =array('success'=>true, $params);
return $response;


}
/*public function addAvgRating(Request $re)
{
	$value= Request::json()->all();
	$custID=$value['CustName'];
	$assocID=$value['assoc_ID'];
	$FeedRating=\DB::table('associate_rating')->select('Rating')
	->where('Cust_ID',$custID)->get();
	$QARting=\DB::table('associate_qarating')->select('Rating')
	->where('Cust_ID',$custID)->get();
	$rate=($FeedRating+$QARting)/2;
	$FullRating=\DB::table('associate_details')->update(array('Rating' => $rate));
		$resp=array('response'=>'Feedback rating retrieved','success'=>true,$QARting,$FeedRating,$FullRating);
		return $resp;
}*/
public function changeQAStatus(Request $r)
{
	
	$value= Request::json()->all();
	$id=$value['assocID'];
	
	$action=$value['Action'];
	/*$sum=\DB::table('associate_qarating')->join('associate_project','associate_qarating.Cust_ID','=','associate_project.Cust_ID')->where('associate_project.Assoc_ID', $id)->sum('associate_qarating.Rating');
	$rating=$sum/5;
	$insertRate=\DB::table('associate_details')->where('associate_details.Assoc_ID',$id)->update(array('associate_details.Rating'=>$rating));*/
	
	if($action=="Verified")
	{
	$status=\DB::table('associate')
	->where('Assoc_ID',$id)
	->update(array('Assoc_Status' => '3'));
	$response =array('response'=>'QAStatus changed to certify', 'success'=>true,$status);
		return $response;
	}
	else if($action=="Rejected")
	{
		$status=\DB::table('associate')
	->where('Assoc_ID',$id)
	->update(array('Assoc_Status' => '7'));
	$response =array('response'=>'QAStatus changed to certify', 'success'=>true,$status);
		return $response;
	}
	else if($action=="VerifiedWC")
	{
		$cmnt=$value['comment'];
		$status=\DB::table('associate')
	->where('Assoc_ID',$id)
	->update(array('Assoc_Status' => '6'));
	$comment=\DB::table('associate_details')
	->where('Assoc_ID',$id)
	->update(array('Comment'=> $cmnt));
	
	$response =array('response'=>'QAStatus changed to certify', 'success'=>true,$status);
		return $response;
	}
		
	
	
	
}
public function changeRegStatus($id)
{
	
	$status=\DB::table('associate')
	->where('Assoc_ID',$id)
	->update(array('Assoc_Status' => '1'));

	$response =array('response'=>'Added changed to Registraion', 'success'=>true,$status);
		return $response;
}
public function changeVerifyStatus($id)
{
	
	$status=\DB::table('associate')
	->where('Assoc_ID',$id)
	->update(array('Assoc_Status' => '3'));
	$response =array('response'=>'certified', 'success'=>true,$status);
		return $response;
}
public function changeFeedStatus($id)
{
	$status=\DB::table('associate')
	->where('Assoc_ID',$id)
	->update(array('Assoc_Status' => '2'));
	$response =array('response'=>'Registerd changed to QAVerified', 'success'=>true,$status);
		return $response;
}
public function checkQACount($id)
{
	$QAP=\DB::table('associate_Project')
	//->where('associate_qarating.Cust_ID',$qaRating['CustName'])
	->join('associate_qarating','associate_project.Cust_ID','=','associate_qarating.Cust_ID')
	->where('associate_Project.Assoc_ID',$id)
	//->distinct('associate_qarating.Cust_ID')
	->get();
	$QAP_Count=count($QAP);
	$response=array($QAP_Count);
		return $response;
	/*if($QAP_Count<3)
	{
		$response=array('response'=>'5 data inserted','success'=>false, $QAP_Count);
		return $response;
	}
	else if($QAP_Count=3)
	{
		$response=array('response'=>'5 data inserted','success'=>true, $QAP_Count);
		return $response;
	}
	else if($QAP_Count=4)
	{
		$response=array('response'=>'5 data inserted','success'=>true, $QAP_Count);
		return $response;
	}*/
}
public function file_uploadAadhar(Request $req)
{
	 
	
$value= Request::json()->all();
	$Name = Request::file('fileKey')->getClientOriginalName();
	
	$assocID=Input::get('associd');
	$type=Input::get('type');
	$docNo=Input::get('docNo');
	$fileName=$assocID.'-'.$Name;
	//$fileName=Input::get('fileName');
	
	//$file2=$value->file['fileKey'];
	//$attachment = Request::file(a);
	$chkDocExists=\DB::table('associate_documents')->where('Assoc_ID', $assocID)->get();
	$count=count($chkDocExists);
	
	if($count==0)
	{
		if($type=='1')
		{

			if(Request::file('fileKey'))
			{
				$file=Request::file('fileKey');
				$file->move('resources/assets/uploads/Aadhar',$fileName);
			$aadhar=\DB::table('associate_documents')->insert(array('Assoc_ID'=>$assocID,'AadharFile' =>$fileName, 'AadharNo'=>$docNo));
			//echo '<img src = 'uploads/'.$file->getClientOriginalName()>';
			$response=array('Success'=>true, $count, 'Empty');
				return $response;
		}
		}
	 if($type=='2')
		{
			if(Request::file('fileKey')){
				$file=Request::file('fileKey');
				$file->move('resources/assets/uploads/Agreement',$fileName);
			$aadhar=\DB::table('associate_documents')->insert(array('Assoc_ID'=>$assocID,'AgreementFile' =>$fileName, 'AgreeNo'=>$docNo));
			//echo '<img src = 'uploads/'.$file->getClientOriginalName()>';
			$response=array('Success'=>true, $count, 'Empty');
				return $response;

		}
		
		}
	
	}
	 if($count==1){
		 
		
		$type=Input::get('type');
		
		if($type=='1')
		{
			if(Request::file('fileKey')){
				$file=Request::file('fileKey');
				$file->move('resources/assets/uploads/Aadhar',$fileName);
			$aadhar=\DB::table('associate_documents')->where('Assoc_ID', $assocID)->update(array('AadharNo'=>$docNo,'AadharFile' =>$fileName));
			//echo '<img src = 'uploads/'.$file->getClientOriginalName()>';
			$response=array('Success'=>true,$count,'NotEmpty');
				return $response;
		}
		else if($type=='2')
		{
			
			if(Request::file('fileKey')){
				$file=Request::file('fileKey');
				$file->move('resources/assets/uploads/Agreement',$fileName);
			$aadhar=\DB::table('associate_documents')->where('Assoc_ID', $assocID)->update(array('AgreementFile' =>$fileName, 'AgreeNo'=>$docNo));
			//echo '<img src = 'uploads/'.$file->getClientOriginalName()>';
			$response=array('Success'=>true, $count,$type, 'NotEmpty');
				return $response;
			
		}
		

	}
	
	}
}
	
	
	
}
public function file_uploadAgreement(Request $req)
{
	 
	
//$value= Request::file();
	$filename1 = Request::file('fileKey1')->getClientOriginalName();
	$name=Input::get('name');
	$id=Input::get('id');
	//$file2=$value->file['fileKey'];
	//$attachment = Request::file(a);
	//$response=array('response'=>'file not uploaded','success'=>true, Request::all(),$filename);
		//return $response;
	if(Request::file('fileKey1')){
		$file=Request::file('fileKey1');
		$file->move('resources/assets/uploads/Agreement',$filename1);
		$agree=\DB::table('associate_documents')->where('Assoc_ID',$id)->update(array('AgreementFile' =>$name));
		
	//echo '<img src = 'uploads/'.$file->getClientOriginalName()>';
	$response=array('response'=>'agreement uploaded','success'=>true);
		return $response;
	}
	else
	{
		$response=array('response'=>'file not uploaded','success'=>true);
		return $response;
	}
}
public function certification(Request $r)
{
	$values=Request::json()->all();
	$id=$values['assoc_ID'];
	$aadhar=$values['AadharNo'];
	$agree=$values['AgreeNo'];
	$documents=\DB::table('associate_documents')->where('Assoc_ID',$id)
	->update(array('AadharNo'=>$aadhar, 'AgreeNo'=>$agree));
	
	$response =array('response'=>'certified','success'=>true);
		return $response;
}
public function changeCertifyStatus($id)
{
	
		$updates=\DB::table('associate')
	->where('Assoc_ID',$id)
	->update(array('Assoc_Status' => '4'));
	$response =array('response'=>'cwertified', 'success'=>true,$updates);
		return $response;
	
}
	
	

public function getSegRate($id)
{
	$segments=\DB::table('associate_segment_rate')
	->join ('segment','associate_segment_rate.Segment_ID','=','segment.Segment_ID')
	->join('services','associate_segment_rate.Service_ID','=','services.Service_ID')
	->select('associate_segment_rate.*','segment.Segment_Name','services.Service_Name')
	  ->where('associate_segment_rate.Assoc_ID',$id)
	 // ->where('associate_segment_rate.Status','1')
	  ->get();
	$res=array('response'=>'segment recieved', 'success'=>true,$segments);
		return $res;
}
public function getSegmentDetails($id)
{
	$rates=\DB::table('associate_segment_rate')
	->join ('segment','associate_segment_rate.Segment_ID','=','segment.Segment_ID')
	->join('services','associate_segment_rate.Service_ID','=','services.Service_ID')
	->select('associate_segment_rate.Assoc_ID','associate_segment_rate.Segment_ID','associate_segment_rate.Service_ID','Segment.Segment_Name','Services.Service_Name','associate_segment_rate.Pattern','associate_segment_rate.StdRateLabour',	'associate_segment_rate.StdRateMatLabour')
	  ->where('associate_segment_rate.Assoc_ID',$id)
	  ->where('associate_segment_rate.Status','2')->get();
	$res=array('response'=>'segment recieved', 'success'=>true,$rates);
		return $res;
}
//To get associate details based on Seg, Serv search
public function searchAssociate($value)
{

	$assocDetails=\DB::table('associate_segment_rate')
	->join('associate','associate.Assoc_ID','=','associate_segment_rate.Assoc_ID')
	->join ('status','associate.Assoc_Status','=','status.Assoc_Status')
			->join ('associate_details', 'associate.Assoc_ID', '=','associate_details.Assoc_ID')
			->join ('location','associate_details.Loc_ID','=','location.Loc_ID')
	->where('associate_segment_rate.Service_ID',$value)
	->select('associate_segment_rate.Assoc_ID','associate.Assoc_code','associate.Assoc_FirstName', 'associate.Assoc_MiddleName','associate.Assoc_LastName','associate_details.Loc_ID', 'location.Loc_Name','status.Status_ColorCode','status.Status_Code','status.Status_Action')
	->get();
	$res=array('response'=>'segment recieved', 'success'=>true,$assocDetails);
	return $res;
}
public function saveRate(Request $r1)
{
	
	$data=Request::json()->all();
	//$seg=$data['param1'];
	$data1 = json_decode($data['param1'], true);
	$segID=$data1['Segment_ID'];
	$serID=$data1['Service_ID'];
	$data2 = json_decode($data['param2'], true);
	
	$id=$data1['Assoc_ID'];
	$save=\DB::table('associate_segment_rate')
	->where('Assoc_ID',$id)
	->where('Segment_ID',$segID)
	->where ('Service_ID',$serID)
	->update(array(
	'Pattern'=>$data2['pattern'],
	'StdRateLabour'=>$data2['rateL'],
	'StdRateMatLabour'=>$data2['rateML'],
	'Status' => '2'));
	$res=array('response'=>'rate updated', 'success'=>true,$data2, $segID,$serID);
		return $res;
}
public function getServiceList($id)
{
	$services=\DB::table('services')->where('Segment_ID',$id)
	->get();
	$resp=array($services);
	return $resp;
}
public function getUserName($id)
{
	$name=\DB::table('associate')->select('Assoc_FirstName')->where('Assoc_ID',$id)->get();

	/*if(!empty($name))
	{
		$hash=Hash::make($json["Assoc_FirstName"].'123');
		$login=\DB::table('logins')->insert(array(
		'User_Name' => $json["Assoc_FirstName"],
		'User_Login' => $json["Assoc_FirstName"],
		'User_Password' => $hash,
		'Role_ID' =>'4'));
		
		
	}*/
	$resp=array('success'=>true,$name);
	return $resp;
}
public function createLogin(Request $r)
{
	$details=Request::json()->all();
	$userName=$details['Assoc_FirstName']['0'];
	$hash=Hash::make($details['Assoc_FirstName']['0'].'123');
		$login=\DB::table('logins')->insert(array(
		'User_Name' => $details['Assoc_FirstName']['0'],
		'User_Login' => $details['Assoc_FirstName']['0'],
		'User_Password' => $hash,
		'Role_ID' =>'4'));
		$resp=array('Login Created','success'=>true,$hash,$login);
	return $resp;
}
public function getHash($value)
{
	$hash=Hash::make($value);
	$response=array($hash);
	return $response;
}
public function getSegmentAssoc($id)
{
	//$cp = array();
	$seg=\DB::table('associate_segment_rate')
	->join('segment','associate_segment_rate.Segment_ID','=','segment.Segment_ID')
	->where('Assoc_ID',$id)
	->select('segment.Segment_Name')->distinct('segment.Segment_Name')->get();
	
	//$arr=array($seg);
	
	//$collapsed = $seg['Segment_Name']->collapse();
	//dd($collapsed);
	//dd($seg);
	$response=$seg;
	return $response;
	
}
public function getRating($id)
{
	
}
public function getUserPermission($name)
{
	$perm=\DB::table('logins')->join('userrole_privillage','userrole_privillage.Role_ID','=','logins.Role_ID')
	->join('menu_previllage','menu_previllage.Priv_ID','=','userrole_privillage.Priv_ID')
	->where('logins.User_Name',$name)
	->select('userrole_privillage.Role_ID','userrole_privillage.Priv_ID','menu_previllage.Priv_ID','menu_previllage.Priv_Name','userrole_privillage.IsActive')
	//->LIMIT(6)
	->orderby('userrole_privillage.Priv_ID')
	->get();
	$count=count($perm);
	 $res=array($count,$perm);
	 return $res;

	
}
public function addUpdateLog(Request $r)
{
	$value=Request::json()->all();
	$assocID=$value['param1'];
	
	$userID=$value['param2'];
	$update=\DB::table('certification_log')->insert(array('Assoc_ID'=>$assocID,'Status_Name'=>'Updation','Login_ID' =>$userID));
	$resp=array('Success', $assocID);
	return $resp;
}
public function addRegisterLog(Request $r)
{
	$value=Request::json()->all();
	$assocID=$value['param1'];
	
	$userID=$value['param2'];
	$update=\DB::table('certification_log')->insert(array('Assoc_ID'=>$assocID,'Status_Name'=>'Updation','Login_ID' =>$userID));
	$resp=array('Success', $assocID);
	return $resp;
}
public function addCertifyLog(Request $r)
{
	$value=Request::json()->all();
	$assocID=$value['param1'];
	$userID=$value['param2'];
	$status=$value['param3'];
	$update=\DB::table('certification_log')->insert(array('Assoc_ID'=>$assocID,'Status_Name'=>$status,'Login_ID' =>$userID));
	$resp=array('Success', $status);
	return $resp;
}

public function getAssocRates($id)
{
	$rates=\DB::table('associate_segment_rate')->join('segment','segment.Segment_ID','=','associate_segment_rate.Segment_ID')
	->join('services','services.Segment_ID','=','segment.Segment_ID')
	->where('Assoc_ID', $id)
	->select('segment.Segment_Name','services.Service_Name','associate_segment_rate.Pattern','associate_segment_rate.StdRateLabour','associate_segment_rate.StdRateMatLabour')->get();
	$resp=array($rates);
	return $resp;
}
public function downloadAadhar($id)
{
	$aadhar=\DB::table('associate_documents')->select('AadharFile')->where('Assoc_ID',$id)->get();
	$split=explode('-',$aadhar);
	$filename=$split[1];
	//$url= Storage::url($aadhar);
	$url='http://bims/resources/assets/uploads/Aadhar/'.$filename;
	$resp=array($url);
	return $resp;
}
public function downloadAgreement($id)
{
	$agree=\DB::table('associate_documents')->select('AgreementFile')->where('Assoc_ID',$id)->get();
	$split=explode('-',$agree);
	$filename=$split[1];
	//$url= Storage::url($aadhar);
	$url='http://bims/resources/assets/uploads/Agreement/'.$filename;
	$resp=array($url);
	return $resp;
}

public function getAssocSource()
{
	$source=\DB::table('associate_source')->get();
	$resp=array($source);
	return $resp;
}

public function getCertfAssocs()
{
	$assocs=\DB::table('associate')->where('Assoc_Status',4)->get();
	$Total_Certf=count($assocs);
	$resp=array($Total_Certf);
	return $resp;
}

public function getPendingCert()
{
	$pending=\DB::table('associate')->where('Assoc_Status',3)->get();
	$Total_Pending=count($pending);
	$resp=array($Total_Pending);
	return $resp;
}

public function getTotalReg()
{
	$registered=\DB::table('associate')->where('Assoc_Status',1)->get();
	$Total_Reg=count($registered);
	$resp=array($Total_Reg);
	return $resp;
}
	
public function getAssocSegments($id)
{
	//$assocID=\DB::table('user_assoc_rel')->where('User_ID',$id)->pluck('Assoc_ID');
	$segments=\DB::table('associate_segment_rate')
->join('segment','segment.Segment_ID','=','associate_segment_rate.Segment_ID')
->where('associate_segment_rate.Assoc_ID', $id)
->distinct()//->where('associate_segment_rate.Segment_ID', $sid)
->select('Segment_Name')
->get();
$resp=array($segments);
return $resp;

}

public function getAssocServices($aid, $sid)
{
$services=\DB::table('associate_segment_rate')
->join('services','services.Service_ID','=','associate_segment_rate.Service_ID')
->where('associate_segment_rate.Assoc_ID', $aid)//->where('associate_segment_rate.Segment_ID', $sid)
//->select('Service_Name')
->get();
$resp=array($services);
return $resp;
}

public function addServiceDetails(Request $r)
{
	$value=Request::json()->all();
	$param1=$value['service'];
	$i=0;
	$rate=$value['rate'].' '.$value['unit'];
/*	$comma_separated=implode(",", $value['service']);
	$rate=$value['rate'].' '.$value['unit'];
	$updateDetail=\DB::table('associate_project')
	->where('Assoc_ID', $value['assocID'])
	->where('Cust_ID', $value['custName'])
	->update(array('OrderValue'=>$value['orderValue'], 'Rate_Unit'=>$rate, 'Service_ID'=>$comma_separated, 'Details_Flag'=>1));*/
	foreach($param1 as $p)
	{
	 //$integerIDs = explode('"', $p);
	 $serID[$i]=(int)$p;
	 $updateDetail=\DB::table('associate_project')
	//->where('Assoc_ID', $value['assocID'])
	//->where('Cust_ID', $value['custName'])
	->insert(array('Assoc_ID'=> $value['assocID'],'Cust_ID'=>$value['custName'],'Work_Detail'=>$value['WorkDetails'],'OrderValue'=>$value['orderValue'], 'Rate_Unit'=>$rate, 'Service_ID'=>$serID[$i], 'Details_Flag'=>1));
	 $i++;
	

	}
	$resp=array($updateDetail);
	return $resp;
}

public function getSegProjectDetails($aid, $sid)
{
	$project_detail=\DB::table('associate_project')
		//->join('associate_segment_rate', 'associate_project.Assoc_ID','=', 'associate_segment_rate.Assoc_ID')
		->join('services', 'services.Service_ID', '=','associate_project.Service_ID')
		->join('segment', 'segment.Segment_ID', '=','services.Segment_ID')
		->join('customer', 'associate_project.Cust_ID','=','customer.Cust_ID')
	->join('Location', 'customer.Loc_ID','=','Location.Loc_ID')
		->where ('associate_project.Assoc_ID',$aid)
		->where('associate_project.Details_Flag', 1)
		->where ('segment.Segment_ID',$sid)
		//->select('customer.Cust_ID','customer.Cust_Name','customer.Contact_No','associate_project.Work_Detail','Location.Loc_Name','associate_project.OrderValue')
	
		->get()->map(function ($item) {
    return get_object_vars($item);});
	//echo $project_details;
	$project_count=count($project_detail);
	$response=array($project_detail);
		return $response;
		
}
public function updateRates(Request $r)
{
	$values=Request::json()->all();
	if($values['pattern']=='Labour Only')
	{
	$rates=\DB::table('associate_segment_rate')->where('ID',$values['rateID'] )->update(array('Pattern'=>$values['pattern'],
	'StdRateLabour'=>$values['rateL']//, 'StdRateMatLabour'=>$values['rateML']
));
}
else if($values['pattern']=='Material + Labour')
{
	$rates=\DB::table('associate_segment_rate')->where('ID',$values['rateID'] )->update(array('Pattern'=>$values['pattern'],
	 'StdRateMatLabour'=>$values['rateML']
));
}
else if($values['pattern']=='Both')
{
	$rates=\DB::table('associate_segment_rate')->where('ID',$values['rateID'] )->update(array('Pattern'=>$values['pattern'],
	'StdRateLabour'=>$values['rateL'], 'StdRateMatLabour'=>$values['rateML']
));
}
	if(!empty($rates))
	{
		$resp=array('Success'=>true);
	}
	else{
		$resp=array('Success'=>false);
	}
	return $resp;
}
public function chkProjectExists($id)
{
	$projExists=\DB::table('customer')->where('Assoc_ID', $id)->get();
	$resp=array($projExists);
	return $resp;
}
public function getQAProject($id)
{
	$project_detail=\DB::table('associate_project')
		//->join('customer','associate_project.Cust_ID','=','customer.Cust_ID')
		//->join('Location', 'customer.Loc_ID','=','Location.Loc_ID')
	//->join('address', 'address.Address_ID','=','customer.Address_ID')
	//->leftjoin('associate_rating', 'associate_rating.Cust_ID','=','customer.Cust_ID')
->leftjoin('associate_qarating', 'associate_qarating.Cust_ID','=','associate_project.Cust_ID')
		->where ('associate_project.Assoc_ID',$id)
		//->select('customer.Cust_ID','customer.Cust_Name','customer.Contact_No', 'associate_project.*', 'address.*', 'associate_qarating.QARating')
	
		->get();
		$resp=array($project_detail);
		return $resp;
}
//Function to find if any documnets uploaded for an associate
public function chkAssocDocExists($id)
{
	$exists=\DB::table('associate_documents')->where('Assoc_ID',$id)->get();
	$count=count($exists);
	$resp=array($count);
	return $resp;
}
public function getAssocDocs($id)
{
	$docs=\DB::table('associate_documents')->where('Assoc_ID', $id)->get();
	$resp=array($docs);
	return $resp;
}
public function docDownload($type, $id)
{
	if($type==1)
	{
		$Doc=\DB::table('associate_documents')->where('Assoc_ID',$id)->pluck('AadharFile');
	
	$url='http://bims/resources/assets/uploads/Aadhar/'.$Doc[0];
	
	$resp=array($url);
	return $resp;
	}
	else if($type==2)
	{
		$Doc=\DB::table('associate_documents')->where('Assoc_ID',$id)->pluck('AgreementFile');
	
	$url='http://bims/resources/assets/uploads/Agreement/'.$Doc[0];
	$resp=array($url);
	return $resp;
	}
}
public function getMaterialAssociates()
{
	$assoc_details=\DB::table('associate')
		->leftjoin ('status','associate.Assoc_Status','=','status.Assoc_Status')
			->leftjoin ('associate_details', 'associate.Assoc_ID', '=','associate_details.Assoc_ID')
			->leftjoin ('location','associate_details.Loc_ID','=','location.Loc_ID')
			->join ('contacts','contacts.Contact_ID','=','associate.Contact_ID')
			->join('address', 'address.Address_ID','=','associate.Address_ID')
			//->join ('services','associate_details.service_ID','=','services.service_ID')
			//->join ('units','associate_details.Unit_ID','=','units.Unit_ID')
			
			->select('associate.Assoc_ID','associate.Assoc_code','associate.Assoc_FirstName',
			'associate.Assoc_MiddleName','associate.Assoc_LastName','associate_details.Loc_ID','associate.Assoc_Status','associate.Assoc_Type','location.Loc_Name','status.Status_ColorCode','status.Status_Code','status.Status_Action','associate_details.No_Projects','associate_details.Total_Amount','contacts.Contact_name', 'contacts.Contact_phone', 'address.*')//'associate_segment_rate.Pattern','associate_segment_rate.StdRateLabour','associate_segment_rate.StdRateMatLabour')
			->orderby('associate.Assoc_ID','DESC')
			//->select('associate.Assoc_code','associate.Assoc_FirstName','associate.Assoc_MiddleName','associate.Assoc_LastName','associate.Loc_ID','associate.Assoc_Status','associate.Assoc_Type','location.Loc_Name')'associate_details.bill_pattern','associate_details.Segment_ID','segment.segment_Name','associate_details.service_ID','services.service_Name','associate_details.stdRate','units.Unit_Code',
			->where('associate.MaterialFlag',1)
			//->where('associate.ServiceFlag',0)
			->get()->map(function ($item) {
    return get_object_vars($item);});
	//echo $assoc_details;
	$response=array($assoc_details);
		return $response;
		
}
public function getServiceAssociate()
{
	$assoc_details=\DB::table('associate')
		->leftjoin ('status','associate.Assoc_Status','=','status.Assoc_Status')
			->leftjoin ('associate_details', 'associate.Assoc_ID', '=','associate_details.Assoc_ID')
			->leftjoin ('location','associate_details.Loc_ID','=','location.Loc_ID')
			->leftjoin ('contacts','contacts.Contact_ID','=','associate.Contact_ID')
			->leftjoin('address', 'address.Address_ID','=','associate.Address_ID')
			//->join ('services','associate_details.service_ID','=','services.service_ID')
			//->join ('units','associate_details.Unit_ID','=','units.Unit_ID')
			
			->select('associate.Assoc_ID','associate.Assoc_code','associate.Assoc_FirstName','associate.Assoc_MiddleName','associate.Assoc_LastName','associate_details.Loc_ID','associate.Assoc_Status','associate.Assoc_Type','location.Loc_Name','status.Status_ColorCode','status.Status_Code','status.Status_Action','associate_details.No_Projects','associate_details.Total_Amount', 'contacts.Contact_phone', 'address.*')//'associate_segment_rate.Pattern','associate_segment_rate.StdRateLabour','associate_segment_rate.StdRateMatLabour')
			->orderby('associate.Assoc_ID','DESC')
			//->select('associate.Assoc_code','associate.Assoc_FirstName','associate.Assoc_MiddleName','associate.Assoc_LastName','associate.Loc_ID','associate.Assoc_Status','associate.Assoc_Type','location.Loc_Name')'associate_details.bill_pattern','associate_details.Segment_ID','segment.segment_Name','associate_details.service_ID','services.service_Name','associate_details.stdRate','units.Unit_Code',
			->where('associate.ServiceFlag',1)
			//->where('associate.MaterialFlag',0)
			->get()->map(function ($item) {
    return get_object_vars($item);});
	//echo $assoc_details;
	$response=array($assoc_details);
		return $response;
}
public function getProductSegments()
{
	$segments=\DB::table('prod_segment')->get();
	$resp=array($segments);
	return $resp;
}
public function getProductGroup(Request $r)
{
	$values = Request::json()->all();
	$groups=\DB::table('prod_groups')
	->join('prod_segment','prod_groups.Seg_ID','=','prod_segment.Seg_ID')
	->whereIn('prod_groups.Seg_ID',$values)->get();
	$resp=array($groups);
	return $resp;
}
public function addMaterialAssoc(Request $r)
{
	\DB::transaction(function() use ($r) {
$now=new DateTime();
	$today=$now->format('Y-m-d');
	$associate = Request::json()->all();
		
		$checkExists=\DB::table('associate')->join('contacts','associate.Contact_ID','=','contacts.Contact_ID')->where('associate.Assoc_FirstName' ,$associate['FirstName'])
		->where('contacts.Contact_phone', $associate['Contact_Number'])
		->get();
		$count=count($checkExists);
		if($count>0)
		{
			$resp=array('success'=>false, $checkExists);
			return $resp;
		}
		else
		{
		
		
		$add=\DB::table('associate')->insertGetID(array(
		'Branch_ID' => 'Kolenchery',//$associate['Branch']
            'Assoc_FirstName'     =>  $associate['FirstName'], 
            'Assoc_MiddleName'   =>   $associate['MidName'],  
			          
			'Assoc_LastName'   =>   $associate['LastName'], 
			'MaterialFlag'=>1 ,
			'Assoc_AccountNo'=>$associate['accountNo'],
			'Assoc_GST'=>$associate['gst']        
		     
			//'Assoc_Type'   =>   $associate['Type'],   
			//'Source_ID' => $associate['Source']     
			
			 
			  
	 ));
	 $address=\DB::table('address')->insertGetID(array(
		'Address_line1' => $associate['Address1'],
		'Address_line2' => $associate['Address2'],
		'Address_town' => $associate['City']
		
		
		));
		$contact=\DB::table('contacts')->insertGetID(array(
			'Contact_name' => $associate['Contact_Person'],
			'Contact_phone' => $associate['Contact_Number'],
			'Alt_phone'=>$associate['Alt_Number'],
			'Contact_whatsapp' => $associate['Whatsapp_Number']));
			if(!empty($contact))//contains contactID
			{
				$update_assoc=\DB::table('associate')
				->where('Assoc_ID',$add)
				->update(array('Assoc_Code' => 'A00'.$add, 'Address_ID' => $address, 'Contact_ID' =>$contact,'Assoc_Status'=>'5'));
			}
	 //$response =array('response'=>'associate added', $add);
		//return $response;
	//$response =array('response'=>'associtae tabledata added',$add);
		//return $response;
	if(!empty($add))
   		{//add contain assocID
		$details=\DB::table('associate_details')->insert (array('Assoc_ID' => $add,
		//'Keral_WKRS' => $associate['Keralite_Workers'],'NonKerala_WKRS' =>$associate['Non_Keralite_Workers'],
			//'Total_WRKS' => $associate['Total_Workers'],
			//'Qualification' =>$associate['Qualifi'],
			//'Prof_Qualification' => $associate['ProfQuali'],
			'Experiece' =>$associate['Years'],
			//'No_Projects' =>$associate['Project_Nos'],
			//'Total_Amount' =>$associate['Total_Value'],
			//'Loc_ID' =>$associate['Territory'],
			'Reference'=>$associate['Ref'],
			//'Bill_Pattern' =>$associate['billing'],
			//'Willing' =>$associate['willing'],
			//'Segment_ID' =>$associate['services'],
			//'Service_ID' =>$associate['categories'],
			//'Quality' =>$associate['Quality'],
			//'StdRate' =>$associate['StdRate'],
			//'Future_Plans' =>$associate['Plans'],
			//'Unit_ID' =>$associate['Unit']
			//'Radius' => $associate['Radius']
			'User'=>$associate['user_ID'],
			'Assoc_CreatedDate'=>$now,
			));
			if(!empty($details))
			{
				$cat=$associate['categories'];
				$segment=$associate['services'];
				$segID;
				$i=0;
				//$id = explode(',', str_replace("[", "", str_replace("]", "", $cat)));

				foreach ($cat as $c) {
					//$id=explode('_',str_replace("\"", "", $c));
					//print $id;
					//$segID[$i]=(int)$id[0];
					$findSeg=\DB::table('prod_groups')->where('Group_ID',(int)$c)->pluck('Seg_ID');
					$seg=\DB::table('prod_assoc_segment')->insert(array(
					'Assoc_ID' => $add,
					'Segment_ID' => $findSeg[0],
					'Group_ID' => (int)$c));
					//$i++
				}

				//$id = explode(',', str_replace("[", "", str_replace("]", "", $segment)));
			/*	$diff=array_diff($segID, $segment);
				
				foreach ($diff as $c) {
					
					$seg=\DB::table('associate_segment_rate')->insert(array(
					'Assoc_ID' => $add,
					'Segment_ID' => $c,
					'Service_ID' => 0));
				}*/
			

				
					$response =array('Success'=>true);
		return $response;
				
				
		
			
}
		   }
		}
});
}
public function getMatAssocSegments($id)
{
	$segments=\DB::table('prod_assoc_segment')
    //->join('ser_assoc_services','ser_assoc_services.SerSev_ID','=','associate_segment_rate.Service_ID')
    ->leftjoin('prod_segment','prod_segment.Seg_ID','=','prod_assoc_segment.Segment_ID')
    ->leftjoin('prod_groups','prod_groups.Group_ID','=','prod_assoc_segment.Group_ID')
     
	->where('prod_assoc_segment.Assoc_ID',$id)
	->where('prod_assoc_segment.DeleteFlag',0)
   // ->select('associate_segment_rate.Segment_ID', 'segment.Segment_Name', 'associate_segment_rate.Service_ID','services.Service_Name')
   ->get();
    $resp=array($segments);
    return $resp;
}
public function removeSegmentGroup(Request $r)
{
	$values = Request::json()->all();
	$remove=\DB::table('prod_assoc_segment')->whereIn('ID',$values)->update(array('DeleteFlag'=>1));
	if($remove)
	{
		$resp=array('Success'=>true);
		return $resp;
	}
}	
public function addNewSegment(Request $r)
{
	$values = Request::json()->all();
	$cat=$values['categories'];
				$segment=$values['services'];
				$segID;
				$i=0;
				//$id = explode(',', str_replace("[", "", str_replace("]", "", $cat)));

				foreach ($cat as $c) {
					$chkServExists=\DB::table('prod_assoc_segment')->where('Assoc_ID',$values['assoc_ID'])->where('Group_ID', (int)$c)->get();
		$count=count($chkServExists);
		if($count==0)
		{
					//$id=explode('_',str_replace("\"", "", $c));
					//print $id;
					//$segID[$i]=(int)$id[0];
					$findSeg=\DB::table('prod_groups')->where('Group_ID',(int)$c)->pluck('Seg_ID');
					$seg=\DB::table('prod_assoc_segment')->insert(array(
					'Assoc_ID' => $values['assoc_ID'],
					'Segment_ID' => $findSeg[0],
					'Group_ID' => (int)$c));
					//$i++
				}
			}
				$resp=array('Success'=>true);
				return $resp;
}
public function getProdAssocSegments($id)
{
	$segments=\DB::table('prod_assoc_segment')->leftjoin('prod_segment','prod_segment.Seg_ID','=','prod_assoc_segment.Segment_ID')
	->leftjoin('prod_groups','prod_groups.Group_ID','=','prod_assoc_segment.Group_ID')
	->where('prod_assoc_segment.Assoc_ID',$id)
	->select('Seg_Name','Group_Name')->get();
	$resp=array($segments);
	return $resp;
}
public function editMaterialAssoc(Request $r)
{
	\DB::transaction(function() use ($r) {
	$associate = Request::json()->all();
	$addrID=\DB::table('associate')->where('Assoc_ID',$associate['assoc_ID'])->pluck('Address_ID');
	$contID=\DB::table('associate')->where('Assoc_ID',$associate['assoc_ID'])->pluck('Contact_ID');
	$add=\DB::table('associate')->where('Assoc_ID',$associate['assoc_ID'])
	->update(array(
		'Branch_ID' => 'Kolenchery',//$associate['Branch']
            'Assoc_FirstName'     =>  $associate['FirstName'], 
            'Assoc_MiddleName'   =>   $associate['MidName'],  
			          
			'Assoc_LastName'   =>   $associate['LastName'], 
			'MaterialFlag'=>1 ,
			'Assoc_AccountNo'=>$associate['accountNo'],
			'Assoc_GST'=>$associate['gst']        
		     
			 
			  
	 ));
	 $address=\DB::table('address')
	 ->where('Address_ID',$addrID[0])
	 ->update(array(
		'Address_line1' => $associate['Address1'],
		'Address_line2' => $associate['Address2'],
		'Address_town' => $associate['City']
		
		
		));
		$contact=\DB::table('contacts')
		->where('Contact_ID',$contID[0])
		->update(array(
			'Contact_name' => $associate['Contact_Person'],
			'Contact_phone' => $associate['Contact_Number'],
			'Alt_phone'=>$associate['Alt_Number'],
			'Contact_whatsapp' => $associate['Whatsapp_Number']));
			
			$resp=array('Success'=>true);
			return $resp;
});

}
}


	
	
				





		