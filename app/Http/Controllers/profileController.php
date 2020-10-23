<?php

namespace App\Http\Controllers;

//use Illuminate\Http\Request;
use Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Crypt;
use File;
use DateTime;

class profileController extends Controller
{
    public function profileUpload(Request $r)
    {
        $value= Request::json()->all();
	$filename = Request::file('fileKey')->getClientOriginalName();
	$name=Input::get('name');
    $id=Input::get('id');
    $message=Input::get('message');
    $flag=Input::get('flag');
    $AttachID=\DB::table('profile_attachments')->insertGetID(array('Filename'=>$filename, 'File_Type'=>$flag));
    $postID=\DB::table('profile_posts')->insertGetID(array('Message'=>$message, 'User_ID'=>$id, 'Status_Flag'=>'1'));
    if(Request::file('fileKey')){
		$file=Request::file('fileKey');
        $file->move('resources/assets/uploads/ProfileAttachments',$filename);
        $relation=\DB::table('post_attach_rel')->insert(array('Attach_ID'=>$AttachID, 'Post_ID'=>$postID));
		
		$response=array('response'=>'Uploaded','success'=>true, $message);
		return $response;
		
	}
    }
    public function getPostFiles($id)
    {
        $postData=\DB::table('profile_posts')->join('post_attach_rel', 'post_attach_rel.Post_ID','=','profile_posts.Post_ID')
        ->join('profile_attachments','profile_attachments.Attach_ID','=','post_attach_rel.Attach_ID')
        ->join('user_assoc_rel', 'user_assoc_rel.User_ID','=','profile_posts.User_ID')
        ->join('associate', 'associate.Assoc_ID','=','user_assoc_rel.Assoc_ID')
        ->where('profile_posts.User_ID',$id)
        ->where('profile_posts.DeleteFlag', 1)
        ->where('profile_posts.Type_Flag', 1)
        ->orderBy('profile_posts.DateTime', 'desc')->get();
        $resp=array($postData);
        return $resp;
    }

    public function getProfileDetails($id)
    {
        $profileDetails=\DB::table('logins')->join('user_assoc_rel', 'user_assoc_rel.User_ID', '=','logins.User_ID')
        ->join('associate', 'associate.Assoc_ID', '=','user_assoc_rel.Assoc_ID')
        ->join('associate_details', 'associate_details.Assoc_ID', '=','associate.Assoc_ID')
        ->join('address','address.Address_ID','=','associate.Address_ID')
        ->join('contacts','contacts.Contact_ID','=','associate.Contact_ID')
        ->join('location','location.Loc_ID','=','associate_details.Loc_ID')
        ->where('logins.User_ID', $id)
        ->get();
        $resp=array($profileDetails);
        return $resp;
    }

    public function getAssociateType($id)
    {
        $assocType=\DB::table('logins')
        ->join('user_assoc_rel','user_assoc_rel.User_ID','=','logins.User_ID')
        ->join('associate_details','associate_details.Assoc_ID','=','user_assoc_rel.Assoc_ID')
        ->where('logins.User_ID', $id)->pluck('Type');
        $resp=array($assocType);
        return $resp;
    }

