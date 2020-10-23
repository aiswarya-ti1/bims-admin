<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Input;
//use Illuminate\Http\Request;
use Request;
use File;
use Illuminate\Support\Facades\Crypt;
use Hash;
class matAssocCertController extends Controller
{
	//To display MatAssoc details in MatAssoc dashboard
    public function getMatAssociates()
	{
		$associates=\DB::table('prod_associate')
		->join('address','prod_associate.address_ID','=','address.address_ID')
		->join('location', 'location.Loc_ID','=','prod_associate.Loc_ID')
		->join('contacts', 'prod_associate.Contact_ID','=','contacts.Contact_ID')
		->select('prod_associate.MAssoc_ID','prod_associate.MAssoc_FirstName','prod_associate.MAssoc_MiddleName','prod_associate.MAssoc_LastName','prod_associate.MAssoc_Code','prod_associate.Loc_ID','prod_associate.address_ID','prod_associate.Contact_ID','Location.Loc_Name','address.Address_line1','contacts.Contact_phone')
		->get();
		$getAssoc=array('success'=>true, $associates);
		return $getAssoc;
	}
}
