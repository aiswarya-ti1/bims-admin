<?php

namespace App\Http\Controllers;
//use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Session;
use Request;
use App\login;
use App\Roles;
use App\User;
use App\user_roles;
use App\LoginUser;
use App\user_log_session;
use Hash;
use Illuminate\Support\Facades\Crypt;
use DateTime;
use Illuminate\Support\Facades\Mail;

use Illuminate\Support\Facades\Auth;
use App\Notifications\NewUserNotification;
use App\Notifications\NewLeadNotification;
use App\Notifications\SiteVisitShceduled;
use App\Notifications\NewUserRegistration;
use App\Notifications\addLeadDBNotification;
use App\Notifications\siteVisitScheduledDB;
use App\Notifications\tenderReceivedDB;
use App\Notifications\tenderReceivedEmail;
use App\Notifications\TenderSubmittedDB;
use App\Notifications\TenderSubmittedEmail;
use App\Notifications\QuotesReceivedEmail;
use App\Notifications\QuotesReceivedDB;
use App\Notifications\PaymentCompletedDB;
use App\Notifications\RequestAssocVisitDB;
use App\Notifications\RequestAssocVisitEmail;
use App\Notifications\CustomerIntrestDB;
use App\Notifications\CustomerIntrestEmail;
use App\Notifications\AssocSiteVisitDB;
use App\Notifications\AssocSiteVisitEmail;
use App\Notifications\WorkConfirmDB;
use App\Notifications\WorkConfirmEmail;
use App\Notifications\SignUpCompletedDB;
use App\Notifications\SignUpCompletedEmail;
use App\Notifications\WorkOrderGeneratedEmail;
use App\Notifications\WorkOrderGeneratedDB;


use PDF;
use Notifiable;
class biwsController extends Controller
{
    protected $connection= 'secondsql';
    protected $notifiables;
    
    public function __construct()
    {
$this->notifiables=array(110);
    }
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
        $values = Request::json()->all();
        $type=$values['user']['first_values']['type'];
        $custName=$values['user']['second_values']['custName'];
        $contact=(string)$values['user']['second_values']['contact'];
        $location=$values['user']['second_values']['loc'];
        $email=$values['user']['second_values']['email'];
       $password=$values['pwd'];
       
       $pwd=$this->biws_getHash($password);


       
        /*$chkContact=\DB::table('users')->where('username',$contact)->get();
        $chkEmail=\DB::table('users')->where('email',$email)->get();
        $countContact=count($chkContact);
        $countEmail=count($chkEmail);
        if($countContact==0 && $countEmail==0)
        {*/
            $contactID=\DB::table('contacts')->insertGetID(array('Contact_name'=>$custName,'Contact_phone'=>$contact, 'Contact_email'=>$values['user']['second_values']['email']));

           

            $userData=array('User_Name'=>$custName,'username'=>$contact, 'password'=>$pwd, 'Role_ID'=>16, 'email'=>$email);
            $signUp=\DB::table('users')->insertGetID($userData);
            $signUp1=\DB::table('logins')->insertGetID($userData);
            
