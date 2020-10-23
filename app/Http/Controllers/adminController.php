<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Input;
//use Illuminate\Http\Request;
use Request;
use File;
use Excel;
use Illuminate\Support\Facades\Crypt;
use Hash;
use DateTime;

class adminController extends Controller
{
 //To get brands list.
    public function getBrands()
	{
		$brands=\DB::table('prod_brands')->get();
		//$brandCount=count($brands);
		$brandResp=array($brands);
		return $brandResp;
	}
	//To get segments list
	public function getAdminSegments()
	{
		$seg=\DB::table('prod_segment')->get();
		$segCount=count($seg);
		$segResp=array($seg,$segCount);
		return $segResp;
	}
	
	////to get Group list per segment
	public function getGroupSegments($id)
	{
		$groupList=\DB::table('prod_groups')->select('Group_ID','Group_Name')
		->where('Seg_ID', $id)->get();
		$groupResp=array($groupList);
		return $groupResp;
	}
	//to get Categorylist per group
	public function getCatGroups($id)
	{
		$catList=\DB::table('prod_categories')->select('Category_ID','Category_Name')->where('Group_ID',$id)->get();
		$CatResp=array($catList);
		return $CatResp;
	}
	//toget subcategories by Category
	public function getSubCatByCat($id)
	{
		$subList=\DB::table('prod_sub_category')->select('SubCat_ID','SubCat_Name')
		->where('Cat_ID',$id)->get();
		$subResp=array($subList);
		return $subResp;
	}
	//to get attribute list
	public function getAttributes()
	{
		$AttrList=\DB::table('prod_attributes')->select('Attrb_ID','Attrb_Name')
		->get();
		$AttrResp=array($AttrList);
		return $AttrResp;
	}
	public function getGroups()
	{
		$groups=\DB::table('prod_groups')->select('Group_ID','Group_Name')->get();
		$listresp=array($groups);
		return $listresp;
	}
	public function getCategories()
	{
		$cats=\DB::table('prod_categories')->select('Category_ID','Category_Name')->get();
		$catList=array($cats);
		return $catList;
	}
	//To add new Group
	public function addNewGroup(Request $g)
	{
			$group = Request::json()->all();
		$newgroup=\DB::table('prod_groups')->insert(array('Group_Name' => $group['group'], 'Seg_ID' =>$group['seg']));
	//	$groupCount=count($newgroup);
		$groupResp=array($newgroup);//, $groupCount
		return $groupResp;
	}
	
	//to add new brand
	public function addNewBrand(Request $b)
	{
		
		$name = Request::json()->all();
		$newBrand=\DB::table('prod_brands')->insert(array('Brand_Name' => $name['brand']));
		$resp=array($newBrand);
		return $resp;
	}
	//to add new Segment
	public function eg(Request $s)
	{
		
		$seg = Request::json()->all();
		$newSeg=\DB::table('prod_segment')->insert(array('Seg_Name' => $seg['segment']));
		$resp=array($newSeg);
		return $resp;
	}
	public function addNewCat(Request $r)
	{
		$cat=Request::json()->all();
		$newCat=\DB::table('prod_categories')->insert(array('Category_Name' =>$cat['category'], 'Group_ID'=>$cat['group']));
		$resp=array($newCat);
		return $resp;
	}
	public function SubCat(Request $r)
	{
		$subCat=Request::json()->all();
		$newSubCat=\DB::table('prod_sub_category')->insert(array('SubCat_Name' =>$subCat['subCategory'], 'Cat_ID'=>$subCat['category']));
		$resp=array($newSubCat);
		return $resp;
	}
	public function addNewAttrb(Request $r)
	{
		$data=Request::json()->all();
		$newAttrb=\DB::table('prod_attributes')->insert(array('Attrb_Name' => $data['subCategory']));
		$res=array($newAttrb);
		return $res;
	}
	public function addCSVFile(Request $req)
	{
		
	/*if ($req->file('fileKey')) 
	{
       $path = $req->file('fileKey')->getRealPath();
        $data = \Excel::load($path)->get();

        if ($data->count()) 
		{
            foreach ($data as $key => $value) 
			{
				$prod_exists=\DB::table('products')->where('Prod_Name', $value->prod_name)->get();
				$procCount=count($prod_exists);
				if($procCount==0)
				{
				$grpID=\DB::table('prod_groups')->where('Group_Name', $value->group)->pluck('Group_ID');
				$BrandID=\DB::table('prod_brands')->where('Brand_Name', $value->brand)->pluck('Brand_ID');
				$arr = array( 
                          "ItemName" => $value->item_name,
                          "Prod_Name" => $value->prod_name,
						  "Group_ID" => $grpID[0],
						  "Brand_ID" => $BrandID[0],
						  "UnitOfMeasure" => $value->attr_unit
						  
                           );
			
            
            if (!empty($arr)) 
			{
              $newProd = \DB::table('products')->insertGetID($arr);
			  if(!empty($newProd))
			  {
			  $Attr1= $value -> attr_itemtype;
			  $Attr2= $value->attr_material;
			  $Attr3= $value -> attr_modelname;
			  $Attr4 = $value -> attr_modelnumber;
			  $Attr5= $value -> attr_color;
			  $Attr6 = $value -> attr_size;
			  $Attr7 = $value -> attr_pressurerating;
			  $Attr8= $value -> attr_unit;
				$Attr9 = $value -> Attr_Gauge;
				$Attr10 = $value -> Attr_Length;
				$Attr11 = $value -> Attr_Breadth;
				$Attr12 = $value -> Attr_Height;
				$Attr13 = $value -> Attr_Thick;
				$Attr14 = $value -> Attr_Diameter;
				$Attr15 = $value -> Attr_Radius;
				$Attr16 = $value -> Attr_Weight;
				$Attr10_U = $value -> Attr_Length_Unit;
				$Attr11_U = $value -> Attr_Breadth_Unit;
				$Attr12_U = $value -> Attr_Height_Unit;
				$Attr13_U = $value -> Attr_Thick_Unit;
				$Attr14_U = $value -> Attr_Diameter_Unit;
				$Attr15_U = $value -> Attr_Radius_Unit;
				$Attr16_U = $value -> Attr_Weight_Unit;


			  if(!empty($Attr1))
				{
				$chkAttr1=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr1)->pluck('Attrb_Value_ID');
		

				if(!$chkAttr1->isEmpty())		
					{

						$insertAttr1=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '1', 'Attrb_Value_ID' =>$chkAttr1[0]));
			
				
					}
		
	
					else
					{
						$attr1ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '1' , 'Attrb_Value' => $Attr1));
						$insertAttr1=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '1', 'Attrb_Value_ID' =>$attr1ID));
			
					}
		
				}



			if(!empty($Attr2))
			{
				$chkAttr2=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr2)->pluck('Attrb_Value_ID');

				if(!$chkAttr2->isEmpty())		
					{
					$insertAttr2=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '2', 'Attrb_Value_ID' =>$chkAttr2[0]));
		
					}
		
	
				else
					{
					$attr2ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '2' , 'Attrb_Value' => $Attr2));
					$insertAttr2=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '2', 'Attrb_Value_ID' =>$attr2ID));
					}
			}
	
			if(!empty($Attr3))
			{
				$chkAttr3=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr3)->pluck('Attrb_Value_ID');

				if(!$chkAttr3->isEmpty())		
					{
				$insertAttr3=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '3', 'Attrb_Value_ID' =>$chkAttr3[0]));
		
					}
		
	
					else
					{
					$attr3ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '3' , 'Attrb_Value' => $Attr3));
					$insertAttr3=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '3', 'Attrb_Value_ID' =>$attr3ID));
					}
			}
	 		if(!empty($Attr4))
			{
				$chkAttr4=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr4)->pluck('Attrb_Value_ID');

				if(!$chkAttr4->isEmpty())		
				{
					$insertAttr4=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '4', 'Attrb_Value_ID' =>$chkAttr4[0]));
		
				}
		
	
			else
			{
				$attr4ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '4' , 'Attrb_Value' => $Attr4));
				$insertAttr4=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '4', 'Attrb_Value_ID' =>$attr4ID));
			}
	}
	 if(!empty($Attr5))
	{
		$chkAttr5=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr5)->pluck('Attrb_Value_ID');

		if(!$chkAttr5->isEmpty())		
		{
		$insertAttr5=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '5', 'Attrb_Value_ID' =>$chkAttr5[0]));
		
		}
		
	
		else
		{
		$attr5ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '5' , 'Attrb_Value' => $Attr5));
		$insertAttr5=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '5', 'Attrb_Value_ID' =>$attr5ID));
		}
	}
	 if(!empty($Attr6))
	{
		$chkAttr6=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr6)->pluck('Attrb_Value_ID');

		if(!$chkAttr6->isEmpty())		
		{
		$insertAttr6=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '6', 'Attrb_Value_ID' =>$chkAttr6[0]));
		
		}
		
	
		else
		{
		$attr6ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '6' , 'Attrb_Value' => $Attr6));
		$insertAttr6=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '6', 'Attrb_Value_ID' =>$attr6ID));
		}
	}
	 if(!empty($Attr7))
	{
		$chkAttr7=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr7)->pluck('Attrb_Value_ID');

		if(!$chkAttr7->isEmpty())		
		{
		$insertAttr7=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '7', 'Attrb_Value_ID' =>$chkAttr7[0]));
		
		}
		
	
		else
		{
		$attr7ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '7' , 'Attrb_Value' => $Attr7));
		$insertAttr7=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '7', 'Attrb_Value_ID' =>$attr7ID));
		}
	}
	if(!empty($Attr8))
	{
		$chkAttr8=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr8)->pluck('Attrb_Value_ID');

		if(!$chkAttr8->isEmpty())		
		{
		$insertAttr8=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '8', 'Attrb_Value_ID' =>$chkAttr8[0]));
		
		}
		
	
		else
		{
		$attr8ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '8' , 'Attrb_Value' => $Attr8));
		$insertAttr8=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '8', 'Attrb_Value_ID' =>$attr8ID));
		}
	}
	if(!empty($Attr9))
	{
		$chkAttr9=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr9)->pluck('Attrb_Value_ID');

		if(!$chkAttr9->isEmpty())		
		{
		$insertAttr9=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '9', 'Attrb_Value_ID' =>$chkAttr9[0]));
		
		}
		
	
		else
		{
		$attr9ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '9' , 'Attrb_Value' => $Attr9));
		$insertAttr9=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '9', 'Attrb_Value_ID' =>$attr9ID));
		}
	}
	if(!empty($Attr10))
	{
		$chkAttr10=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr10)->pluck('Attrb_Value_ID');

		if(!$chkAttr10->isEmpty())		
		{
		$insertAttr10=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '10', 'Attrb_Value_ID' =>$chkAttr10[0]));
		
		}
		
	
		else
		{
		$attr10ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '10' , 'Attrb_Value' => $Attr10,'Attrb_Unit_ID'=>$Attr10_U));
		$insertAttr10=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '10', 'Attrb_Value_ID' =>$attr10ID));
		}
	}
	if(!empty($Attr11))
	{
		$chkAttr11=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr11)->pluck('Attrb_Value_ID');

		if(!$chkAttr11->isEmpty())		
		{
		$insertAttr11=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '11', 'Attrb_Value_ID' =>$chkAttr11[0]));
		
		}
		
	
		else
		{
		$attr11ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '11' , 'Attrb_Value' => $Attr11,'Attrb_Unit_ID'=>$Attr11_U));
		$insertAttr11=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '11', 'Attrb_Value_ID' =>$attr11ID));
		}
	}
	if(!empty($Attr12))
	{
		$chkAttr12=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr12)->pluck('Attrb_Value_ID');

		if(!$chkAttr12->isEmpty())		
		{
		$insertAttr12=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '12', 'Attrb_Value_ID' =>$chkAttr12[0]));
		
		}
		
	
		else
		{
		$attr12ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '12' , 'Attrb_Value' => $Attr12,'Attrb_Unit_ID'=>$Attr12_U));
		$insertAttr12=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '12', 'Attrb_Value_ID' =>$attr12ID));
		}
	}
	if(!empty($Attr13))
	{
		$chkAttr13=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr13)->pluck('Attrb_Value_ID');

		if(!$chkAttr13->isEmpty())		
		{
		$insertAttr13=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '13', 'Attrb_Value_ID' =>$chkAttr13[0]));
		
		}
		
	
		else
		{
		$attr13ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '13' , 'Attrb_Value' => $Attr13,'Attrb_Unit_ID'=>$Attr13_U));
		$insertAttr13=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '13', 'Attrb_Value_ID' =>$attr13ID));
		}
	}
	if(!empty($Attr14))
	{
		$chkAttr14=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr14)->pluck('Attrb_Value_ID');

		if(!$chkAttr14->isEmpty())		
		{
		$insertAttr14=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '14', 'Attrb_Value_ID' =>$chkAttr14[0]));
		
		}
		
	
		else
		{
		$attr14ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '14' , 'Attrb_Value' => $Attr14,'Attrb_Unit_ID'=>$Attr14_U));
		$insertAttr14=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '14', 'Attrb_Value_ID' =>$attr14ID));
		}
	}
	if(!empty($Attr15))
	{
		$chkAttr15=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr15)->pluck('Attrb_Value_ID');

		if(!$chkAttr15->isEmpty())		
		{
		$insertAttr15=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '15', 'Attrb_Value_ID' =>$chkAttr15[0]));
		
		}
		
	
		else
		{
		$attr15ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '15' , 'Attrb_Value' => $Attr15,'Attrb_Unit_ID'=>$Attr15_U));
		$insertAttr15=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '15', 'Attrb_Value_ID' =>$attr15ID));
		}
	}
	if(!empty($Attr16))
	{
		$chkAttr16=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr16)->pluck('Attrb_Value_ID');

		if(!$chkAttr16->isEmpty())		
		{
		$insertAttr16=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '16', 'Attrb_Value_ID' =>$chkAttr16[0]));
		
		}
		
	
		else
		{
		$attr16ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '16' , 'Attrb_Value' => $Attr16,'Attrb_Unit_ID'=>$Attr16_U));
		$insertAttr16=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '16', 'Attrb_Value_ID' =>$attr16ID));
		}
	}
	

	

			  
			  

            }
			}
		}
		
			
		}
	
		$resp=array("Success" =>true, $grpID, $BrandID);
			return $resp;
		
		}
	
		
			
			}*/
			if ($req->file('fileKey')) 
	{
       $path = $req->file('fileKey')->getRealPath();
        $data = \Excel::load($path)->get();

        if ($data->count()) 
		{
            foreach ($data as $key => $value) 
			{
				
				$Attr1= $value -> attr_itemtype;
			  $Attr2= $value-> attr_material;
			  $Attr3= $value -> attr_modelname;
			  $Attr4 = $value -> attr_modelnumber;
			  $Attr5= $value -> attr_color;
			  $Attr6 = $value -> attr_size;
			  $Attr7 = $value -> attr_pressurerating;
			  $Attr8= $value -> attr_unit;
				$Attr9 = $value -> attr_gauge;
				$Attr10 = $value -> attr_length;
				$Attr11 = $value -> attr_breadth;
				$Attr12 = $value -> attr_height;
				$Attr13 = $value -> attr_thick;
				$Attr14 = $value -> attr_diameter;
				$Attr15 = $value -> attr_radius;
				$Attr16 = $value -> attr_volume;
				$Attr10_U = $value -> attr_length_unit;
				$Attr11_U = $value -> attr_breadth_unit;
				$Attr12_U = $value -> attr_height_unit;
				$Attr13_U = $value -> attr_thick_unit;
				$Attr14_U = $value -> attr_diameter_unit;
				$Attr15_U = $value -> attr_radius_unit;
				$Attr16_U = $value -> attr_volume_unit;
				
				$grpID=\DB::table('prod_groups')->where('Group_Name', $value -> group)->pluck('Group_ID');
				$BrandID=\DB::table('prod_brands')->where('Brand_Name', $value -> brand)->pluck('Brand_ID');

				if($grpID[0]==11)
				{
					if(!empty($Attr13))
					{
				$attrb_size=$Attr13.$Attr13_U."(t)";
				$productName=$value['itemname']." ".$Attr1." ".$Attr2." ".$Attr3." ".$Attr5." ".$attrb_size." ".$Attr7." ".$Attr8." ".$value -> brand;
				$prod_exists=\DB::table('products')->where('Prod_Name', $productName)->get();
				$procCount=count($prod_exists);
				if($procCount==0)
				{
				
				$arr = array( 
                          "ItemName" => $value->itemname,
                          "Prod_Name" => $productName,
						  "Group_ID" => $grpID[0],
						  "Brand_ID" => $BrandID[0],
						  "UnitOfMeasure" => $Attr8
						  
                           );
			
            
            if (!empty($arr)) 
			{
              $newProd = \DB::table('products')->insertGetID($arr);
			  if(!empty($newProd))
			  {
			  

			  if(!empty($Attr1))
				{
				$chkAttr1=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr1)->pluck('Attrb_Value_ID');
		

				if(!$chkAttr1->isEmpty())		
					{

						$insertAttr1=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '1', 'Attrb_Value_ID' =>$chkAttr1[0]));
			
				
					}
		
	
					else
					{
						$attr1ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '1' , 'Attrb_Value' => $Attr1));
						$insertAttr1=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '1', 'Attrb_Value_ID' =>$attr1ID));
			
					}
		
				}



			if(!empty($Attr2))
			{
				$chkAttr2=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr2)->pluck('Attrb_Value_ID');

				if(!$chkAttr2->isEmpty())		
					{
					$insertAttr2=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '2', 'Attrb_Value_ID' =>$chkAttr2[0]));
		
					}
		
	
				else
					{
					$attr2ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '2' , 'Attrb_Value' => $Attr2));
					$insertAttr2=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '2', 'Attrb_Value_ID' =>$attr2ID));
					}
			}
	
			if(!empty($Attr3))
			{
				$chkAttr3=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr3)->pluck('Attrb_Value_ID');

				if(!$chkAttr3->isEmpty())		
					{
				$insertAttr3=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '3', 'Attrb_Value_ID' =>$chkAttr3[0]));
		
					}
		
	
					else
					{
					$attr3ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '3' , 'Attrb_Value' => $Attr3));
					$insertAttr3=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '3', 'Attrb_Value_ID' =>$attr3ID));
					}
			}
	 		if(!empty($Attr4))
			{
				$chkAttr4=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr4)->pluck('Attrb_Value_ID');

				if(!$chkAttr4->isEmpty())		
				{
					$insertAttr4=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '4', 'Attrb_Value_ID' =>$chkAttr4[0]));
		
				}
		
	
			else
			{
				$attr4ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '4' , 'Attrb_Value' => $Attr4));
				$insertAttr4=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '4', 'Attrb_Value_ID' =>$attr4ID));
			}
	}
	 if(!empty($Attr5))
	{
		$chkAttr5=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr5)->pluck('Attrb_Value_ID');

		if(!$chkAttr5->isEmpty())		
		{
		$insertAttr5=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '5', 'Attrb_Value_ID' =>$chkAttr5[0]));
		
		}
		
	
		else
		{
		$attr5ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '5' , 'Attrb_Value' => $Attr5));
		$insertAttr5=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '5', 'Attrb_Value_ID' =>$attr5ID));
		}
	}
	 if(!empty($Attr6))
	{
		$chkAttr6=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr6)->pluck('Attrb_Value_ID');

		if(!$chkAttr6->isEmpty())		
		{
		$insertAttr6=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '6', 'Attrb_Value_ID' =>$chkAttr6[0]));
		
		}
		
	
		else
		{
		$attr6ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '6' , 'Attrb_Value' => $Attr6));
		$insertAttr6=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '6', 'Attrb_Value_ID' =>$attr6ID));
		}
	}
	 if(!empty($Attr7))
	{
		$chkAttr7=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr7)->pluck('Attrb_Value_ID');

		if(!$chkAttr7->isEmpty())		
		{
		$insertAttr7=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '7', 'Attrb_Value_ID' =>$chkAttr7[0]));
		
		}
		
	
		else
		{
		$attr7ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '7' , 'Attrb_Value' => $Attr7));
		$insertAttr7=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '7', 'Attrb_Value_ID' =>$attr7ID));
		}
	}
	if(!empty($Attr8))
	{
		$chkAttr8=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr8)->pluck('Attrb_Value_ID');

		if(!$chkAttr8->isEmpty())		
		{
		$insertAttr8=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '8', 'Attrb_Value_ID' =>$chkAttr8[0]));
		
		}
		
	
		else
		{
		$attr8ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '8' , 'Attrb_Value' => $Attr8));
		$insertAttr8=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '8', 'Attrb_Value_ID' =>$attr8ID));
		}
	}
	if(!empty($Attr9))
	{
		$chkAttr9=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr9)->pluck('Attrb_Value_ID');

		if(!$chkAttr9->isEmpty())		
		{
		$insertAttr9=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '9', 'Attrb_Value_ID' =>$chkAttr9[0]));
		
		}
		
	
		else
		{
		$attr9ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '9' , 'Attrb_Value' => $Attr9));
		$insertAttr9=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '9', 'Attrb_Value_ID' =>$attr9ID));
		}
	}
	if(!empty($Attr10))
	{
		$chkAttr10=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr10)->pluck('Attrb_Value_ID');

		if(!$chkAttr10->isEmpty())		
		{
		$insertAttr10=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '10', 'Attrb_Value_ID' =>$chkAttr10[0]));
		
		}
		
	
		else
		{
		$attr10ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '10' , 'Attrb_Value' => $Attr10,'Attrb_Unit_ID'=>$Attr10_U));
		$insertAttr10=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '10', 'Attrb_Value_ID' =>$attr10ID));
		}
	}
	if(!empty($Attr11))
	{
		$chkAttr11=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr11)->pluck('Attrb_Value_ID');

		if(!$chkAttr11->isEmpty())		
		{
		$insertAttr11=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '11', 'Attrb_Value_ID' =>$chkAttr11[0]));
		
		}
		
	
		else
		{
		$attr11ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '11' , 'Attrb_Value' => $Attr11,'Attrb_Unit_ID'=>$Attr11_U));
		$insertAttr11=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '11', 'Attrb_Value_ID' =>$attr11ID));
		}
	}
	if(!empty($Attr12))
	{
		$chkAttr12=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr12)->pluck('Attrb_Value_ID');

		if(!$chkAttr12->isEmpty())		
		{
		$insertAttr12=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '12', 'Attrb_Value_ID' =>$chkAttr12[0]));
		
		}
		
	
		else
		{
		$attr12ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '12' , 'Attrb_Value' => $Attr12,'Attrb_Unit_ID'=>$Attr12_U));
		$insertAttr12=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '12', 'Attrb_Value_ID' =>$attr12ID));
		}
	}
	if(!empty($Attr13))
	{
		$chkAttr13=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr13)->pluck('Attrb_Value_ID');

		if(!$chkAttr13->isEmpty())		
		{
		$insertAttr13=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '13', 'Attrb_Value_ID' =>$chkAttr13[0]));
		
		}
		
	
		else
		{
		$attr13ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '13' , 'Attrb_Value' => $Attr13,'Attrb_Unit_ID'=>$Attr13_U));
		$insertAttr13=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '13', 'Attrb_Value_ID' =>$attr13ID));
		}
	}
	if(!empty($Attr14))
	{
		$chkAttr14=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr14)->pluck('Attrb_Value_ID');

		if(!$chkAttr14->isEmpty())		
		{
		$insertAttr14=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '14', 'Attrb_Value_ID' =>$chkAttr14[0]));
		
		}
		
	
		else
		{
		$attr14ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '14' , 'Attrb_Value' => $Attr14,'Attrb_Unit_ID'=>$Attr14_U));
		$insertAttr14=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '14', 'Attrb_Value_ID' =>$attr14ID));
		}
	}
	if(!empty($Attr15))
	{
		$chkAttr15=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr15)->pluck('Attrb_Value_ID');

		if(!$chkAttr15->isEmpty())		
		{
		$insertAttr15=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '15', 'Attrb_Value_ID' =>$chkAttr15[0]));
		
		}
		
	
		else
		{
		$attr15ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '15' , 'Attrb_Value' => $Attr15,'Attrb_Unit_ID'=>$Attr15_U));
		$insertAttr15=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '15', 'Attrb_Value_ID' =>$attr15ID));
		}
	}
	if(!empty($Attr16))
	{
		$chkAttr16=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr16)->pluck('Attrb_Value_ID');

		if(!$chkAttr16->isEmpty())		
		{
		$insertAttr16=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '16', 'Attrb_Value_ID' =>$chkAttr16[0]));
		
		}
		
	
		else
		{
		$attr16ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '16' , 'Attrb_Value' => $Attr16,'Attrb_Unit_ID'=>$Attr16_U));
		$insertAttr16=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '16', 'Attrb_Value_ID' =>$attr16ID));
		}
	}
}
			}
		}
	}