    public function editProfileDetails(Request $r)
    {
        $value= Request::json()->all();
       $assocID=\DB::table('user_assoc_rel')->where('User_ID',$value['userID'])->pluck('Assoc_ID');
       $addressID=\DB::table('associate')->where('Assoc_ID', $assocID)->pluck('Address_ID');

       if(!empty($addressID))
       {
           $editAddress=\DB::table('address')->where('Address_ID', $addressID[0])
           ->update(array('Address_line1'=>$value['addr1'], 'Address_line2'=>$value['addr2'],'Address_town'=>$value['city'],
            'Address_email'=>$value['email'], 'Address_url'=>$value['website']));
            
            $editDetails=\DB::table('associate_details')
            ->where('Assoc_ID', $assocID[0])
            ->update(array('Descr'=>$value['descr'], 'Experiece'=>$value['exp'], 
        'Grade'=>$value['grade'],'OrganizationName'=>$value['orgName'],
        'No_Projects'=>$value['projects'], 'License'=>$value['license'],'GST'=>$value['gst'],
         'StdRate'=>$value['stdRate'], 'Keral_WKRS'=>$value['keralaWrks'], 'NonKerala_WKRS'=>$value['nonKeralaWrks'], 'Total_WRKS'=>$value['total'],
        'Territory'=>$value['territory'], 'Radius'=>$value['radius']));

       }
       $resp=array("Success"=>true, $editAddress, $editDetails);
       return $resp;
       
        
       
    }
    public function getPhotosVideos($id)
    {
        $photos=\DB::table('profile_posts')->join('post_attach_rel', 'post_attach_rel.Post_ID','=','profile_posts.Post_ID')
        ->join('profile_attachments', 'profile_attachments.Attach_ID','=','post_attach_rel.Attach_ID')
        ->where('profile_posts.User_ID', $id)
        ->where('profile_attachments.File_Type',1)
        ->where('profile_posts.DeleteFlag',1)
        //->groupby (\DB::raw('MONTH(profile_attachments.Date)'))
        ->get();
        $resp=array($photos);
        return $resp;
    }
    public function deletePost($id)
    {
        $value= Request::json()->all();
        $delete=\DB::table('profile_posts')
        ->where('Post_ID',$id)
        ->update(array('DeleteFlag'=>2));
        $resp=array("Success"=>true);
        return $resp;
    }

    public function getActivityDetails($id)
    {
        $activities=\DB::table('profile-activity-track')
        ->join('user_assoc_rel', 'user_assoc_rel.User_ID','=','profile-activity-track.User_ID')
        ->join('associate','associate.Assoc_ID','=', 'user_assoc_rel.Assoc_ID')
        ->where('profile-activity-track.User_ID', $id)
        ->get();
        $resp=array($activities);
        return $resp;
    }

    public function sharePost($id, $user_ID)
    {
        $share=\DB::table('profile_posts')
        ->where('Post_ID',$id)
        ->update(array('Share_Flag'=>2));
        $activity=\DB::table('profile-activity-track')->insert(array('Activity_Name'=>'Post Shared', 'Activity_Message'=>' Shared a post  ', 'User_ID'=>$user_ID));
        $resp=array($share);
        return $resp;
    }

    public function getSharedPosts($id)
    {
        $sharedPosts=\DB::table('profile_posts')->join('post_attach_rel', 'post_attach_rel.Post_ID','=','profile_posts.Post_ID')
        ->join('profile_attachments','profile_attachments.Attach_ID','=','post_attach_rel.Attach_ID')
        ->join('user_assoc_rel', 'user_assoc_rel.User_ID','=','profile_posts.User_ID')
        ->join('associate', 'associate.Assoc_ID','=','user_assoc_rel.Assoc_ID')
        ->where('profile_posts.User_ID',$id)
        ->where('profile_posts.Share_Flag', 2)
        //->where('profile_posts.Type_Flag', 2)
        ->orderBy('profile_posts.DateTime', 'desc')->get();
        $resp=array($sharedPosts);
        return $resp;
    }

    public function postComment(Request $r)
    {
        $value= Request::json()->all();
        /*$commentID=\DB::table('forum_comments')->insertGetID(array('Comment_Message'=>$value['comment'], 'User_ID'=>$value['userID']));
        $rel=\DB::table('post_comment_rel')->insert(array('Post_ID'=>$value['postID'], 'Comment_ID'=>$commentID));*/
        $resp=array($value['postID']);
        return $resp; 


    }

    public function getCivilServices()
    {
        $civilServices=\DB::table('services')
        ->join('service_segment_map','service_segment_map.Service_ID','=','services.Service_ID')
        ->where('services.DeleteFlag',1)
        ->where('service_segment_map.DeleteFlag',0)
        ->orderBy('Segment_ID')->get();
        $resp=array($civilServices);
        return $resp;
    }

    public function getSegments()
    {
        $segment=\DB::table('segment')->get();
        $resp=array($segment);
        return $resp;
    }

