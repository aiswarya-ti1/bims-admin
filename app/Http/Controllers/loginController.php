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

class LoginController extends Controller
{
	public function CreateToken(Request $r)
    {
		//print('hi');
        $creds=Request::only(['username','password']);
	$token=auth()->attempt($creds);
	return response()->json(['token'=>$token]);
    }
	
    public function login(Request $req)
	{
		
		//\DB::transaction(function() use ($req) {
		$user=new login;
		//$role=new Roles;
		//$user_role=new user_roles;
		//$userlog=new user_log_session;
		$inputs = Request::json()->all();
		//$response =array('response'=>'success', $inputs);
		//return $response;
$user->User_Login = $inputs['username'];
$user->User_Password = $inputs['password'];
//$trim=str_replace("'","",$user->User_Login);
//$username=$req->input('username');
//$password=$req->input('password');
//echo $user->User_Login, $user->User_Password ;
//$hash=Hash::make($password);
//echo $password;
$userExists=\DB::table('logins')->where('username',$inputs['username'])->where('ActiveFlag',0)->get();
$count=count($userExists);
if($count==0)
{
	$resp=array('success'=>false);
	return $resp;
}
else{

	$users=\DB::table('logins')->select('password')
	->where('username',$inputs['username'])->first();
	//echo $users;	s
	//$response=array($users,$hash);
	//return $response;
	
		if(Hash::check($user->User_Password, $users->password))
		 {
			$user_details=\DB::table('logins')
			->join ('user_roles', 'logins.ID', '=','user_roles.user_ID')
			->join ('roles','logins.Role_ID','=','roles.Role_ID')
			//->join('user_assoc_rel', 'user_assoc_rel.User_ID','=','logins.User_ID')
			//->join('associate', 'associate.Assoc_ID','=','user_assoc_rel.Assoc_ID')
			//->select('logins.User_Name','logins.User_ID','logins.User_Login','logins.User_Image','logins.User_Status','logins.Reg_Status','roles.Role_Name','logins.User_Email','roles.Role_ID')
			->select('logins.*','roles.*')
		
			->where('logins.username',$inputs['username'])
			->get();
	  //echo $user_details;
//echo $user_details[0]['User_Name'];
			//$user_details = $user_details->row();
			//$result=$data = get_object_vars($user_details);
			
			//echo $user_details[0]->User_Name;
			//echo json($user_details)->toArray();
   //echo $user_details[0]['User_ID'];;
  /*if(!empty($user_details))
   {
 $ip = $_SERVER["REMOTE_ADDR"];
$log=\DB::table('user_log_sessions')->insert(array(
//'user_ID' =>$user_details[0]['User_ID'],
'Log_IP' => $ip));
   }*/
  
	$response=array('response'=>'login success..','success'=>true,'Session added','Log Session added',$user_details);
	return $response;
   }
		
}
		//});
  
}

	
		
		