else
{
	
				$productName=$value['itemname']." ".$Attr1." ".$Attr2." ".$Attr3." ".$Attr5." ".$Attr7." ".$Attr8." ".$value -> brand;
				$prod_exists=\DB::table('products')->where('Prod_Name', $productName)->get();
				$procCount=count($prod_exists);
				if($procCount==0)
				{
				
				$arr = array( 
                          "ItemName" => $value->itemname,
                          "Prod_Name" => $productName,
						  "Group_ID" => $grpID[0],
						  "Brand_ID" => $BrandID[0],
						  "UnitOfMeasure" => $Attr8
						  
                           );
			
            
            if (!empty($arr)) 
			{
              $newProd = \DB::table('products')->insertGetID($arr);
			  if(!empty($newProd))
			  {
			  

			  if(!empty($Attr1))
				{
				$chkAttr1=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr1)->pluck('Attrb_Value_ID');
		

				if(!$chkAttr1->isEmpty())		
					{

						$insertAttr1=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '1', 'Attrb_Value_ID' =>$chkAttr1[0]));
			
				
					}
		
	
					else
					{
						$attr1ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '1' , 'Attrb_Value' => $Attr1));
						$insertAttr1=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '1', 'Attrb_Value_ID' =>$attr1ID));
			
					}
		
				}



			if(!empty($Attr2))
			{
				$chkAttr2=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr2)->pluck('Attrb_Value_ID');

				if(!$chkAttr2->isEmpty())		
					{
					$insertAttr2=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '2', 'Attrb_Value_ID' =>$chkAttr2[0]));
		
					}
		
	
				else
					{
					$attr2ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '2' , 'Attrb_Value' => $Attr2));
					$insertAttr2=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '2', 'Attrb_Value_ID' =>$attr2ID));
					}
			}
	
			if(!empty($Attr3))
			{
				$chkAttr3=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr3)->pluck('Attrb_Value_ID');

				if(!$chkAttr3->isEmpty())		
					{
				$insertAttr3=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '3', 'Attrb_Value_ID' =>$chkAttr3[0]));
		
					}
		
	
					else
					{
					$attr3ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '3' , 'Attrb_Value' => $Attr3));
					$insertAttr3=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '3', 'Attrb_Value_ID' =>$attr3ID));
					}
			}
	 		if(!empty($Attr4))
			{
				$chkAttr4=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr4)->pluck('Attrb_Value_ID');

				if(!$chkAttr4->isEmpty())		
				{
					$insertAttr4=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '4', 'Attrb_Value_ID' =>$chkAttr4[0]));
		
				}
		
	
			else
			{
				$attr4ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '4' , 'Attrb_Value' => $Attr4));
				$insertAttr4=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '4', 'Attrb_Value_ID' =>$attr4ID));
			}
	}
	 if(!empty($Attr5))
	{
		$chkAttr5=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr5)->pluck('Attrb_Value_ID');

		if(!$chkAttr5->isEmpty())		
		{
		$insertAttr5=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '5', 'Attrb_Value_ID' =>$chkAttr5[0]));
		
		}
		
	
		else
		{
		$attr5ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '5' , 'Attrb_Value' => $Attr5));
		$insertAttr5=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '5', 'Attrb_Value_ID' =>$attr5ID));
		}
	}
	 if(!empty($Attr6))
	{
		$chkAttr6=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr6)->pluck('Attrb_Value_ID');

		if(!$chkAttr6->isEmpty())		
		{
		$insertAttr6=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '6', 'Attrb_Value_ID' =>$chkAttr6[0]));
		
		}
		
	
		else
		{
		$attr6ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '6' , 'Attrb_Value' => $Attr6));
		$insertAttr6=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '6', 'Attrb_Value_ID' =>$attr6ID));
		}
	}
	 if(!empty($Attr7))
	{
		$chkAttr7=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr7)->pluck('Attrb_Value_ID');

		if(!$chkAttr7->isEmpty())		
		{
		$insertAttr7=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '7', 'Attrb_Value_ID' =>$chkAttr7[0]));
		
		}
		
	
		else
		{
		$attr7ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '7' , 'Attrb_Value' => $Attr7));
		$insertAttr7=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '7', 'Attrb_Value_ID' =>$attr7ID));
		}
	}
	if(!empty($Attr8))
	{
		$chkAttr8=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr8)->pluck('Attrb_Value_ID');

		if(!$chkAttr8->isEmpty())		
		{
		$insertAttr8=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '8', 'Attrb_Value_ID' =>$chkAttr8[0]));
		
		}
		
	
		else
		{
		$attr8ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '8' , 'Attrb_Value' => $Attr8));
		$insertAttr8=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '8', 'Attrb_Value_ID' =>$attr8ID));
		}
	}
	if(!empty($Attr9))
	{
		$chkAttr9=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr9)->pluck('Attrb_Value_ID');

		if(!$chkAttr9->isEmpty())		
		{
		$insertAttr9=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '9', 'Attrb_Value_ID' =>$chkAttr9[0]));
		
		}
		
	
		else
		{
		$attr9ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '9' , 'Attrb_Value' => $Attr9));
		$insertAttr9=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '9', 'Attrb_Value_ID' =>$attr9ID));
		}
	}
	if(!empty($Attr10))
	{
		$chkAttr10=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr10)->pluck('Attrb_Value_ID');

		if(!$chkAttr10->isEmpty())		
		{
		$insertAttr10=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '10', 'Attrb_Value_ID' =>$chkAttr10[0]));
		
		}
		
	
		else
		{
		$attr10ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '10' , 'Attrb_Value' => $Attr10,'Attrb_Unit_ID'=>$Attr10_U));
		$insertAttr10=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '10', 'Attrb_Value_ID' =>$attr10ID));
		}
	}
	if(!empty($Attr11))
	{
		$chkAttr11=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr11)->pluck('Attrb_Value_ID');

		if(!$chkAttr11->isEmpty())		
		{
		$insertAttr11=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '11', 'Attrb_Value_ID' =>$chkAttr11[0]));
		
		}
		
	
		else
		{
		$attr11ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '11' , 'Attrb_Value' => $Attr11,'Attrb_Unit_ID'=>$Attr11_U));
		$insertAttr11=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '11', 'Attrb_Value_ID' =>$attr11ID));
		}
	}
	if(!empty($Attr12))
	{
		$chkAttr12=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr12)->pluck('Attrb_Value_ID');

		if(!$chkAttr12->isEmpty())		
		{
		$insertAttr12=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '12', 'Attrb_Value_ID' =>$chkAttr12[0]));
		
		}
		
	
		else
		{
		$attr12ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '12' , 'Attrb_Value' => $Attr12,'Attrb_Unit_ID'=>$Attr12_U));
		$insertAttr12=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '12', 'Attrb_Value_ID' =>$attr12ID));
		}
	}
	if(!empty($Attr13))
	{
		$chkAttr13=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr13)->pluck('Attrb_Value_ID');

		if(!$chkAttr13->isEmpty())		
		{
		$insertAttr13=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '13', 'Attrb_Value_ID' =>$chkAttr13[0]));
		
		}
		
	
		else
		{
		$attr13ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '13' , 'Attrb_Value' => $Attr13,'Attrb_Unit_ID'=>$Attr13_U));
		$insertAttr13=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '13', 'Attrb_Value_ID' =>$attr13ID));
		}
	}
	if(!empty($Attr14))
	{
		$chkAttr14=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr14)->pluck('Attrb_Value_ID');

		if(!$chkAttr14->isEmpty())		
		{
		$insertAttr14=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '14', 'Attrb_Value_ID' =>$chkAttr14[0]));
		
		}
		
	
		else
		{
		$attr14ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '14' , 'Attrb_Value' => $Attr14,'Attrb_Unit_ID'=>$Attr14_U));
		$insertAttr14=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '14', 'Attrb_Value_ID' =>$attr14ID));
		}
	}
	if(!empty($Attr15))
	{
		$chkAttr15=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr15)->pluck('Attrb_Value_ID');

		if(!$chkAttr15->isEmpty())		
		{
		$insertAttr15=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '15', 'Attrb_Value_ID' =>$chkAttr15[0]));
		
		}
		
	
		else
		{
		$attr15ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '15' , 'Attrb_Value' => $Attr15,'Attrb_Unit_ID'=>$Attr15_U));
		$insertAttr15=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '15', 'Attrb_Value_ID' =>$attr15ID));
		}
	}
	if(!empty($Attr16))
	{
		$chkAttr16=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr16)->pluck('Attrb_Value_ID');

		if(!$chkAttr16->isEmpty())		
		{
		$insertAttr16=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '16', 'Attrb_Value_ID' =>$chkAttr16[0]));
		
		}
		
	
		else
		{
		$attr16ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '16' , 'Attrb_Value' => $Attr16,'Attrb_Unit_ID'=>$Attr16_U));
		$insertAttr16=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '16', 'Attrb_Value_ID' =>$attr16ID));
		}
	}
}
			}
		}
	}
}




	
				
				else if($grpID[0]==12)
				{
					if(!empty($Attr14))
					{
				//	$attrb_size=$Attr11.$Attr11_U."(b)"."x".$Attr12.$Attr12_U."(h)";
				$attrb_size=$Attr14.$Attr14_U."(d)";
				$productName=$value['itemname']." ".$Attr1." ".$Attr2." ".$Attr5." ".$attrb_size." ".$Attr7." ".$Attr8." ".$value -> brand;
				$prod_exists=\DB::table('products')->where('Prod_Name', $productName)->get();
				$procCount=count($prod_exists);
				if($procCount==0)
				{
				
				$arr = array( 
                          "ItemName" => $value->itemname,
                          "Prod_Name" => $productName,
						  "Group_ID" => $grpID[0],
						  "Brand_ID" => $BrandID[0],
						  "UnitOfMeasure" => $Attr8
						  
                           );
			
            
            if (!empty($arr)) 
			{
              $newProd = \DB::table('products')->insertGetID($arr);
			  if(!empty($newProd))
			  {
			  

			  if(!empty($Attr1))
				{
				$chkAttr1=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr1)->pluck('Attrb_Value_ID');
		

				if(!$chkAttr1->isEmpty())		
					{

						$insertAttr1=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '1', 'Attrb_Value_ID' =>$chkAttr1[0]));
			
				
					}
		
	
					else
					{
						$attr1ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '1' , 'Attrb_Value' => $Attr1));
						$insertAttr1=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '1', 'Attrb_Value_ID' =>$attr1ID));
			
					}
		
				}



			if(!empty($Attr2))
			{
				$chkAttr2=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr2)->pluck('Attrb_Value_ID');

				if(!$chkAttr2->isEmpty())		
					{
					$insertAttr2=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '2', 'Attrb_Value_ID' =>$chkAttr2[0]));
		
					}
		
	
				else
					{
					$attr2ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '2' , 'Attrb_Value' => $Attr2));
					$insertAttr2=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '2', 'Attrb_Value_ID' =>$attr2ID));
					}
			}
	
			if(!empty($Attr3))
			{
				$chkAttr3=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr3)->pluck('Attrb_Value_ID');

				if(!$chkAttr3->isEmpty())		
					{
				$insertAttr3=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '3', 'Attrb_Value_ID' =>$chkAttr3[0]));
		
					}
		
	
					else
					{
					$attr3ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '3' , 'Attrb_Value' => $Attr3));
					$insertAttr3=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '3', 'Attrb_Value_ID' =>$attr3ID));
					}
			}
	 		if(!empty($Attr4))
			{
				$chkAttr4=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr4)->pluck('Attrb_Value_ID');

				if(!$chkAttr4->isEmpty())		
				{
					$insertAttr4=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '4', 'Attrb_Value_ID' =>$chkAttr4[0]));
		
				}
		
	
			else
			{
				$attr4ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '4' , 'Attrb_Value' => $Attr4));
				$insertAttr4=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '4', 'Attrb_Value_ID' =>$attr4ID));
			}
	}
	 if(!empty($Attr5))
	{
		$chkAttr5=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr5)->pluck('Attrb_Value_ID');

		if(!$chkAttr5->isEmpty())		
		{
		$insertAttr5=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '5', 'Attrb_Value_ID' =>$chkAttr5[0]));
		
		}
		
	
		else
		{
		$attr5ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '5' , 'Attrb_Value' => $Attr5));
		$insertAttr5=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '5', 'Attrb_Value_ID' =>$attr5ID));
		}
	}
	 if(!empty($Attr6))
	{
		$chkAttr6=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr6)->pluck('Attrb_Value_ID');

		if(!$chkAttr6->isEmpty())		
		{
		$insertAttr6=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '6', 'Attrb_Value_ID' =>$chkAttr6[0]));
		
		}
		
	
		else
		{
		$attr6ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '6' , 'Attrb_Value' => $Attr6));
		$insertAttr6=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '6', 'Attrb_Value_ID' =>$attr6ID));
		}
	}
	 if(!empty($Attr7))
	{
		$chkAttr7=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr7)->pluck('Attrb_Value_ID');

		if(!$chkAttr7->isEmpty())		
		{
		$insertAttr7=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '7', 'Attrb_Value_ID' =>$chkAttr7[0]));
		
		}
		
	
		else
		{
		$attr7ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '7' , 'Attrb_Value' => $Attr7));
		$insertAttr7=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '7', 'Attrb_Value_ID' =>$attr7ID));
		}
	}
	if(!empty($Attr8))
	{
		$chkAttr8=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr8)->pluck('Attrb_Value_ID');

		if(!$chkAttr8->isEmpty())		
		{
		$insertAttr8=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '8', 'Attrb_Value_ID' =>$chkAttr8[0]));
		
		}
		
	
		else
		{
		$attr8ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '8' , 'Attrb_Value' => $Attr8));
		$insertAttr8=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '8', 'Attrb_Value_ID' =>$attr8ID));
		}
	}
	if(!empty($Attr9))
	{
		$chkAttr9=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr9)->pluck('Attrb_Value_ID');

		if(!$chkAttr9->isEmpty())		
		{
		$insertAttr9=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '9', 'Attrb_Value_ID' =>$chkAttr9[0]));
		
		}
		
	
		else
		{
		$attr9ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '9' , 'Attrb_Value' => $Attr9));
		$insertAttr9=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '9', 'Attrb_Value_ID' =>$attr9ID));
		}
	}
	if(!empty($Attr10))
	{
		$chkAttr10=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr10)->pluck('Attrb_Value_ID');

		if(!$chkAttr10->isEmpty())		
		{
		$insertAttr10=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '10', 'Attrb_Value_ID' =>$chkAttr10[0]));
		
		}
		
	
		else
		{
		$attr10ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '10' , 'Attrb_Value' => $Attr10,'Attrb_Unit_ID'=>$Attr10_U));
		$insertAttr10=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '10', 'Attrb_Value_ID' =>$attr10ID));
		}
	}
	if(!empty($Attr11))
	{
		$chkAttr11=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr11)->pluck('Attrb_Value_ID');

		if(!$chkAttr11->isEmpty())		
		{
		$insertAttr11=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '11', 'Attrb_Value_ID' =>$chkAttr11[0]));
		
		}
		
	
		else
		{
		$attr11ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '11' , 'Attrb_Value' => $Attr11,'Attrb_Unit_ID'=>$Attr11_U));
		$insertAttr11=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '11', 'Attrb_Value_ID' =>$attr11ID));
		}
	}
	if(!empty($Attr12))
	{
		$chkAttr12=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr12)->pluck('Attrb_Value_ID');

		if(!$chkAttr12->isEmpty())		
		{
		$insertAttr12=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '12', 'Attrb_Value_ID' =>$chkAttr12[0]));
		
		}
		
	
		else
		{
		$attr12ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '12' , 'Attrb_Value' => $Attr12,'Attrb_Unit_ID'=>$Attr12_U));
		$insertAttr12=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '12', 'Attrb_Value_ID' =>$attr12ID));
		}
	}
	if(!empty($Attr13))
	{
		$chkAttr13=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr13)->pluck('Attrb_Value_ID');

		if(!$chkAttr13->isEmpty())		
		{
		$insertAttr13=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '13', 'Attrb_Value_ID' =>$chkAttr13[0]));
		
		}
		
	
		else
		{
		$attr13ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '13' , 'Attrb_Value' => $Attr13,'Attrb_Unit_ID'=>$Attr13_U));
		$insertAttr13=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '13', 'Attrb_Value_ID' =>$attr13ID));
		}
	}
	if(!empty($Attr14))
	{
		$chkAttr14=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr14)->pluck('Attrb_Value_ID');

		if(!$chkAttr14->isEmpty())		
		{
		$insertAttr14=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '14', 'Attrb_Value_ID' =>$chkAttr14[0]));
		
		}
		
	
		else
		{
		$attr14ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '14' , 'Attrb_Value' => $Attr14,'Attrb_Unit_ID'=>$Attr14_U));
		$insertAttr14=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '14', 'Attrb_Value_ID' =>$attr14ID));
		}
	}
	if(!empty($Attr15))
	{
		$chkAttr15=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr15)->pluck('Attrb_Value_ID');

		if(!$chkAttr15->isEmpty())		
		{
		$insertAttr15=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '15', 'Attrb_Value_ID' =>$chkAttr15[0]));
		
		}
		
	
		else
		{
		$attr15ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '15' , 'Attrb_Value' => $Attr15,'Attrb_Unit_ID'=>$Attr15_U));
		$insertAttr15=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '15', 'Attrb_Value_ID' =>$attr15ID));
		}
	}
	if(!empty($Attr16))
	{
		$chkAttr16=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr16)->pluck('Attrb_Value_ID');

		if(!$chkAttr16->isEmpty())		
		{
		$insertAttr16=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '16', 'Attrb_Value_ID' =>$chkAttr16[0]));
		
		}
		
	
		else
		{
		$attr16ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '16' , 'Attrb_Value' => $Attr16,'Attrb_Unit_ID'=>$Attr16_U));
		$insertAttr16=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '16', 'Attrb_Value_ID' =>$attr16ID));
		}
	}
}
			}
		}
	}
	else{
	$attrb_size=$Attr11.$Attr11_U."(b)"."x".$Attr12.$Attr12_U."(h)";
		//$attrb_size=$Attr14.$Attr14_U."(d)";
		$productName=$value['itemname']." ".$Attr1." ".$Attr2." ".$Attr5." ".$attrb_size." ".$Attr7." ".$Attr8." ".$value -> brand;
		$prod_exists=\DB::table('products')->where('Prod_Name', $productName)->get();
		$procCount=count($prod_exists);
		if($procCount==0)
		{
		
		$arr = array( 
											"ItemName" => $value->itemname,
											"Prod_Name" => $productName,
					"Group_ID" => $grpID[0],
					"Brand_ID" => $BrandID[0],
					"UnitOfMeasure" => $Attr8
					
											 );
	
				
				if (!empty($arr)) 
	{
					$newProd = \DB::table('products')->insertGetID($arr);
		if(!empty($newProd))
		{
		

		if(!empty($Attr1))
		{
		$chkAttr1=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr1)->pluck('Attrb_Value_ID');


		if(!$chkAttr1->isEmpty())		
			{

				$insertAttr1=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '1', 'Attrb_Value_ID' =>$chkAttr1[0]));
	
		
			}


			else
			{
				$attr1ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '1' , 'Attrb_Value' => $Attr1));
				$insertAttr1=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '1', 'Attrb_Value_ID' =>$attr1ID));
	
			}

		}



	if(!empty($Attr2))
	{
		$chkAttr2=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr2)->pluck('Attrb_Value_ID');

		if(!$chkAttr2->isEmpty())		
			{
			$insertAttr2=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '2', 'Attrb_Value_ID' =>$chkAttr2[0]));

			}


		else
			{
			$attr2ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '2' , 'Attrb_Value' => $Attr2));
			$insertAttr2=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '2', 'Attrb_Value_ID' =>$attr2ID));
			}
	}

	if(!empty($Attr3))
	{
		$chkAttr3=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr3)->pluck('Attrb_Value_ID');

		if(!$chkAttr3->isEmpty())		
			{
		$insertAttr3=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '3', 'Attrb_Value_ID' =>$chkAttr3[0]));

			}


			else
			{
			$attr3ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '3' , 'Attrb_Value' => $Attr3));
			$insertAttr3=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '3', 'Attrb_Value_ID' =>$attr3ID));
			}
	}
	 if(!empty($Attr4))
	{
		$chkAttr4=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr4)->pluck('Attrb_Value_ID');

		if(!$chkAttr4->isEmpty())		
		{
			$insertAttr4=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '4', 'Attrb_Value_ID' =>$chkAttr4[0]));

		}


	else
	{
		$attr4ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '4' , 'Attrb_Value' => $Attr4));
		$insertAttr4=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '4', 'Attrb_Value_ID' =>$attr4ID));
	}
}
if(!empty($Attr5))
{
$chkAttr5=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr5)->pluck('Attrb_Value_ID');

