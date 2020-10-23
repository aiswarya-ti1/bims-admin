<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Input;
//use Illuminate\Http\Request;
use Request;
use DateTime;
use Response;
use File;

class priceManagementController extends Controller
{
	//Function for displaying Product Segment Details
    public function getProducts()
	{
		$dt = new \DateTime('now -2 month');
		$products=\DB::table('prod_assoc_rates')->select('MAssoc_ID','Rate')
			//->having('prod_assoc_rates.Rate',\DB::raw("MIN(prod_assoc_rates.Rate)"))
			//->groupBy('prod_assoc_rates.Prod_ID')
			->where('prod_assoc_rates.CurrentDate','<', Now())
		->where('prod_assoc_rates.CurrentDate','>',$dt)->get();
		$count=count($products);
		$results= \DB::table('prod_assoc_rates')
       
        ->join('products', 'products.Prod_ID', '=', 'prod_assoc_rates.Prod_ID')

	
		->join('prod_groups','products.Group_ID','=','prod_groups.Group_ID')
		->join('prod_segment','prod_segment.Seg_ID','=','prod_groups.Seg_ID')
		->leftjoin('prod_brands','prod_brands.Brand_ID','=','products.Brand_ID')
		->join('prod_associate','prod_associate.MAssoc_ID','=','prod_assoc_rates.MAssoc_ID')
		 ->select('products.Prod_Name','prod_associate.MAssoc_ID','prod_assoc_rates.Prod_ID','prod_groups.Group_Name','prod_associate.MAssoc_FirstName','prod_brands.Brand_Name','products.UnitofMeasure','prod_segment.Seg_ID','prod_assoc_rates.MAssoc_ID',\DB::raw("MIN(prod_assoc_rates.Rate)as Rate"))
       // ->orderBy('Rate', 'desc')
	   	
        ->groupBy('prod_assoc_rates.Prod_ID')
			//->having('prod_assoc_rates.Rate',\DB::raw("MIN(prod_assoc_rates.Rate)"))
			->where('prod_assoc_rates.CurrentDate','<', Now())
		->where('prod_assoc_rates.CurrentDate','>',$dt)
		
       // ->take($count)
        ->get();
		/*$results=\DB::table('products')
		->join('prod_assoc_rates','products.Prod_ID','=','prod_assoc_rates.Prod_ID')
		->join('prod_assoc_segment','products.Seg_ID','=','prod_assoc_segment.Seg_ID')
		->join('prod_categories','prod_categories.Category_ID','=','products.Model')
		->join('prod_groups','prod_groups.Group_ID','=','products.Group_ID')
		->join('prod_associate','prod_associate.MAssoc_ID','=','prod_assoc_segment.MAssoc_ID')
		->join('prod_segment', 'prod_segment.Seg_ID','=','products.Seg_ID')
		->join('prod_brands','prod_brands.Brand_ID','=','products.Brand_ID')
		->select('products.Prod_ID','products.Prod_Code','products.Prod_Name',
		'products.Seg_ID','products.Model','prod_categories.Category_Name','prod_assoc_rates.MAssoc_ID','prod_associate.MAssoc_FirstName','prod_associate.MAssoc_MiddleName','prod_associate.MAssoc_LastName','products.Brand_ID','prod_segment.Seg_Name','prod_brands.Brand_Name','prod_groups.Group_Name'
		)
		->selectRaw("MIN(Rate) AS Rate")->groupBy('prod_assoc_rates.Prod_ID')
		->where ('products.RateStatus','2')
		->get();*/
		/*$dt = new \DateTime('now -2 month');
		$rates=\DB::table('prod_assoc_rates')//->selectRaw("MIN(Rate) AS Rate")->groupBy('prod_assoc_rates.Prod_ID')
		
		->join('products','products.Prod_ID','=','prod_assoc_rates.Prod_ID')
		->join('prod_groups','products.Group_ID','=','prod_groups.Group_ID')
		->join('prod_segment','prod_segment.Seg_ID','=','prod_groups.Seg_ID')
		->leftjoin('prod_brands','prod_brands.Brand_ID','=','products.Brand_ID')
		->join('prod_associate','prod_associate.MAssoc_ID','=','prod_assoc_rates.MAssoc_ID')
	
		->select('products.Prod_Name','prod_assoc_rates.MAssoc_ID','prod_assoc_rates.Prod_ID','prod_groups.Group_Name','prod_associate.MAssoc_FirstName','prod_brands.Brand_Name','products.UnitofMeasure','prod_segment.Seg_ID',MIN('prod_assoc_rates.Rate'))
		->groupBy('prod_assoc_rates.Prod_ID')
			->where('prod_assoc_rates.CurrentDate','<', Now())
		->where('prod_assoc_rates.CurrentDate','>',$dt)
		
		
		->get();*/
		
		
		
		$prodResp=array('success'=>true,$results);
		return $prodResp;
				
		//select('products.Prod_ID','prod_assoc_rates.MAssoc_ID','prod_assoc_rates.Rate')->having(min('prod_assoc_rates.Rate'))->get();
		
	/*$results=\DB::table('products')->select('products.Prod_ID','products.Prod_Code','products.Prod_Name',
		'products.Seg_ID','products.Model')
		->join(\DB::raw("(SELECT * from prod_segment) ts on p.Seg_ID=ts.Seg_ID INNER JOIN(select * from prod_groups)ps on p.Group_ID=ps.Group_ID INNER JOIN(SELECT MAssoc_ID, Prod_ID, MIN(Rate) FROM prod_assoc_rates GROUP BY Prod_ID)ap on ap.Prod_Id=p.Prod_ID INNER JOIN (select * from prod_associate)assoc on ap.MAssoc_ID=assoc.MAssoc_ID)"))->get();*/
		
		
	
		/*if(!empty($results))
		{
		/*$products=\DB::table('products')->join('prod_segment', 'prod_segment.Seg_ID','=','products.Seg_ID')
		->join('prod_categories','prod_categories.Category_ID','=','products.Model')
		->join('prod_groups','prod_groups.Group_ID','=','products.Group_ID')
		->join('prod_assoc_segment','products.Seg_ID','=','prod_assoc_segment.Seg_ID')
		->join('prod_associate','prod_associate.MAssoc_ID','=','prod_assoc_segment.MAssoc_ID')
		->join('prod_brands','prod_brands.Brand_ID','=','products.Brand_ID')
		->join('prod_assoc_rates','prod_associate.MAssoc_ID','=','prod_assoc_rates.MAssoc_ID')
		->select('products.Prod_ID','products.Prod_Code','products.Prod_Name','products.Seg_ID','prod_segment.Seg_Name','products.Model',
		'prod_categories.Category_Name','prod_assoc_segment.MAssoc_ID','prod_associate.MAssoc_ID','prod_associate.MAssoc_FirstName','prod_associate.MAssoc_MiddleName','prod_associate.MAssoc_LastName','products.Brand_ID','prod_brands.Brand_Name','prod_groups.Group_Name','prod_assoc_rates.Rate')
		//->groupBy(\DB::raw('products.Prod_ID'))
		//->where('prod_assoc_rates.Rate', $results)
	//->groupBy('products.Prod_ID')
		->get();
		$prodResp=array('success'=>true, $products);
		return $prodResp;*/
		
		/*$products=\DB::select(\DB::raw("SELECT *from products p INNER JOIN (SELECT Seg_ID,Seg_Name from prod_segment) ts on p.Seg_ID=ts.Seg_ID INNER JOIN(select * from prod_groups)ps on p.Group_ID=ps.Group_ID INNER JOIN(SELECT Prod_ID, MIN(Rate) FROM prod_assoc_rates GROUP BY Prod_ID)ap on ap.Prod_Id=p.Prod_ID INNER JOIN (select * from prod_associate)assoc on prod_assoc_rates.MAssoc_ID=assoc.MAssoc_ID"));
		
		
		$prodResp=array('success'=>true, $products);
		return $prodResp;*/
		
	}
	public function getDetails()
	{
		$dt = new \DateTime('now -2 month');
		$products=\DB::table('prod_assoc_rates')->selectRaw("MIN(Rate) AS Rate")->groupBy('prod_assoc_rates.Prod_ID')
		->join('products','products.Prod_ID','=','prod_assoc_rates.Prod_ID')
		->join('prod_groups','products.Group_ID','=','prod_groups.Group_ID')
		->join('prod_segment','prod_segment.Seg_ID','=','prod_groups.Seg_ID')
		->leftjoin('prod_brands','prod_brands.Brand_ID','=','products.Brand_ID')
		->join('prod_associate','prod_associate.MAssoc_ID','=','prod_assoc_rates.MAssoc_ID')
		->groupBy('prod_assoc_rates.Prod_ID')
		->select('products.Prod_Name','prod_assoc_rates.MAssoc_ID','prod_assoc_rates.Prod_ID','prod_groups.Group_Name','prod_associate.MAssoc_FirstName','prod_brands.Brand_Name','products.UnitofMeasure','prod_segment.Seg_ID')
		//->select('prod_assoc_rates.Prod_ID','products.Prod_Name','prod_groups.Group_Name','prod_brands.Brand_Name','prod_associate.MAssoc_FirstName','products.UnitofMeasure','prod_assoc_rates.MAssoc_ID','prod_assoc_rates.Rate','prod_segment.Seg_ID')
		
		
		//->where('prod_assoc_rates.Rate',\DB::raw("MIN(prod_assoc_rates.Rate)as Rate"))
		->where('prod_assoc_rates.CurrentDate','<', Now())
		->where('prod_assoc_rates.CurrentDate','>',$dt)
		
		
		->get();
		
		$prodResp=array('success'=>true,$products);
		return $prodResp;
	}
	