            $token=auth()->attempt(['username' =>$contact, 'password' => $values['pwd']]);
           $customerID=\DB::table('sales_customer')->insertGetID(array('Cust_FirstName'=>$custName, 'Flag'=>1, 'Contact_ID'=>$contactID, 'User_ID'=>$signUp,'PinCode'=>$location));
            if($type==1)
            {
                $plan=$values['user']['first_values']['plan'];
        $area=$values['user']['first_values']['area'];
        $floors=$values['user']['first_values']['floor'];
        $work_Start=$values['user']['first_values']['start'];
                    $leadID=\DB::table('sales_lead')->insertGetID(array('Cust_ID'=>$customerID,'Lead_StatusID'=>2,'Proj_Details'=>"New Home",'Source_ID'=>8,'Flag'=>2,'Cust_Status_ID'=>1,'PinCode'=>$location));  
                    if($leadID)
                {
                   
                  //  
                  
                  
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
                else{
                    $category=$values['user']['second_values']['category'];
                    $catName=\DB::table('enq_category')->where('Enq_Cat_ID',$category)->pluck('Cat_Name');
                    $leadID=\DB::table('sales_lead')->insertGetID(array('Cust_ID'=>$customerID,'Lead_StatusID'=>2,'Proj_Details'=>$catName[0],'Source_ID'=>8,'Flag'=>2,'Cust_Status_ID'=>1,'PinCode'=>$location));
                    $mapCat=\DB::table('lead_category')->insert(array('Lead_ID'=>$leadID, 'Cat_ID'=>$category));
                    
                }
                \Notification::route('mail',$email)->notify(new NewUserRegistration());
            $customer=\DB::table('sales_customer')
            ->leftjoin('contacts','contacts.Contact_ID','=','sales_customer.Contact_ID')
            ->join('users','users.username','=','contacts.Contact_phone')

->where('sales_customer.Flag',1)->where('contacts.Contact_phone','=',$contact)
->select('sales_customer.Customer_ID', 'sales_customer.Cust_FirstName','users.ID','Contact_phone','Contact_email','Role_ID')->get();
            return response()->json(['token'=>$token,'Success'=>true,'Cust_ID'=>$customer]);
           /* }
            
            else{
                if($countEmail!=0)
                {
                    $resp=array('Success'=>false,'Error'=>'This email is already registered. Please register with a new email.');
                    return $resp;
                }
               else if($countContact!=0)
                {
                    $resp=array('Success'=>false,'Error'=>'This number is already registered. Please register with a new number.');
                    return $resp;
                }
            }*/
            
        }
       
       /* $creds=Request::only(['username','password','email']);
        $username=$creds['username'];
        $password=$creds['password'];
        $email=$creds['email'];
    
        
        $pwd=$this->biws_getHash($password);
	
        
        //$signUp=\DB::table('users')->insertGetID(array('username'=>$username, 'password'=>$pwd, 'Role_ID'=>16));
        $userData=array('username'=>$username, 'password'=>$pwd, 'Role_ID'=>16, 'email'=>$email);
        $signUp=LoginUser::create($userData);
        $signUp1=\DB::table('logins')->insertGetID(array('username'=>$username, 'password'=>$pwd, 'Role_ID'=>16, 'email'=>$email));
        
      // LoginUser::orderBy('ID', 'desc')->first()->notify(new NewUserNotification());
        if($signUp1)
        {
            $token=auth()->attempt($creds);
           // User::find(1)->notify(new NewUserNotification(auth()->user()));
           //$notify_users=User::whereIn('id', $this->notifiables)->get();
           \Notification::route('mail',$email)->notify(new NewUserRegistration());
            $customer=\DB::table('sales_customer')
            ->leftjoin('contacts','contacts.Contact_ID','=','sales_customer.Contact_ID')
            ->join('users','users.username','=','contacts.Contact_phone')

->where('sales_customer.Flag',1)->where('contacts.Contact_phone','=',$username)
->select('sales_customer.Customer_ID', 'sales_customer.Cust_FirstName','users.ID','Contact_phone','Contact_email')->get();
            return response()->json(['token'=>$token,'Success'=>true,'Cust_ID'=>$customer]);
        }
        

    }*/
    public function biws_AssocSignUp(Request $r)
	{
       /* $creds=Request::only(['username','password','email']);
        $username=$creds['username'];
        $password=$creds['password'];
        $email=$creds['email'];
    
        
        $pwd=$this->biws_getHash($password);
	
        
        $signUp=\DB::table('users')->insertGetID(array('username'=>$username, 'password'=>$pwd, 'Role_ID'=>16,'email'=>$email));
        $signUp1=\DB::table('logins')->insertGetID(array('username'=>$username, 'password'=>$pwd, 'Role_ID'=>16,'email'=>$email));
        if($signUp1)
        {
            $token=auth()->attempt($creds);
            \Notification::route('mail',$email)->notify(new NewUserRegistration());
            $assoc=\DB::table('associate')
            ->join('contacts','contacts.Contact_ID','=','associate.Contact_ID')
            ->join('users','users.username','=','contacts.Contact_phone')
            ->where('associate.Online_Flag',1)->where('contacts.Contact_phone','=',$username)
            ->select('associate.Assoc_ID', 'associate.Assoc_FirstName', 'users.ID')->first();
            return response()->json(['token'=>$token,'Success'=>true,'Assoc'=>$assoc]);
        }*/
        $inputs = Request::json()->all();
        
        $username=$inputs['user']['first_values']['custName'] ;
        $contact=$inputs['user']['first_values']['contact'];
        $email=$inputs['user']['first_values']['email'];
        //$name=$inputs['name'];

        $password=$inputs['pwd'];
        $pwd=$this->biws_getHash($password);
       
      /* $chkContact=\DB::table('users')->where('username',$contact)->get();
               $chkEmail=\DB::table('users')->where('email',$email)->get();
               $countContact=count($chkContact);
               $countEmail=count($chkEmail);
               if($countContact==0 && $countEmail==0)
               {*/
                   $contactID=\DB::table('contacts')->insertGetID(array('Contact_name'=>$username,'Contact_phone'=>$contact, 'Contact_email'=>$email));
       
                  
       
                   $userData=array('User_Name'=>$username,'username'=>$contact, 'password'=>$pwd, 'Role_ID'=>11, 'email'=>$email);
                   $signUp=\DB::table('users')->insertGetID($userData);
                   $signUp1=\DB::table('logins')->insertGetID($userData);
                   $newAssocID=\DB::table('associate')->insertGetID(array('Assoc_FirstName'=>$username,'Contact_ID'=>$contactID,'Online_Flag'=>1,'User_ID'=>$signUp));
                   
                   $token=auth()->attempt(['username' =>$contact, 'password' => $inputs['pwd']]);


      
            
            $assoc=\DB::table('associate')->join('contacts','contacts.Contact_ID','=','associate.Contact_ID')
      ->join('users','users.username','=','contacts.Contact_phone')
       ->where('associate.Online_Flag',1)->where('contacts.Contact_phone','=',$contact)
       ->select('associate.Assoc_ID', 'associate.Assoc_FirstName','associate.Assoc_MiddleName','associate.Assoc_LastName', 'users.Role_ID')->first();
       return response()->json(['token'=>$token,'Success'=>true,'Assoc'=>$assoc]); 
           
      /*  }
        else{
            if($countEmail!=0)
            {
                $resp=array('Success'=>false,'Error'=>'This email is already registered. Please register with a new email.');
                return $resp;
            }
           else if($countContact!=0)
            {
                $resp=array('Success'=>false,'Error'=>'This number is already registered. Please register with a new number.');
                return $resp;
            }
        }*/


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
->select('sales_customer.Customer_ID', 'sales_customer.Cust_FirstName','users.ID', 'Contact_phone','email', 'Role_ID')->get();
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
        
     $type=$values['first_values']['type'];
        $custName=$values['second_values']['custName'];
        $contact=(string)$values['second_values']['contact'];
        $location=$values['second_values']['loc'];
        $chkContact=\DB::table('sales_customer')->join('contacts','contacts.Contact_ID','=','sales_customer.Contact_ID')
        ->where('sales_customer.Flag',1)->where('Contact_phone',$contact)->get();
        $chkEmail=\DB::table('sales_customer')->join('contacts','contacts.Contact_ID','=','sales_customer.Contact_ID')
        ->where('sales_customer.Flag',1)->where('Contact_email',$values['second_values']['email'])->get();
        $countContact=count($chkContact);
        $countEmail=count($chkEmail);
        if($countContact==0 && $countEmail==0)
        {
        $contactID=\DB::table('contacts')->insertGetID(array('Contact_name'=>$custName,'Contact_phone'=>$contact, 'Contact_email'=>$values['second_values']['email']));
        if($contactID)
        {
            $customerID=\DB::table('sales_customer')->insertGetID(array('Cust_FirstName'=>$custName, 'Flag'=>1, 'Contact_ID'=>$contactID));
            if($customerID)
            {
                if($type==1)
                {
        $plan=$values['first_values']['plan'];
        $area=$values['first_values']['area'];
        $floors=$values['first_values']['floor'];
        $work_Start=$values['first_values']['start'];
        $leadID=\DB::table('sales_lead')->insertGetID(array('Cust_ID'=>$customerID,'Lead_StatusID'=>2,'Proj_Details'=>"New Home",'Source_ID'=>8,'Flag'=>2,'Cust_Status_ID'=>1,'PinCode'=>$location));  
                    if($leadID)
                {
                    $notify_users=User::whereIn('id', $this->notifiables)->get();
                   foreach($notify_users as $user)
                   {
                    \Notification::route('mail', $user->email)->notify(new NewLeadNotification($custName));
                    \Notification::send($user,new addLeadDBNotification($custName));
                   }
                  //  
                  
                  
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
                else{
                    $category=$values['second_values']['category'];
                    $catName=\DB::table('enq_category')->where('Enq_Cat_ID',$category)->pluck('Cat_Name');
                    $leadID=\DB::table('sales_lead')->insertGetID(array('Cust_ID'=>$customerID,'Lead_StatusID'=>2,'Proj_Details'=>$catName[0],'Source_ID'=>8,'Flag'=>2,'Cust_Status_ID'=>1,'PinCode'=>$location));
                    $mapCat=\DB::table('lead_category')->insert(array('Lead_ID'=>$leadID, 'Cat_ID'=>$category));
                    if($leadID)
                {
                    $notify_users=User::whereIn('id', $this->notifiables)->get();
                    foreach($notify_users as $user)
                    {
                     \Notification::route('mail', $user->email)->notify(new NewLeadNotification($custName));
                     \Notification::send($user,new addLeadDBNotification($custName));
                    }
                   
                   
                }
                }
               
                
            }
        }
       
        
       /* $data = array('workID'=>20);
        \Mail::send('welcome',$data,function($message)
             {
                 $message->from('noreply.ipl2020@gmail.com','Wisebrix');
                 $message->to('emm@inframall.net');
                 $message->to('ti1@inframall.net');
                // $message->to('vkv@inframall.net');
                //$message->to('ti1@inframall.net');
                 $message->subject('Welcome to Wisebrix');
             }) ;*/
       $resp=array('Success'=>true);
        return $resp;
    }
    else{
        if($countEmail!=0)
        {
            $resp=array('Success'=>false,'Error'=>'This email is already registered. Please register with a new email.');
            return $resp;
        }
       else if($countContact!=0)
        {
            $resp=array('Success'=>false,'Error'=>'This number is already registered. Please register with a new number.');
            return $resp;
        }
       
    }
    
        
       
    }

    public function biws_getAllLeads()
    {
        $leads=\DB::table('sales_lead')
         ->leftjoin('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
        
        ->leftjoin('location', 'location.Loc_ID','=','sales_customer.Loc_ID')
        ->leftjoin('customer_status','customer_status.Cust_Status_ID','=','sales_lead.Cust_Status_ID')
      ->leftjoin('lead_status','lead_status.Lead_Status_ID','=','sales_lead.Lead_StatusID')
        ->where('sales_lead.Flag',2)->where('sales_lead.DeleteFlag',0)
        ->orderBy('sales_lead.Lead_ID','desc')->get();
        $resp=array($leads);
        return $resp;
    }
    public function biws_AllLeadsByCust($id)
    {
        /*$leads=\DB::table('sales_lead')
        ->leftjoin('service_work','service_work.Lead_ID','=','sales_lead.Lead_ID')
        
        ->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
        //->leftjoin('customer_status','customer_status.Cust_Status_ID','=','service_work.Cust_Status_ID')
        //->leftjoin('location', 'location.Loc_ID','=','sales_customer.Loc_ID')
        ->where('sales_lead.Flag',2)->where('sales_lead.DeleteFlag',0)->where('sales_lead.Cust_ID',$id)
      
        //->select('service_work.Cust_Status_ID')
        ->get();
        $newArray1=[];
        $newArray2=[];
        $newArray=[];
        foreach($leads as $lead)
        {
            if($lead->Cust_Status_ID==null)
            {
                $leadData=\DB::table('sales_lead')
               // ->leftjoin('service_work','service_work.Lead_ID','=','sales_lead.Lead_ID')
                
                ->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
                //->leftjoin('customer_status','customer_status.Cust_Status_ID','=','service_work.Cust_Status_ID')
                ->leftjoin('location', 'location.Loc_ID','=','sales_customer.Loc_ID')
                ->where('sales_lead.Flag',2)->where('sales_lead.DeleteFlag',0)->where('sales_lead.Cust_ID',$id)
              
                ->select('sales_lead.Lead_ID','Cust_ID','Lead_StatusID','Cust_FirstName','Cust_MidName','Cust_LastName',
                'Loc_Name','PinCode AS Loc_Name','Proj_Details AS WorkDetail',\DB::raw('"Enquiry Received" AS Cust_Status_Name') )->get();
                array_push($newArray1,$leadData);
            }
            else 
            {
                $workData=\DB::table('sales_lead')
               ->leftjoin('service_work','service_work.Lead_ID','=','sales_lead.Lead_ID')
                
                ->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
                ->leftjoin('customer_status','customer_status.Cust_Status_ID','=','service_work.Cust_Status_ID')
                ->leftjoin('location', 'location.Loc_ID','=','sales_customer.Loc_ID')
                ->where('sales_lead.Flag',2)->where('sales_lead.DeleteFlag',0)->where('sales_lead.Cust_ID',$id)
              
                ->select('sales_lead.Lead_ID','Cust_ID','Lead_StatusID','service_work.Cust_Status_ID','Cust_ID','service_work.Work_ID',
                'WorkSpec','Work_Type','Cust_FirstName','Cust_MidName','Cust_LastName','DeleteFlag','Site_Analysis_Date','WorkStatus',
                'ActualSite_Analysis_Date','SiteAnalysis_Flag','Loc_Name','WorkDetail', 'Cust_Status_Name')->get();
                array_push($newArray2,$workData);
            }
           
        }
    array_merge($newArray1,$newArray2);
        
        $resp=array($newArray1);
        return $resp;*/


    /*$leads=\DB::table('sales_lead')
    ->leftjoin('service_work','service_work.Lead_ID','=','sales_lead.Lead_ID')
    
    ->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
    ->leftjoin('customer_status','customer_status.Cust_Status_ID','=','service_work.Cust_Status_ID')
    ->leftjoin('location', 'location.Loc_ID','=','sales_customer.Loc_ID')
    ->where('sales_lead.Flag',2)->where('sales_lead.DeleteFlag',0)->where('sales_lead.Cust_ID',$id)
  
    ->select('sales_lead.Lead_ID','Cust_ID','Lead_StatusID','service_work.Cust_Status_ID','Cust_ID','service_work.Work_ID',
    'WorkSpec','Work_Type','Cust_FirstName','Cust_MidName','Cust_LastName','DeleteFlag','Site_Analysis_Date','WorkStatus',
    'ActualSite_Analysis_Date','SiteAnalysis_Flag','Loc_Name','PinCode',
    \DB::raw('(CASE WHEN service_work.Cust_Status_ID>=1 THEN service_work.WorkDetail ELSE Proj_Details END) AS WorkDetail'),
    \DB::raw('(CASE WHEN service_work.Cust_Status_ID>=1 THEN customer_status.Cust_Status_Name ELSE "Enquiry Received" END) AS Cust_Status_Name'),
    \DB::raw('(CASE WHEN service_work.Cust_Status_ID>=1 THEN Loc_Name ELSE PinCode END) AS Loc_Name'))
       // 'Proj_Details AS WorkDetail','1 AS Cust_Status_ID','Enquiry Received AS Cust_Status_Name')
    ->get();
    
    

$resp=array($leads);
        return $resp;*/


   

        
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
        
$leads=\DB::table('sales_lead')
->leftjoin('service_work','service_work.Lead_ID','=','sales_lead.Lead_ID')
//->leftjoin('work_tendering','work_tendering.Work_ID','=','service_work.Work_ID')
->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
->leftjoin('customer_status','customer_status.Cust_Status_ID','=','service_work.Cust_Status_ID')
->leftjoin('location', 'location.Loc_ID','=','sales_customer.Loc_ID')
->where('sales_lead.Flag',2)->where('sales_lead.DeleteFlag',0)->where('sales_lead.Cust_ID',$id)

->select('sales_lead.Lead_ID','Cust_ID','Lead_StatusID','service_work.Cust_Status_ID','Cust_ID','service_work.Work_ID',
'WorkSpec','Work_Type','Cust_FirstName','Cust_MidName','Cust_LastName','DeleteFlag','Site_Analysis_Date','WorkStatus',
'ActualSite_Analysis_Date','SiteAnalysis_Flag','Loc_Name','sales_customer.PinCode','WorkDetail','Proj_Details','Cust_Status_Name')->get();

foreach($leads as $lead)
{
if($lead->Cust_Status_ID == null)
{
    $lead->Cust_Status_Name='Enquiry Received';
    $lead->WorkDetail=$lead->Proj_Details;
   $lead->Loc_Name=$lead->PinCode;
   
}
/*if($lead->Cust_Status_ID ==12)
{
$woSignUpStatus=\DB::table('work_tendering')->where('Work_ID',$lead->Work_ID)->where('SelectStatus',1)->select('Assoc_WOSignUp_Flag','Cust_WOSignUp_Flag')->get();
if($woSignUpStatus->Cust_WOSignUp_Flag ==1 && $woSignUpStatus->Assoc_WOSignUp_Flag==0)
{
    $lead->Cust_Status_Name='Contractor Signup Pending';
}

else
{
    $lead->Cust_Status_Name=$lead->Cust_Status_Name;
}
}*/
else{
$lead->Cust_Status_Name=$lead->Cust_Status_Name;
$lead->WorkDetail=$lead->WorkDetail;
$lead->Loc_Name=$lead->Loc_Name;

}
}

$resp=array($leads);
return $resp;
    }
    public function biws_getWorkTenderDetails($id)
    {
        $leads=\DB::table('service_work')
        ->join('sales_lead','service_work.Lead_ID','=','sales_lead.Lead_ID')
        ->leftjoin('work_tendering','work_tendering.Work_ID','=','service_work.Work_ID')
        ->where('work_tendering.ReqSiteVisit_Flag',1)
        ->where('sales_lead.Flag',2)->where('sales_lead.Cust_ID',$id)
        ->select('service_work.Work_ID','Sch_Site_Visit','Act_Site_Visit', 'sales_lead.Lead_ID')
       
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
        ->select('sales_customer.Customer_ID', 'sales_customer.Cust_FirstName', 'contacts.Contact_email')->get();
        $resp=array($customer);
        return $resp;

    }
    public function biws_getAssocID($user)
 {
    $Assoc=\DB::table('associate')
    ->join('contacts','contacts.Contact_ID','=','associate.Contact_ID')
        //->where('associate.Online_Flag',1)
        ->where('contacts.Contact_phone',$user)
        ->select('associate.Assoc_ID', 'associate.Assoc_FirstName','Contact_email')->get();
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
        $contactID=\DB::table('contacts')->insertGetID(array('Contact_phone'=>$username,'Contact_name'=>$contact, 'Contact_email'=>$email));
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
      ->join('users','users.username','=','contacts.Contact_phone')
       ->where('associate.Online_Flag',1)->where('contacts.Contact_phone','=',$username)
       ->select('associate.Assoc_ID', 'associate.Assoc_FirstName','associate.Assoc_MiddleName','associate.Assoc_LastName', 'users.Role_ID')->first();
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
        ->leftjoin ('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')
         ->leftjoin('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
        
        ->leftjoin('location', 'location.Loc_ID','=','sales_customer.Loc_ID')
        ->leftjoin('customer_status','customer_status.Cust_Status_ID','=','sales_lead.Cust_Status_ID')
        ->leftjoin('work_status', 'work_status.Work_Status_ID','=','service_work.WorkStatus')
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
    $CustID=\DB::table('sales_lead')->where('Lead_ID',$values['lead_ID'])->pluck('Cust_ID');
            
           
                $lastWorkID=\DB::table('service_work')->where('Work_ID','<',10000)->orWhere('Work_ID','>',20000)->orderBy('Work_ID','DESC')->first();
            $insertID=$lastWorkID->Work_ID +1;
    
            if($values['lead_ID']==0)
            {
                $work=\DB::table('service_work')->insert(array('Work_ID'=>(int)$insertID,'Lead_ID'=>$values['lead'],'Status_ID' => 2,'WorkStatus'=>2,
                'Segment_ID' => $values['ser'], 'Service_ID' => $comma_separated,
                'Category' => $values['category'],'Work_Type'=>$values['worktype'],
                'WorkDetail' => $values['workDetails'], 'WorkSpec' => $values['workSpec'], 'Comments'=> $values['workcomments'], 'Assigned_To'=>'PMQA', 'AssignedDept'=>'PMQA', 'RemoveFlag'=>1,'Cust_Status_ID'=>1));
                
                if(!empty($work))
                {
                    $work_history=\DB::table('work_history')-> insert(array('Lead_ID' =>$values['lead'], 'Status_ID' =>2,'WorkStatus'=>2,
                     'WorkDetail' => $values['workDetails'], 'WorkSpec' => $values['workSpec'],'Comments'=> $values['workcomments'], 'Work_ID' => (int)$insertID)); 
                    $access=\DB::table('work_access_table')->insert(array('Work_ID'=> (int)$insertID , 'PMQA'=>'PMQA'));
                    $work_create_date=\DB::table('work_timeline')->insert(array('Work_ID'=>(int)$insertID, 'Work_Attrb_ID'=>13, 'Value'=>$today));
                    $work_limit=\DB::table('work_timeline')->insert(array('Work_ID'=>(int)$insertID, 'Work_Attrb_ID'=>38, 'Value'=>1));
    $updateLead=\DB::table('sales_lead')->where('Lead_ID',$values['lead'])->update(array('Lead_StatusID'=>3, 'Loc_ID'=>$values['loc']));
    $updateLoc=\DB::table('sales_customer')->where('Customer_ID',$CustID[0])->update(array('Loc_ID'=>$values['loc']));
                    
                }
            }
             if($values['lead_ID']!=0)
            {
                $work=\DB::table('service_work')->insert(array('Work_ID'=>(int)$insertID,'Lead_ID'=>$values['lead_ID'],'Status_ID' => 2,'WorkStatus'=>1,
                'Segment_ID' => $values['ser'], 'Service_ID' => $comma_separated,
                'Category' => $values['category'],'Work_Type'=>$values['worktype'],
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
                $updateLoc=\DB::table('sales_customer')->where('Customer_ID',$CustID[0])->update(array('Loc_ID'=>$values['loc']));
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
        $notify_user_id=\DB::table('sales_customer')->join('sales_lead','sales_lead.Cust_ID','=','sales_customer.Customer_ID')
       // ->join('contacts','contacts.Contact_ID','=','sales_customer.Contact_ID')
        ->join('users','users.id','=','sales_customer.User_ID')->where('sales_lead.Lead_ID',$leadID[0])
        ->select('id')->first();
        $notify_user=User::where('id', $notify_user_id->id)->get();
        $tid=\DB::table('work_tendering')->where('Work_ID',$values['work'])->where('ReqSiteVisit_Flag',1)->pluck('WorkTender_ID');
        if($values['typeID']==1)
        {
            $updateWork=\DB::table('service_work')->where('Work_ID',$values['work'])->update(array('Site_Analysis_Date'=>$expSiteDate->format('Y-m-d'),'WorkStatus'=>2,'Cust_Status_ID'=>2));
           
  //  LoginUser::find(1)->notify(new SiteVisitShceduled());
 
    foreach($notify_user as $user)
    {
        \Notification::send($user,new siteVisitScheduledDB($expSiteDate->modify('+1 day')));
\Notification::route('mail',$user['email'])->notify(new SiteVisitShceduled($expSiteDate->modify('+1 day')));
    }
  
  
            //$updateLead=\DB::table('sales_lead')->where('Lead_ID',$leadID[0])->update(array());
        }
        else if($values['typeID']==2)
        {
            $updateWork=\DB::table('service_work')->where('Work_ID',$values['work'])->update(array('ActualSite_Analysis_Date'=>$actSiteDate->format('Y-m-d'),'WorkStatus'=>10, 'SiteAnalysis_Flag'=>1,'Cust_Status_ID'=>4));
           // User::find(1)->notify(new SiteVisitShceduled());
            //$updateLead=\DB::table('sales_lead')->where('Lead_ID',$leadID[0])->update(array());
        }
        else if($values['typeID']==3)
        {
$updateTender=\DB::table('work_tendering')->where('WorkTender_ID',$tid[0])->update(array('Sch_Site_Visit'=>$expAssocVisit->format('Y-m-d'),'Assoc_Status'=>5));
$updateWork=\DB::table('service_work')->where('Work_ID',$values['work'])->update(array('WorkStatus'=>18,'Cust_Status_ID'=>9));

//Customer Notification
$notify_user_id=\DB::table('sales_customer')->join('sales_lead','sales_lead.Cust_ID','=','sales_customer.Customer_ID')
->join('contacts','contacts.Contact_ID','=','sales_customer.Contact_ID')
->join('users','users.username','=','contacts.Contact_phone')->where('sales_lead.Lead_ID',$leadID[0])
->select('id')->first();
$notify_user=User::where('id', $notify_user_id->id)->get();
                    foreach($notify_user as $user)
                    {
            \Notification::send($user,new AssocSiteVisitDB($expAssocVisit->format('Y-m-d')));
            \Notification::route('mail',$user['email'])->notify(new AssocSiteVisitEmail($expAssocVisit->format('Y-m-d')));
                    }
            //$updateLead=\DB::table('sales_lead')->where('Lead_ID',$leadID[0])->update(array());
        }
        else if($values['typeID']==4)
        {
            $updateTender=\DB::table('work_tendering')->where('WorkTender_ID',$tid[0])->update(array('Act_Site_Visit'=>$actAssocVisit->format('Y-m-d'),'Assoc_Status'=>6));
            $updateWork=\DB::table('service_work')->where('Work_ID',$values['work'])->update(array('WorkStatus'=>6,'Cust_Status_ID'=>10));
            //$updateLead=\DB::table('sales_lead')->where('Lead_ID',$leadID[0])->update(array());
        }
        $resp=array('Success'=>true, 'User'=>LoginUser::find(1));
        return $resp;
        
    }
    
      public function biws_getOneWork($id)
     {
         $oneWork=\DB::table('service_work')
         ->leftjoin('segment','segment.Segment_ID','=','service_work.Segment_ID')
         ->leftjoin('services','services.Service_ID','=','service_work.Service_ID')
         //left->join('work_color_status', 'work_color_status.Color_StatusID','=','service_work.Generate_Work_Status')
         ->join ('sales_lead', 'sales_lead.Lead_ID','=','service_work.Lead_ID')
         ->leftjoin('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
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
       // $updateWork=\DB::table('service_work')->where('Work_ID',$data['param1'])->update(array('Cust_Status_ID'=>4));
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
	->update(array('AssignedDept'=> 'BI', 'Assigned_To'=>'BID', 'WorkStatus'=>'3', 'Update_Status'=>2,'Est_Flag'=>1,'Cust_Status_ID'=>6));
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
            //Notification
            
        $online_assocs=\DB::table('work_tendering')->where('Work_ID',$id)->where('Online_Flag',1)
        ->select('Assoc_ID')->get();
               
                foreach($online_assocs as $assoc)
                    {
                        $assoc_user_id=\DB::table('associate')->join('contacts','contacts.Contact_ID','=','associate.Contact_ID')->join('users','users.username','=','contacts.Contact_phone')
                        ->where('associate.Assoc_ID',$assoc->Assoc_ID)->select('users.id')->first();
                        $notify_user=User::where('id', $assoc_user_id->id)->get();
                        foreach($notify_user as $user)
                    {
                       
                        \Notification::send($user,new QuotesReceivedDB());
                \Notification::route('mail',$user['email'])->notify(new QuotesReceivedEmail());
                    }
                    }
            
		
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
public function biws_getOnlineAssocs($id)
{
    $t_assocs=\DB::table('work_tendering')->where('work_tendering.Work_ID', $id)
    ->join('associate', 'associate.Assoc_ID','=','work_tendering.Assoc_ID')
    ->where('work_tendering.TenderFinish_Flag',1)->select('work_tendering.Assoc_ID')->get();
    //->where('work_tendering.updateFlag',1)
	
    $onlineAssoc=\DB::table('associate')
    ->leftjoin('contacts', 'contacts.Contact_ID','=','associate.Contact_ID')
    ->leftjoin('associate_details','associate_details.Assoc_ID','=','associate.Assoc_ID')
    ->leftjoin('location', 'location.Loc_ID','=','associate_details.Loc_ID')
    //->where('Assoc_Status','4')
    ->where('associate.Online_Flag',1)->get();
    $newArray=[];
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
       

    }
    
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
    ->where('work_tendering.DeleteFlag',0)
    ->select('WorkSpec','service_work.Work_ID','Loc_Name','Sch_Site_Visit','Act_Site_Visit','Tender_Status_Name','Tender_Status_ID','WorkDetail','Assoc_FirstName','Assoc_MiddleName','Assoc_LastName','Work_Type')->get();

    $resp=array($works);
    return $resp;
}
public function biws_pushTenderToCust(Request $r)
{
    $values = Request::json()->all();
    $id=$values['param1'];
    $items=$values['param2'];
    //For online tenders
   
      
	foreach($items as $value)
	{
$tenderID=\DB::table('work_tendering')->where('Work_ID',$id)->where('Assoc_ID', $value['name'])
->update(array('pushTender_Flag'=>1));
    }

    /*foreach($items as $value)
	{
$online_assocs=\DB::table('work_tendering')->where('Work_ID',$id)->where('pushTender_Flag', 1)->where('Online_Flag',1)
->select('Assoc_ID')->get();
       
        foreach($online_assocs as $assoc)
            {
                $assoc_user_id=\DB::table('associate')->join('contacts','contacts.Contact_ID','=','associate.Contact_ID')->join('users','users.username','=','contacts.Contact_phone')
                ->where('associate.Assoc_ID',$assoc->Assoc_ID)->select('users.id')->first();
                $notify_user=User::where('id', $assoc_user_id->id)->get();
                foreach($notify_user as $user)
            {
               
                \Notification::send($user,new QuotesReceivedDB());
        \Notification::route('mail',$user['email'])->notify(new QuotesReceivedEmail());
            }
            }
    }*/
    $notify_user_id=\DB::table('service_work')
    ->join('sales_lead','service_work.Lead_ID','=','sales_lead.Lead_ID')
    ->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
    ->join('contacts','contacts.Contact_ID','=','sales_customer.Contact_ID')
    ->join('users','users.username','=','contacts.Contact_phone')->where('service_work.Work_ID',$id)
    ->select('id')->first();
    $notify_users=User::where('id', $notify_user_id->id)->get();
                        foreach($notify_users as $user)
                        {
                            \Notification::send($user,new QuotesReceivedDB());
                            \Notification::route('mail',$user['email'])->notify(new QuotesReceivedEmail());
                            
                        }
                        

    $leadID=\DB::table('service_work')->where('Work_ID',$id)->pluck('Lead_ID');
//$updateLead=\DB::table('sales_lead')->where('Lead_ID',$leadID[0])->update(array());
$updateWorkStatus=\DB::table('service_work')->where('Work_ID',$id)->update(array('WorkStatus'=>15,'Cust_Status_ID'=>7));
    $resp=array('Success'=>true);
    return $resp;


    
}
public function biws_getSelectedTenderAssocs($id)
{
    
    $assocs=\DB::table('work_tendering')
    ->join('associate', 'associate.Assoc_ID', '=','work_tendering.Assoc_ID')
     ->leftjoin('service_work', 'service_work.Work_ID', '=','work_tendering.Work_ID')
->join('associate_details', 'associate_details.Assoc_ID','=','associate.Assoc_ID')
->join('contacts', 'contacts.Contact_ID', '=','associate.Contact_ID')
->join('address','address.Address_ID','=','associate.Address_ID')
->leftjoin('status', 'status.Assoc_Status','=','associate.Assoc_Status')
->where('work_tendering.Work_ID',$id)->where('work_tendering.DeleteFlag',0)->where('pushTender_Flag',1)
->select('work_tendering.WorkTender_ID','work_tendering.Work_ID','work_tendering.Assoc_ID','TotalQuote','SelectStatus','Tender_Rec_Flag','Payment_Terms','work_tendering.Work_Days','Sch_Site_Visit','Act_Site_Visit','TenderFinish_Flag','pushTender_Flag','ReqSiteVisit_Flag','associate.Assoc_Status','Assoc_FirstName','Assoc_MiddleName','Assoc_LastName','Assoc_Code','WorkDetail','WorkSpec','ActualSite_Analysis_Date','AssocVisitFlag','Rating','Payment_Flag','Address_town')
    ->get();
    
    foreach($assocs as $assoc)
    {
        if($assoc->Payment_Flag ==0)
        {
            $assoc->TotalQuote=10;
        }
        else 
        {
            $assoc->TotalQuote= $assoc->TotalQuote;
        }
    }
    $resp=array($assocs);
    return $resp;
}
public function biws_getSelectedTenderItems($id)
{
    
    $tenders=\DB::table('work_tender_details_lab')
    ->join('work_tendering', 'work_tendering.WorkTender_ID','=','work_tender_details_lab.WorkTender_ID')
    ->join('serv_line_items', 'serv_line_items.LineItem_ID','=','work_tender_details_lab.LineItem_ID')
    ->join('units', 'units.Unit_ID','=','serv_line_items.UnitID')
   ->where('work_tender_details_lab.WorkTender_ID',$id)
   ->select('LineItem_Name','serv_line_items.LineItem_ID','work_tender_details_lab.Comments','work_tender_details_lab.Quantity','Unit_Code','work_tender_details_lab.Value','work_tender_details_lab.Rate','TenderFinish_Flag','WorkTenderLab_ID','work_tendering.WorkTender_ID')
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
    //$updateLead=\DB::table('sales_lead')->where('Lead_ID',$leadID[0])->update(array('Cust_Status_ID'=>8));
    $updateWorkStatus=\DB::table('service_work')->where('Work_ID',$wid)->update(array('WorkStatus'=>17,'Cust_Status_ID'=>8));

    //Cust Notification
    $CustName=\DB::table('service_work')->join('sales_lead','sales_lead.Lead_ID','=','service_work.Lead_ID')
    ->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
    ->where('service_work.Work_ID',$wid)->select('sales_customer.Cust_FirstName','sales_customer.Cust_LastName')->first();
    $notify_users=User::whereIn('id', $this->notifiables)->get();
                        foreach($notify_users as $user)
                        {
                \Notification::send($user,new RequestAssocVisitDB($CustName->Cust_FirstName . $CustName->Cust_LastName, $wid));
                \Notification::route('mail',$user['email'])->notify(new RequestAssocVisitEmail($CustName->Cust_FirstName . $CustName->Cust_LastName, $wid));
                        }
                        //Assoc Notification
                        //Check if associate is online or offline
                        $chkAssociate=\DB::table('work_tendering')->where('Assoc_ID',$associd)->where('Online_Flag',1)->get();
                        if(count($chkAssociate)!=0)
                        {
                            $assoc_user_id=\DB::table('associate')
                            // ->join('contacts','contacts.Contact_ID','=','associate.Contact_ID')
                             ->join('users','users.id','=','associate.User_ID')
                                    ->where('associate.Assoc_ID',$associd)->select('users.id')->first();
                                    $notify_user=User::where('id', $assoc_user_id->id)->get();
                                    foreach($notify_user as $assoc)
                                {
                                   
                                    \Notification::send($assoc,new CustomerIntrestDB());
                            \Notification::route('mail',$assoc['email'])->notify(new CustomerIntrestEmail());
                                }
                        }
       

    $resp=array($tids);
    return $resp;
}
public function biws_confirmAssoc(Request $r)
{
    $values = Request::json()->all();
    $tid=$values['param1'];
    $wid=\DB::table('work_tendering')->where('WorkTender_ID', $tid)->pluck('Work_ID');
    $assocID=\DB::table('work_tendering')->where('WorkTender_ID', $tid)->pluck('Assoc_ID');
    $custName=\DB::table('service_work')->join('sales_lead','sales_lead.Lead_ID','=','service_work.Lead_ID')
    ->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
    ->where('service_work.Work_ID',$wid[0])->select('sales_customer.Cust_FirstName','sales_customer.Cust_LastName')->first();
    
    $confirmAssoc=\DB::table('work_tendering')->where('WorkTender_ID', $tid)->update(array('SelectStatus'=>1,'Assoc_Status'=>7));
    if($confirmAssoc)
    {
        $leadID=\DB::table('service_work')->where('Work_ID',$wid[0])->pluck('Lead_ID');
        $updateLead=\DB::table('sales_lead')->where('Lead_ID',$leadID[0])->update(array('Cust_Status_ID'=>11));
        $updateWorkStatus=\DB::table('service_work')->where('Work_ID',$wid[0])->update(array('WorkStatus'=>6, 'InitWO_Flag'=>1,'Cust_Status_ID'=>11));

        //PMQA Notification
        $notify_user=User::where('id', $this->notifiables)->get();
                foreach($notify_user as $user)
            {
                \Notification::send($user,new WorkConfirmDB($wid[0],$custName->Cust_FirstName.' ' .$custName->Cust_LastName,1));
                \Notification::route('mail',$user['email'])->notify(new WorkConfirmEmail($wid[0],$custName->Cust_FirstName.' ' .$custName->Cust_LastName,1));
            }

            //AssocNotification
            $assoc_user_id=\DB::table('associate')->join('contacts','contacts.Contact_ID','=','associate.Contact_ID')->join('users','users.username','=','contacts.Contact_phone')
                ->where('associate.Assoc_ID',$assocID[0])->select('users.id')->first();
                $notify_assoc_user=User::where('id', $assoc_user_id->id)->get();
                foreach($notify_assoc_user as $assoc)
            {
               
                \Notification::send($assoc,new WorkConfirmDB($wid[0],$custName->Cust_FirstName.' ' .$custName->Cust_LastName,2));
        \Notification::route('mail',$assoc['email'])->notify(new WorkConfirmEmail($wid[0],$custName->Cust_FirstName.' ' .$custName->Cust_LastName,2));
            }
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
        $updateWorkStatus=\DB::table('service_work')->where('Work_ID',$wid[0])->update(array('WorkStatus'=>16,'Cust_Status_ID'=>7));

 //Notification
 $custName=\DB::table('service_work')->join('sales_lead','sales_lead.Lead_ID','=','service_work.Lead_ID')
 ->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
 ->where('service_work.Work_ID',$wid[0])->select('sales_customer.Cust_FirstName','sales_customer.Cust_LastName')->first();

 $assocName=\DB::table('work_tendering')->join('associate','associate.Assoc_ID','=','work_tendering.Assoc_ID')
 ->where('WorkTender_ID', $tid)->select('associate.Assoc_FirstName','associate.Assoc_LastName')->first();

 $notify_user=User::where('id', $this->notifiables)->get();
                foreach($notify_user as $user)
            {
                \Notification::send($user,new WorkRejectedDB($custName->Cust_FirstName.' ' .$custName->Cust_LastName,$assocName->Assoc_FirstName.' '.$assocName->Assoc_LastName,$wid[0]));
                \Notification::route('mail',$user['email'])->notify(new WorkRejectedDB($custName->Cust_FirstName.' ' .$custName->Cust_LastName,$assocName->Assoc_FirstName.' '.$assocName->Assoc_LastName,$wid[0]));
            }       
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
    ->select('work_tendering.*','location.Loc_Name', 'service_work.Work_Type','service_work.Category')->get();
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
    if($chkWorkDays[0]==null)
    {
        $count=0;
    }
    else
    {
        $count=1;
    }

    $resp=array($count);
    return $resp;
}
public function biws_pushBackToPMA(Request $r)
{
    $values = Request::json()->all();
    $tid=$values['param1'];
    $statusChange=\DB::table('work_tendering')->where('WorkTender_ID',$tid)
    ->update(array('Tender_Rec_Flag'=>1,'TenderFinish_Flag'=>1,'Assoc_status'=>2));
    $assoc_ID=\DB::table('work_tendering')->join('associate','associate.Assoc_ID','=','work_tendering.Assoc_ID')->where('WorkTender_ID',$tid)->select('work_tendering.Assoc_ID','Assoc_FirstName','Assoc_LastName')->first();
   
    $notify_user=User::whereIn('id', $this->notifiables)->get();
    foreach($notify_user as $user)
{
   
    \Notification::send($user,new TenderSubmittedDB($assoc_ID->Assoc_FirstName . $assoc_ID->Assoc_LastName));
\Notification::route('mail',$user['email'])->notify(new TenderSubmittedEmail($assoc_ID->Assoc_FirstName . $assoc_ID->Assoc_LastName));
}
$resp=array('Success'=>true);
return $resp;    
          
  


   
}
public function biws_finishWO(Request $r)
{
    $values = Request::json()->all();
    $leadID=\DB::table('service_work')->where('Work_ID',$values['param1'])->pluck('Lead_ID');
        $updateLead=\DB::table('sales_lead')->where('Lead_ID',$leadID[0])->update(array('Cust_Status_ID'=>12));

    $finish=\DB::table('service_work')->where('Work_ID',  $values['param1'])
    ->update(array('WorkStatus'=>14,'WOSignUp_Flag'=>1,'Cust_Status_ID'=>12 ));//7
//Customer Notification
$notify_user_id=\DB::table('sales_customer')->join('sales_lead','sales_lead.Cust_ID','=','sales_customer.Customer_ID')
        ->join('contacts','contacts.Contact_ID','=','sales_customer.Contact_ID')
        ->join('users','users.username','=','contacts.Contact_phone')->where('sales_lead.Lead_ID',$leadID[0])
        ->select('id')->first();
        $notify_user=User::where('id', $notify_user_id->id)->get();
    
foreach($notify_user as $user)
        {
            \Notification::send($user,new WorkOrderGeneratedDB());
    \Notification::route('mail',$user['email'])->notify(new WorkOrderGeneratedEmail($values['param1']));
        }

        //Assoc Notification
        $assocID=\DB::table('work_tendering')->where('Work_ID', $values['param1'])->where('SelectStatus',1)->pluck('Assoc_ID');
        $assoc_user_id=\DB::table('associate')->join('contacts','contacts.Contact_ID','=','associate.Contact_ID')->join('users','users.username','=','contacts.Contact_phone')
                ->where('associate.Assoc_ID',$assocID[0])->select('users.id')->first();
                $assoc_user=User::where('id', $assoc_user_id->id)->get();
                foreach($assoc_user as $assoc)
                {
                    \Notification::send($assoc,new WorkOrderGeneratedDB());
                    \Notification::route('mail',$assoc['email'])->notify(new WorkOrderGeneratedEmail($values['param1']));
                }
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
    public function biws_chkRejectCount($id)
    {
        $Rejects=\DB::table('work_tendering')->where('Work_ID',$id)
        ->where('SelectStatus',2)->get();
        $count=count($Rejects);
        $resp=array("RejectFlag"=>$count);
        return $resp;
    }
     public function biws_AllOnlineLeadsByCust($id)
    {
        

    /*$leads=\DB::table('sales_lead')
    ->leftjoin('service_work','service_work.Lead_ID','=','sales_lead.Lead_ID')
        ->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
        ->where('sales_lead.Flag',2)->where('sales_lead.DeleteFlag',0)->where('sales_lead.Cust_ID',$id)
  
    ->get();
    
    $newArray=[];
    foreach($leads as $lead)
    {
        if($lead->Cust_Status_ID==null)
        {
            $leadData=\DB::table('sales_lead')
            ->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
            ->leftjoin('location', 'location.Loc_ID','=','sales_customer.Loc_ID')
            ->where('sales_lead.Flag',2)->where('sales_lead.DeleteFlag',0)->where('sales_lead.Cust_ID',$id)
                      ->select('sales_lead.Lead_ID','Cust_ID','Lead_StatusID','Cust_FirstName','Cust_MidName','Cust_LastName',
            'Loc_Name','PinCode AS Loc_Name','Proj_Details AS WorkDetail',\DB::raw('"Enquiry Received" AS Cust_Status_Name') )->get();
            array_push($newArray,$leadData);
        }
        else 
        {
            $workData=\DB::table('sales_lead')
           ->leftjoin('service_work','service_work.Lead_ID','=','sales_lead.Lead_ID')            
            ->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
            ->leftjoin('customer_status','customer_status.Cust_Status_ID','=','service_work.Cust_Status_ID')
            ->leftjoin('location', 'location.Loc_ID','=','sales_customer.Loc_ID')
            ->where('sales_lead.Flag',2)->where('sales_lead.DeleteFlag',0)->where('sales_lead.Cust_ID',$id)
                      ->select('sales_lead.Lead_ID','Cust_ID','Lead_StatusID','service_work.Cust_Status_ID','Cust_ID','service_work.Work_ID',
            'WorkSpec','Work_Type','Cust_FirstName','Cust_MidName','Cust_LastName','DeleteFlag','Site_Analysis_Date','WorkStatus',
            'ActualSite_Analysis_Date','SiteAnalysis_Flag','Loc_Name','WorkDetail', 'Cust_Status_Name')->get();
            array_push($newArray,$workData);
        }
       
    }

    
    $resp=array($newArray);
    return $resp;*/

$leads=\DB::table('sales_lead')
    ->leftjoin('service_work','service_work.Lead_ID','=','sales_lead.Lead_ID')
    
    ->join('sales_customer','sales_customer.Customer_ID','=','sales_lead.Cust_ID')
    ->leftjoin('customer_status','customer_status.Cust_Status_ID','=','service_work.Cust_Status_ID')
    ->leftjoin('location', 'location.Loc_ID','=','sales_customer.Loc_ID')
    ->where('sales_lead.Flag',2)->where('sales_lead.DeleteFlag',0)->where('sales_lead.Cust_ID',$id)
  
    ->select('sales_lead.Lead_ID','Cust_ID','Lead_StatusID','service_work.Cust_Status_ID','Cust_ID','service_work.Work_ID',
    'WorkSpec','Work_Type','Cust_FirstName','Cust_MidName','Cust_LastName','DeleteFlag','Site_Analysis_Date','WorkStatus',
    'ActualSite_Analysis_Date','SiteAnalysis_Flag','Loc_Name','PinCode','WorkDetail','Proj_Details','Cust_Status_Name',
    'Work_Type')->get();

foreach($leads as $lead)
{
    if($lead->Cust_Status_ID == null)
    {
        $lead->Cust_Status_Name='Enquiry Received';
        $lead->WorkDetail=$lead->Proj_Details;
       $lead->Loc_Name=$lead->PinCode;
       
    }
  else{
    $lead->Cust_Status_Name=$lead->Cust_Status_Name;
    $lead->WorkDetail=$lead->WorkDetail;
   $lead->Loc_Name=$lead->Loc_Name;

  }
}

$resp=array($leads);
return $resp;

    }
    public function addNewUser(Request $r)
    {
        $userData = array('User_Name'=>'test2','username' => 'Me2', 'Role_ID' => '11');
        LoginUser::create($userData);
       
$resp=array('Success'=>true);
return $resp;

    }
    public function pdfview($id)
{
//$items = DB::table("items")->get();
//view()->share('items',$items);


$pdf = PDF::loadView('downloadpdf')->setPaper('a4');
//$pdf->stream('pdfview.pdf');
return $pdf->download('pdfview.pdf');

//return view('pdfview');
}
public function cust_woSigned(Request $r)
{
    $values = Request::json()->all();
    /*$leadID=\DB::table('service_work')->where('Work_ID',$values['param1'])->pluck('Lead_ID');
    $updateLead=\DB::table('sales_lead')->where('Lead_ID',$leadID[0])->update(array('Cust_Status_ID'=>13));

$finish=\DB::table('service_work')->where('Work_ID',  $values['param1'])
->update(array('WorkStatus'=>13,'Cust_Status_ID'=>13 ,'WOSignedUp_Flag'=>1));*/
$assocSign=\DB::table('work_tendering')->where('Work_ID',$values['param1'])->where('SelectStatus',1)
->pluck('Assoc_WOSignUp_Flag');
$custSign=\DB::table('work_tendering')->where('Work_ID',$values['param1'])->where('SelectStatus',1)
->update(array('Cust_WOSignUp_Flag'=>1));

     $changeStatus=\DB::table('service_work')->where('Work_ID',  $values['param1'])
->update(array('WorkStatus'=>14,'Cust_Status_ID'=>13));

if($assocSign[0]==1)
{
    $finish=\DB::table('service_work')->where('Work_ID',  $values['param1'])
->update(array('WorkStatus'=>13,'Cust_Status_ID'=>15 ,'WOSignedUp_Flag'=>1));
}

//Notification

$notify_user=User::where('id', $this->notifiables)->get();
foreach($notify_user as $user)
        {
            \Notification::send($user,new SignUpCompletedDB($values['param1'],1));
    \Notification::route('mail',$user['email'])->notify(new SignUpCompletedEmail($values['param1'],1));
        }
$resp1=array('Success'=>true);
return $resp1;


}
public function assoc_woSigned(Request $r)
{
    $values = Request::json()->all();
    /*$leadID=\DB::table('service_work')->where('Work_ID',$values['param1'])->pluck('Lead_ID');
    $updateLead=\DB::table('sales_lead')->where('Lead_ID',$leadID[0])->update(array('Cust_Status_ID'=>13));

$finish=\DB::table('service_work')->where('Work_ID',  $values['param1'])
->update(array('WorkStatus'=>13,'Cust_Status_ID'=>13 ,'WOSignedUp_Flag'=>1));*/
$custSign=\DB::table('work_tendering')->where('Work_ID',$values['param1'])->where('SelectStatus',1)
->pluck('Cust_WOSignUp_Flag');
$assocSign=\DB::table('work_tendering')->where('Work_ID',$values['param1'])->where('SelectStatus',1)
->update(array('Assoc_WOSignUp_Flag'=>1));
$changeStatus=\DB::table('service_work')->where('Work_ID',  $values['param1'])
->update(array('WorkStatus'=>14,'Cust_Status_ID'=>13));
if($custSign[0]==1)
{
    $finish=\DB::table('service_work')->where('Work_ID',  $values['param1'])
->update(array('WorkStatus'=>13,'Cust_Status_ID'=>15 ,'WOSignedUp_Flag'=>1));
}

//Notification
$notify_user=User::where('id', $this->notifiables)->get();
foreach($notify_user as $user)
        {
            \Notification::send($user,new SignUpCompletedDB($values['param1'],2));
    \Notification::route('mail',$user['email'])->notify(new SignUpCompletedEmail($values['param1'],2));
        }
$resp1=array("Success"=>true);
return $resp1;
}
public function chkWOSigned($id)
{
    $chkSignWork=\DB::table('service_work')->where('Work_ID',$id)->pluck('WOSignedUp_Flag');
    $chkAllSign=\DB::table('work_tendering')->where('Work_ID',$id)->where('SelectStatus',1)->select('Cust_WOSignUp_Flag','Assoc_WOSignUp_Flag')->get();
    $resp=array("WOSign"=>$chkSignWork[0],$chkAllSign
    // "Assoc_Sign"=>$chkAllSign[0]['Assoc_WOSignUp_Flag'],"Cust_Sign"=>$chkAllSign[0]['Cust_WOSignUp_Flag']
);
    return $resp;
}
public function getWorkDays($id)
{
    $chkWorkDays=\DB::table('work_tendering')->where('WorkTender_ID',$id)->pluck('Work_Days');
   
    $resp=array($chkWorkDays[0]);
    return $resp;
}
    public function getItemRate($id)
    {
        $rate=\DB::table('work_tender_details_lab')
        ->where('WorkTenderLab_ID', $id)->pluck('Rate');
        $resp=array($rate[0]);
        return $resp;
    }
    public function getStatusNames($id)
    {
        
        $Status_ID=\DB::table('work_tendering')
        ->join('service_work','service_work.Work_ID','=','work_tendering.Work_ID')
        ->join('customer_status','customer_status.Cust_Status_ID','=','service_work.Cust_Status_ID')
        ->where('work_tendering.Work_ID',$id)->where('work_tendering.SelectStatus',1)
        ->where('customer_status.Cust_Status_ID','>=',11)
        ->select('Cust_Status_Name')
        ->get();

        
        $resp=array($Status_ID);
        return $resp;

    }
    public function getAssocStatus($aid,$wid)
    {
        $status=\DB::table('work_tendering')
        ->leftjoin('assoc_tender_status','assoc_tender_status.Tender_Status_ID','=','work_tendering.Assoc_Status')
        ->where('work_tendering.Work_ID',$wid)->where('Assoc_ID',$aid)
        ->where('work_tendering.Assoc_Status','!=',5)
        ->where('work_tendering.Assoc_Status','!=',6)
       
        ->select('Tender_Status_Name')->get();
      
        $resp=array($status);
        return $resp;
    }
    public function getUserDetails($id)
    {
        //$username=\DB::table('users')->pluck('username');
        $details=\DB::table('users')
        ->join('sales_customer','sales_customer.User_ID','=','users.id')
        ->join('contacts','contacts.Contact_ID','=','sales_customer.Contact_ID')
        ->where('id',$id)->select('username','email','Cust_FirstName','sales_customer.Contact_ID' )
        ->get();
        $resp=array($details);
        return $resp;
    }
    public function saveEmailID(Request $r)
    {
        $values = Request::json()->all();
        $updateEmail=\DB::table('contacts')->where('Contact_ID',$values['contact_id'])->update(array('Contact_email'=>$values['rate']));
        if($updateEmail)
        {
            $resp=array('Success'=>true);
            return $resp;
        }
        else{
            $resp=array('Success'=>false);
            return $resp;
        }
    }
    public function biws_addCustomer(Request $r)
    {
        $values = Request::json()->all();
        $custName=$values['user']['first_values']['custName'];
        $contact=(string)$values['user']['first_values']['contact'];
        $location=$values['user']['first_values']['loc'];
        $email=$values['user']['first_values']['email'];
        $password=$values['pwd'];
        $pwd=$this->biws_getHash($password);
      /*  $chkContact=\DB::table('users')->where('username',$contact)->get();
        $chkEmail=\DB::table('users')->where('email',$email)->get();
        $countContact=count($chkContact);
        $countEmail=count($chkEmail);
        if($countContact==0 && $countEmail==0)
        {*/
            $contactID=\DB::table('contacts')->insertGetID(array('Contact_name'=>$custName,'Contact_phone'=>$contact, 'Contact_email'=>$email));

           

            $userData=array('User_Name'=>$custName,'username'=>$contact, 'password'=>$pwd, 'Role_ID'=>16, 'email'=>$email);
            $signUp=\DB::table('users')->insertGetID($userData);
            $signUp1=\DB::table('logins')->insertGetID($userData);
            
            $token=auth()->attempt(['username' =>$contact, 'password' => $values['pwd']]);
           $customerID=\DB::table('sales_customer')->insertGetID(array('Cust_FirstName'=>$custName, 'Flag'=>1, 'Contact_ID'=>$contactID, 'User_ID'=>$signUp,'Pincode'=>$location));
        
        
        $customer=\DB::table('sales_customer')
            ->leftjoin('contacts','contacts.Contact_ID','=','sales_customer.Contact_ID')
            ->join('users','users.username','=','contacts.Contact_phone')

->where('sales_customer.Flag',1)->where('contacts.Contact_phone','=',$contact)
->select('sales_customer.Customer_ID', 'sales_customer.Cust_FirstName','users.ID','Contact_phone','Contact_email')->get();
            return response()->json(['token'=>$token,'Success'=>true,'Cust_ID'=>$customer]);
           /* }
            
            else{
                if($countEmail!=0)
                {
                    $resp=array('Success'=>false,'Error'=>'This email is already registered. Please register with a new email.');
                    return $resp;
                }
               else if($countContact!=0)
                {
                    $resp=array('Success'=>false,'Error'=>'This number is already registered. Please register with a new number.');
                    return $resp;
                }
            }*/
    }
  public function testEmailNoti() 
  {
    /*$notify_users=User::whereIn('id', $this->notifiables)->get();*/
    $leadID=\DB::table('service_work')->join('sales_lead','sales_lead.Lead_ID','=','service_work.Lead_ID')
    ->where('Work_ID',214)->pluck('sales_lead.Cust_ID');
    $notify_user_id=\DB::table('users')
    ->join('sales_customer','users.id','=','sales_customer.User_ID')
  
       // ->join('contacts','contacts.Contact_ID','=','sales_customer.Contact_ID')
        ->where('sales_customer.Customer_ID',$leadID[0])
      //  ->select('users.id')->first();
      ->get();
       /* $notify_user=User::where('id', $notify_user_id->id)->get();
        foreach($notify_user as $user)
        {
            \Notification::send($user,new siteVisitScheduledDB(21/2/2021));
    \Notification::route('mail',$user['email'])->notify(new SiteVisitShceduled(21/02/2021));
        }
*/
        $resp=array($leadID[0]);
        return $resp;
                      
  }

  public function chkContactExists($no)
  {
      $exists=\DB::table('users')->where('username',$no)->select('Role_ID')->first();
      foreach($exists as $s)
      {
        if($exists->Role_ID ==11)
        {
          $userDetails=\DB::table('users')->where('username',$no)->leftjoin('associate','associate.User_ID','=','users.id')->get();

        }
        if($exists->Role_ID ==16)
        {
          $userDetails=\DB::table('users')->where('username',$no)->leftjoin('sales_customer','sales_customer.User_ID','=','users.id')
          ->get();
         
        }
       
      }
      
     
      foreach($userDetails as $user)
      {
          $user->Reg_Flag =2;
         
      }
      $resp=array($userDetails);
      return $resp;
          
  }
  public function biws_resetPassword(Request $r)
  {
    $values = Request::json()->all();
    //$contact=(string)$values['user']['contact'];
    
      
if($values['user']['first_values']['Role_ID']==11)
{
    $contact=(string)$values['user']['first_values']['username'];
    $password=$values['pwd'];
   
   $pwd=$this->biws_getHash($password);
$updatePwd=\DB::table('users')->where('username',$contact)->update(array('password'=>$pwd));
$token=auth()->attempt(['username' =>$contact, 'password' => $values['pwd']]);
    $assoc=\DB::table('associate')->join('contacts','contacts.Contact_ID','=','associate.Contact_ID')
    ->join('users','users.username','=','contacts.Contact_phone')
     ->where('associate.Online_Flag',1)->where('contacts.Contact_phone','=',$contact)
     ->select('associate.Assoc_ID', 'associate.Assoc_FirstName','associate.Assoc_MiddleName','associate.Assoc_LastName', 'users.Role_ID')->first();
     return response()->json(['token'=>$token,'Success'=>true,'Assoc'=>$assoc]); 
    
}
if($values['user']['first_values']['Role_ID']==16){
    $contact=(string)$values['user']['first_values']['username'];
    $password=$values['pwd'];
   
   $pwd=$this->biws_getHash($password);
$updatePwd=\DB::table('users')->where('username',$contact)->update(array('password'=>$pwd));
$token=auth()->attempt(['username' =>$contact, 'password' => $values['pwd']]);
    $customer=\DB::table('sales_customer')
    ->leftjoin('contacts','contacts.Contact_ID','=','sales_customer.Contact_ID')
    ->join('users','users.username','=','contacts.Contact_phone')

->where('sales_customer.Flag',1)->where('contacts.Contact_phone','=',$contact)
->select('sales_customer.Customer_ID', 'sales_customer.Cust_FirstName','users.ID','Contact_phone','Contact_email')->get();
    return response()->json(['token'=>$token,'Success'=>true,'Cust_ID'=>$customer]);

}

      $resp=array($values);
      return $resp;
  }
  public function biws_addNewEnquiry(Request $r)
  {
    $values = Request::json()->all();
    $customerID=$values['Cust_ID'];
    $type=$values['first_values']['type'];
    $customer=\DB::table('sales_customer')->where('Customer_ID',$customerID)->select('Pincode','Cust_FirstName')->first();
    if($type==1)
                {
        $plan=$values['first_values']['plan'];
        $area=$values['first_values']['area'];
        $floors=$values['first_values']['floor'];
        $work_Start=$values['first_values']['start'];
        
        $leadID=\DB::table('sales_lead')->insertGetID(array('Cust_ID'=>$customerID,'Lead_StatusID'=>2,'Proj_Details'=>"New Home",'Source_ID'=>8,'Flag'=>2,'Cust_Status_ID'=>1,'PinCode'=>$customer->Pincode));  
                    if($leadID)
                {
                    $notify_users=User::whereIn('id', $this->notifiables)->get();
                   foreach($notify_users as $user)
                   {
                   // \Notification::route('mail', $user->email)->notify(new NewLeadNotification($customer->Cust_FirstName));
                    \Notification::send($user,new addLeadDBNotification($customer->Cust_FirstName));
                   }
                  //  
                  
                  
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
                else{
                    $category=$values['first_values']['category'];
                    $catName=\DB::table('enq_category')->where('Enq_Cat_ID',$category)->pluck('Cat_Name');
                    $leadID=\DB::table('sales_lead')->insertGetID(array('Cust_ID'=>$customerID,'Lead_StatusID'=>2,'Proj_Details'=>$catName[0],'Source_ID'=>8,'Flag'=>2,'Cust_Status_ID'=>1,'PinCode'=>$customer->Pincode));
                    $mapCat=\DB::table('lead_category')->insert(array('Lead_ID'=>$leadID, 'Cat_ID'=>$category));
                    if($leadID)
                {
                    $notify_users=User::whereIn('id', $this->notifiables)->get();
                    foreach($notify_users as $user)
                    {
                    // \Notification::route('mail', $user->email)->notify(new NewLeadNotification($custName));
                     \Notification::send($user,new addLeadDBNotification($custName));
                    }
                   
                   
                }
            }
            $resp=array("Success"=>true);
            return $resp;
        }
    

public function chkUserExists($contact, $email)  
{
    $chkContact=\DB::table('users')->where('username',$contact)->get();
    $chkEmail=\DB::table('users')->where('email',$email)->get();
    $countContact=count($chkContact);
    $countEmail=count($chkEmail);
    if($countContact==0 && $countEmail==0)
    {
        $resp=array('Success'=>true,'Error'=>'This email and contact is already registered. Please register with a new email.');
        return $resp;
    }
    else if($countContact!=0 || $countEmail!=0)
    {
        if($countEmail!=0)
        {
            $resp=array('Success'=>false,'Error'=>'This email is already registered. Please register with a new email.');
            return $resp;
        }
       else if($countContact!=0)
        {
            $resp=array('Success'=>false,'Error'=>'This number is already registered. Please register with a new number.');
            return $resp;
        }
    }
    
    
}

}