    public function saveServices(Request $r)
    {
        $value= Request::json()->all();
        $param1=$value['param1'];
        $i=0;
        $assocID=\DB::table('user_assoc_rel')->where('User_ID',$value['param2'])->pluck('Assoc_ID');
       foreach($param1 as $p)
       {
        $integerIDs = explode('"', $p);
        $serID[$i]=(int)$integerIDs[0];
        $segID=\DB::table('services')->where('Service_ID',$serID[$i])->pluck('Segment_ID');
        $seg=\DB::table('associate_segment_rate')->insert(array(
        'Assoc_ID' =>$assocID[0] ,
        'Segment_ID' => $segID[0],
        'Service_ID' =>  $serID[$i]));
        $i++;
       

       }
       $resp=array("Success"=>true);
       return $resp;
        
    }

     public function saveArticle(Request $r)//to continue with article id empty or not
    {
        $value= Request::json()->all();
	$filename = Request::file('fileKey')->getClientOriginalName();
	$name=Input::get('name');
    $id=Input::get('id');
    $articleID=Input::get('artID');
    $title=Input::get('title');
    $post=Input::get('post');
    $flag=Input::get('flag');
    //$assocID=\DB::table('user_assoc_rel')->where('User_ID',$id)->pluck('Assoc_ID');

    $AttachID=\DB::table('profile_attachments')->insertGetID(array('Filename'=>$filename, 'File_Type'=>$flag));
    if($articleID=="undefined")
    {
    $postID=\DB::table('associate_articles')->insertGetID(array('User_ID'=>$id,'Article_Title'=>$title, 'Article_Post'=>$post, 'Attach_ID'=>$AttachID));
    if(Request::file('fileKey'))
    {
		$file=Request::file('fileKey');
        $file->move('resources/assets/uploads/ProfileAttachments',$filename);
        //$relation=\DB::table('post_attach_rel')->insert(array('Attach_ID'=>$AttachID, 'Post_ID'=>$postID));
        $activity=\DB::table('profile-activity-track')->insert(array('Activity_Name'=>'Article Posted', 'Activity_Message'=>' Posted An Article  ', 'User_ID'=>$id));	
		$response=array('response'=>'Uploaded','success'=>true, $postID);
		return $response;
    }
}
    else if(Request::file('fileKey'))
    {
		$file=Request::file('fileKey');
        $file->move('resources/assets/uploads/ProfileAttachments',$filename);
        //$relation=\DB::table('post_attach_rel')->insert(array('Attach_ID'=>$AttachID, 'Post_ID'=>$postID));
         $editArticle=\DB::table('associate_articles')->where('Article_ID', $articleID)
        ->update(array('Article_Title'=>$title, 'Article_Post'=>$post, 'Attach_ID'=>$AttachID));
        $response=array('response'=>'Uploaded','success'=>true, $postID);
		return $response;
    }
    else{
        $editArticle=\DB::table('associate_articles')->where('Article_ID', $articleID)
        ->update(array('Article_Title'=>$title, 'Article_Post'=>$post));
        $response=array('response'=>'Uploaded','success'=>true);
		return $response;
    }
    
}

public function getArticleOnTimeline($id)
{
    $article=\DB::table('associate_articles')
        ->join('profile_attachments','profile_attachments.Attach_ID','=','associate_articles.Attach_ID')
        ->join('user_assoc_rel', 'user_assoc_rel.User_ID','=','associate_articles.User_ID')
        ->join('associate', 'associate.Assoc_ID','=','user_assoc_rel.Assoc_ID')
        ->where('associate_articles.User_ID',$id)
        ->where('associate_articles.DeleteFlag', 0)
        //->where('profile_posts.Type_Flag', 1)
        ->orderBy('associate_articles.Article_Time', 'desc')
        ->get();
        $resp=array($article);
        return $resp;
}

public function viewArticle($id)
{
    $article=\DB::table('associate_articles')
        ->join('profile_attachments','profile_attachments.Attach_ID','=','associate_articles.Attach_ID')
        ->join('user_assoc_rel', 'user_assoc_rel.User_ID','=','associate_articles.User_ID')
        ->join('associate', 'associate.Assoc_ID','=','user_assoc_rel.Assoc_ID')
        ->where('associate_articles.Article_ID',$id)
       
        ->get();
        $resp=array($article);
        return $resp;
}

public function deleteArticle($id)
{
    $delete=\DB::table('associate_articles')
        ->where('Article_ID',$id)
        ->update(array('DeleteFlag'=>1));
        $resp=array("Success"=>true);
        return $resp;
}


  
}