	public function products()
	{
		$products=\DB::select(\DB::raw("select MAssoc_ID, MIN(Rate) from prod_assoc_rates group By Prod_ID"));
	$results=\DB::table('products')
		->join('prod_assoc_rates','products.Prod_ID','=','prod_assoc_rates.Prod_ID')
		->join('prod_assoc_segment','products.Seg_ID','=','prod_assoc_segment.Seg_ID')
		->join('prod_categories','prod_categories.Category_ID','=','products.Model')
		->join('prod_groups','prod_groups.Group_ID','=','products.Group_ID')
		->join('prod_associate','prod_associate.MAssoc_ID','=','prod_assoc_segment.MAssoc_ID')
		->join('prod_segment', 'prod_segment.Seg_ID','=','products.Seg_ID')
		->join('prod_brands','prod_brands.Brand_ID','=','products.Brand_ID')
		->select('products.Prod_ID','products.Prod_Code','products.Prod_Name',
		'products.Seg_ID','products.Model','prod_categories.Category_Name','prod_assoc_rates.MAssoc_ID','prod_associate.MAssoc_FirstName','prod_associate.MAssoc_MiddleName','prod_associate.MAssoc_LastName','products.Brand_ID','prod_segment.Seg_Name','prod_brands.Brand_Name','prod_groups.Group_Name'
		)
		->selectRaw("MIN(Rate) AS Rate")->groupBy('prod_assoc_rates.Prod_ID')
		->where ('products.RateStatus','2')
		->get();

		
		$resp=array($results,$products);
		return $resp;
	}
	