	public function getUserPermission($userID)
{
	$perm=\DB::table('logins')
				->join('user_roles','logins.User_ID','=','user_roles.User_ID')
				->join('userrole_privillage', 'userrole_privillage.Role_ID', '=', 'user_roles.role_ID')
				->join('menu_previllage', 'menu_previllage.Priv_ID','=','userrole_privillage.Priv_ID')
				->join('menu_details','menu_details.Priv_ID','=','menu_previllage.Priv_ID')
				->where('logins.User_ID',$userID)
                ->where('menu_previllage.Parent_ID', '!=', -1)
				->select('menu_previllage.IsActive','menu_previllage.priv_ID','menu_previllage.Parent_ID','menu_details.*')
				
				->get();
	 $res=array($perm);
	 return $res;
	
}
public function getBIMSPrivillageDetails($userID)
{
	$perm=\DB::table('logins')
				->join('user_roles','logins.ID','=','user_roles.User_ID')
				->join('userrole_privillage', 'userrole_privillage.Role_ID', '=', 'user_roles.role_ID')
				->join('menu_previllage', 'menu_previllage.Priv_ID','=','userrole_privillage.Priv_ID')
				->join('menu_details','menu_details.Priv_ID','=','menu_previllage.Priv_ID')
				->where('logins.ID',$userID)
				->where('menu_previllage.Parent_ID', '!=', -1)
				->where('menu_previllage.TypeFlag',1)
				->select('menu_previllage.IsActive','menu_previllage.priv_ID','menu_previllage.Parent_ID','menu_details.*')
				->get();
	 $res=array($perm);
	 return $res;
	
}
public function getBIWSPermissions($typeID)
{
	/*$perm=\DB::table('biws_category_privillage')
				//->join('user_roles','logins.User_ID','=','user_roles.User_ID')
				->join('logins', 'biws_category_privillage.User_Category', '=', 'logins.User_Category')
				->join('menu_previllage', 'menu_previllage.Priv_ID','=','biws_category_privillage.Priv_ID')
				->join('menu_details','menu_details.Priv_ID','=','menu_previllage.Priv_ID')
				->where('biws_category_privillage.User_Category',$typeID)
				->where('biws_category_privillage.IsActive',1)
                ->where('menu_previllage.Parent_ID', '!=', -1)
				->select('menu_previllage.IsActive','menu_previllage.priv_ID','menu_previllage.Parent_ID','menu_details.*')
				->get();
	 $res=array($perm);
	 return $res;*/

	 $perm=\DB::table('biws_category_privillage')
	 //->join('user_roles','logins.User_ID','=','user_roles.User_ID')
	 ->join('logins', 'biws_category_privillage.User_Category', '=', 'logins.User_Category')
	 ->join('menu_previllage', 'menu_previllage.Priv_ID','=','biws_category_privillage.Priv_ID')
	 ->join('menu_details','menu_details.Priv_ID','=','menu_previllage.Priv_ID')
				->where('logins.User_Category',$typeID)
                ->where('menu_previllage.Parent_ID', '!=', -1)
				->where('biws_category_privillage.IsActive','1')
				->select('biws_category_privillage.IsActive','menu_previllage.priv_ID','menu_previllage.Parent_ID','menu_details.*')
				->distinct('menu_previllage.priv_ID')
				->orderBy('menu_previllage.priv_ID')
				->get();
	 $res=array($perm);
	 return $res;
	
}


	public function logout($value)
	{
		$now = new \DateTime();
		try{
		$logID=\DB::table('user_log_sessions')->max('Log_ID');
		\DB::table('session')->where('UserId',$value)->delete();
		$response=array('response'=>'session cleared','success'=>true);
		$UpdateDetails = \DB::table('user_log_sessions')
            ->where('Log_ID',$logID)
            ->update(array('Log_Ottime'=>$now,'Log_Status'=>200, 'Log_St_Desc'=>'Login Success'));
			$response=array('response'=>'session cleared','success'=>true,'Log table updated');
			//echo 'updated';
			return $response;
		}
		catch(Exception $e) {
        return $e;
    }
	}
public function getHash() //Function to find password in encrypted format only through postman
	{
		$logName=Request::json()->all();
		$hash=Hash::make('assoc');
		$resp=array($hash);
		return $resp;
		
	}
	public function getMenuPermission($id)
	{
		$perm=\DB::table('logins')
				->join('user_roles','logins.User_ID','=','user_roles.User_ID')
				->join('userrole_privillage', 'userrole_privillage.Role_ID', '=', 'user_roles.role_ID')
				->join('menu_previllage', 'menu_previllage.Priv_ID','=','userrole_privillage.Priv_ID')
				->join('menu_details','menu_details.Priv_ID','=','menu_previllage.Priv_ID')
				->where('userrole_privillage.Role_ID',$id)
                ->where('menu_previllage.Parent_ID', '!=', -1)
				->where('userrole_privillage.IsActive','1')
				->select('userrole_privillage.IsActive','menu_previllage.priv_ID','menu_previllage.Parent_ID','menu_details.*')
				->distinct('menu_previllage.priv_ID')
				->get();
	 $res=array($perm);
	 return $res;
	
	}

	public function signupAssoc(Request $r)
	{
		\DB::transaction(function() use ($r) {
		$values = Request::json()->all();
		//$comma_separated = implode(",", $values['type']);
		$chekContactExists=\DB::table('contacts')->where('Contact_phone',$values['email'])->get();
		$countContact=count($chekContactExists);
		if($countContact==0)
		 {
		$contactID=\DB::table('contacts')->insertGetID(array('Contact_Name'=>$values['fname'], 'Contact_phone'=>$values['email'], 'Contact_position'=>'Associate'));
		if(!empty($contactID))
		{
			$assocID=\DB::table('associate')->insertGetID(array('Assoc_FirstName'=>$values['fname'], 'Assoc_LastName'=>$values['lname'], 'Contact_ID'=>$contactID, 'Assoc_Status'=>'5'));
			$types=$values['type'];
			
			$rel=\DB::table('associate_type_rel')->insert(array('Assoc_ID'=>$assocID, 'Type_ID'=>$types));
				//$newArray=$newArray.push($type);
			
			
			$password=Hash::make($values['password']);
			$userID=\DB::table('logins')->insertGetID(array('User_Name'=>$values['fname'], 'User_Login'=>$values['email'], 'User_Password'=>$password,'Role_ID'=>11,'Reg_Status'=>'5', 'User_Category'=>$values['type']));
			if(!empty($userID))
			{
				$roleRel=\DB::table('user_roles')->insert(array('user_ID'=>$userID, 'role_ID'=>'11'));
				$assocUserRel=\DB::table('user_assoc_rel')->insert(array('User_ID'=>$userID, 'Assoc_ID'=>$assocID));

			}
			$resp=array("Success"=>true, "Category"=>$values['type'], "UserID"=>$userID);
			return $resp;

		}
	}
	else{
		$resp=array("Success"=>false);
		return $resp;
	
	
	}
});	
	}