if(!$chkAttr5->isEmpty())		
{
$insertAttr5=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '5', 'Attrb_Value_ID' =>$chkAttr5[0]));

}


else
{
$attr5ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '5' , 'Attrb_Value' => $Attr5));
$insertAttr5=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '5', 'Attrb_Value_ID' =>$attr5ID));
}
}
if(!empty($Attr6))
{
$chkAttr6=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr6)->pluck('Attrb_Value_ID');

if(!$chkAttr6->isEmpty())		
{
$insertAttr6=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '6', 'Attrb_Value_ID' =>$chkAttr6[0]));

}


else
{
$attr6ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '6' , 'Attrb_Value' => $Attr6));
$insertAttr6=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '6', 'Attrb_Value_ID' =>$attr6ID));
}
}
if(!empty($Attr7))
{
$chkAttr7=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr7)->pluck('Attrb_Value_ID');

if(!$chkAttr7->isEmpty())		
{
$insertAttr7=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '7', 'Attrb_Value_ID' =>$chkAttr7[0]));

}


else
{
$attr7ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '7' , 'Attrb_Value' => $Attr7));
$insertAttr7=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '7', 'Attrb_Value_ID' =>$attr7ID));
}
}
if(!empty($Attr8))
{
$chkAttr8=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr8)->pluck('Attrb_Value_ID');

