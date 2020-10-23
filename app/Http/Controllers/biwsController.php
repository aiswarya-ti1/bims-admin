<?php

namespace App\Http\Controllers;
//use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Session;
use Request;
use App\login;
use App\Roles;
use App\user_roles;
use App\user_log_session;
use Hash;
use Illuminate\Support\Facades\Crypt;
use DateTime;
use Illuminate\Support\Facades\Mail;
class biwsController extends Controller
{
    protected $connection= 'secondsql';
    
    public function biws_CreateToken(Request $r)
    {
        $creds=Request::only(['username','password']);
	$token=auth()->attempt($creds);
	return response()->json(['token'=>$token]);
    }
    public function biws_createTokenCust(Request $r)
    {
        
        $creds=Request::only(['username','password']);
        $username=$creds['username'];
        $password=$creds['password'];
    $token=auth()->attempt($creds);

    
       $users=\DB::table('users')->where('username',$username)->pluck('password');
$customer=\DB::table('sales_customer')->join('contacts','contacts.Contact_ID','=','sales_customer.Contact_ID')
->where('sales_customer.Flag',1)->where('contacts.Contact_phone','=',$username)
->select('sales_customer.Customer_ID', 'sales_customer.Cust_FirstName')->get();

if(Hash::check($password, $users[0]))
		 {
			
            return response()->json(['token'=>$token,'Success'=>true,'Cust_ID'=>$customer]);

   }
   else{
    return response()->json(['token'=>$token,'Success'=>false]);

   }


    
	
    }
    public function biws_SignUp(Request $r)
	{
        $creds=Request::only(['username','password']);
        $username=$creds['username'];
        $password=$creds['password'];
    
        
        $pwd=$this->biws_getHash($password);
	
        
        $signUp=\DB::table('users')->insertGetID(array('username'=>$username, 'password'=>$pwd, 'Role_ID'=>16));
        $signUp1=\DB::table('logins')->insertGetID(array('username'=>$username, 'password'=>$pwd, 'Role_ID'=>16));
        if($signUp1)
        {
            $token=auth()->attempt($creds);
            $customer=\DB::table('sales_customer')
            ->join('contacts','contacts.Contact_ID','=','sales_customer.Contact_ID')
            ->join('users','users.username','=','contacts.Contact_phone')

->where('sales_customer.Flag',1)->where('contacts.Contact_phone','=',$username)
->select('sales_customer.Customer_ID', 'sales_customer.Cust_FirstName','users.ID')->get();
            return response()->json(['token'=>$token,'Success'=>true,'Cust_ID'=>$customer]);
        }

    }
    public function biws_AssocSignUp(Request $r)
	{
        $creds=Request::only(['username','password']);
        $username=$creds['username'];
        $password=$creds['password'];
    
        
        $pwd=$this->biws_getHash($password);
	
        
        $signUp=\DB::table('users')->insertGetID(array('username'=>$username, 'password'=>$pwd, 'Role_ID'=>16));
        $signUp1=\DB::table('logins')->insertGetID(array('username'=>$username, 'password'=>$pwd, 'Role_ID'=>16));
        if($signUp1)
        {
            $token=auth()->attempt($creds);
            $assoc=\DB::table('associate')
            ->join('contacts','contacts.Contact_ID','=','associate.Contact_ID')
            ->join('users','users.username','=','contacts.Contact_phone')
            ->where('associate.Online_Flag',1)->where('contacts.Contact_phone','=',$username)
            ->select('associate.Assoc_ID', 'associate.Assoc_FirstName', 'users.ID')->first();
            return response()->json(['token'=>$token,'Success'=>true,'Assoc'=>$assoc]);
        }


    }
    
    public function biws_generateOTP()
	{
		$result = '';
			for($i = 0; $i < 4; $i++) {
			$result .= mt_rand(0, 9);
			}
			$resp=array($result);
			return $resp;
	}

