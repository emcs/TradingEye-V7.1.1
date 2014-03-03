<?php
/*
=======================================================================================
Copyright: TradingEye
Version: 7.1.1
=======================================================================================
*/
defined('_TEEXEC') or die;
class c_userDb
{
	#CONSTRUCTOR
	function c_userDb()
	{
		$this->libFunc=new c_libFunctions();
	}

	# INSERT NEW CUSTOMERS
	function m_insertUser()
	{
		$comFunc=new c_commonFunctions();
		$comFunc->obDb=$this->obDb;
		$timestamp=time();
		$this->request['status']=$this->libFunc->ifSet($this->request,"status","");
		$this->request['bill_state_id']=$this->libFunc->ifSet($this->request,"bill_state_id","");
		$this->request['bill_state']=$this->libFunc->ifSet($this->request,"bill_state","");
		$this->request['customertype']=$this->libFunc->ifSet($this->request,"vRetail","");			
		#INSERTING CUSTOMER
		$this->obDb->query="INSERT INTO ".CUSTOMERS."
		(iCustmerid_PK,vFirstName,vLastName,
		 vEmail ,vPassword ,vAddress1,vAddress2,vCity,
		  vState,vStateName,vCountry,vRetail,vZip,vCompany ,vPhone ,
			 vHomePage ,iMailList,fMemberPoints,iStatus,tmSignupDate,iRegistered) 
			values('',
			'".$this->libFunc->m_addToDB($this->request['first_name'])."',
			'".$this->libFunc->m_addToDB($this->request['last_name'])."',
			'".$this->libFunc->m_addToDB($this->request['txtemail'])."',
			PASSWORD('".$this->libFunc->m_addToDB($this->request['password'])."'),
			'".$this->libFunc->m_addToDB($this->request['address1'])."',
			'".$this->libFunc->m_addToDB($this->request['address2'])."',
			'".$this->libFunc->m_addToDB($this->request['city'])."',
			'".$this->libFunc->m_addToDB($this->request['bill_state_id'])."',
			'".$this->libFunc->m_addToDB($this->request['bill_state'])."',
			'".$this->libFunc->m_addToDB($this->request['bill_country_id'])."',
			'".$this->libFunc->m_addToDB($this->request['customertype'])."',
			'".$this->libFunc->m_addToDB($this->request['zip'])."',
			'".$this->libFunc->m_addToDB($this->request['company'])."',
			'".$this->libFunc->m_addToDB($this->request['phone'])."',
			'".$this->libFunc->m_addToDB($this->request['homepage'])."',
			'".$this->request['mail_list']."',	
			'".$this->libFunc->m_addToDB($this->request['member_points'])."',
			'".$this->request['status']."',
			'$timestamp','1')";
			$this->obDb->updateQuery();
			$subObjId=$this->obDb->last_insert_id;
			$comFunc->m_sendDetails($this->request['txtemail']);
			$this->libFunc->m_mosRedirect(SITE_URL."user/adminindex.php?action=user.details&id=$subObjId");	
	}#END INSERT CUSTOMER