if(!$chkAttr8->isEmpty())		
{
$insertAttr8=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '8', 'Attrb_Value_ID' =>$chkAttr8[0]));

}


else
{
$attr8ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '8' , 'Attrb_Value' => $Attr8));
$insertAttr8=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '8', 'Attrb_Value_ID' =>$attr8ID));
}
}
if(!empty($Attr9))
{
$chkAttr9=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr9)->pluck('Attrb_Value_ID');

if(!$chkAttr9->isEmpty())		
{
$insertAttr9=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '9', 'Attrb_Value_ID' =>$chkAttr9[0]));

}


else
{
$attr9ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '9' , 'Attrb_Value' => $Attr9));
$insertAttr9=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '9', 'Attrb_Value_ID' =>$attr9ID));
}
}
if(!empty($Attr10))
{
$chkAttr10=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr10)->pluck('Attrb_Value_ID');

if(!$chkAttr10->isEmpty())		
{
$insertAttr10=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '10', 'Attrb_Value_ID' =>$chkAttr10[0]));

}


else
{
$attr10ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '10' , 'Attrb_Value' => $Attr10,'Attrb_Unit_ID'=>$Attr10_U));
$insertAttr10=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '10', 'Attrb_Value_ID' =>$attr10ID));
}
}
if(!empty($Attr11))
{
$chkAttr11=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr11)->pluck('Attrb_Value_ID');

if(!$chkAttr11->isEmpty())		
{
$insertAttr11=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '11', 'Attrb_Value_ID' =>$chkAttr11[0]));

}


else
{
$attr11ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '11' , 'Attrb_Value' => $Attr11,'Attrb_Unit_ID'=>$Attr11_U));
$insertAttr11=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '11', 'Attrb_Value_ID' =>$attr11ID));
}
}
if(!empty($Attr12))
{
$chkAttr12=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr12)->pluck('Attrb_Value_ID');

if(!$chkAttr12->isEmpty())		
{
$insertAttr12=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '12', 'Attrb_Value_ID' =>$chkAttr12[0]));

}


else
{
$attr12ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '12' , 'Attrb_Value' => $Attr12,'Attrb_Unit_ID'=>$Attr12_U));
$insertAttr12=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '12', 'Attrb_Value_ID' =>$attr12ID));
}
}
if(!empty($Attr13))
{
$chkAttr13=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr13)->pluck('Attrb_Value_ID');

if(!$chkAttr13->isEmpty())		
{
$insertAttr13=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '13', 'Attrb_Value_ID' =>$chkAttr13[0]));

}


else
{
$attr13ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '13' , 'Attrb_Value' => $Attr13,'Attrb_Unit_ID'=>$Attr13_U));
$insertAttr13=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '13', 'Attrb_Value_ID' =>$attr13ID));
}
}
if(!empty($Attr14))
{
$chkAttr14=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr14)->pluck('Attrb_Value_ID');

if(!$chkAttr14->isEmpty())		
{
$insertAttr14=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '14', 'Attrb_Value_ID' =>$chkAttr14[0]));

}


else
{
$attr14ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '14' , 'Attrb_Value' => $Attr14,'Attrb_Unit_ID'=>$Attr14_U));
$insertAttr14=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '14', 'Attrb_Value_ID' =>$attr14ID));
}
}
if(!empty($Attr15))
{
$chkAttr15=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr15)->pluck('Attrb_Value_ID');

if(!$chkAttr15->isEmpty())		
{
$insertAttr15=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '15', 'Attrb_Value_ID' =>$chkAttr15[0]));

}


else
{
$attr15ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '15' , 'Attrb_Value' => $Attr15,'Attrb_Unit_ID'=>$Attr15_U));
$insertAttr15=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '15', 'Attrb_Value_ID' =>$attr15ID));
}
}
if(!empty($Attr16))
{
$chkAttr16=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr16)->pluck('Attrb_Value_ID');

if(!$chkAttr16->isEmpty())		
{
$insertAttr16=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '16', 'Attrb_Value_ID' =>$chkAttr16[0]));

}