	public function saveDetails(Request $r)
	{
		\DB::transaction(function() use ($r) {
		$values = Request::json()->all();
		$comma_separated=implode(",", $values['workSpec']);
		$assocID=\DB::table('user_assoc_rel')->where('User_ID','=', $values['userID'])
		->pluck('Assoc_ID');
		$addressID=\DB::table('address')->insertGetID(array('Address_email'=>$values['email'], 'Address_url'=>$values['website'] ));

		
		$details=\DB::table('associate_details')->insert(array('Assoc_ID'=>$assocID[0],'Type'=>$values['organisation'],'Descr'=>$values['descr'], 'Experiece'=>$values['exp'], 
		'Gender'=>$values['gender'],'Grade'=>$values['grade'],'No_Projects'=>$values['works'],
		'No_Projects'=>$values['works'], 'Loc_ID'=>$values['Location'],'License'=>$values['license'],
		 'StdRate'=>$values['stdRate'], 'Work_Spec'=>$comma_separated));
		 if(!empty($values['QualiArch']))
		 {
			 $updateQuali=\DB::table('associate_details')
			 ->where('Assoc_ID',$assocID[0])->update (array('Qualification'=>$values['QualiArch']));
		 }
		 else if(!empty($values['QualiEng']))
		 {
			 $updateQuali=\DB::table('associate_details')
			 ->where('Assoc_ID',$assocID[0])->update (array('Qualification'=>$values['QualiEng']));
		 }
		 else if(!empty($values['QualiCon']))
		 {
			 $updateQuali=\DB::table('associate_details')
			 ->where('Assoc_ID',$assocID[0])->update (array('Qualification'=>$values['QualiCon']));
		 }
		 else if(!empty($values['QualiInt']))
		 {
			 $updateQuali=\DB::table('associate_details')
			 ->where('Assoc_ID',$assocID[0])->update (array('Qualification'=>$values['QualiInt']));
		 }
		 $associate=\DB::table('associate')->where('Assoc_ID','=', $assocID[0])
		 ->update(array('Address_ID'=>$addressID));
		 if($values['Category']==5)
		 {
			$loginStatus=\DB::table('logins')->where('User_ID','=', $values['userID'])
			->update(array('Reg_Status'=>'9'));
			$assocStatus=\DB::table('associate')->where('Assoc_ID',$assocID[0])
		 ->update(array('Assoc_Status'=>'9'));
		 }
		 else{
		 $loginStatus=\DB::table('logins')->where('User_ID','=', $values['userID'])
		 ->update(array('Reg_Status'=>'1'));

		 $assocStatus=\DB::table('associate')->where('Assoc_ID',$assocID[0])
		 ->update(array('Assoc_Status'=>'1'));
		}
		 $activity=\DB::table('profile-activity-track')->insert(array('Activity_Name'=>'Registered', 'Activity_Message'=>'Joined on date ', 'User_ID'=>$values['userID']));
		 $user_details=\DB::table('logins')
			->join ('user_roles', 'logins.User_ID', '=','user_roles.user_ID')
			->join ('roles','logins.Role_ID','=','roles.Role_ID')
			//->join('user_assoc_rel', 'user_assoc_rel.User_ID','=','logins.User_ID')
			//->join('associate', 'associate.Assoc_ID','=','user_assoc_rel.Assoc_ID')
			//->select('logins.User_Name','logins.User_ID','logins.User_Login','logins.User_Image','logins.User_Status','logins.Reg_Status','roles.Role_Name','logins.User_Email','roles.Role_ID')
			->select('logins.*','roles.*')
		
			->where('logins.User_ID', $values['userID'])
			->get();

		 $resp=array("Success"=>true, $assocID[0], $addressID, "Category"=>$values['Category'], $user_details);
		 return $resp; 
	
	});
	}