	#FUNCTION TO UPDATE CUSTOMER	
	function m_updateUser()
	{
		if(isset($this->request['customertype'])){
			$customertype = "t";
		}else{
			$customertype = "n";
		}
		$this->request['status']=$this->libFunc->ifSet($this->request,"status","");
		$this->request['bill_state_id']=$this->libFunc->ifSet($this->request,"bill_state_id","");
		$this->request['bill_state']=$this->libFunc->ifSet($this->request,"bill_state","");			
		#UPDATE CUSTOMER
		$this->obDb->query="UPDATE ".CUSTOMERS." SET 
		vFirstName='".$this->libFunc->m_addToDB($this->request['first_name'])."',
		vLastName='".$this->libFunc->m_addToDB($this->request['last_name'])."',
		vEmail='".$this->libFunc->m_addToDB($this->request['txtemail'])."',";
		if(!empty($this->request['password']))
		{
			$this->obDb->query=$this->obDb->query."vPassword=PASSWORD('".$this->libFunc->m_addToDB($this->request['password'])."'),";
		}
		$this->obDb->query=$this->obDb->query."vAddress1='".$this->libFunc->m_addToDB($this->request['address1'])."',
		vAddress2='".$this->libFunc->m_addToDB($this->request['address2'])."',
		vCity='".$this->libFunc->m_addToDB($this->request['city'])."',
		vState='".$this->libFunc->m_addToDB($this->request['bill_state_id'])."',
		vStateName='".$this->libFunc->m_addToDB($this->request['bill_state'])."',
		vCountry='".$this->libFunc->m_addToDB($this->request['bill_country_id'])."',
		vRetail='".$this->libFunc->m_addToDB($customertype)."',
		vZip='".$this->libFunc->m_addToDB($this->request['zip'])."',
		vCompany ='".$this->libFunc->m_addToDB($this->request['company'])."',
		vPhone ='".$this->libFunc->m_addToDB($this->request['phone'])."',
		vHomePage ='".$this->libFunc->m_addToDB($this->request['homepage'])."',
		iMailList='".$this->request['mail_list']."',	
		fMemberPoints='".$this->libFunc->m_addToDB($this->request['member_points'])."',
		iStatus ='".$this->request['status']."' WHERE (iCustmerid_PK ='".$this->request['id']."')";
		$this->obDb->updateQuery();
		$this->libFunc->m_mosRedirect(SITE_URL."user/adminindex.php?action=user.details&id=".$this->request['id']);	
	}
	
	#FUNCTION TO CHANGE STATUS
	function m_changeStatus()
	{
		$this->request['todo']=$this->libFunc->ifSet($this->request,"todo","");

		switch($this->request['todo'])
		{
			case "delete":
				$status=$this->m_deleteCustomer();
			break;
			case "disable":
				$status=$this->m_disableCustomer();
			break;
			default:
				$status=$this->m_enableCustomer();
			break;
		}
		$this->libFunc->m_mosRedirect(SITE_URL."user/adminindex.php?action=user.home&todo=".$this->request['todo']."&status=".$status."&".$this->request['extrastring']);	
	}

	#FUNCTION TO DELETE CUSTOMER
	function m_deleteCustomer()
	{
		if(isset($this->request['del']))
		{
			foreach($this->request['del'] as $id)
			{
				$this->obDb->query="DELETE FROM ".CUSTOMERS." WHERE (iCustmerid_PK ='".$id."')";
				$this->obDb->updateQuery();
				$this->obDb->query="DELETE FROM ".WISHLIST." WHERE (iCustomerid_FK ='".$id."')";
				$this->obDb->updateQuery();
				$this->obDb->query="DELETE FROM ".WISHEMAILS." WHERE (iCustomerid_FK ='".$id."')";
				$this->obDb->updateQuery();
			}
			return 1;	
		}
		return 0;
	}

	#FUNCTION TO DISABLE CUSTOMER
	function m_disableCustomer()
	{
		if(isset($this->request['del']))
		{
			foreach($this->request['del'] as $id)
			{
				$this->obDb->query="UPDATE ".CUSTOMERS." SET iStatus='0' WHERE (iCustmerid_PK ='".$id."')";
				$this->obDb->updateQuery();
			}
			return 1;	
		}
		return 0;
	}
	
	#FUNCTION TO ENABLE CUSTOMER
	function m_enableCustomer()
	{
		if(isset($this->request['del']))
		{
			foreach($this->request['del'] as $id)
			{
				$this->obDb->query="UPDATE ".CUSTOMERS." SET iStatus='1' WHERE (iCustmerid_PK ='".$id."')";
				$this->obDb->updateQuery();
			}
			return 1;		
		}
		return 0;
	}

}#CLASS ENDS
?>