else
{
$attr16ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '16' , 'Attrb_Value' => $Attr16,'Attrb_Unit_ID'=>$Attr16_U));
$insertAttr16=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '16', 'Attrb_Value_ID' =>$attr16ID));
}
}
}
	}
}

	}
				}
				else if($grpID[0]==19)
				{
				if(!empty($Attr14))
				{
					$attrb_size=$Attr14.$Attr14_U."(d)";
					$productName=$value['itemname']." ".$Attr1." ".$Attr2." ".$Attr5." ".$attrb_size." ".$Attr7." ".$Attr8." ".$value -> brand;
					$prod_exists=\DB::table('products')->where('Prod_Name', $productName)->get();
					$procCount=count($prod_exists);
					if($procCount==0)
					{
					
						$arr = array( 
							"ItemName" => $value->itemname,
							"Prod_Name" => $productName,
	"Group_ID" => $grpID[0],
	"Brand_ID" => $BrandID[0],
	"UnitOfMeasure" => $Attr8
														 );
				
							
							if (!empty($arr)) 
				{
								$newProd = \DB::table('products')->insertGetID($arr);
					if(!empty($newProd))
					{
					
	
					if(!empty($Attr1))
					{
					$chkAttr1=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr1)->pluck('Attrb_Value_ID');
			
	
					if(!$chkAttr1->isEmpty())		
						{
	
							$insertAttr1=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '1', 'Attrb_Value_ID' =>$chkAttr1[0]));
				
					
						}
			
		
						else
						{
							$attr1ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '1' , 'Attrb_Value' => $Attr1));
							$insertAttr1=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '1', 'Attrb_Value_ID' =>$attr1ID));
				
						}
			
					}
	
	
	
				if(!empty($Attr2))
				{
					$chkAttr2=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr2)->pluck('Attrb_Value_ID');
	
					if(!$chkAttr2->isEmpty())		
						{
						$insertAttr2=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '2', 'Attrb_Value_ID' =>$chkAttr2[0]));
			
						}
			
		
					else
						{
						$attr2ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '2' , 'Attrb_Value' => $Attr2));
						$insertAttr2=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '2', 'Attrb_Value_ID' =>$attr2ID));
						}
				}
		
				if(!empty($Attr3))
				{
					$chkAttr3=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr3)->pluck('Attrb_Value_ID');
	
					if(!$chkAttr3->isEmpty())		
						{
					$insertAttr3=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '3', 'Attrb_Value_ID' =>$chkAttr3[0]));
			
						}
			
		
						else
						{
						$attr3ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '3' , 'Attrb_Value' => $Attr3));
						$insertAttr3=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '3', 'Attrb_Value_ID' =>$attr3ID));
						}
				}
				 if(!empty($Attr4))
				{
					$chkAttr4=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr4)->pluck('Attrb_Value_ID');
	
					if(!$chkAttr4->isEmpty())		
					{
						$insertAttr4=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '4', 'Attrb_Value_ID' =>$chkAttr4[0]));
			
					}
			
		
				else
				{
					$attr4ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '4' , 'Attrb_Value' => $Attr4));
					$insertAttr4=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '4', 'Attrb_Value_ID' =>$attr4ID));
				}
		}
		 if(!empty($Attr5))
		{
			$chkAttr5=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr5)->pluck('Attrb_Value_ID');
	
			if(!$chkAttr5->isEmpty())		
			{
			$insertAttr5=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '5', 'Attrb_Value_ID' =>$chkAttr5[0]));
			
			}
			
		
			else
			{
			$attr5ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '5' , 'Attrb_Value' => $Attr5));
			$insertAttr5=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '5', 'Attrb_Value_ID' =>$attr5ID));
			}
		}
		 if(!empty($Attr6))
		{
			$chkAttr6=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr6)->pluck('Attrb_Value_ID');
	
			if(!$chkAttr6->isEmpty())		
			{
			$insertAttr6=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '6', 'Attrb_Value_ID' =>$chkAttr6[0]));
			
			}
			
		
			else
			{
			$attr6ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '6' , 'Attrb_Value' => $Attr6));
			$insertAttr6=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '6', 'Attrb_Value_ID' =>$attr6ID));
			}
		}
		 if(!empty($Attr7))
		{
			$chkAttr7=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr7)->pluck('Attrb_Value_ID');
	
			if(!$chkAttr7->isEmpty())		
			{
			$insertAttr7=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '7', 'Attrb_Value_ID' =>$chkAttr7[0]));
			
			}
			
		
			else
			{
			$attr7ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '7' , 'Attrb_Value' => $Attr7));
			$insertAttr7=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '7', 'Attrb_Value_ID' =>$attr7ID));
			}
		}
		if(!empty($Attr8))
		{
			$chkAttr8=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr8)->pluck('Attrb_Value_ID');
	
			if(!$chkAttr8->isEmpty())		
			{
			$insertAttr8=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '8', 'Attrb_Value_ID' =>$chkAttr8[0]));
			
			}
			
		
			else
			{
			$attr8ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '8' , 'Attrb_Value' => $Attr8));
			$insertAttr8=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '8', 'Attrb_Value_ID' =>$attr8ID));
			}
		}
		if(!empty($Attr9))
		{
			$chkAttr9=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr9)->pluck('Attrb_Value_ID');
	
			if(!$chkAttr9->isEmpty())		
			{
			$insertAttr9=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '9', 'Attrb_Value_ID' =>$chkAttr9[0]));
			
			}
			
		
			else
			{
			$attr9ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '9' , 'Attrb_Value' => $Attr9));
			$insertAttr9=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '9', 'Attrb_Value_ID' =>$attr9ID));
			}
		}
		if(!empty($Attr10))
		{
			$chkAttr10=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr10)->pluck('Attrb_Value_ID');
	
			if(!$chkAttr10->isEmpty())		
			{
			$insertAttr10=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '10', 'Attrb_Value_ID' =>$chkAttr10[0]));
			
			}
			
		
			else
			{
			$attr10ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '10' , 'Attrb_Value' => $Attr10,'Attrb_Unit_ID'=>$Attr10_U));
			$insertAttr10=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '10', 'Attrb_Value_ID' =>$attr10ID));
			}
		}
		if(!empty($Attr11))
		{
			$chkAttr11=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr11)->pluck('Attrb_Value_ID');
	
			if(!$chkAttr11->isEmpty())		
			{
			$insertAttr11=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '11', 'Attrb_Value_ID' =>$chkAttr11[0]));
			
			}
			
		
			else
			{
			$attr11ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '11' , 'Attrb_Value' => $Attr11,'Attrb_Unit_ID'=>$Attr11_U));
			$insertAttr11=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '11', 'Attrb_Value_ID' =>$attr11ID));
			}
		}
		if(!empty($Attr12))
		{
			$chkAttr12=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr12)->pluck('Attrb_Value_ID');
	
			if(!$chkAttr12->isEmpty())		
			{
			$insertAttr12=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '12', 'Attrb_Value_ID' =>$chkAttr12[0]));
			
			}
			
		
			else
			{
			$attr12ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '12' , 'Attrb_Value' => $Attr12,'Attrb_Unit_ID'=>$Attr12_U));
			$insertAttr12=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '12', 'Attrb_Value_ID' =>$attr12ID));
			}
		}
		if(!empty($Attr13))
		{
			$chkAttr13=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr13)->pluck('Attrb_Value_ID');
	
			if(!$chkAttr13->isEmpty())		
			{
			$insertAttr13=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '13', 'Attrb_Value_ID' =>$chkAttr13[0]));
			
			}
			
		
			else
			{
			$attr13ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '13' , 'Attrb_Value' => $Attr13,'Attrb_Unit_ID'=>$Attr13_U));
			$insertAttr13=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '13', 'Attrb_Value_ID' =>$attr13ID));
			}
		}
		if(!empty($Attr14))
		{
			$chkAttr14=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr14)->pluck('Attrb_Value_ID');
	
			if(!$chkAttr14->isEmpty())		
			{
			$insertAttr14=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '14', 'Attrb_Value_ID' =>$chkAttr14[0]));
			
			}
			
		
			else
			{
			$attr14ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '14' , 'Attrb_Value' => $Attr14,'Attrb_Unit_ID'=>$Attr14_U));
			$insertAttr14=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '14', 'Attrb_Value_ID' =>$attr14ID));
			}
		}
		if(!empty($Attr15))
		{
			$chkAttr15=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr15)->pluck('Attrb_Value_ID');
	
			if(!$chkAttr15->isEmpty())		
			{
			$insertAttr15=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '15', 'Attrb_Value_ID' =>$chkAttr15[0]));
			
			}
			
		
			else
			{
			$attr15ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '15' , 'Attrb_Value' => $Attr15,'Attrb_Unit_ID'=>$Attr15_U));
			$insertAttr15=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '15', 'Attrb_Value_ID' =>$attr15ID));
			}
		}
		if(!empty($Attr16))
		{
			$chkAttr16=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr16)->pluck('Attrb_Value_ID');
	
			if(!$chkAttr16->isEmpty())		
			{
			$insertAttr16=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '16', 'Attrb_Value_ID' =>$chkAttr16[0]));
			
			}
			
		
			else
			{
			$attr16ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '16' , 'Attrb_Value' => $Attr16,'Attrb_Unit_ID'=>$Attr16_U));
			$insertAttr16=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '16', 'Attrb_Value_ID' =>$attr16ID));
			}
		}
	}
				}
			}
				}
				else	if(!empty($Attr10))
					{
						$attrb_size=$Attr11.$Attr11_U."(b)"."x".$Attr12.$Attr12_U."(h)"." ".$Attr13.$Attr13_U."(t)"." ".$Attr10.$Attr10_U."(l)";
						$productName=$value['itemname']." ".$Attr1." ".$Attr2." ".$Attr5." ".$attrb_size." ".$Attr7." ".$Attr8." ".$value -> brand;
						$prod_exists=\DB::table('products')->where('Prod_Name', $productName)->get();
						$procCount=count($prod_exists);
						if($procCount==0)
						{
						
							$arr = array( 
								"ItemName" => $value->itemname,
								"Prod_Name" => $productName,
		"Group_ID" => $grpID[0],
		"Brand_ID" => $BrandID[0],
		"UnitOfMeasure" => $Attr8
															 );
					
								
								if (!empty($arr)) 
					{
									$newProd = \DB::table('products')->insertGetID($arr);
						if(!empty($newProd))
						{
						
		
						if(!empty($Attr1))
						{
						$chkAttr1=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr1)->pluck('Attrb_Value_ID');
				
		
						if(!$chkAttr1->isEmpty())		
							{
		
								$insertAttr1=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '1', 'Attrb_Value_ID' =>$chkAttr1[0]));
					
						
							}
				
			
							else
							{
								$attr1ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '1' , 'Attrb_Value' => $Attr1));
								$insertAttr1=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '1', 'Attrb_Value_ID' =>$attr1ID));
					
							}
				
						}
		
		
		
					if(!empty($Attr2))
					{
						$chkAttr2=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr2)->pluck('Attrb_Value_ID');
		
						if(!$chkAttr2->isEmpty())		
							{
							$insertAttr2=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '2', 'Attrb_Value_ID' =>$chkAttr2[0]));
				
							}
				
			
						else
							{
							$attr2ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '2' , 'Attrb_Value' => $Attr2));
							$insertAttr2=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '2', 'Attrb_Value_ID' =>$attr2ID));
							}
					}
			
					if(!empty($Attr3))
					{
						$chkAttr3=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr3)->pluck('Attrb_Value_ID');
		
						if(!$chkAttr3->isEmpty())		
							{
						$insertAttr3=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '3', 'Attrb_Value_ID' =>$chkAttr3[0]));
				
							}
				
			
							else
							{
							$attr3ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '3' , 'Attrb_Value' => $Attr3));
							$insertAttr3=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '3', 'Attrb_Value_ID' =>$attr3ID));
							}
					}
					 if(!empty($Attr4))
					{
						$chkAttr4=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr4)->pluck('Attrb_Value_ID');
		
						if(!$chkAttr4->isEmpty())		
						{
							$insertAttr4=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '4', 'Attrb_Value_ID' =>$chkAttr4[0]));
				
						}
				
			
					else
					{
						$attr4ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '4' , 'Attrb_Value' => $Attr4));
						$insertAttr4=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '4', 'Attrb_Value_ID' =>$attr4ID));
					}
			}
			 if(!empty($Attr5))
			{
				$chkAttr5=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr5)->pluck('Attrb_Value_ID');
		
				if(!$chkAttr5->isEmpty())		
				{
				$insertAttr5=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '5', 'Attrb_Value_ID' =>$chkAttr5[0]));
				
				}
				
			
				else
				{
				$attr5ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '5' , 'Attrb_Value' => $Attr5));
				$insertAttr5=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '5', 'Attrb_Value_ID' =>$attr5ID));
				}
			}
			 if(!empty($Attr6))
			{
				$chkAttr6=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr6)->pluck('Attrb_Value_ID');
		
				if(!$chkAttr6->isEmpty())		
				{
				$insertAttr6=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '6', 'Attrb_Value_ID' =>$chkAttr6[0]));
				
				}
				
			
				else
				{
				$attr6ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '6' , 'Attrb_Value' => $Attr6));
				$insertAttr6=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '6', 'Attrb_Value_ID' =>$attr6ID));
				}
			}
			 if(!empty($Attr7))
			{
				$chkAttr7=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr7)->pluck('Attrb_Value_ID');
		
				if(!$chkAttr7->isEmpty())		
				{
				$insertAttr7=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '7', 'Attrb_Value_ID' =>$chkAttr7[0]));
				
				}
				
			
				else
				{
				$attr7ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '7' , 'Attrb_Value' => $Attr7));
				$insertAttr7=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '7', 'Attrb_Value_ID' =>$attr7ID));
				}
			}
			if(!empty($Attr8))
			{
				$chkAttr8=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr8)->pluck('Attrb_Value_ID');
		
				if(!$chkAttr8->isEmpty())		
				{
				$insertAttr8=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '8', 'Attrb_Value_ID' =>$chkAttr8[0]));
				
				}
				
			
				else
				{
				$attr8ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '8' , 'Attrb_Value' => $Attr8));
				$insertAttr8=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '8', 'Attrb_Value_ID' =>$attr8ID));
				}
			}
			if(!empty($Attr9))
			{
				$chkAttr9=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr9)->pluck('Attrb_Value_ID');
		
				if(!$chkAttr9->isEmpty())		
				{
				$insertAttr9=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '9', 'Attrb_Value_ID' =>$chkAttr9[0]));
				
				}
				
			
				else
				{
				$attr9ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '9' , 'Attrb_Value' => $Attr9));
				$insertAttr9=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '9', 'Attrb_Value_ID' =>$attr9ID));
				}
			}
			if(!empty($Attr10))
			{
				$chkAttr10=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr10)->pluck('Attrb_Value_ID');
		
				if(!$chkAttr10->isEmpty())		
				{
				$insertAttr10=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '10', 'Attrb_Value_ID' =>$chkAttr10[0]));
				
				}
				
			
				else
				{
				$attr10ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '10' , 'Attrb_Value' => $Attr10,'Attrb_Unit_ID'=>$Attr10_U));
				$insertAttr10=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '10', 'Attrb_Value_ID' =>$attr10ID));
				}
			}
			if(!empty($Attr11))
			{
				$chkAttr11=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr11)->pluck('Attrb_Value_ID');
		
				if(!$chkAttr11->isEmpty())		
				{
				$insertAttr11=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '11', 'Attrb_Value_ID' =>$chkAttr11[0]));
				
				}
				
			
				else
				{
				$attr11ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '11' , 'Attrb_Value' => $Attr11,'Attrb_Unit_ID'=>$Attr11_U));
				$insertAttr11=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '11', 'Attrb_Value_ID' =>$attr11ID));
				}
			}
			if(!empty($Attr12))
			{
				$chkAttr12=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr12)->pluck('Attrb_Value_ID');
		
				if(!$chkAttr12->isEmpty())		
				{
				$insertAttr12=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '12', 'Attrb_Value_ID' =>$chkAttr12[0]));
				
				}
				
			
				else
				{
				$attr12ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '12' , 'Attrb_Value' => $Attr12,'Attrb_Unit_ID'=>$Attr12_U));
				$insertAttr12=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '12', 'Attrb_Value_ID' =>$attr12ID));
				}
			}
			if(!empty($Attr13))
			{
				$chkAttr13=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr13)->pluck('Attrb_Value_ID');
		
				if(!$chkAttr13->isEmpty())		
				{
				$insertAttr13=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '13', 'Attrb_Value_ID' =>$chkAttr13[0]));
				
				}
				
			
				else
				{
				$attr13ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '13' , 'Attrb_Value' => $Attr13,'Attrb_Unit_ID'=>$Attr13_U));
				$insertAttr13=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '13', 'Attrb_Value_ID' =>$attr13ID));
				}
			}
			if(!empty($Attr14))
			{
				$chkAttr14=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr14)->pluck('Attrb_Value_ID');
		
				if(!$chkAttr14->isEmpty())		
				{
				$insertAttr14=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '14', 'Attrb_Value_ID' =>$chkAttr14[0]));
				
				}
				
			
				else
				{
				$attr14ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '14' , 'Attrb_Value' => $Attr14,'Attrb_Unit_ID'=>$Attr14_U));
				$insertAttr14=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '14', 'Attrb_Value_ID' =>$attr14ID));
				}
			}
			if(!empty($Attr15))
			{
				$chkAttr15=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr15)->pluck('Attrb_Value_ID');
		
				if(!$chkAttr15->isEmpty())		
				{
				$insertAttr15=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '15', 'Attrb_Value_ID' =>$chkAttr15[0]));
				
				}
				
			
				else
				{
				$attr15ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '15' , 'Attrb_Value' => $Attr15,'Attrb_Unit_ID'=>$Attr15_U));
				$insertAttr15=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '15', 'Attrb_Value_ID' =>$attr15ID));
				}
			}
			if(!empty($Attr16))
			{
				$chkAttr16=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr16)->pluck('Attrb_Value_ID');
		
				if(!$chkAttr16->isEmpty())		
				{
				$insertAttr16=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '16', 'Attrb_Value_ID' =>$chkAttr16[0]));
				
				}
				
			
				else
				{
				$attr16ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '16' , 'Attrb_Value' => $Attr16,'Attrb_Unit_ID'=>$Attr16_U));
				$insertAttr16=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '16', 'Attrb_Value_ID' =>$attr16ID));
				}
			}
		}
					}
				}
					}
					else
					{
						$attrb_size=$Attr11.$Attr11_U."(b)"."x".$Attr12.$Attr12_U."(h)"." ".$Attr13.$Attr13_U."(t)";
						$productName=$value['itemname']." ".$Attr1." ".$Attr2." ".$Attr5." ".$attrb_size." ".$Attr7." ".$Attr8." ".$value -> brand;
						$prod_exists=\DB::table('products')->where('Prod_Name', $productName)->get();
						$procCount=count($prod_exists);
						if($procCount==0)
						{
						
					$arr = array( 
							"ItemName" => $value->itemname,
							"Prod_Name" => $productName,
	"Group_ID" => $grpID[0],
	"Brand_ID" => $BrandID[0],
	"UnitOfMeasure" => $Attr8
									
															 );
					
								
								if (!empty($arr)) 
					{
									$newProd = \DB::table('products')->insertGetID($arr);
						if(!empty($newProd))
						{
						
		
						if(!empty($Attr1))
						{
						$chkAttr1=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr1)->pluck('Attrb_Value_ID');
				
		
						if(!$chkAttr1->isEmpty())		
							{
		
								$insertAttr1=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '1', 'Attrb_Value_ID' =>$chkAttr1[0]));
					
						
							}
				
			
							else
							{
								$attr1ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '1' , 'Attrb_Value' => $Attr1));
								$insertAttr1=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '1', 'Attrb_Value_ID' =>$attr1ID));
					
							}
				
						}
		
		
		
					if(!empty($Attr2))
					{
						$chkAttr2=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr2)->pluck('Attrb_Value_ID');
		
						if(!$chkAttr2->isEmpty())		
							{
							$insertAttr2=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '2', 'Attrb_Value_ID' =>$chkAttr2[0]));
				
							}
				
			
						else
							{
							$attr2ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '2' , 'Attrb_Value' => $Attr2));
							$insertAttr2=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '2', 'Attrb_Value_ID' =>$attr2ID));
							}
					}
			
					if(!empty($Attr3))
					{
						$chkAttr3=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr3)->pluck('Attrb_Value_ID');
		
						if(!$chkAttr3->isEmpty())		
							{
						$insertAttr3=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '3', 'Attrb_Value_ID' =>$chkAttr3[0]));
				
							}
				
			
							else
							{
							$attr3ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '3' , 'Attrb_Value' => $Attr3));
							$insertAttr3=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '3', 'Attrb_Value_ID' =>$attr3ID));
							}
					}
					 if(!empty($Attr4))
					{
						$chkAttr4=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr4)->pluck('Attrb_Value_ID');
		
						if(!$chkAttr4->isEmpty())		
						{
							$insertAttr4=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '4', 'Attrb_Value_ID' =>$chkAttr4[0]));
				
						}
				
			
					else
					{
						$attr4ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '4' , 'Attrb_Value' => $Attr4));
						$insertAttr4=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '4', 'Attrb_Value_ID' =>$attr4ID));
					}
			}
			 if(!empty($Attr5))
			{
				$chkAttr5=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr5)->pluck('Attrb_Value_ID');
		
				if(!$chkAttr5->isEmpty())		
				{
				$insertAttr5=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '5', 'Attrb_Value_ID' =>$chkAttr5[0]));
				
				}
				
			
				else
				{
				$attr5ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '5' , 'Attrb_Value' => $Attr5));
				$insertAttr5=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '5', 'Attrb_Value_ID' =>$attr5ID));
				}
			}
			 if(!empty($Attr6))
			{
				$chkAttr6=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr6)->pluck('Attrb_Value_ID');
		
				if(!$chkAttr6->isEmpty())		
				{
				$insertAttr6=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '6', 'Attrb_Value_ID' =>$chkAttr6[0]));
				
				}
				
			
				else
				{
				$attr6ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '6' , 'Attrb_Value' => $Attr6));
				$insertAttr6=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '6', 'Attrb_Value_ID' =>$attr6ID));
				}
			}
			 if(!empty($Attr7))
			{
				$chkAttr7=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr7)->pluck('Attrb_Value_ID');
		
				if(!$chkAttr7->isEmpty())		
				{
				$insertAttr7=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '7', 'Attrb_Value_ID' =>$chkAttr7[0]));
				
				}
				
			
				else
				{
				$attr7ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '7' , 'Attrb_Value' => $Attr7));
				$insertAttr7=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '7', 'Attrb_Value_ID' =>$attr7ID));
				}
			}
			if(!empty($Attr8))
			{
				$chkAttr8=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr8)->pluck('Attrb_Value_ID');
		
				if(!$chkAttr8->isEmpty())		
				{
				$insertAttr8=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '8', 'Attrb_Value_ID' =>$chkAttr8[0]));
				
				}
				
			
				else
				{
				$attr8ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '8' , 'Attrb_Value' => $Attr8));
				$insertAttr8=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '8', 'Attrb_Value_ID' =>$attr8ID));
				}
			}
			if(!empty($Attr9))
			{
				$chkAttr9=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr9)->pluck('Attrb_Value_ID');
		
				if(!$chkAttr9->isEmpty())		
				{
				$insertAttr9=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '9', 'Attrb_Value_ID' =>$chkAttr9[0]));
				
				}
				
			
				else
				{
				$attr9ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '9' , 'Attrb_Value' => $Attr9));
				$insertAttr9=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '9', 'Attrb_Value_ID' =>$attr9ID));
				}
			}
			if(!empty($Attr10))
			{
				$chkAttr10=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr10)->pluck('Attrb_Value_ID');
		
				if(!$chkAttr10->isEmpty())		
				{
				$insertAttr10=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '10', 'Attrb_Value_ID' =>$chkAttr10[0]));
				
				}
				
			
				else
				{
				$attr10ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '10' , 'Attrb_Value' => $Attr10,'Attrb_Unit_ID'=>$Attr10_U));
				$insertAttr10=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '10', 'Attrb_Value_ID' =>$attr10ID));
				}
			}
			if(!empty($Attr11))
			{
				$chkAttr11=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr11)->pluck('Attrb_Value_ID');
		
				if(!$chkAttr11->isEmpty())		
				{
				$insertAttr11=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '11', 'Attrb_Value_ID' =>$chkAttr11[0]));
				
				}
				
			
				else
				{
				$attr11ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '11' , 'Attrb_Value' => $Attr11,'Attrb_Unit_ID'=>$Attr11_U));
				$insertAttr11=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '11', 'Attrb_Value_ID' =>$attr11ID));
				}
			}
			if(!empty($Attr12))
			{
				$chkAttr12=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr12)->pluck('Attrb_Value_ID');
		
				if(!$chkAttr12->isEmpty())		
				{
				$insertAttr12=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '12', 'Attrb_Value_ID' =>$chkAttr12[0]));
				
				}
				
			
				else
				{
				$attr12ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '12' , 'Attrb_Value' => $Attr12,'Attrb_Unit_ID'=>$Attr12_U));
				$insertAttr12=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '12', 'Attrb_Value_ID' =>$attr12ID));
				}
			}
			if(!empty($Attr13))
			{
				$chkAttr13=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr13)->pluck('Attrb_Value_ID');
		
				if(!$chkAttr13->isEmpty())		
				{
				$insertAttr13=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '13', 'Attrb_Value_ID' =>$chkAttr13[0]));
				
				}
				
			
				else
				{
				$attr13ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '13' , 'Attrb_Value' => $Attr13,'Attrb_Unit_ID'=>$Attr13_U));
				$insertAttr13=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '13', 'Attrb_Value_ID' =>$attr13ID));
				}
			}
			if(!empty($Attr14))
			{
				$chkAttr14=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr14)->pluck('Attrb_Value_ID');
		
				if(!$chkAttr14->isEmpty())		
				{
				$insertAttr14=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '14', 'Attrb_Value_ID' =>$chkAttr14[0]));
				
				}
				
			
				else
				{
				$attr14ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '14' , 'Attrb_Value' => $Attr14,'Attrb_Unit_ID'=>$Attr14_U));
				$insertAttr14=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '14', 'Attrb_Value_ID' =>$attr14ID));
				}
			}
			if(!empty($Attr15))
			{
				$chkAttr15=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr15)->pluck('Attrb_Value_ID');
		
				if(!$chkAttr15->isEmpty())		
				{
				$insertAttr15=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '15', 'Attrb_Value_ID' =>$chkAttr15[0]));
				
				}
				
			
				else
				{
				$attr15ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '15' , 'Attrb_Value' => $Attr15,'Attrb_Unit_ID'=>$Attr15_U));
				$insertAttr15=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '15', 'Attrb_Value_ID' =>$attr15ID));
				}
			}
			if(!empty($Attr16))
			{
				$chkAttr16=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr16)->pluck('Attrb_Value_ID');
		
				if(!$chkAttr16->isEmpty())		
				{
				$insertAttr16=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '16', 'Attrb_Value_ID' =>$chkAttr16[0]));
				
				}
				
			
				else
				{
				$attr16ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '16' , 'Attrb_Value' => $Attr16,'Attrb_Unit_ID'=>$Attr16_U));
				$insertAttr16=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '16', 'Attrb_Value_ID' =>$attr16ID));
				}
			}
		}
					}
				}
					}
					
				}
				else if($grpID[0]==20)
				{
					$attrb_size=$Attr16.$Attr16_U;
				$productName=$value['itemname']." ".$Attr3." ".$Attr5." ".$attrb_size." ".$Attr8." ".$value -> brand;
				$prod_exists=\DB::table('products')->where('Prod_Name', $productName)->get();
				$procCount=count($prod_exists);
				if($procCount==0)
				{
				
				$arr = array( "ItemName" => $value->itemname,
				"Prod_Name" => $productName,
"Group_ID" => $grpID[0],
"Brand_ID" => $BrandID[0],
"UnitOfMeasure" => $Attr8
                           );
			
            
            if (!empty($arr)) 
			{
              $newProd = \DB::table('products')->insertGetID($arr);
			  if(!empty($newProd))
			  {
			  

			  if(!empty($Attr1))
				{
				$chkAttr1=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr1)->pluck('Attrb_Value_ID');
		

				if(!$chkAttr1->isEmpty())		
					{

						$insertAttr1=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '1', 'Attrb_Value_ID' =>$chkAttr1[0]));
			
				
					}
		
	
					else
					{
						$attr1ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '1' , 'Attrb_Value' => $Attr1));
						$insertAttr1=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '1', 'Attrb_Value_ID' =>$attr1ID));
			
					}
		
				}



			if(!empty($Attr2))
			{
				$chkAttr2=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr2)->pluck('Attrb_Value_ID');

				if(!$chkAttr2->isEmpty())		
					{
					$insertAttr2=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '2', 'Attrb_Value_ID' =>$chkAttr2[0]));
		
					}
		
	
				else
					{
					$attr2ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '2' , 'Attrb_Value' => $Attr2));
					$insertAttr2=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '2', 'Attrb_Value_ID' =>$attr2ID));
					}
			}
	
			if(!empty($Attr3))
			{
				$chkAttr3=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr3)->pluck('Attrb_Value_ID');

				if(!$chkAttr3->isEmpty())		
					{
				$insertAttr3=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '3', 'Attrb_Value_ID' =>$chkAttr3[0]));
		
					}
		
	
					else
					{
					$attr3ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '3' , 'Attrb_Value' => $Attr3));
					$insertAttr3=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '3', 'Attrb_Value_ID' =>$attr3ID));
					}
			}
	 		if(!empty($Attr4))
			{
				$chkAttr4=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr4)->pluck('Attrb_Value_ID');

				if(!$chkAttr4->isEmpty())		
				{
					$insertAttr4=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '4', 'Attrb_Value_ID' =>$chkAttr4[0]));
		
				}
		
	
			else
			{
				$attr4ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '4' , 'Attrb_Value' => $Attr4));
				$insertAttr4=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '4', 'Attrb_Value_ID' =>$attr4ID));
			}
	}
	 if(!empty($Attr5))
	{
		$chkAttr5=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr5)->pluck('Attrb_Value_ID');

		if(!$chkAttr5->isEmpty())		
		{
		$insertAttr5=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '5', 'Attrb_Value_ID' =>$chkAttr5[0]));
		
		}
		
	
		else
		{
		$attr5ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '5' , 'Attrb_Value' => $Attr5));
		$insertAttr5=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '5', 'Attrb_Value_ID' =>$attr5ID));
		}
	}
	 if(!empty($Attr6))
	{
		$chkAttr6=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr6)->pluck('Attrb_Value_ID');

		if(!$chkAttr6->isEmpty())		
		{
		$insertAttr6=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '6', 'Attrb_Value_ID' =>$chkAttr6[0]));
		
		}
		
	
		else
		{
		$attr6ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '6' , 'Attrb_Value' => $Attr6));
		$insertAttr6=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '6', 'Attrb_Value_ID' =>$attr6ID));
		}
	}
	 if(!empty($Attr7))
	{
		$chkAttr7=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr7)->pluck('Attrb_Value_ID');

		if(!$chkAttr7->isEmpty())		
		{
		$insertAttr7=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '7', 'Attrb_Value_ID' =>$chkAttr7[0]));
		
		}
		
	
		else
		{
		$attr7ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '7' , 'Attrb_Value' => $Attr7));
		$insertAttr7=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '7', 'Attrb_Value_ID' =>$attr7ID));
		}
	}
	if(!empty($Attr8))
	{
		$chkAttr8=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr8)->pluck('Attrb_Value_ID');

		if(!$chkAttr8->isEmpty())		
		{
		$insertAttr8=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '8', 'Attrb_Value_ID' =>$chkAttr8[0]));
		
		}
		
	
		else
		{
		$attr8ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '8' , 'Attrb_Value' => $Attr8));
		$insertAttr8=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '8', 'Attrb_Value_ID' =>$attr8ID));
		}
	}
	if(!empty($Attr9))
	{
		$chkAttr9=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr9)->pluck('Attrb_Value_ID');

		if(!$chkAttr9->isEmpty())		
		{
		$insertAttr9=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '9', 'Attrb_Value_ID' =>$chkAttr9[0]));
		
		}
		
	
		else
		{
		$attr9ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '9' , 'Attrb_Value' => $Attr9));
		$insertAttr9=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '9', 'Attrb_Value_ID' =>$attr9ID));
		}
	}
	if(!empty($Attr10))
	{
		$chkAttr10=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr10)->pluck('Attrb_Value_ID');

		if(!$chkAttr10->isEmpty())		
		{
		$insertAttr10=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '10', 'Attrb_Value_ID' =>$chkAttr10[0]));
		
		}
		
	
		else
		{
		$attr10ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '10' , 'Attrb_Value' => $Attr10,'Attrb_Unit_ID'=>$Attr10_U));
		$insertAttr10=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '10', 'Attrb_Value_ID' =>$attr10ID));
		}
	}
	if(!empty($Attr11))
	{
		$chkAttr11=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr11)->pluck('Attrb_Value_ID');

		if(!$chkAttr11->isEmpty())		
		{
		$insertAttr11=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '11', 'Attrb_Value_ID' =>$chkAttr11[0]));
		
		}
		
	
		else
		{
		$attr11ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '11' , 'Attrb_Value' => $Attr11,'Attrb_Unit_ID'=>$Attr11_U));
		$insertAttr11=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '11', 'Attrb_Value_ID' =>$attr11ID));
		}
	}
	if(!empty($Attr12))
	{
		$chkAttr12=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr12)->pluck('Attrb_Value_ID');

		if(!$chkAttr12->isEmpty())		
		{
		$insertAttr12=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '12', 'Attrb_Value_ID' =>$chkAttr12[0]));
		
		}
		
	
		else
		{
		$attr12ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '12' , 'Attrb_Value' => $Attr12,'Attrb_Unit_ID'=>$Attr12_U));
		$insertAttr12=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '12', 'Attrb_Value_ID' =>$attr12ID));
		}
	}
	if(!empty($Attr13))
	{
		$chkAttr13=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr13)->pluck('Attrb_Value_ID');

		if(!$chkAttr13->isEmpty())		
		{
		$insertAttr13=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '13', 'Attrb_Value_ID' =>$chkAttr13[0]));
		
		}
		
	
		else
		{
		$attr13ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '13' , 'Attrb_Value' => $Attr13,'Attrb_Unit_ID'=>$Attr13_U));
		$insertAttr13=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '13', 'Attrb_Value_ID' =>$attr13ID));
		}
	}
	if(!empty($Attr14))
	{
		$chkAttr14=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr14)->pluck('Attrb_Value_ID');

		if(!$chkAttr14->isEmpty())		
		{
		$insertAttr14=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '14', 'Attrb_Value_ID' =>$chkAttr14[0]));
		
		}
		
	
		else
		{
		$attr14ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '14' , 'Attrb_Value' => $Attr14,'Attrb_Unit_ID'=>$Attr14_U));
		$insertAttr14=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '14', 'Attrb_Value_ID' =>$attr14ID));
		}
	}
	if(!empty($Attr15))
	{
		$chkAttr15=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr15)->pluck('Attrb_Value_ID');

		if(!$chkAttr15->isEmpty())		
		{
		$insertAttr15=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '15', 'Attrb_Value_ID' =>$chkAttr15[0]));
		
		}
		
	
		else
		{
		$attr15ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '15' , 'Attrb_Value' => $Attr15,'Attrb_Unit_ID'=>$Attr15_U));
		$insertAttr15=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '15', 'Attrb_Value_ID' =>$attr15ID));
		}
	}
	if(!empty($Attr16))
	{
		$chkAttr16=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr16)->pluck('Attrb_Value_ID');

		if(!$chkAttr16->isEmpty())		
		{
		$insertAttr16=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '16', 'Attrb_Value_ID' =>$chkAttr16[0]));
		
		}
		
	
		else
		{
		$attr16ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '16' , 'Attrb_Value' => $Attr16,'Attrb_Unit_ID'=>$Attr16_U));
		$insertAttr16=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '16', 'Attrb_Value_ID' =>$attr16ID));
		}
	}
}
			}
		}
				}
				
		
										

		}
		$resp=array("Success"=>true);
		return $resp;
	}
}
	}

	
		
	   

		
	
	
	public function SerSeg(Request $r)
	{
		$seg=Request::json()->all();
		$newSeg=\DB::table('serv_segments')->insert(array('SerSeg_Name' => $seg['segment']));
		$resp=array($newSeg);
		return $resp;
	}
	public function getSerSegments()
	{
		$serSegments=\DB::table('serv_segments')->select('SerSeg_ID','SerSeg_Name') -> get();
		$serResp=array($serSegments);
		return $serResp;
		
	}
	public function addNewSerCat(Request $r)
	{
		$ser=Request::json()->all();
		$newSer=\DB::table('services')->insert(array('Service_Name' => $ser['cat'], 'Segment_ID' => $ser['seg']));
		$resp=array($newSer);
		return $resp;
	}
	public function addNewProduct(Request $r)
	{
		/*$details=Request::json()->all();
		$descr=$details['descr'];
		$cat=\DB::table('prod_categories')->where('Category_ID', $details['cat'])->pluck('Category_Name');
	//$name=array_shift($cat);
//$name=(string)$cat;
		if(!empty($details['subCat']))
		{
			$subcat=\DB::table('prod_sub_category')->where('SubCat_ID', $details['subCat'])->pluck('SubCat_Name');
			//$subname=serialize($subcat);
			//$subCatName=$subcat['SubCat_Name'];
		}
		$productName=$cat[0]." ".$subcat[0]." ".$descr;
		
		$words = explode(" ", ucwords($productName));
$acronym = "";

foreach ($words as $w) {
  $acronym .= $w[0];
}
preg_match('/[^ ]*$/', $descr, $results);
$last_word = $results[0];
$code= 'P'.$acronym.$last_word;

$newProduct=\DB::table('products')->insertGetID(array('Prod_Code' => $code,
 'Prod_Name'=> $productName, 
'Group_ID' => $details['group'],
'Seg_ID' => $details['seg'],
'Brand_ID' => $details['brand'],
'Model' => $details['cat'],
'Item' => $details['subCat'],
'Descr' => $details['descr']
));
if(!empty($newProduct))
{
	$attrb=\DB::table('prod_attribute_value') ->insert(array(
	array('Prod_ID' =>$newProduct,'Attrb_ID' => $details['spec1'],'Attrb_Value' => $details['value1']),
	array('Prod_ID' =>$newProduct,'Attrb_ID' => $details['spec2'],'Attrb_Value' => $details['value2']),
	array('Prod_ID' =>$newProduct,'Attrb_ID' => $details['spec3'],'Attrb_Value' => $details['value3']),
	array('Prod_ID' =>$newProduct,'Attrb_ID' => $details['spec4'],'Attrb_Value' => $details['value4']),
	array('Prod_ID' =>$newProduct,'Attrb_ID' => $details['spec5'],'Attrb_Value' => $details['value5']))
	);
	$resp=array('Product Inserted', $newProduct);
		return $resp;
	
	
}*/

//$prodName=$details['itemName']." ".$details['Attr1']." ".$details['Attr2']." "/*.$details['Attr3']." ".$details['Attr4']." ".$details['Attr5']." ".$details['Attr6']." ".$details['Attr7']." ".$details['Attr8']." "*/.$details['Attr9'];

/*$itemName=$r->input('itemName');
$Attr1=$r->input('Attr1');
$Attr2=$r->input('Attr2');
$Attr3=$r->input('Attr3');
$Attr4=$r->input('Attr4');
$Attr5=$r->input('Attr5');
$Attr6=$r->input('Attr6');
$Attr7=$r->input('Attr7');
$Attr8=$r->input('Attr8');
$Attr9=$r->input('Attr9');
$seg=$r->input('seg');
$group=$r->input('group');
$brand=$r->input('brand');*/
$details=Request::json()->all();
$itemName=$details['itemName'];
$Attr1=$details['Attr1'];
$Attr2=$details['Attr2'];
$Attr3=$details['Attr3'];
$Attr4=$details['Attr4'];
$Attr5=$details['Attr5'];
$Attr6=$details['Attr6'];
$Attr7=$details['Attr7'];
$Attr8=$details['Attr8'];
$Attr9=$details['Attr9'];
$seg=$details['seg'];
$group=$details['group'];
$brand=$details['brand'];
$prodName=$itemName." ".$Attr1." ".$Attr2." ".$Attr3." ".$Attr4." ".$Attr5." ".$Attr7." ".$Attr8;

$newProd=\DB::table('products')
->insertGetID(array('ItemName' => $itemName, 'Prod_Name'=>$prodName, 'Group_ID'=>$group, 'UnitofMeasure' =>$Attr9));


if(!empty($newProd))
{
	if(!empty($Attr1))
{
		$chkAttr1=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr1)->pluck('Attrb_Value_ID');
		

		if(!$chkAttr1->isEmpty())		
		{

		$insertAttr1=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '1', 'Attrb_Value_ID' =>$chkAttr1[0]));
			
				
		}
		
	
		else
		{
		$attr1ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '1' , 'Attrb_Value' => $Attr1));
		$insertAttr1=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '1', 'Attrb_Value_ID' =>$attr1ID));
			
		}
		
	}



	if(!empty($Attr2))
	{
		$chkAttr2=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr2)->pluck('Attrb_Value_ID');

		if(!$chkAttr2->isEmpty())		
		{
		$insertAttr2=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '2', 'Attrb_Value_ID' =>$chkAttr2[0]));
		
		}
		
	
		else
		{
		$attr2ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '2' , 'Attrb_Value' => $Attr2));
		$insertAttr2=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '2', 'Attrb_Value_ID' =>$attr2ID));
		}
	}
	
	if(!empty($Attr3))
	{
		$chkAttr3=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr3)->pluck('Attrb_Value_ID');

		if(!$chkAttr3->isEmpty())		
		{
		$insertAttr3=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '3', 'Attrb_Value_ID' =>$chkAttr3[0]));
		
		}
		
	
		else
		{
		$attr3ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '3' , 'Attrb_Value' => $Attr3));
		$insertAttr3=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '3', 'Attrb_Value_ID' =>$attr3ID));
		}
	}
	 if(!empty($Attr4))
	{
		$chkAttr4=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr4)->pluck('Attrb_Value_ID');

		if(!$chkAttr4->isEmpty())		
		{
		$insertAttr4=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '4', 'Attrb_Value_ID' =>$chkAttr4[0]));
		
		}
		
	
		else
		{
		$attr4ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '4' , 'Attrb_Value' => $Attr4));
		$insertAttr4=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '4', 'Attrb_Value_ID' =>$attr4ID));
		}
	}
	 if(!empty($Attr5))
	{
		$chkAttr5=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr5)->pluck('Attrb_Value_ID');

		if(!$chkAttr5->isEmpty())		
		{
		$insertAttr5=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '5', 'Attrb_Value_ID' =>$chkAttr5[0]));
		
		}
		
	
		else
		{
		$attr5ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '5' , 'Attrb_Value' => $Attr5));
		$insertAttr5=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '5', 'Attrb_Value_ID' =>$attr5ID));
		}
	}
	 if(!empty($Attr6))
	{
		$chkAttr6=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr6)->pluck('Attrb_Value_ID');

		if(!$chkAttr6->isEmpty())		
		{
		$insertAttr6=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '6', 'Attrb_Value_ID' =>$chkAttr6[0]));
		
		}
		
	
		else
		{
		$attr6ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '6' , 'Attrb_Value' => $Attr6));
		$insertAttr6=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '6', 'Attrb_Value_ID' =>$attr6ID));
		}
	}
	 if(!empty($Attr7))
	{
		$chkAttr7=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr7)->pluck('Attrb_Value_ID');

		if(!$chkAttr7->isEmpty())		
		{
		$insertAttr7=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '7', 'Attrb_Value_ID' =>$chkAttr7[0]));
		
		}
		
	
		else
		{
		$attr7ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '7' , 'Attrb_Value' => $Attr7));
		$insertAttr7=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '7', 'Attrb_Value_ID' =>$attr7ID));
		}
	}
	if(!empty($Attr8))
	{
		$chkAttr8=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr8)->pluck('Attrb_Value_ID');

		if(!$chkAttr8->isEmpty())		
		{
		$insertAttr8=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '8', 'Attrb_Value_ID' =>$chkAttr8[0]));
		
		}
		
	
		else
		{
		$attr8ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '8' , 'Attrb_Value' => $Attr8));
		$insertAttr8=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '8', 'Attrb_Value_ID' =>$attr8ID));
		}
	}
	if(!empty($Attr9))
	{
		$chkAttr9=\DB::table('attrb_value_rel')->where('Attrb_Value', $Attr9)->pluck('Attrb_Value_ID');

		if(!$chkAttr9->isEmpty())		
		{
		$insertAttr9=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '9', 'Attrb_Value_ID' =>$chkAttr9[0]));
		
		}
		
	
		else
		{
		$attr9ID=\DB::table('attrb_value_rel')->insertGetID(array('Attrb_ID' => '9' , 'Attrb_Value' => $Attr9));
		$insertAttr9=\DB::table('prod_attribute_value')->insert(array('Prod_ID' => $newProd, 'Attrb_ID' => '9', 'Attrb_Value_ID' =>$attr9ID));
		}
	}
	
}
$resp=array('Product added');
return $resp;
}