	public function biws_sendOTP(Request $r)
	{
       // print('Hi');
        $values = Request::json()->all();
        $otp=$values['param1'];
	$mobile=$values['param2'];
	
		$text = urlencode($otp);
		 
        $curl = curl_init();
 
        // Send the POST request with cURL
        curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => "http://message.adrieya.com/api/sms/format/xml",
        CURLOPT_POST => 1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_HTTPHEADER => array('X-Authentication-Key:e8af52235ed797e3894140f439e0ffb7', 'X-Api-Method:MT'),
        CURLOPT_POSTFIELDS => array(
                        'mobile' => 917994901032,
                        'route' => 'TL',
                        'text' => $text,
                        'sender' => 'INFRAM')));
 
    // Send the request & save response to $response
    $response = curl_exec($curl);
 
    // Close request to clear up some resources
    curl_close($curl);
    print_r($response);
 
	// Print response
	$resp=array("Success"=>true);
    return $resp;
    }
    public function biws_getHash($value)
	{
		
		$hash=Hash::make($value);
		
		return $hash;
		
    }
    public function biws_SignIn(Request $r)
    {
        $creds=Request::only(['username','password']);
        $username=$creds['username'];
        $password=$creds['password'];
    $token=auth()->attempt($creds);

    
       /*$users=\DB::table('users')->where('username',$username)->pluck('password');
$customer=\DB::table('sales_customer')->join('contacts','contacts.Contact_ID','=','sales_customer.Contact_ID')
->where('sales_customer.Flag',1)->where('contacts.Contact_phone','=',$username)
->select('sales_customer.Customer_ID', 'sales_customer.Cust_FirstName')->get();

if(Hash::check($password, $users[0]))
		 {
			
            return response()->json(['token'=>$token,'Success'=>true,'Cust_ID'=>$customer]);

   }
   else{
    return response()->json(['token'=>$token,'Success'=>false]);

   }*/
   if($token)
   {
       $customer=\DB::table('sales_customer')
       ->join('contacts','contacts.Contact_ID','=','sales_customer.Contact_ID')
       ->join('users','users.username','=','contacts.Contact_phone')
       
->where('sales_customer.Flag',1)->where('contacts.Contact_phone','=',$username)
->select('sales_customer.Customer_ID', 'sales_customer.Cust_FirstName','users.ID')->get();
      return response()->json(['token'=>$token,'Success'=>true,'Cust_ID'=>$customer]); 
   }
   else
   {
       return response()->json(['token'=>$token,'Success'=>false]); 
   }



    
    
    }
    //for online leads data from landing page get free spec
    public function biws_addLead(Request $r)
    {
        $values = Request::json()->all();
     $type=$values['type'];
        $plan=$values['plan'];
        $area=$values['area'];
        $floors=$values['floor'];
        $work_Start=$values['start'];
        $category=$values['category'];
        $catName=\DB::table('enq_category')->where('Enq_Cat_ID',$category)->pluck('Cat_Name');
        $custName=$values['custName'];
        $contact=(string)$values['contact'];
        $location=$values['loc'];
        $chkContact=\DB::table('sales_customer')->join('contacts','contacts.Contact_ID','=','sales_customer.Contact_ID')
        ->where('sales_customer.Flag',1)->where('Contact_phone',$contact)->get();
        $count=count($chkContact);
        if($count==0)
        {
        $contactID=\DB::table('contacts')->insertGetID(array('Contact_name'=>$custName,'Contact_phone'=>$contact));
        if($contactID)
        {
            $customerID=\DB::table('sales_customer')->insertGetID(array('Cust_FirstName'=>$custName,'Loc_ID'=>$location, 'Flag'=>1, 'Contact_ID'=>$contactID));
            if($customerID)
            {
                if($type==1)
                {
                    $leadID=\DB::table('sales_lead')->insertGetID(array('Cust_ID'=>$customerID,'Lead_StatusID'=>2,'Proj_Details'=>"New Home",'Source_ID'=>8,'Flag'=>2,'Cust_Status_ID'=>1));  
                }
                else{
                    $leadID=\DB::table('sales_lead')->insertGetID(array('Cust_ID'=>$customerID,'Lead_StatusID'=>2,'Proj_Details'=>$catName[0],'Source_ID'=>8,'Flag'=>2,'Cust_Status_ID'=>1));
                }
               
                if($leadID)
                {
                    $mapCat=\DB::table('lead_category')->insert(array('Lead_ID'=>$leadID, 'Cat_ID'=>$category));
                    if($plan)
                    {
                        $map_Plan=\DB::table('lead_attrb_value')->insert(array('Lead_ID'=>$leadID,'Attrb_ID'=>1,'Value'=>$plan));
                    }
                    if($type)
                    {
                        $map_Type=\DB::table('lead_attrb_value')->insert(array('Lead_ID'=>$leadID,'Attrb_ID'=>5,'Value'=>$type));
                    }
                    if($area)
                    {
                       $map_Area=\DB::table('lead_attrb_value')->insert(array('Lead_ID'=>$leadID,'Attrb_ID'=>3,'Value'=>$area));
                    }
                    if($floors)
                    {
                        $map_Floor=\DB::table('lead_attrb_value')->insert(array('Lead_ID'=>$leadID,'Attrb_ID'=>4,'Value'=>$floors));
                    }
                    if($work_Start)
                    {
                        $map_Start=\DB::table('lead_attrb_value')->insert(array('Lead_ID'=>$leadID,'Attrb_ID'=>2,'Value'=>$work_Start));
                    }
                }
            }
        }
        $resp=array('Success'=>true);
        return $resp;
    }
    else{
        $resp=array('Success'=>false);
        return $resp;
    }
    
        
       
    }

    public function biws_getAllLeads()
    {
        $leads=\DB::table('sales_lead')
         ->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
        
        ->join('location', 'location.Loc_ID','=','sales_customer.Loc_ID')
        ->join('customer_status','customer_status.Cust_Status_ID','=','sales_lead.Cust_Status_ID')
      ->join('lead_status','lead_status.Lead_Status_ID','=','sales_lead.Lead_StatusID')
        ->where('sales_lead.Flag',2)->where('sales_lead.DeleteFlag',0)
        ->orderBy('sales_lead.Lead_ID','desc')->get();
        $resp=array($leads);
        return $resp;
    }
    public function biws_AllLeadsByCust($id)
    {
        

    $leads=\DB::table('sales_lead')
    ->leftjoin('service_work','service_work.Lead_ID','=','sales_lead.Lead_ID')
    
    ->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
    ->leftjoin('customer_status','customer_status.Cust_Status_ID','=','service_work.Cust_Status_ID')
    ->join('location', 'location.Loc_ID','=','sales_customer.Loc_ID')
    ->where('sales_lead.Flag',2)->where('sales_lead.DeleteFlag',0)->where('sales_lead.Cust_ID',$id)
  
    ->select('sales_lead.Lead_ID','Cust_ID','Lead_StatusID','service_work.Cust_Status_ID','Cust_ID','service_work.Work_ID',
    'WorkSpec','Work_Type','Cust_FirstName','Cust_MidName','Cust_LastName','DeleteFlag','Site_Analysis_Date','WorkStatus',
    'ActualSite_Analysis_Date','SiteAnalysis_Flag','Loc_Name',
    \DB::raw('(CASE WHEN service_work.Cust_Status_ID>=1 THEN service_work.WorkDetail ELSE Proj_Details END) AS WorkDetail'),
    \DB::raw('(CASE WHEN service_work.Cust_Status_ID>=1 THEN customer_status.Cust_Status_Name ELSE "Enquiry Received" END) AS Cust_Status_Name'))
       // 'Proj_Details AS WorkDetail','1 AS Cust_Status_ID','Enquiry Received AS Cust_Status_Name')
    ->get();
    
    

$resp=array($leads);
        return $resp;

   

        
        /*$leads=\DB::table('sales_lead')
        ->leftjoin('service_work','service_work.Lead_ID','=','sales_lead.Lead_ID')
        
        ->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
        ->leftjoin('customer_status','customer_status.Cust_Status_ID','=','service_work.Cust_Status_ID')
        ->join('location', 'location.Loc_ID','=','sales_customer.Loc_ID')
        ->where('sales_lead.Flag',2)->where('sales_lead.DeleteFlag',0)->where('sales_lead.Cust_ID',$id)
      
        //->
        ->get();

        //$resp=array($leads);
        //return $resp;
        return $leads->Work_ID;*/
    }
    public function biws_getWorkTenderDetails($id)
    {
        $leads=\DB::table('service_work')
        ->join('sales_lead','service_work.Lead_ID','=','sales_lead.Lead_ID')
        ->leftjoin('work_tendering','work_tendering.Work_ID','=','service_work.Work_ID')
        ->where('work_tendering.ReqSiteVisit_Flag',1)
        ->where('sales_lead.Flag',2)->where('sales_lead.Cust_ID',$id)
       
        //->
        ->get();
        $resp=array($leads);
        return $resp;
    }
    public function biws_getCategory()
    {
        $cat=\DB::table('enq_category')->get();
        $resp=array($cat);
        return $resp;
    }
    public function biws_getCustID($user)
 {
    $customer=\DB::table('sales_customer')
    ->join('contacts','contacts.Contact_ID','=','sales_customer.Contact_ID')
        ->where('sales_customer.Flag',1)->where('contacts.Contact_phone',$user)
        ->select('sales_customer.Customer_ID', 'sales_customer.Cust_FirstName')->get();
        $resp=array($customer);
        return $resp;

    }
    public function biws_getAssocID($user)
 {
    $Assoc=\DB::table('associate')
    ->join('contacts','contacts.Contact_ID','=','associate.Contact_ID')
        //->where('associate.Online_Flag',1)
        ->where('contacts.Contact_phone',$user)
        ->select('associate.Assoc_ID', 'associate.Assoc_FirstName')->get();
        $resp=array($Assoc);
        return $resp;

    }
    public function biws_addAssociate(Request $r)
    {
        $inputs = Request::json()->all();
        $username=$inputs['phNo'] ;
        $contact=$inputs['contact'];
        $email=$inputs['email'];
        $name=$inputs['name'];
        $contactID=\DB::table('contacts')->insertGetID(array('Contact_phone'=>$username,'Contact_name'=>$contact));
        $addrID=\DB::table('address')->insertGetID(array('Address_email'=>$email));
        if($contactID)
        {
            $newAssocID=\DB::table('associate')->insertGetID(array('Assoc_FirstName'=>$name,'Contact_ID'=>$contactID,'Address_ID'=>$addrID,'Online_Flag'=>1));
            $resp=array('Success'=>true,$newAssocID);
            return $resp;
        }
    }
    public function biws_assocSignIn(Request $r)
    {
        /*$inputs = Request::json()->all();
        $username=$inputs['username'];
        $password=$inputs['password'];
        $users=\DB::table('users')->where('username',$username)->pluck('password');
        $assoc=\DB::table('associate')->join('contacts','contacts.Contact_ID','=','associate.Contact_ID')
        ->where('associate.Online_Flag',1)->where('contacts.Contact_phone','=',$username)
        ->select('associate.Assoc_ID', 'associate.Assoc_FirstName')->first();
        if($users[0])
        {
        
        if(Hash::check($password, $users[0]))
                 {
                    
        
        $response=array('Success'=>true,'Assoc'=>$assoc);
            return $response;
           }
           else
        {
            $response=array('Success'=>false);
        return $response;
        }
    }*/
    $creds=Request::only(['username','password']);
        $username=$creds['username'];
        $password=$creds['password'];
    $token=auth()->attempt($creds);

    
      /* $users=\DB::table('users')->where('username',$username)->pluck('password');
       $assoc=\DB::table('associate')->join('contacts','contacts.Contact_ID','=','associate.Contact_ID')
       ->where('associate.Online_Flag',1)->where('contacts.Contact_phone','=',$username)
       ->select('associate.Assoc_ID', 'associate.Assoc_FirstName')->first();
       
       
       if(Hash::check($password, $users[0]))
                {
			
            return response()->json(['token'=>$token,'Success'=>true,'Assoc'=>$assoc]);

   }
   else{
    return response()->json(['token'=>$token,'Success'=>false]);

   }*/
   if($token)
   {
      $assoc=\DB::table('associate')->join('contacts','contacts.Contact_ID','=','associate.Contact_ID')
       ->where('associate.Online_Flag',1)->where('contacts.Contact_phone','=',$username)
       ->select('associate.Assoc_ID', 'associate.Assoc_FirstName')->first();
       return response()->json(['token'=>$token,'Success'=>true,'Assoc'=>$assoc]); 
   }
   else
   {
        return response()->json(['token'=>$token,'Success'=>false]);
   }

    }
    public function biws_AllWorks()
    {
        $works=\DB::table('service_work')
        ->join ('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')
         ->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
        
        ->join('location', 'location.Loc_ID','=','sales_customer.Loc_ID')
        ->join('customer_status','customer_status.Cust_Status_ID','=','sales_lead.Cust_Status_ID')
        ->join('work_status', 'work_status.Work_Status_ID','=','service_work.WorkStatus')
        ->where('sales_lead.Flag',2)->where('sales_lead.DeleteFlag',0)
        ->orderBy('service_work.Work_ID','desc')->get();
        $resp=array($works);
        return $resp;

    }
    public function biws_getOneLead($id)
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
    public function biws_addWork(Request $r)
    {
        $now=new DateTime();
        $today=$now->format('Y-m-d');
    
    
            $values = Request::json()->all();
         $siteDate=new DateTime($values['expAnalysisDate']);
		$siteDate->modify('+1 day');
            $service_list= $values['seg'];
            
	
	$comma_separated = implode(",", $values['seg']);
            
           
                $lastWorkID=\DB::table('service_work')->where('Work_ID','<',10000)->orWhere('Work_ID','>',20000)->orderBy('Work_ID','DESC')->first();
            $insertID=$lastWorkID->Work_ID +1;
    
            if($values['lead_ID']==0)
            {
                $work=\DB::table('service_work')->insert(array('Work_ID'=>(int)$insertID,'Lead_ID'=>$values['lead'],'Status_ID' => 2,'WorkStatus'=>2,
                'Segment_ID' => $values['ser'], 'Service_ID' => $comma_separated,
                'Category' => $values['category'],
                'WorkDetail' => $values['workDetails'], 'WorkSpec' => $values['workSpec'], 'Comments'=> $values['workcomments'], 'Assigned_To'=>'PMQA', 'AssignedDept'=>'PMQA', 'RemoveFlag'=>1,'Cust_Status_ID'=>1));
                
                if(!empty($work))
                {
                    $work_history=\DB::table('work_history')-> insert(array('Lead_ID' =>$values['lead'], 'Status_ID' =>2,'WorkStatus'=>2,
                     'WorkDetail' => $values['workDetails'], 'WorkSpec' => $values['workSpec'],'Comments'=> $values['workcomments'], 'Work_ID' => (int)$insertID)); 
                    $access=\DB::table('work_access_table')->insert(array('Work_ID'=> (int)$insertID , 'PMQA'=>'PMQA'));
                    $work_create_date=\DB::table('work_timeline')->insert(array('Work_ID'=>(int)$insertID, 'Work_Attrb_ID'=>13, 'Value'=>$today));
                    $work_limit=\DB::table('work_timeline')->insert(array('Work_ID'=>(int)$insertID, 'Work_Attrb_ID'=>38, 'Value'=>1));
    $updateLead=\DB::table('sales_lead')->where('Lead_ID',$values['lead'])->update(array('Lead_StatusID'=>3));
                    
                }
            }
             if($values['lead_ID']!=0)
            {
                $work=\DB::table('service_work')->insert(array('Work_ID'=>(int)$insertID,'Lead_ID'=>$values['lead_ID'],'Status_ID' => 2,'WorkStatus'=>1,
                'Segment_ID' => $values['ser'], 'Service_ID' => $comma_separated,
                'Category' => $values['category'],
                'WorkDetail' => $values['workDetails'], 'WorkSpec' => $values['workSpec'], 'Comments'=> $values['workcomments'], 'Assigned_To'=>'PMQA', 'AssignedDept'=>'PMQA', 'RemoveFlag'=>1,'Cust_Status_ID'=>1));
                
              
                if(!empty($work))
                {
                    $work_history=\DB::table('work_history')-> insert(array('Lead_ID' =>$values['lead_ID'], 'Status_ID' =>2,'WorkStatus'=>2,
                     'WorkDetail' => $values['workDetails'], 'WorkSpec' => $values['workSpec'],'Comments'=> $values['workcomments'], 'Work_ID' =>(int)$insertID)); 
                    $access=\DB::table('work_access_table')->insert(array('Work_ID'=>(int)$insertID , 'PMQA'=>'PMQA'));
                    $work_create_date=\DB::table('work_timeline')->insert(array('Work_ID'=>(int)$insertID, 'Work_Attrb_ID'=>13, 'Value'=>$today));
                    $work_limit=\DB::table('work_timeline')->insert(array('Work_ID'=>(int)$insertID, 'Work_Attrb_ID'=>38, 'Value'=>1));
                    if($values['expAnalysisDate'])
                    {
                        $updateWork=\DB::table('service_work')->where('Work_ID',(int)$insertID)->update(array('Site_Analysis_Date'=>$siteDate->format('Y-m-d'),'WorkStatus'=>2,'Cust_Status_ID'=>2));
                        //$updateLead=\DB::table('sales_lead')->where('Lead_ID',$values['lead_ID'])->update(array());
                        
                    }
                    foreach ($service_list as $ser) {
                        $serv_map=\DB::table('work_service_map')->insert(array('Work_ID'=> (int)$insertID, 'Service_ID'=>$ser));
                    } 
                }
                $updateLead=\DB::table('sales_lead')->where('Lead_ID',$values['lead_ID'])->update(array('Lead_StatusID'=>3));
       
            }

            /*$email=\Mail::send('email',['data'=>$values],function($message)
             {
                 $message->from('ti1@inframall.net','Laravel');
                 $message->to('aiswaryagireesh14@gmail.com');
             }) ;*/
        
            $resp=array('Success'=>true, 'Lead_ID'=>$values['lead_ID'], 'Work_ID'=>(int)$insertID);//'Work_ID'=>(int)$insertID
            return $resp;
            
    }

    public function biws_chkSiteAnalysis($id)
    {
        $siteExists=\DB::table('service_work')->where('Work_ID', $id)->select('Site_Analysis_Date','ActualSite_Analysis_Date')->get();
        $resp=array($siteExists);
        return $resp;
    }
    public function changeCustStatus(Request $r)
    {
        $values = Request::json()->all();
        if($values['param2']==1)
        {
$updateLead=\DB::table('sales_lead')->where('Lead_ID', $values['param1'])->update(array('Cust_Status_ID'=>2));
$resp=array('Success'=>true);
return $resp;
        }
        if($values['param2']==2)
        {
$updateLead=\DB::table('sales_lead')->where('Lead_ID', $values['param1'])->update(array('Cust_Status_ID'=>3));
$resp=array('Success'=>true);
return $resp;
        }
        
    }
    public function biws_updateSiteAnalysisDate(Request $r)
    {
        $values = Request::json()->all();
        $expSiteDate=new DateTime($values['expAnalysisDate']);
        $actSiteDate=new DateTime($values['actualAnalysisDate']);
        $expAssocVisit=new DateTime($values['expAssocVisitDate']);
        $actAssocVisit=new DateTime($values['actualAssocVisitDate']);
		$expSiteDate->modify('+1 day');
        $actSiteDate->modify('+1 day');
        $expAssocVisit->modify('+1 day');
        $actAssocVisit->modify('+1 day');
        $leadID=\DB::table('service_work')->where('Work_ID',$values['work'])->pluck('Lead_ID');
        $tid=\DB::table('work_tendering')->where('Work_ID',$values['work'])->where('ReqSiteVisit_Flag',1)->pluck('WorkTender_ID');
        if($values['typeID']==1)
        {
            $updateWork=\DB::table('service_work')->where('Work_ID',$values['work'])->update(array('Site_Analysis_Date'=>$expSiteDate->format('Y-m-d'),'WorkStatus'=>2,'Cust_Status_ID'=>2));
            //$updateLead=\DB::table('sales_lead')->where('Lead_ID',$leadID[0])->update(array());
        }
        else if($values['typeID']==2)
        {
            $updateWork=\DB::table('service_work')->where('Work_ID',$values['work'])->update(array('ActualSite_Analysis_Date'=>$actSiteDate->format('Y-m-d'),'WorkStatus'=>10, 'SiteAnalysis_Flag'=>1,'Cust_Status_ID'=>3));
            //$updateLead=\DB::table('sales_lead')->where('Lead_ID',$leadID[0])->update(array());
        }
        else if($values['typeID']==3)
        {
$updateTender=\DB::table('work_tendering')->where('WorkTender_ID',$tid[0])->update(array('Sch_Site_Visit'=>$expAssocVisit->format('Y-m-d'),'Assoc_Status'=>5));
$updateWork=\DB::table('service_work')->where('Work_ID',$values['work'])->update(array('WorkStatus'=>18,'Cust_Status_ID'=>9));
            //$updateLead=\DB::table('sales_lead')->where('Lead_ID',$leadID[0])->update(array());
        }
        else if($values['typeID']==4)
        {
            $updateTender=\DB::table('work_tendering')->where('WorkTender_ID',$tid[0])->update(array('Act_Site_Visit'=>$actAssocVisit->format('Y-m-d'),'Assoc_Status'=>6));
            $updateWork=\DB::table('service_work')->where('Work_ID',$values['work'])->update(array('WorkStatus'=>6,'Cust_Status_ID'=>10));
            //$updateLead=\DB::table('sales_lead')->where('Lead_ID',$leadID[0])->update(array());
        }
        $resp=array('Success'=>true);
        return $resp;
        
    }
    
      public function biws_getOneWork($id)
     {
         $oneWork=\DB::table('service_work')
         ->leftjoin('segment','segment.Segment_ID','=','service_work.Segment_ID')
         ->leftjoin('services','services.Service_ID','=','service_work.Service_ID')
         //->join('work_color_status', 'work_color_status.Color_StatusID','=','service_work.Generate_Work_Status')
         ->join ('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')
         ->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
         ->leftjoin('address', 'address.Address_ID','=','sales_customer.Address_ID')
         ->leftjoin('contacts','contacts.Contact_ID','=','sales_customer.Contact_ID')
         ->leftjoin('location','location.Loc_ID','=','sales_lead.Lead_LocID')
         ->leftjoin('work_status', 'work_status.Work_Status_ID','=','service_work.WorkStatus')
         //->join('work_status','work_status.Work_Status_ID','=','service_work.WorkStatus')
         //->join('department', 'department.Dept_Name','=','service_work.AssignedDept')
         //->join('logins', 'logins.User_Login', '=','service_work.Assigned_To')
         ->where('Work_ID', $id)->get();
         $resp=array($oneWork);
         return $resp;
     }   
     public function biws_getAllLineItems($id)
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
public function biws_addLabLineItem(Request $r)
	{
		
$data= Request::json()->all();
$EstFlag=\DB::table('work_labour_estimation')->where('Work_ID',$data['param1'])->select(\DB::raw('MAX(Amend_Flag) AS Max'))->get();
$insertFlag=$EstFlag[0]->Max +1;
foreach($data['param2'] as $value)
{
	
if($data['param3']==0)
{
	$chkItems=\DB::table('work_labour_estimation')->where('Work_ID',$data['param1'])->get();
	$count=count($chkItems);
	if($count==0)
	{
        $leadID=\DB::table('service_work')->where('Work_ID',$data['param1'])->pluck('Lead_ID');
        $updateWork=\DB::table('service_work')->where('Work_ID',$data['param1'])->update(array('Cust_Status_ID'=>4));
		//$updateLead=\DB::table('sales_lead')->where('Lead_ID',$leadID[0])->update(array('Cust_Status_ID'=>4));
	}
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
public function biws_changeStatusEst($id)
{
	$now=new DateTime();
	$today=$now->format('Y-m-d');

	$estimateDate=\DB::table('work_timeline')->insert(array('Work_ID'=>$id, 'Work_Attrb_ID'=>16, 'Value'=>$today));
	$assignee=\DB::table('service_work')->where('Work_ID', $id)->pluck('Assigned_To');
	$status=\DB::table('service_work')->where('Work_ID', $id)
	->update(array('AssignedDept'=> 'BI', 'Assigned_To'=>'BID', 'WorkStatus'=>'3', 'Update_Status'=>2,'Est_Flag'=>1,'Cust_Status_ID'=>5));
	/*$leadID=\DB::table('service_work')->where('Work_ID',$id)->pluck('Lead_ID');
		$updateLead=\DB::table('sales_lead')->where('Lead_ID',$leadID[0])->update(array());*/
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
public function biws_getAllLabEst($id)
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
public function biws_saveAssocList(Request $r)
	{
        $values = Request::json()->all();
        
		$id=$values['param1'];
    $items=$values['param2'];
    $types=$values['param3'];
	
		$dataset1=[];
        $assocList =[];
        $scope=\DB::table('work_labour_estimation')->where('Work_ID', $id)->where('deleteFlag',0)->select('LE_ID','LineItem_ID', 'Quantity','Comments', 'Priority')->get();
        $keys=\DB::table('wo_key_deliverables')->where('Work_ID',$id)->where('Delete_Flag',0)->select('Key_ID')->get();
        $terms=\DB::table('wo_terms_conditions')->where('Work_ID',$id)->where('Delete_Flag',0)->select('Term_ID')->get();
        $payTerms=\DB::table('work_timeline')->where('Work_ID', $id)->where('Work_Attrb_ID',11)->pluck('Value');
	foreach($items as $value)
	{
        $dataset=[];
       // $typeID=$value['typeID'];
		$exists=\DB::table('work_tendering')->where('Work_ID', $id)
		->where('Assoc_ID', $value['name'])->get();
		$count=count($exists);
		if($count==0)
		{
            if($types==0)
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
                    else{
                        return;
                    }
                    
                }
              
            /*
            
                //to copy keys to tender-key table
                foreach($keys as $key)
                {
                    $existsKey=\DB::table('tender_key_deliverables')->where('Tender_ID',$tenderID)->where('Key_ID',$key->Key_ID)->get();
                    $keyCount=count($existsKey);
                    if($keyCount==0)
                    {
                        $keySet=['Tender_ID'=>$tenderID,'Key_ID'=>$key->Key_ID];
                    }
                    else{
                        return;
                    }
            
                }
                
            
            
                //to copy terms to tender table
                foreach($terms as $term)
                {
                    $termsExists=\DB::table('tender_terms_conditions')->where('Tender_ID', $tenderID)->where('Term_ID',$term->Term_ID)->get();
                    $termCount=count($termsExists);
                    if($termCount==0)
                    {
                        $termSet=['Tender_ID'=>$tenderID,'Term_ID'=>$term->Term_ID];
                    }
                    else{
                        return;
                    }
                }
                */
            
                $items_insert=\DB::table('work_tender_details_lab')->insert($dataset);
               // $keys_insert=\DB::table('tender_key_deliverables')->insert($keySet);
                //$term_insert=\DB::table('tender_terms_conditions')->insert($termSet);
                //to copy payment terms to tendering table
                $payInsert=\DB::table('work_tendering')->where('WorkTender_ID',$tenderID)->update(array('Payment_Terms'=>$payTerms[0]));
                    }
                   
            
        
            else if($types==1)
            {
                
                $tenderID=\DB::table('work_tendering')->insertGetID(array('Work_ID'=>$id, 'Assoc_ID'=>$value['name'],'Online_Flag'=>1,'Assoc_Status'=>1));
            
           
           //to copy line items to tender table
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
    

/*
    //to copy keys to tender-key table
    foreach($keys as $key)
    {
        $existsKey=\DB::table('tender_key_deliverables')->where('Tender_ID',$tenderID)->where('Key_ID',$key->Key_ID)->get();
        $keyCount=count($existsKey);
        if($keyCount==0)
        {
            $keySet=['Tender_ID'=>$tenderID,'Key_ID'=>$key->Key_ID];
        }
        

    }
   


    //to copy terms to tender table
    foreach($terms as $term)
    {
        $termsExists=\DB::table('tender_terms_conditions')->where('Tender_ID', $tenderID)->where('Term_ID',$term->Term_ID)->get();
        $termCount=count($termsExists);
        if($termCount==0)
        {
            $termSet=['Tender_ID'=>$tenderID,'Term_ID'=>$term->Term_ID];
        }
        
    }
*/
    $items_insert=\DB::table('work_tender_details_lab')->insert($dataset);
    //$keys_insert=\DB::table('tender_key_deliverables')->insert($keySet);
      // $term_insert=\DB::table('tender_terms_conditions')->insert($termSet);
    //to copy payment terms to tendering table
    $payInsert=\DB::table('work_tendering')->where('WorkTender_ID',$tenderID)->update(array('Payment_Terms'=>$payTerms[0]));
        }
        
	
    
}

}
	
	
		
$leadID=\DB::table('service_work')->where('Work_ID',$id)->pluck('Lead_ID');
		
			$changeWorkFlag=\DB::table('service_work')->where('Work_ID',$id)
            ->update(array('AssocSelectFlag'=>2,'Cust_Status_ID'=>6));
            //$updateLead=\DB::table('sales_lead')->where('Lead_ID',$leadID[0])->update(array());

		
		$resp=array("Success"=>true);
		return $resp;
	}
public function biws_getAllTenders($id)
{
    $tenders=\DB::table('work_tendering')
    ->join('work_tender_details_lab', 'work_tendering.WorkTender_ID','=','work_tender_details_lab.WorkTender_ID')
	->join('serv_line_items', 'serv_line_items.LineItem_ID','=','work_tender_details_lab.LineItem_ID')
	->join('units', 'units.Unit_ID','=','serv_line_items.UnitID')
    ->join('associate', 'associate.Assoc_ID','=','work_tendering.Assoc_ID')
    
	->where('work_tendering.Work_ID', $id)
	
	//->select('work_tender_details_lab.*','work_labour_estimation.Comments','serv_line_items.*')
    ->get();
    $resp=array($tenders);
    return $resp;
}
public function biws_saveTenderKeys(Request $r)
{
$data= Request::json()->all();
	$id=$data['param1'];
    $items=$data['param2'];
    $type=$data['param3'];
    $tid=$data['param4'];
    if($type==0)
    {
        foreach($items as $item)
        {
            
                $items=\DB::table('wo_key_deliverables')->insert(array('Work_ID' => $id, 'Key_ID'=> $item['name']));
            
            
        } 
    }
    else if($type==1)
    {
        foreach($items as $item)
        {
            
                $items=\DB::table('tender_key_deliverables')->insert(array('Tender_ID' => $tid, 'Key_ID'=> $item['name']));
            
            
        }
    }
    
    /*$type=$data['param3'];
    $tenderID=\DB::table('work_tendering')->where('Work_ID', $id)->select('WorkTender_ID')->get();
    foreach($tenderID as $tid)
    {
	//$dataset=[];
	foreach($items as $item)
	{
		if($type==0)
		{
			$items=\DB::table('tender_key_deliverables')->insert(array('Tender_ID' => $tid->WorkTender_ID, 'Key_ID'=> $item['name']));
		}
		/*else if($type==1)
		{
			
	$items=\DB::table('wo_key_deliverables')->insert(array('Work_ID' => $id, 'Key_ID'=> $item['name'],'Amend_Flag'=>$data['param4']));
        }*/
    

    $resp=array("Success"=>true);
    return $resp;
}

 public function biws_chkKeysExists($id)
 {
    $keysExists=\DB::table('wo_key_deliverables')
    
    ->where('Work_ID',$id)->get();
	$count=count($keysExists);
	$resp=array($count);
	return $resp;
 }   
 public function biws_checkTermsExists($id)
 {
    $keysExists=\DB::table('wo_terms_conditions')
    
    ->where('Work_ID',$id)->get();
	$count=count($keysExists);
	$resp=array($count);
	return $resp;
 }
 public function biws_getKeyDeliverables($id)
 {
$keys=\DB::table('wo_key_deliverables')
->join('key_deliverables','key_deliverables.Key_ID','=','wo_key_deliverables.Key_ID')
->where('wo_key_deliverables.Delete_Flag',0)
->where('Work_ID', $id)->get();
$resp=array($keys);
return $resp;
 }
 public function biws_saveTerms(Request $r)
	{
		$data= Request::json()->all();
	$id=$data['param1'];
	$items=$data['param2'];
	$type=$data['param3'];
	//$dataset=[];
	foreach($items as $item)
	{
			$items=\DB::table('wo_terms_conditions')->insert(array('Work_ID' => $id, 'Term_ID'=> $item['name']));
    }
    $resp=array("Success"=>true, $items);
    return $resp;
}
public function biws_getTerms($id)
 {
$terms=\DB::table('wo_terms_conditions')
->join('terms_conditions','terms_conditions.Term_ID','=','wo_terms_conditions.Term_ID')
		
        ->where('wo_terms_conditions.Delete_Flag',0)
        ->where('Work_ID',$id)->get();
$resp=array($terms);
return $resp;
 }
 public function biws_getAllOfflineTenders($id)
 {
   /*$scope=\DB::table('work_labour_estimation')->where('Work_ID', $id)->where('deleteFlag',0)->select('LE_ID','LineItem_ID', 'Quantity','Comments', 'Priority')->get();
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
    $items_insert=\DB::table('work_tender_details_lab')->insert($dataset);*/
     $tenders=\DB::table('work_tendering')
     ->join('work_tender_details_lab', 'work_tender_details_lab.WorkTender_ID', '=','work_tendering.WorkTender_ID')
     ->join('serv_line_items', 'serv_line_items.LineItem_ID','=','work_tender_details_lab.LineItem_ID')
     ->join('units', 'units.Unit_ID','=','serv_line_items.UnitID')
    ->where('work_tendering.Work_ID',$id)->where('work_tendering.Online_Flag',0)
    ->orderBy('work_tender_details_lab.Priority', 'ASC')
     ->get();
     $resp=array($tenders);
     return $resp;
 }
 public function biws_chkOffTenderExists($id)
 {
     $offTender=\DB::table('work_tendering')->where('Work_ID', $id)->where('Online_Flag',0)->get();
     $count=count($offTender);
     $resp=array($count);
     return $resp;
 }
 public function biws_chkOnTenderExists($id)
 {
    $onTender=\DB::table('work_tendering')->where('Work_ID', $id)->where('Online_Flag',1)->get();
    $count=count($onTender);
    $resp=array($count);
    return $resp;
 }
 public function biws_getAllOfflineAssocs($id)
 {
    $offAssoc=\DB::table('work_tendering')
    ->leftjoin('associate', 'associate.Assoc_ID', '=','work_tendering.Assoc_ID')
->leftjoin('associate_details', 'associate_details.Assoc_ID','=','associate.Assoc_ID')
->leftjoin('contacts', 'contacts.Contact_ID', '=','associate.Contact_ID')
->leftjoin('address','address.Address_ID','=','associate.Address_ID')
->leftjoin('status', 'status.Assoc_Status','=','associate.Assoc_Status')
->where('Work_ID',$id)
->where('work_tendering.Online_Flag',0)
->where('work_tendering.DeleteFlag',0)
    ->get();
    $resp=array($offAssoc);
    return $resp;
 }
public function getAllTenderKeys($id)
{
    $tenderKeys=\DB::table('tender_key_deliverables')->join('work_tendering','work_tendering.WorkTender_ID','=','tender_key_deliverables.Tender_ID')
    ->join('key_deliverables','key_deliverables.Key_ID','=','tender_key_deliverables.Key_ID')
    ->where('work_tendering.Work_ID', $id)->where('tender_key_deliverables.DeleteFlag',0)->get();
    $resp=array($tenderKeys);
    return $resp;
}
public function getAllTenderTerms($id)
{
    $tenderTerms=\DB::table('tender_terms_conditions')->join('work_tendering','work_tendering.WorkTender_ID','=','tender_terms_conditions.Tender_ID')
    ->join('terms_conditions','terms_conditions.Term_ID','=','tender_terms_conditions.Term_ID')
    ->where('work_tendering.Work_ID', $id)->where('tender_terms_conditions.DeleteFlag',0)->get();
    $resp=array($tenderTerms);
    return $resp;
}
public function biws_saveWorkDays(Request $r)
{
    $data= Request::json()->all();
    $saveWorkDays=\DB::table('work_tendering')->where('WorkTender_ID',$data['TL_ID'])->update(array('Work_Days'=>$data['rate']));
    if($saveWorkDays)
    {
        $resp=array('Success'=>true);
        return $resp;
    }
    else{
        
        $resp=array('Success'=>false);
        return $resp;
    }
    
}

public function biws_saveTenderLabDetails(Request $r)
{
    $values = Request::json()->all();
		$qty=$values['qty'];
		$rate=$values['rate'];
		$saveDetails=\DB::table('work_tender_details_lab')->where('WorkTenderLab_ID',$values['MEID'])
		->where('LineItem_ID',$values['itemID'])->update(array('Rate'=>$values['rate'],
		 'Quantity'=>$values['qty'], 'Value'=>$qty * $rate, 'LabNo'=>$values['labNo'],'Days'=>$values['days'],'updateFlag'=>1));

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
public function biws_saveCustKeys(Request $r)
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
        $tenderKey=\DB::table('tender_key_deliverables')->insert(array('Tender_ID'=>$values['tenderID'], 'Key_ID'=>$keyID));
    }
    $resp=array('Success'=>true);
    return $resp;
}
    }
    public function biws_delInTenderKeys(Request $r)
    {
        $values = Request::json()->all();
		$kid=$values['param1'];
        $tid=$values['param2'];   
        $updateKey=\DB::table('tender_key_deliverables')->where('Tender_ID', $tid)->where('Key_ID',$kid)
        ->update(array('DeleteFlag'=>1));
        if($updateKey)
        {
            $resp=array('Success'=>true);
            return $resp;
        }
       else
        {
            $resp=array('Success'=>false);
            return $resp;
        }
    }
    public function biws_saveTenderTerms(Request $r)
    {
        
		$data= Request::json()->all();
	$id=$data['param1'];
	$items=$data['param2'];
    $type=$data['param3'];
    $tid=$data['param4'];
	//$dataset=[];
	foreach($items as $item)
	{
		if($type==0)
		{
			$items=\DB::table('wo_terms_conditions')->insert(array('Work_ID' => $id, 'Term_ID'=> $item['name']));
		}
		else if($type==1)
		{
			
	$items=\DB::table('tender_terms_conditions')->insert(array('Tender_ID' => $tid, 'Term_ID'=> $item['name']));
		}
    }
    $resp=array('Success'=>true);
    return $resp;
    }
    public function biws_saveCustTerms(Request $r)
    {
        $values= Request::json()->all();
		$termID=\DB::table('terms_conditions')->insertGetID(array('Term_Name'=>$values['termName'], 'Segment_ID'=>$values['segID'], 'CustomFlag'=>1));
if(!empty($termID))
{
	if($values['typeID']==0)
	{
		$woKey=\DB::table('wo_terms_conditions')->insert(array('Work_ID'=>$values['workID'], 'Term_ID'=>$termID));
	}
	else if($values['typeID']==1)
	{
        $tenderKey=\DB::table('tender_terms_conditions')->insert(array('Tender_ID'=>$values['tenderID'], 'Term_ID'=>$termID));
    }
    $resp=array('Success'=>true);
    return $resp; 
    }
}
public function biws_delInTenderTerm(Request $r)
{
    $values = Request::json()->all();
		$kid=$values['param1'];
        $tid=$values['param2'];   
        $updateKey=\DB::table('tender_terms_conditions')->where('Tender_ID', $tid)->where('Term_ID',$kid)
        ->update(array('DeleteFlag'=>1));
        if($updateKey)
        {
            $resp=array('Success'=>true);
            return $resp;
        }
       else
        {
            $resp=array('Success'=>false);
            return $resp;
        }
}
public function biws_getTenderPayTerm($id)
{
    $payTerm=\DB::table('work_tendering')->where('WorkTender_ID', $id)->pluck('Payment_Terms');
    $resp=array($payTerm[0]);
    return $resp;
}
public function biws_finishTender(Request $r)
{
    $values = Request::json()->all();
        $tid=$values['param1'];
        $finishTender=\DB::table('work_tendering')->where('WorkTender_ID',$tid)->update(array('TenderFinish_Flag'=>1));
        if($finishTender)
        {
            $resp=array('Success'=>true);
            return $resp;
        }
}
public function biws_getAllOnlineAssocs($id)
{
    $onAssoc=\DB::table('work_tendering')
    ->join('associate', 'associate.Assoc_ID', '=','work_tendering.Assoc_ID')
->join('associate_details', 'associate_details.Assoc_ID','=','associate.Assoc_ID')
->join('contacts', 'contacts.Contact_ID', '=','associate.Contact_ID')
->join('address','address.Address_ID','=','associate.Address_ID')
->leftjoin('status', 'status.Assoc_Status','=','associate.Assoc_Status')
->where('Work_ID',$id)->where('work_tendering.Online_Flag',1)->where('work_tendering.DeleteFlag',0)
    ->get();
    $resp=array($onAssoc);
    return $resp;
}
public function biws_getOnlineAssocs()
{
   /* $t_assocs=\DB::table('work_tendering')->where('work_tendering.Work_ID', $id)
    ->join('associate', 'associate.Assoc_ID','=','work_tendering.Assoc_ID')
    ->where('work_tendering.TenderFinish_Flag',1)->select('work_tendering.Assoc_ID')->get();*/
    //->where('work_tendering.updateFlag',1)
	
    $onlineAssoc=\DB::table('associate')
    ->leftjoin('contacts', 'contacts.Contact_ID','=','associate.Contact_ID')
    ->leftjoin('associate_details','associate_details.Assoc_ID','=','associate.Assoc_ID')
    ->leftjoin('location', 'location.Loc_ID','=','associate_details.Loc_ID')
    //->where('Assoc_Status','4')
    ->where('associate.Online_Flag',1)->get();
    /*$newArray=[];
    $finalArray=[];
    foreach($t_assocs as $t_assoc){
        foreach($onlineAssoc as $o_assoc)
        {
            if($t_assoc->Assoc_ID==$o_assoc->Assoc_ID)
            {
                $o_assoc['DisableFlag']=1;
                array_push($newArray,$o_assoc);
            }
            else{
                $o_assoc['DisableFlag']=0;
                array_push($newArray,$o_assoc);

            }
        }
       

    }*/
    
    $resp=array($onlineAssoc);
    return $resp;
}
public function biws_getOnlineAssocsNew($id)
{
    $t_assocs=\DB::table('work_tendering')->where('Work_ID',$id)->pluck('Assoc_ID');
    $onlineAssoc=\DB::table('associate')
    ->leftjoin('contacts', 'contacts.Contact_ID','=','associate.Contact_ID')
    ->leftjoin('associate_details','associate_details.Assoc_ID','=','associate.Assoc_ID')
    ->leftjoin('location', 'location.Loc_ID','=','associate_details.Loc_ID')
    //->where('Assoc_Status','4')
    ->where('associate.Online_Flag',1)->whereNotIn('associate.Assoc_ID',$t_assocs)->get();
   
    
    
    $resp=array($onlineAssoc);
    return $resp;
}
public function biws_getAllOnlineTenders($id)
{
    $tenders=\DB::table('work_tendering')
    ->join('work_tender_details_lab', 'work_tender_details_lab.WorkTender_ID', '=','work_tendering.WorkTender_ID')
    ->join('serv_line_items', 'serv_line_items.LineItem_ID','=','work_tender_details_lab.LineItem_ID')
    ->join('units', 'units.Unit_ID','=','serv_line_items.UnitID')
   ->where('work_tendering.Work_ID',$id)->where('work_tendering.Online_Flag',1)->where('work_tendering.DeleteFlag',0)
    ->get();
    $resp=array($tenders);
    return $resp; 
}
public function biws_getAllTenderList($id)
{
    $list=\DB::table('work_tendering')
    ->join('associate', 'associate.Assoc_ID', '=','work_tendering.Assoc_ID')
->join('associate_details', 'associate_details.Assoc_ID','=','associate.Assoc_ID')
->join('contacts', 'contacts.Contact_ID', '=','associate.Contact_ID')
->join('address','address.Address_ID','=','associate.Address_ID')
->leftjoin('status', 'status.Assoc_Status','=','associate.Assoc_Status')
->where('Work_ID',$id)->where('work_tendering.DeleteFlag',0)->where('TenderFinish_Flag',1)

    ->get();
    $resp=array($list);
    return $resp;
}
public function biws_chkFinishTenderExists($id)
{
    $list=\DB::table('work_tendering')    
->where('Work_ID',$id)->where('work_tendering.DeleteFlag',0)->where('TenderFinish_Flag',1)->get();
$count=count($list);
    $resp=array($count);
    return $resp;
}
public function biws_editTender(Request $r)
{
    $values = Request::json()->all();
        $tid=$values['param1'];
        $editTender=\DB::table('work_tendering')->where('WorkTender_ID',$tid)->update(array('UpdateFlag'=>1, 'TenderFinish_Flag'=>0));
        if($editTender)
        {
            $resp=array('Success'=>true);
            return $resp;
        }
}
public function biws_deleteTender(Request $r)
{
    $values = Request::json()->all();
        $tid=$values['param1'];
        $deleteTender=\DB::table('work_tendering')->where('WorkTender_ID',$tid)->update(array('DeleteFlag'=>1));
        if($deleteTender)
        {
            $resp=array('Success'=>true);
            return $resp;
        }
}
public function biws_AssocWorks($associd)
{
    $works=\DB::table('work_tendering')
    ->join('service_work','service_work.Work_ID','=','work_tendering.Work_ID')
    ->join ('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')
		->leftjoin('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
		->leftjoin('address', 'address.Address_ID','=','sales_customer.Address_ID')
		->leftjoin('contacts','contacts.Contact_ID','=','sales_customer.Contact_ID')
        ->leftjoin('location','location.Loc_ID','=','sales_customer.Loc_ID')
        ->join('associate', 'associate.Assoc_ID','=','work_tendering.Assoc_ID')
        ->leftjoin('assoc_tender_status','assoc_tender_status.Tender_Status_ID','=','work_tendering.Assoc_Status')
    ->where('work_tendering.Assoc_ID', $associd)->where('associate.Online_Flag',1)
    ->where('sales_lead.Flag',2)
    ->where('work_tendering.DeleteFlag',0)->get();
    $resp=array($works);
    return $resp;
}
public function biws_pushTenderToCust(Request $r)
{
    $values = Request::json()->all();
    $id=$values['param1'];
    $items=$values['param2'];
    
      
	foreach($items as $value)
	{
$tenderID=\DB::table('work_tendering')->where('Work_ID',$id)->where('Assoc_ID', $value['name'])
->update(array('pushTender_Flag'=>1));
    }
if($tenderID)
{
    $leadID=\DB::table('service_work')->where('Work_ID',$id)->pluck('Lead_ID');
//$updateLead=\DB::table('sales_lead')->where('Lead_ID',$leadID[0])->update(array());
$updateWorkStatus=\DB::table('service_work')->where('Work_ID',$id)->update(array('WorkStatus'=>15,'Cust_Status_ID'=>7));
    $resp=array('Success'=>true);
    return $resp;
}

    
}
public function biws_getSelectedTenderAssocs($id)
{
    $assocs=\DB::table('work_tendering')
    ->join('associate', 'associate.Assoc_ID', '=','work_tendering.Assoc_ID')
->join('associate_details', 'associate_details.Assoc_ID','=','associate.Assoc_ID')
->join('contacts', 'contacts.Contact_ID', '=','associate.Contact_ID')
->join('address','address.Address_ID','=','associate.Address_ID')
->leftjoin('status', 'status.Assoc_Status','=','associate.Assoc_Status')
->where('Work_ID',$id)->where('work_tendering.DeleteFlag',0)->where('pushTender_Flag',1)

    ->get();
    $resp=array($assocs);
    return $resp;
}
public function biws_getSelectedTenderItems($id)
{
    $tenders=\DB::table('work_tender_details_lab')
    
    ->join('serv_line_items', 'serv_line_items.LineItem_ID','=','work_tender_details_lab.LineItem_ID')
    ->join('units', 'units.Unit_ID','=','serv_line_items.UnitID')
   ->where('work_tender_details_lab.WorkTender_ID',$id)
    ->get();
    $resp=array($tenders);
    return $resp; 
}
public function sendLetterOfIntrest(Request $r)
{
    $values = Request::json()->all();
    //$id=$values['param1'];
   // $msg1="This is a demo mail";
    $mail=\Mail::send('ti1@inframall.net',function($msg)
    {
        $msg->to('aiswaryagireesh14@gmail.com')->subject('Reminder');
    });
    $resp=array('Success'=>true);
    return $resp;
}
public function biws_updateReqSiteVisit(Request $r)
{
    $values = Request::json()->all();
    $associd=$values['param1'];
    $wid=$values['param2'];
    $tids=\DB::table('work_tendering')->where('Work_ID',$wid)->where('DeleteFlag',0)->select('WorkTender_ID','Assoc_ID')->get();
    foreach($tids as $tid)
    {
if($tid->Assoc_ID==$associd)
{
    $updateFlag=\DB::table('work_tendering')->where('WorkTender_ID',$tid->WorkTender_ID)->update(array('ReqSiteVisit_Flag'=>1, 'Assoc_Status'=>3));

}
else{
    $updateFlag=\DB::table('work_tendering')->where('WorkTender_ID',$tid->WorkTender_ID)->update(array('ReqSiteVisit_Flag'=>2));
}
    }
    $leadID=\DB::table('service_work')->where('Work_ID',$wid)->pluck('Lead_ID');
    $updateLead=\DB::table('sales_lead')->where('Lead_ID',$leadID[0])->update(array('Cust_Status_ID'=>8));
    $updateWorkStatus=\DB::table('service_work')->where('Work_ID',$wid)->update(array('WorkStatus'=>17));
    $resp=array($tids);
    return $resp;
}
public function biws_confirmAssoc(Request $r)
{
    $values = Request::json()->all();
    $tid=$values['param1'];
    $wid=\DB::table('work_tendering')->where('WorkTender_ID', $tid)->pluck('Work_ID');
    $confirmAssoc=\DB::table('work_tendering')->where('WorkTender_ID', $tid)->update(array('SelectStatus'=>1,'Assoc_Status'=>7));
    if($confirmAssoc)
    {
        $leadID=\DB::table('service_work')->where('Work_ID',$wid[0])->pluck('Lead_ID');
        $updateLead=\DB::table('sales_lead')->where('Lead_ID',$leadID[0])->update(array('Cust_Status_ID'=>11));
        $updateWorkStatus=\DB::table('service_work')->where('Work_ID',$wid[0])->update(array('WorkStatus'=>6, 'InitWO_Flag'=>1));
        $resp=array('Success'=>true);
        return $resp;
    }
}
public function biws_rejectAssoc(Request $r)
{
    $values = Request::json()->all();
    $tid=$values['param1'];
    $reason=$values['param2'];
    $rejAssoc=\DB::table('work_tendering')->where('WorkTender_ID', $tid)
    ->update(array('SelectStatus'=>2,'Assoc_Status'=>8,'Reason_ID'=>$reason));
    $wid=\DB::table('work_tendering')->where('WorkTender_ID', $tid)->pluck('Work_ID');
    if($rejAssoc)
    {
        
        $tids=\DB::table('work_tendering')->where('Work_ID',$wid[0])->where('DeleteFlag',0)->select('WorkTender_ID','Assoc_ID')->get();
    foreach($tids as $tid)
    {

    $updateFlag=\DB::table('work_tendering')->where('WorkTender_ID',$tid->WorkTender_ID)->update(array('ReqSiteVisit_Flag'=>0));



        
    }
    $leadID=\DB::table('service_work')->where('Work_ID',$wid[0])->pluck('Lead_ID');
        $updateLead=\DB::table('sales_lead')->where('Lead_ID',$leadID[0])->update(array('Cust_Status_ID'=>7));
        $updateWorkStatus=\DB::table('service_work')->where('Work_ID',$wid[0])->update(array('WorkStatus'=>16));
}
    $resp=array('Success'=>true);
        return $resp;
}
public function biws_chkPaymentDone($id)
{
    $chkPayment=\DB::table('service_work')
    ->where('Work_ID',$id)->where('Payment_Flag',1)->get();
    $count=count($chkPayment);
    $resp=array($count);
    return $resp;
}
public function biws_getAssocTender($aid, $wid)
{
    $workDetails=\DB::table('work_tendering')
    ->join('service_work','service_work.Work_ID','=','work_tendering.Work_ID')
    ->join ('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')
    ->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
        
    ->join('location', 'location.Loc_ID','=','sales_customer.Loc_ID')
    ->where('work_tendering.Work_ID',$wid)->where('work_tendering.Assoc_ID', $aid)
    ->select('work_tendering.*','sales_customer.*','location.Loc_Name', 'service_work.Work_Type','service_work.Category')->get();
    $resp=array($workDetails);
    return $resp;
}
public function biws_saveRate(Request $r)
{
    $values = Request::json()->all();
        $qty=\DB::table('work_tender_details_lab')->where('WorkTenderLab_ID',$values['TL_ID'])->pluck('Quantity');
     $tid=\DB::table('work_tender_details_lab')->where('WorkTenderLab_ID',$values['TL_ID'])->pluck('WorkTender_ID');
		$rate=$values['rate'];
		$saveDetails=\DB::table('work_tender_details_lab')->where('WorkTenderLab_ID',$values['TL_ID'])
	->update(array('Rate'=>$values['rate'],
		 'Value'=>$qty[0] * $rate, 'updateFlag'=>1));

		 if(!empty($saveDetails))
		 {
			$totalQuote=\DB::table('work_tender_details_lab')->where('WorkTender_ID',$tid[0])->sum('Value');
			 $update=\DB::table('work_tendering')->where('WorkTender_ID', $tid[0])->update(array('TotalQuote'=>$totalQuote));
			
		 }
		 $resp=array("Success"=>true, $tid);
		 return $resp;
}
public function biws_getItemDetails($id)
{
    $itemDetails=\DB::table('work_tender_details_lab')
    ->join('serv_line_items', 'serv_line_items.LineItem_ID','=','work_tender_details_lab.LineItem_ID')
    ->join('units', 'units.Unit_ID','=','serv_line_items.UnitID')->where('WorkTenderLab_ID', $id)->get();
    $resp=array($itemDetails);
    return $resp;
}
public function biws_chkWorkDaysExists($id)
{
    $chkWorkDays=\DB::table('work_tendering')->where('WorkTender_ID',$id)->pluck('Work_Days');
$count=count($chkWorkDays);
    $resp=array($count);
    return $resp;
}
public function biws_pushBackToPMA(Request $r)
{
    $values = Request::json()->all();
    $tid=$values['param1'];
    $statusChange=\DB::table('work_tendering')->where('WorkTender_ID',$tid)
    ->update(array('Tender_Rec_Flag'=>1,'TenderFinish_Flag'=>1,'Assoc_status'=>2));
    $resp=array('Success'=>true);
    return $resp;
}
public function biws_finishWO(Request $r)
{
    $values = Request::json()->all();
    $leadID=\DB::table('service_work')->where('Work_ID',$values['param1'])->pluck('Lead_ID');
        $updateLead=\DB::table('sales_lead')->where('Lead_ID',$leadID[0])->update(array('Cust_Status_ID'=>12));

    $finish=\DB::table('service_work')->where('Work_ID',  $values['param1'])
    ->update(array('WorkStatus'=>13,'WOSignUp_Flag'=>1));//7
    $resp1=array("Success"=>true);
    return $resp1;
}
public function biws_getWorkOrderDetails($id)
{   
    $workType=\DB::table('service_work')->where('Work_ID', $id)->pluck('Work_Type');
    $WO_Date=\DB::table('work_timeline')->where('Work_ID', $id)->where('Work_Attrb_ID',27)->pluck('Value');
    $totalQuote=\DB::table('work_tendering')->where('Work_ID', $id)->where('SelectStatus',1)->pluck('TotalQuote');
	$resp=array($WO_Date[0],$workType[0],$totalQuote[0]);
	return $resp;
}
public function biws_getCustDetails($id)
{
    $custDetails=\DB::table('service_work')
	->join('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')
	//->join('location', 'location.Loc_ID','=','sales_lead.Lead_Loc_ID')
	->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
	->join('contacts', 'contacts.Contact_ID','=','sales_customer.Contact_ID')
	->leftjoin('location', 'location.Loc_ID','=','sales_customer.Loc_ID')
	->leftjoin('address', 'address.Address_ID','=','sales_customer.Address_ID')
	->where('service_work.Work_ID', $id)
	->get();
	$resp=array($custDetails);
	return $resp;
}
public function biws_getAssocDetails($id)
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
public function biws_getFinalTenderDetails($id)
{
    $tid=\DB::table('work_tendering')->where('Work_ID',$id)->where('SelectStatus',1)->pluck('WorkTender_ID');
    $tenders=\DB::table('work_tender_details_lab')
    
    ->join('serv_line_items', 'serv_line_items.LineItem_ID','=','work_tender_details_lab.LineItem_ID')
    ->join('units', 'units.Unit_ID','=','serv_line_items.UnitID')
   ->where('work_tender_details_lab.WorkTender_ID',$tid[0])
    ->get();
    $resp=array($tenders);
    return $resp; 
}
public function biws_getWorkSchedule($id)
{
    $workSched=\DB::table('work_schedule')
	->leftjoin('work_amendment','work_schedule.Work_ID','=','work_amendment.Work_ID')
	//->where('work_amendment.WorkSched_Amend_Flag',1)
	->where('work_schedule.Work_ID', $id)->where('DeleteFlag',0)->orderBy('work_schedule.Start_Date')->get();
	$resp=array($workSched);
	return $resp;
}
public function biws_getPaySchedule($id)
{
    $pay=\DB::table('payment_schedule')->leftjoin('work_amendment','payment_schedule.Work_ID','=','work_amendment.Work_ID')
	//->leftjoin('actual_payments', 'actual_payments.PaySched_ID','=','payment_schedule.Pay_Schedule_ID')
	->where('payment_schedule.Work_ID', $id)->where('payment_schedule.DeleteFlag',0)->orderBy('payment_schedule.Payment_Date')->get();
	$resp=array($pay);
	return $resp;
}
public function sendMail(Request $r)
{
    $values = Request::json()->all();
    \Mail::send('email',['data'=>$values],function($message)
             {
                 $message->from('aiswaryagireesh14@gmail.com','Laravel');
                 $message->to('ti1@inframall.net');
             }) ;
             $resp=array('Success'=>true);
             return $resp;
}
public function biws_updateAssociate(Request $r)
{
    $values = Request::json()->all();
    $assocID=\DB::table('associate')->where('Assoc_ID',$values['param1'])->update(array('Online_Flag'=>1));
    $resp=array('Success'=>true);
    return $resp;
}
public function biws_getTenderTotal($id)
{
    $total=\DB::table('work_tendering')->where('WorkTender_ID',$id)->pluck('TotalQuote');
    $resp=array($total);
    return $resp;
}
public function biws_chkQtyExists($id)
{
    $chkQty=\DB::table('work_tender_details_lab')->where('WorkTenderLab_ID',$id)->pluck('Quantity');
    $resp=array($chkQty);
    return $resp;
}
public function biws_getRejectReason(Request $r)
{
    $reasons=\DB::table('tender_rejection_reason')->get();
    $resp=array($reasons);
    return $resp;
}
public function getTimeLineData($id)
{
    $new=array();
    
    $data=\DB::table('service_work')
    ->where('Work_ID',$id)->pluck('Cust_Status_ID');
    if($data[0])
    {
        $resp=array($data[0]);
        $status=\DB::table('customer_status')
                ->select('Cust_Status_ID AS Timeline_ID',
                //\DB::raw('(CASE WHEN Cust_Status_ID ='.$data[0].' THEN 1 ELSE 0 END) AS is_Active'),
                \DB::raw('(CASE WHEN Cust_Status_ID <'.$data[0].' THEN "-1" WHEN Cust_Status_ID ='.$data[0].' THEN "0" ELSE "+1" END) AS Status'))
                ->get();
        /*$resp=array($new);*/
        return $status;
    }
    else{
        $resp=array('WorkID not exists');
        return $resp;
    }

}
public function biws_getTenderedAssocs($id)
{
	$assocs=\DB::table('work_tendering')->where('work_tendering.Work_ID', $id)
	->join('associate', 'associate.Assoc_ID','=','work_tendering.Assoc_ID')->where('work_tendering.TenderFinish_Flag',1)->get();//->where('work_tendering.updateFlag',1)
	$resp=array($assocs);
	return $resp;
}
public function biws_getCertifyAssocList($id)
	{
	    $t_assocs=\DB::table('work_tendering')->where('Work_ID',$id)->pluck('Assoc_ID');
		$service_Id=\DB::table('work_service_map')->where('Work_ID', $id)->pluck('Service_ID');
	if(!empty($service_Id))
	{
		$certifyList=\DB::table('associate')
		->join('associate_segment_rate', 'associate_segment_rate.Assoc_ID','=','associate.Assoc_ID')
		
		->join('associate_details', 'associate_details.Assoc_ID', '=','associate.Assoc_ID')
		->leftjoin('location', 'location.Loc_ID', '=','associate_details.Loc_ID')
		->whereIn('associate_segment_rate.Service_ID', $service_Id)
		->whereNotIn('associate.Assoc_ID',$t_assocs)
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

}