	//Function for Displaying Service Segment Details
	public function getServices()
	{
		$service=\DB::table('ser_assoc_rate')->join('ser_services','ser_assoc_rate.SerServ_ID','=','ser_services.SerServ_ID')
		->join('serv_segments', 'ser_services.SerSeg_ID','=','serv_segments.SerSeg_ID')
		->join('associate','associate.Assoc_ID','=','ser_assoc_rate.Assoc_ID')
		->select('ser_services.SerServ_ID','ser_services.SerServ_Name','ser_services.SerSeg_ID','serv_segments.SerSeg_Name','ser_assoc_rate.Pattern','associate.Assoc_FirstName', 'associate.Assoc_MiddleName','associate.Assoc_LastName')

->selectRaw("MIN(ser_assoc_rate.Rate) AS Rate")->groupby('ser_assoc_rate.SerServ_ID')->groupby('ser_assoc_rate.Pattern')->orderby('ser_assoc_rate.SerServ_ID')->get();
		
		/*$service=\DB::table('ser_services')->join('serv_segments', 'ser_services.SerSeg_ID','=','serv_segments.SerSeg_ID')
		->join('ser_assoc_rate','ser_assoc_rate.SerServ_ID','=','ser_services.SerServ_ID')
		->join('associate','associate.Assoc_ID','=','ser_assoc_rate.Assoc_ID')
		->select('ser_services.SerServ_ID','ser_services.SerServ_Name','ser_services.SerSeg_ID','serv_segments.SerSeg_Name','ser_assoc_rate.Pattern','ser_assoc_rate.Rate','associate.Assoc_FirstName', 'associate.Assoc_MiddleName','associate.Assoc_LastName')
		//->orderby('associate.Assoc_ID','DESC')
		//->groupBy('ser_assoc_rate.SerServ_ID')
		->get();*/
		$serResp=array('success'=>true, $service);
		return $serResp;
		
	}
	//Function for displaying Asscoiate list according to a product
	public function getAssocRateList($id)
	{
		$dt = new \DateTime('now -2 month');
		$list=\DB::table('prod_assoc_rates')->join('products','prod_assoc_rates.Prod_ID','=','products.Prod_ID')
		->join('prod_associate','prod_associate.MAssoc_ID','=','prod_assoc_rates.MAssoc_ID')
		->where('prod_assoc_rates.Prod_ID',$id)
		->where('prod_assoc_rates.CurrentDate','<', Now())
		//->where('prod_assoc_rates.CurrentDate','>',$dt)
		
		->select('prod_assoc_rates.MAssoc_ID','prod_assoc_rates.Rate','prod_assoc_rates.CurrentDate','prod_assoc_rates.ExpDate','prod_associate.MAssoc_FirstName','prod_associate.MAssoc_MiddleName','prod_associate.MAssoc_LastName','products.UnitofMeasure')
		->orderby('prod_assoc_rates.Rate','ASC')
		->get();
		$listResp=array('success'=>true, $list);
		return $listResp;
			}
			