public function getServices($id)
{
	//console.log($id);
	$services=\DB::table('services')->where('Segment_ID', $id)->get();
	$resp=array($services);
	return $resp;
}
public function addNewItem(Request $r)
{
	$values=Request::json()->all();
	
	$newSeg=\DB::table('serv_line_items')->insert(array
	('Service_ID' => $values['categories'], 'LineItem_Name'=> $values['itemName'],'LineItem_Desc' => $values['description'],'UnitID'=> $values['unit'],
	'AvgRateL'=> $values['rateL'] ,'AvgRateLM'=> $values['rateLM'] ));
	
	$resp=array("Success" => true, $newSeg);
	return $resp;
}

public function addLineItemsCSV(Request $req)
{
	if ($req->file('fileKey')) 
	{
       $path = $req->file('fileKey')->getRealPath();
        $data = \Excel::load($path)->get();

        if ($data->count()) 
		{
            foreach ($data as $key => $value) 
			{
				
				
				$servID=\DB::table('services')->where('Service_Name', $value->service_id)->pluck('Service_ID');
				$Unit_ID=\DB::table('units')->where('Unit_Name', $value->unit_id)->pluck('Unit_ID');
				$arr = array( 
                          "Service_ID" => $servID[0],
                          "LineItem_Name" => $value->lineitem_name,
						  
						  "LineItem_Desc" => $value->lineitem_desc,
						  "UnitID" => $Unit_ID[0],
						  "AvgRateL" => $value->avgratel,
						  "AvgRateLM" => $value->avgratelm

						  
						   );
						}
						if (!empty($arr)) 
			{
			  $newProd = \DB::table('serv_line_items')->insertGetID($arr);
			  $resp=array("Success"=>true, $value);
			  return $resp;
			}
					}
				}
}