	public function generateOTP()
	{
		$result = '';
			for($i = 0; $i < 4; $i++) {
			$result .= mt_rand(0, 9);
			}
			$resp=array($result);
			return $resp;
	}

	public function sendOTP(Request $r)
	{
		/*$values = Request::json()->all();
		$otp=$values['otp'];
		$mobile=$values['contact'];
		$text = urlencode('Hello');
		 
        $curl = curl_init();
 
        // Send the POST request with cURL
        curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => "http://message.adrieya.com/api/sms/format/xml",
        CURLOPT_POST => 1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_HTTPHEADER => array('X-Authentication-Key:511c7d532075f5592094e2787a9f30ae', 'X-Api-Method:MT'),
        CURLOPT_POSTFIELDS => array(
                        'mobile' => $mobile,
                        'route' => 'TL',
                        'text' => $text,
                        'sender' => 'INFRAM')));
 
    // Send the request & save response to $response
    $response = curl_exec($curl);
 
    // Close request to clear up some resources
    curl_close($curl);*/
 
	// Print response
	$resp=array("Success"=>true);
    return $resp;
	}

	public function getAssocTypes()
	{
		$types=\DB::table('associate_type')->get();
		$resp=array($types);
		return $resp;
	}

	public function getUserTypes($id)
	{
		/*$userTypes=\DB::table('user_assoc_rel')
		->join('associate_type_rel', 'associate_type_rel.Assoc_ID','=','user_assoc_rel.Assoc_ID')
		->where('user_assoc_rel.User_ID',$id)
		->select('associate_type_rel.Type_ID')
		->get();*/
		
		$resp=array($userTypes);
		return $resp;
	}
	public function saveContractorDetails(Request $r)
	{
		\DB::transaction(function() use ($r) {
		$values = Request::json()->all();
		$assocID=\DB::table('user_assoc_rel')->where('User_ID','=', $values['userID'])
		->pluck('Assoc_ID');
		
		$saveDetails=\DB::table('associate_details')->where('Assoc_ID',$assocID[0])->update(array('Keral_WKRS'=>$values['KWorkers'],
		'NonKerala_WKRS'=>$values['NKWorkers'], 'Total_WRKS'=>$values['totalWorkers'], 'Radius'=>$values['radius'],
		'Type'=>$values['organisation'],'GST'=>$values['gst'], 'OrganizationName'=>$values['orgName'], 
		'Territory'=>$values['Territory']));

		$changeStatus=\DB::table('logins')->where('User_ID',$values['userID'])->update(array('Reg_Status'=>1));
		$assocStatus=\DB::table('associate')->where('Assoc_ID',$assocID[0])
		 ->update(array('Assoc_Status'=>'1'));

		$user_details=\DB::table('logins')
			->join ('user_roles', 'logins.User_ID', '=','user_roles.user_ID')
			->join ('roles','logins.Role_ID','=','roles.Role_ID')
			//->join('user_assoc_rel', 'user_assoc_rel.User_ID','=','logins.User_ID')
			//->join('associate', 'associate.Assoc_ID','=','user_assoc_rel.Assoc_ID')
			//->select('logins.User_Name','logins.User_ID','logins.User_Login','logins.User_Image','logins.User_Status','logins.Reg_Status','roles.Role_Name','logins.User_Email','roles.Role_ID')
			->select('logins.*','roles.*')
		
			->where('logins.User_ID', $values['userID'])
			->get();

		$resp=array("Success"=>true, $user_details);
		return $resp;

		});
	}

	public function resetPassword(Request $r)
	{
		$values = Request::json()->all();
		$password=Hash::make($values['confirmPass']);
		$changePwd=\DB::table('logins')
		->where('User_Login',$value['contact'])
		->update(array('User_Password'=>$password));
		$resp=array("Success"=>true);
		return $resp;



	}
	public function changeActiveStatus(Request $r)
	{
		$values = Request::json()->all();
		$type=$values['param2'];
		$userID=$values['param1'];
		if($type==1)
		{
			$changeStatus=\DB::table('users')->where('ID',$userID)->update(array('ActiveFlag'=>1));
		}
		else if($type==2)
		{
			$changeStatus=\DB::table('users')->where('ID',$userID)->update(array('ActiveFlag'=>0));
		}
		$resp=array('Success'=>true);
		return $resp;
	}
}