		//To get Associate's rate per service
		public function getServAssocRate($value)
		{
			$id=explode(',',$value);
			
			$serList=\DB::table('ser_assoc_rate')->join('associate','ser_assoc_rate.Assoc_ID','=','associate.Assoc_ID')
			->join('ser_services', 'ser_services.SerServ_ID','=','ser_assoc_rate.SerServ_ID')
			->where('ser_assoc_rate.SerServ_ID', $id[0])
			->where('ser_assoc_rate.Pattern',$id[1])
		
			->select('ser_assoc_rate.Assoc_ID','ser_assoc_rate.Pattern','ser_assoc_rate.Rate','associate.Assoc_FirstName','associate.Assoc_MiddleName','associate.Assoc_LastName','ser_assoc_rate.Current_Date','ser_assoc_rate.Expiry_Date','ser_services.SerServ_Name')
				->orderby('ser_assoc_rate.Rate')
			->get();
			$listResp=array('success'=>true, $serList);
		return $listResp;
		}
		
		//To get list of Associates  per product
		public function getProdAssociates($id)
		{
			$prodAssocs=\DB::table('prod_assoc_segment')->join('prod_associate','prod_associate.MAssoc_ID','=','prod_assoc_segment.MAssoc_ID')
			->select('prod_assoc_segment.MAssoc_ID','prod_associate.MAssoc_FirstName')->where('prod_assoc_segment.Seg_ID',$id)->get();
			$listProAssoc=array('success'=>true, $prodAssocs);
		return $listProAssoc;
			
		}
		//to add/edit price of a product for a particular vendor
		public function addProductRate(Request $r)
		{
			$values = Request::json()->all();
			
		
		 
			
		$add=\DB::table('prod_assoc_rates')->insert(array(
		'Prod_ID' => $values['prod_ID'],
            'MAssoc_ID'     =>   $values['seg_ID'], 
            'Rate'   =>   $values['Rate'],            
			'CurrentDate'   =>    $values['currentDate'],          
		     
			'ExpDate'   =>    $values['expiryDate'],          
			
			 
			  
     ));
	 if(!empty($add))
	 {$pid=$values['prod_ID'];
			$updateStatus=\DB::table('products')
			->where('Prod_ID',$pid)
			->update(array('RateStatus' => '2'));
		 
	 
	 }
	 
			$resp=array($pid);
			return $resp;
			
		}
		
		//to get list of service associate per service
		public function getServAssociates($value)
		{
			$id=explode(',',$value);
			$servAssoc=\DB::table('ser_assoc_services')
			->join('associate','ser_assoc_services.Assoc_ID','=','associate.Assoc_ID')
			->join('ser_assoc_rate','ser_assoc_rate.Assoc_ID','=','associate.Assoc_ID')
			->select('ser_assoc_services.Assoc_ID','associate.Assoc_FirstName')
			->where('ser_assoc_services.SerSev_ID',$id[0])
			->where('ser_assoc_rate.Pattern',$id[1])
			->get();
			$listSerAssoc=array($servAssoc);
			return $listSerAssoc;
		}
		
		//to add/edit price of a particular service
		public function addServiceRate(Request $r)
		{
			$values = Request::json()->all();
			
		$add=\DB::table('ser_assoc_rate')->insert(array(
		'SerServ_ID' => $values['ser_ID'],
            'Assoc_ID'     =>   $values['seg_ID'], 
			'Pattern' => $values['pattern'],    
            'Rate'   =>   $values['Rate'],  
			      
			'Current_Date'   =>    $values['currentDate'],          
		     
			'Expiry_Date'   =>    $values['expiryDate'],          
			
			 
			  
     ));
	 
			$resp=array($values);
			return $resp;
		}
		
		//To list new products without rate
		public function getNewProdLists()
		{
			$newLists=\DB::table('products')
			
			->join('prod_groups','prod_groups.Group_ID','=','products.Group_ID')
			->join('prod_segment', 'prod_segment.Seg_ID','=','prod_groups.Seg_ID')
			->select('products.Prod_ID','products.Prod_Name','prod_groups.Seg_ID', 'prod_segment.Seg_Name', 'products.UnitofMeasure')
			->where('products.RateStatus','1')
			->get();
			
			$newProdResp=array($newLists);
			return $newProdResp;
		}
		
		//function to change rate status
		/*public function changeStatus($id)
		{
			
			 $updateStatus=\DB::table('products')->update(array('RateStatus' =>'2'))
		 ->where('Prod_ID','=',$id)->get();
		 
	 
			$resp=array('Rate Status updated', $id);
			return $resp;
		}*/
}