public function addKeyDelCSV(Request $req)
{
	//$values=Request::json()->all();
	if ($req->file('fileKey')) 
	{
       $path = $req->file('fileKey')->getRealPath();
        $data = \Excel::load($path)->get();

        if ($data->count()) 
		{
            foreach ($data as $key => $value) 
			{
				
				
				//$servID=\DB::table('services')->where('Service_Name', $value->service_id)->pluck('Service_ID');
				//$Unit_ID=\DB::table('units')->where('Unit_Name', $value->unit_id)->pluck('Unit_ID');
				$arr = array( 
                          
						  "Key_Name" => $value->key_name,
						  "Service_ID" => $value->service_id
						  
						 						  
						   );
						}
						if (!empty($arr)) 
			{
			  $newProd = \DB::table('key_deliverables')->insert($arr);
			  $resp=array("Success"=>true, $newProd);
			  return $resp;
			}
					}
				}
}
public function getServKeys($value)
	{
		$keys=\DB::table('key_deliverables')->where('Service_ID', $value)->where('customFlag',0)->get();
		$resp=array($keys);
		return $resp;
	}
public function getSerLineItems($id)
{
	$lineItems=\DB::table('serv_line_items')->where('Service_ID', $id)->get();
	$resp=array($lineItems);
	return $resp;
}
public function getTotalWorks()
{
	$works=\DB::table('service_work')->whereNotIn('WorkStatus', array(8,11))->get();
	$total_works=count($works);
	$resp=array($total_works);
	return $resp;
}

public function getAssocTypeList($type)
{
	/*$list=\DB::table('associate_type_rel')
		->join('associate','associate_type_rel.Assoc_ID','=','associate.Assoc_ID')
		->join('contacts', 'contacts.Contact_ID', '=','associate.Contact_ID')
		->join('user_assoc_rel', 'user_assoc_rel.Assoc_ID', '=','associate.Assoc_ID')
		->join('logins','logins.User_ID','=','user_assoc_rel.User_ID')
		->leftjoin('associate_details', 'associate_details.Assoc_ID','=','associate.Assoc_ID')
		->leftjoin('location', 'location.Loc_ID', '=','associate_details.Loc_ID')
		->leftjoin('status','status.Assoc_Status','=','logins.Reg_Status')
	//->select('associate.Assoc_ID', 'contacts.Contact_phone', 'associate.Assoc_FirstName', 'associate.Assoc_MiddleName', 'associate.Assoc_LastName', 'location.Loc_Name', 'associate_details.Grade' )
		->where('associate_type_rel.Type_ID', $type)->get();*/
		$list=\DB::table('logins')
		->join('user_assoc_rel', 'user_assoc_rel.User_ID','=','logins.User_ID')
		
		->join('associate','user_assoc_rel.Assoc_ID','=','associate.Assoc_ID')
		
->join('contacts', 'contacts.Contact_ID', '=','associate.Contact_ID')

->join('status','status.Assoc_Status','=','associate.Assoc_Status')
	//->join('location', 'location.Loc_ID', '=','associate_details.Loc_ID')
	//->leftjoin('associate_details', 'associate_details.Assoc_ID','=','associate.Assoc_ID')
		->where('logins.User_Category',$type)
	
		->get();
	//	$count=count($list);



		
		$resp=array($list);
		return $resp;
}
public function getAssocServiceList($id)
{
$servList=\DB::table('associate_project')
->join('services', 'services.Service_ID','=','associate_project.Service_ID')
->select('associate_project.Service_ID', 'Services.Service_Name')
->where('associate_project.Assoc_ID', $id)->get();
$resp=array($servList);
return $resp;
}

public function getServCustomers($id, $aid)
{
	$custList=\DB::table('associate_project')->join('customer', 'customer.Cust_ID','=','associate_project.Cust_ID')
	->where('Service_ID',$id)
	->where('associate_project.Assoc_ID',$aid)
	->where('associate_project.FeedStatus',0)->get();
	$resp=array($custList);
	return $resp;
}

public function getCustomerDetails($id, $aid)
{
	$projectDetails=\DB::table('associate_project')
	->join('customer', 'customer.Cust_ID','=','associate_project.Cust_ID')
	->where('associate_project.Cust_ID',$id)
	->where('associate_project.Service_ID',$aid)
	->get();
	$resp=array($projectDetails);
	return $resp;
}

public function saveFeedback(Request $r)
{
	$feedback=Request::json()->all();
	$addressID=\DB::table('address')->insertGetID(array('Address_line1'=>$feedback['addr1'], 'Address_line1'=>$feedback['addr2'],
	 'Loc_ID'=>$feedback['location'], 'landmark'=>$feedback['landmark']));
	 if(!empty($addressID))
	 {
		 $custAddress=\DB::table('customer')->where('Cust_ID', $feedback['custName'])->update(array('Address_ID'=>$addressID));
	 }
	$rating=floatval($feedback['Behaviour']+$feedback['Knowledge']+$feedback['Quality']+$feedback['WorkLevel']+$feedback['Time']+$feedback['Payment']+$feedback['Pricing']+$feedback['Service'])/8;
	$params=\DB::table('associate_rating')->insert(array(
				
		'Cust_ID' => $feedback['custName'],
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
	
					foreach ($feedback['servID'] as $serv) 
		{
			$feedStatus=\DB::table('associate_project')
			->where('Cust_ID',$feedback['custName'])
			->where('Service_ID', $serv)
			->update(array('FeedStatus'=>1));
		}
	


		
			
			
					
			
					$feedCount=\DB::table('associate_project')->where('Assoc_ID', $feedback['assocID'])
					->where('FeedStatus',1)->get();
					$count=count($feedCount);
			
			
			$resp=array("Success"=>true, $count);
			return $resp;
}

public function checkFeedCount($id)
{
	$totalFeedCount=\DB::table('associate_project')->where('Assoc_ID',$id)
	->get();
	$countTotal=count($totalFeedCount);
$feedCount=\DB::table('associate_project')->where('Assoc_ID',$id)->where('FeedStatus',1)
->get();

	$countFeed=count($feedCount);
	if($countTotal==$countFeed)
	{
		$success=1;
		$resp=array($success);
	return $resp;
	}
	else
	{
		$success1=0;
		$resp=array($success1);
		return $resp;

	}

}
public function checkQACount($id)
{
	$totalFeedCount=\DB::table('associate_project')->where('Assoc_ID',$id)
	->get();
	$countTotal=count($totalFeedCount);
$feedCount=\DB::table('associate_project')->where('Assoc_ID',$id)->where('QAStatus',1)
->get();

	$countFeed=count($feedCount);
	if($countTotal==$countFeed)
	{
		$success=1;
		$resp=array($success);
	return $resp;
	}
	else
	{
		$success1=0;
		$resp=array($success1);
		return $resp;

	}

}

public function getFeedCustomers($id, $aid)
{
	$custList=\DB::table('associate_project')->join('customer', 'customer.Cust_ID','=','associate_project.Cust_ID')
	->where('Service_ID',$id)
	->where('associate_project.Assoc_ID',$aid)
	->where('associate_project.FeedStatus',1)->get();
	$resp=array($custList);
	return $resp;

}

public function changeVerifyStatus($id)
{
	//$userID=\DB::table('user_assoc_rel')->where('Assoc_ID',$id)->pluck('User_ID');
	//$changeStatus=\DB::table('logins')->where('User_ID',$userID[0])->update(array('Reg_Status'=>3));
	$changeStatus=\DB::table('associate')->where('Assoc_ID', $id)->update(array('Assoc_Status'=>3));
	$success=1;
	$resp=array($success);
	return $resp;
}
public function changeQAStatus($id)
{
	//$userID=\DB::table('user_assoc_rel')->where('Assoc_ID',$id)->pluck('User_ID');
	//$changeStatus=\DB::table('logins')->where('User_ID',$userID[0])->update(array('Reg_Status'=>8));
	$changeStatus=\DB::table('associate')->where('Assoc_ID', $id)->update(array('Assoc_Status'=>8));
	$success=1;
	$resp=array($success);
	return $resp;
}

public function saveQAFeedback(Request $r)
{
	$qaRating = Request::json()->all();
	$rating=floatval(($qaRating['QAP1']+$qaRating['QAP2']+$qaRating['QAP3']+$qaRating['QAP4'])/4);
	$action=$qaRating['Action'];
	//$cmnt=$qaRating['comment1'];
	$userID=\DB::table('user_assoc_rel')->where('Assoc_ID',$qaRating['assocID'])->pluck('User_ID');

	$params=\DB::table('associate_qarating')->insert(array(
				
				'Cust_ID' => $qaRating['custName'],
			'QAParam1' => $qaRating['QAP1'],
			'QAParam2' => $qaRating['QAP2'],
			'QAParam3' => $qaRating['QAP3'],
			'QAParam4' => $qaRating['QAP4'],
			'Rating' => $rating	
			
			));
			if(!empty($params))
			{
				
				$update=\DB::table('associate_project')
				->where('Cust_ID',$qaRating['custName'])
				->where('Assoc_ID',$qaRating['assocID'])
				->where('Service_ID',$qaRating['servName'])
				->update(array('QAStatus'=>'1') );
			
			}
			if(!empty($action))
			{
			if($action=="Verified")
	{
	$status=\DB::table('associate_project')
	->where('Cust_ID',$qaRating['custName'])
				->where('Assoc_ID',$qaRating['assocID'])
				->where('Service_ID',$qaRating['servName'])
	->update(array('Cert_Status' => '8'));

	}
	else if($action=="Rejected")
	{
		$status=\DB::table('associate_project')
		->where('Cust_ID',$qaRating['custName'])
					->where('Assoc_ID',$qaRating['assocID'])
					->where('Service_ID',$qaRating['servName'])
	->update(array('Cert_Status' => '7'));
	
	}
	else if($action=="VerifiedWC")
	{
		
		$status=\DB::table('associate_project')
		->where('Cust_ID',$qaRating['custName'])
					->where('Assoc_ID',$qaRating['assocID'])
					->where('Service_ID',$qaRating['servName'])
	->update(array('Cert_Status' => '6'));
	/*if(!empty($qaRating['comment1']))
{
	$comment=\DB::table('associate_details')
	->where('Assoc_ID',$qaRating['assocID'])
	->update(array('Comment'=> $qaRating['comment1']));
}*/
	}
}

			$response =array('response'=>'Data inserted','success'=>true);
		return $response;
}

public function getAssocCustList($id)
{
	$custList=\DB::table('associate_project')
	->join('customer', 'customer.Cust_ID','=','associate_project.Cust_ID')
	->where('associate_project.Assoc_ID',$id)
	->where('associate_project.FeedStatus',0)->select('associate_project.Cust_ID','customer.Cust_Name')->distinct()->get();
	$resp=array($custList);
	return $resp;
}

public function getAssocCustListQA($id)
{
	$custList=\DB::table('associate_project')
	->join('customer', 'customer.Cust_ID','=','associate_project.Cust_ID')
	->where('associate_project.Assoc_ID',$id)
	->where('associate_project.QAStatus',0)
	->select('associate_project.Cust_ID','customer.Cust_Name')->distinct()->get();
	$resp=array($custList);
	return $resp;
}
public function getServiceList($cid, $aid)
{
	$servList=\DB::table('associate_project')
	->join('services', 'services.Service_ID','=','associate_project.Service_ID')
	->where('associate_project.Assoc_ID',$aid)
	->where('associate_project.Cust_ID',$cid)
//	->where('associate_project.FeedStatus',0)
	->select('associate_project.Service_ID', 'services.Service_Name')
	->get();
	$resp=array($servList);
	return $resp;
}
public function getServiceListQA($cid, $aid)
{
	$servList=\DB::table('associate_project')
	->join('services', 'services.Service_ID','=','associate_project.Service_ID')
	->where('associate_project.Assoc_ID',$aid)
	->where('associate_project.Cust_ID',$cid)
	->where('associate_project.QAStatus',0)
	->select('associate_project.Service_ID', 'services.Service_Name')
	->get();
	$resp=array($servList);
	return $resp;
}

public function getAssocServiceDetails($cid, $aid)
{
	$servList=\DB::table('associate_project')
	->join('customer', 'customer.Cust_ID','=','associate_project.Cust_ID')
//	->join('services', 'services.Service_ID','=','associate_project.Service_ID')
	->where('associate_project.Assoc_ID',$aid)
	->where('associate_project.Cust_ID',$cid)
	->where('associate_project.FeedStatus',0)
	->select('associate_project.Work_Detail', 'associate_project.OrderValue','associate_project.Rate_Unit','customer.Contact_No')
	->distinct()
	->get();
	$resp=array($servList);
	return $resp;
}
public function getAssocServiceDetailsQA($sid, $aid, $cid)
{
	$servList=\DB::table('associate_project')
	->join('customer', 'customer.Cust_ID','=','associate_project.Cust_ID')
	->join('services', 'services.Service_ID','=','associate_project.Service_ID')
	->where('associate_project.Assoc_ID',$aid)
	->where('associate_project.Service_ID',$sid)
	->where('associate_project.Cust_ID',$cid)
	->where('associate_project.QAStatus',0)
	->select('associate_project.Work_Detail', 'associate_project.OrderValue','associate_project.Rate_Unit','customer.Contact_No')
	->distinct()
	->get();
	$resp=array($servList);
	return $resp;
}

public function postQAFiles(Request $r)
{
	
	$value= Request::json()->all();
	$aadhar = Request::file('fileKey1')->getClientOriginalName();
	$agreement = Request::file('fileKey2')->getClientOriginalName();
	$id=Input::get('id');
	$adNo=Input::get('aadarNo');
	$agNo=Input::get('agreeNo');
    
   
	if((Request::file('fileKey1'))&& (Request::file('fileKey2')))
		{
				$file1=Request::file('fileKey1');
				$file1->move('resources/assets/uploads/Aadhar',$aadhar);
				$file2=Request::file('fileKey2');
        $file2->move('resources/assets/uploads/Agreemengt',$agreement);
				$aadharUpload=\DB::table('associate_documents')
				->insert(array('Assoc_ID'=>$id,'AadharFile' =>$aadhar, 'AadharNo'=>$adNo,
			'AgreementFile'=>$agreement, 'AgreeNo'=>$agNo));
			if(!empty($aadharUpload))
			{
				//$userID=\DB::table('user_assoc_rel')->where('Assoc_ID',$id)->pluck('User_ID');
				//$changeStatus=\DB::table('logins')->where('User_ID',$userID[0])->update(array('Reg_Status'=>4));
				$changeStatus=\DB::table('associate')->where('Assoc_ID', $id)->update(array('Assoc_Status'=>4));
			}
		}
	
		$response=array('response'=>'Uploaded','success'=>true, $aadharUpload, $changeStatus);
		return $response;
	}
	public function getUserID($id)
	{
		$userID=\DB::table('user_assoc_rel')->where('Assoc_ID',$id)->pluck('User_ID');
		$resp=array($userID);
		return $resp;
	}

	public function getProfileDetailsAdmin($id)
	{
		//$userID=\DB::table('user_assoc_rel')->where('Assoc_ID',$id)->pluck('User_ID');
		$profileDetails=\DB::table('logins')->join('user_assoc_rel', 'user_assoc_rel.User_ID', '=','logins.User_ID')
        ->join('associate', 'associate.Assoc_ID', '=','user_assoc_rel.Assoc_ID')
        ->join('associate_details', 'associate_details.Assoc_ID', '=','associate.Assoc_ID')
        ->join('address','address.Address_ID','=','associate.Address_ID')
       ->join('contacts','contacts.Contact_ID','=','associate.Contact_ID')
       ->join('location','location.Loc_ID','=','associate_details.Loc_ID')
        ->where('associate.Assoc_ID', $id)
        ->get();
        $resp=array($profileDetails);
        return $resp;
	}

	public function getAssocTypeAdmin($id)
	{
	//	$userID=\DB::table('user_assoc_rel')->where('Assoc_ID',$id)->pluck('User_ID');
		$assocType=\DB::table('logins')
        ->join('user_assoc_rel','user_assoc_rel.User_ID','=','logins.User_ID')
        ->join('associate_details','associate_details.Assoc_ID','=','user_assoc_rel.Assoc_ID')
        ->where('logins.User_ID', $id)->pluck('User_Category');
        $resp=array($assocType);
        return $resp;
	}

	public function setVerified($id)
	{
		/*$status=\DB::table('logins')	
				->where('User_ID',$id)
	->update(array('Reg_Status' => '4'));*/
	$changeStatus=\DB::table('associate')->where('Assoc_ID', $id)->update(array('Assoc_Status'=>4));
	$resp=array($status);
	return $resp;
	}
	public function getArticlesList()
	{
		$articles=\DB::table('associate_articles')
		->join('user_assoc_rel','user_assoc_rel.User_ID','=','associate_articles.User_ID')
		->join('associate', 'associate.Assoc_ID','=','user_assoc_rel.Assoc_ID')
		->where('associate_articles.Approval_Status',0)
		->get();
		$resp=array($articles);
		return $resp;
	}
	public function verifyArticle($id)
	{

	$approve=\DB::table('associate_articles')
	->where('Article_ID', $id)->update(array('Approval_Status'=>1));
	$resp=array($approve);
	return $resp;
	}

	public function rejectArticle(Request $r)

	{
		$values = Request::json()->all();
	$approve=\DB::table('associate_articles')
	->where('Article_ID', $values['article_ID'])->update(array('Approval_Status'=>2, 'Article_Comment'=>$values['comment']));
	$resp=array($approve);
	return $resp;

	}
	public function getAllArticles()
	{
		$articles=\DB::table('associate_articles')
		->join('profile_attachments','profile_attachments.Attach_ID','=','associate_articles.Attach_ID')
		->join('user_assoc_rel', 'user_assoc_rel.User_ID','=','associate_articles.User_ID')
		->join('associate', 'associate.Assoc_ID','=','user_assoc_rel.Assoc_ID')
		//->where('Approval_Status',1)
		->where('associate_articles.DeleteFlag', 0)
		->orderBy('associate_articles.Article_Time', 'desc')
		->get();
		$resp=array($articles);
		return $resp;
	}
		
		public function getArticleByAssoc($id)
		{
			$articles=\DB::table('associate_articles')
		->join('profile_attachments','profile_attachments.Attach_ID','=','associate_articles.Attach_ID')
		->join('user_assoc_rel', 'user_assoc_rel.User_ID','=','associate_articles.User_ID')
		->join('associate', 'associate.Assoc_ID','=','user_assoc_rel.Assoc_ID')
		//->where('Approval_Status',1)
		->where('associate_articles.DeleteFlag', 0)
		->where('associate_articles.User_ID',$id)
		->orderBy('associate_articles.Article_Time', 'desc')
		->get();
		$resp=array($articles);
		return $resp;
		}
		public function addSegment(Request $r)
		{
			$value= Request::json()->all();
			$seg=\DB::table('segment')->insert(array('Segment_Name'=>$value['segName']));
			if($seg)
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
		public function addService(Request $r)
		{
			$value= Request::json()->all();
			$seg=\DB::table('services')->insertGetID(array('Service_Name'=>$value['service']));
			if($seg)
			{
				$serviceRel=\DB::table('service_segment_map')->insert(array('Segment_ID'=>$value['segName'],'Service_ID'=>$seg));
				$resp=array('Success'=>true);
			return $resp;
			}
			
			else
			{
				$resp=array('Success'=>false);
			return $resp;
			}
		}
        
		public function getItemsByServ($id)
		{
			$lineItems=\DB::table('serv_line_items')
			->join('units','units.Unit_ID','=','serv_line_items.UnitID')
			->join('service_servlineitem_rel', 'service_servlineitem_rel.LineItem_ID','=','serv_line_items.LineItem_ID')
			->join('services', 'services.Service_ID', '=','service_servlineitem_rel.Service_ID')
			->join('segment','segment.Segment_ID','=','services.Segment_ID')
			//->where('customFlag', 0)
			->where('service_servlineitem_rel.Service_ID',$id)
			->get();
			$resp=array($lineItems);
			return $resp;
		}
public function addItem(Request $r)
{
	$data= Request::json()->all();
			$itemID=\DB::table('serv_line_items')->insertGetID(array('LineItem_Name'=> $data['itemName'],
			'LineItem_Desc'=> $data['desc'],'UnitID'=> $data['unit']));
			if($itemID)
			{
				$map=\DB::table('service_servlineitem_rel')->insert(array('Service_ID'=>$data['service'], 'LineItem_ID'=>$itemID));
				$resp=array('Success'=>true);
				return $resp;
			}
			
			else
			{
				$resp=array('Success'=>false);
			return $resp;
			}
}
public function getFilteredAssocs(Request $r)
{
	$values= Request::json()->all();
	$newArray=[];
	$filterArray=[];
	$assocs=\DB::table('associate_segment_rate')
	->join('associate','associate.Assoc_ID','=','associate_segment_rate.Assoc_ID')
	->join ('status','associate.Assoc_Status','=','status.Assoc_Status')
		->join ('associate_details', 'associate.Assoc_ID', '=','associate_details.Assoc_ID')
		->leftjoin ('location','associate_details.Loc_ID','=','location.Loc_ID')
		->leftjoin ('contacts','contacts.Contact_ID','=','associate.Contact_ID')
		->leftjoin('address', 'address.Address_ID','=','associate.Address_ID')
		->leftjoin ('services','associate_segment_rate.service_ID','=','services.service_ID')
		->leftjoin('segment','segment.Segment_ID','=','services.Segment_ID')
		
		->select('associate.Assoc_ID','associate.Assoc_code','associate.Assoc_FirstName','associate.Assoc_MiddleName','associate.Assoc_LastName','associate_details.Loc_ID','associate.Assoc_Status','associate.Assoc_Type','location.Loc_Name','status.Status_ColorCode','status.Status_Code','status.Status_Action','associate_details.No_Projects','associate_details.Total_Amount', 'contacts.Contact_phone', 'segment.*','services.*', 'address.*')//'associate_segment_rate.Pattern','associate_segment_rate.StdRateLabour','associate_segment_rate.StdRateMatLabour')
		->orderby('associate.Assoc_ID','DESC')
		//->select('associate.Assoc_code','associate.Assoc_FirstName','associate.Assoc_MiddleName','associate.Assoc_LastName','associate.Loc_ID','associate.Assoc_Status','associate.Assoc_Type','location.Loc_Name')'associate_details.bill_pattern','associate_details.Segment_ID','segment.segment_Name','associate_details.service_ID','services.service_Name','associate_details.stdRate','units.Unit_Code',
		//->where('associate.Assoc_ID','1534')
		->distinct('associate.Assoc_ID')
		->get();
		if($assocs)
		{
			foreach ($assocs as $assoc)
			{
				array_push($newArray, $assoc);
			}
		}
		foreach($newArray as $a)
  {
    if(( $values['assoc'] == null || ($values['assoc']&& $a->Assoc_ID==$values['assoc'])) &&
    ($values['segName'] == null  || ($values['segName'] && $a->Segment_ID==$values['segName'])) &&
    ($values['service'] == null  || ($values['service']&& $a->Service_ID==$values['service'])
    ))
    {
        array_push($filterArray,$a);
       

    }
   
  }

		$resp=array($filterArray);
		return $resp;
}
public function getAssoServices($id)
{
    $segments=\DB::table('associate_segment_rate')
    //->join('ser_assoc_services','ser_assoc_services.SerSev_ID','=','associate_segment_rate.Service_ID')
    
    ->join('services','services.Service_ID','=','associate_segment_rate.Service_ID')
//->join('service_segment_map','service_segment_map.Segment_ID','=','associate_segment_rate.Segment_ID')
	->where('associate_segment_rate.Assoc_ID',$id)
	->where('associate_segment_rate.DeleteFlag',0)
   // ->select('associate_segment_rate.Segment_ID', 'segment.Segment_Name', 'associate_segment_rate.Service_ID','services.Service_Name')
   ->get();
    $resp=array($segments);
    return $resp;
}
public function deleteRate($id)
{
	$delRate=\DB::table('associate_segment_rate')->where('ID',$id)
	->update(array('DeleteFlag'=>1));
	if($delRate)
	{
		$resp=array('Success'=>true);
		return $resp;
	}
	

}
public function addNewService(Request $r)
{
	$now=new DateTime();
	$today=$now->format('Y-m-d');
	$values= Request::json()->all();
	$cat=$values['service'];
	foreach ($cat as $c) {
		//$id=explode('_',str_replace("\"", "", $c));
		//print $id;
		//$segID[$i]=(int)$id[0];
		$chkServExists=\DB::table('associate_segment_rate')->where('Assoc_ID',$values['assoc_ID'])->where('Service_ID', (int)$c)->get();
		$count=count($chkServExists);
		if($count==0)
		{
			//$findSeg=\DB::table('services')->where('Service_ID',(int)$c)->pluck('Segment_ID');
			$seg=\DB::table('associate_segment_rate')->insert(array(
			'Assoc_ID' => $values['assoc_ID'],
			//'Segment_ID' => $findSeg[0],
			'Service_ID' => (int)$c,
		'Certf_Flag'=>2));
			//$i++;
		}
		
		
		
	}
	$updateUser=\DB::table('associate_details')->where('Assoc_ID',$values['assoc_ID'])
		->update (array('Updated_User'=>$values['user_ID'],
		'Updated_Date'=>$now));
	$resp=array('Success'=>true);
	return $resp;
	
}
public function addNewProdSeg(Request $r)
{
	$values= Request::json()->all();
	$newSeg=\DB::table('prod_segment')->insert(array('Seg_Name'=>$values['segName']));
	$resp=array('Success'=>true);
	return $resp;
}
public function getProdGroups()
{
	$groups=\DB::table('prod_groups')->get();
	$resp=array($groups);
	return $resp;
}
public function addProdGroup(Request $r)
{
	$values= Request::json()->all();
	$newSeg=\DB::table('prod_groups')->insert(array('Group_Name'=>$values['service'], 'Seg_ID'=>$values['segName']));
	$resp=array('Success'=>true);
	return $resp;
}

}

	

	
		
	



	
	
	
	
	

