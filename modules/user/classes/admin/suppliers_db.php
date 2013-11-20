<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.1.0
=======================================================================================
*/
defined('_TEEXEC') or die;
class c_supplierDb
{
	#CONSTRUCTOR
	function c_supplierDb()
	{
		$this->libFunc=new c_libFunctions();	
	}

	# Insert new department
	function m_insertSupplier()
	{
		$timestamp=time();
		$status=$this->libFunc->ifSet($this->request,'status',"");

		if(!isset($this->request['bill_state_id']) || empty($this->request['bill_state_id']))
		{
			$this->request['bill_state_id']="";
		}
		else
		{
			$this->request['bill_state']="";
		}
		if($this->libFunc->checkImageUpload("image"))
		{
			$fileUpload = new FileUpload();
			$fileUpload->source =$_FILES["image"]["tmp_name"];
			$fileUpload->target =$this->imagePath."suppliers/".$_FILES["image"]["name"];
			$newName1 = $fileUpload->upload();
			if($newName1 != false)
				$image = $newName1;
		}
		else
		{
			$image = "";
		}	
		#INSERTING CUSTOMER
		$this->obDb->query="INSERT INTO ".SUPPLIERS."
		(iVendorid_PK,vContact,
		 vEmail ,vAddress1,vAddress2,vCity,
		  vState,vStateName,vCountry,vZip,vCompany ,vPhone ,
			 vWebsite ,vPhone2,vImage,iStatus,tmBuildDate,`iAdminUser`) 
			values('',
			'".$this->libFunc->m_addToDB($this->request['contact'])."',
			'".$this->libFunc->m_addToDB($this->request['email'])."',
			'".$this->libFunc->m_addToDB($this->request['address1'])."',
			'".$this->libFunc->m_addToDB($this->request['address2'])."',
			'".$this->libFunc->m_addToDB($this->request['city'])."',
			'".$this->libFunc->m_addToDB($this->request['bill_state_id'])."',
			'".$this->libFunc->m_addToDB($this->request['bill_state'])."',
			'".$this->libFunc->m_addToDB($this->request['bill_country_id'])."',
			'".$this->libFunc->m_addToDB($this->request['zip'])."',
			'".$this->libFunc->m_addToDB($this->request['company'])."',
			'".$this->libFunc->m_addToDB($this->request['phone'])."',
			'".$this->libFunc->m_addToDB($this->request['website'])."',
			'".$this->libFunc->m_addToDB($this->request['phone1'])."',	
			'".$image."',	
			'".$this->request['status']."',
			'$timestamp', '".$_SESSION['uid']."' )";
			$this->obDb->updateQuery();
			$subObjId=$this->obDb->last_insert_id;
			$this->libFunc->m_mosRedirect(SITE_URL."user/adminindex.php?action=supplier.home");	
				
	}#END INSERT CUSTOMER

	function m_updateSupplier()
	{
		$status=$this->libFunc->ifSet($this->request,'status',"");

		if(!isset($this->request['bill_state_id']) || empty($this->request['bill_state_id']))
		{
			$this->request['bill_state_id']="";
		}
		else
		{
			$this->request['bill_state']="";
		}
		
		#INSERTING CUSTOMER
		$this->obDb->query="UPDATE ".SUPPLIERS." SET 
		vContact='".$this->libFunc->m_addToDB($this->request['contact'])."',
		vEmail='".$this->libFunc->m_addToDB($this->request['email'])."',
		vAddress1='".$this->libFunc->m_addToDB($this->request['address1'])."',
		vAddress2='".$this->libFunc->m_addToDB($this->request['address2'])."',
		vCity='".$this->libFunc->m_addToDB($this->request['city'])."',
		vState='".$this->libFunc->m_addToDB($this->request['bill_state_id'])."',
		vStateName='".$this->libFunc->m_addToDB($this->request['bill_state'])."',
		vCountry='".$this->libFunc->m_addToDB($this->request['bill_country_id'])."',
		vZip='".$this->libFunc->m_addToDB($this->request['zip'])."',
		vCompany ='".$this->libFunc->m_addToDB($this->request['company'])."',
		vPhone ='".$this->libFunc->m_addToDB($this->request['phone'])."',
		vWebsite ='".$this->libFunc->m_addToDB($this->request['website'])."',
		vComments='".$this->libFunc->m_addToDB($this->request['comments'])."',
		vPhone2='".$this->request['phone1']."',	
		iStatus ='".$this->request['status']."',tmEditDate='".time()."', `iAdminUser` ='".$_SESSION['uid']."' WHERE (iVendorid_PK='".$this->request['id']."')";
		$this->obDb->updateQuery();
		$this->libFunc->m_mosRedirect(SITE_URL."user/adminindex.php?action=supplier.home");	
	}

	# FUNCTION TO DELETE SUPPLIER	
	function m_deleteSupplier()
	{
		if(isset($this->request['id']) && !empty($this->request['id']))
		{
			 $this->obDb->query = "DELETE FROM ".SUPPLIERS." where iVendorid_PK =".$this->request['id'];
			$this->obDb->updateQuery();
		}
		$this->libFunc->m_mosRedirect(SITE_URL."user/adminindex.php?action=supplier.home");	
	}

	#FUNCTION TO UPLOAD IMAGE
	function m_uploadImage()
	{
		$fileUpload = new FileUpload();
		$libFunc=new c_libFunctions();
		$this->obDb->query = "select iVendorid_PK,vImage from ".SUPPLIERS." where iVendorid_PK = ".$this->request['id'];
		$row_code = $this->obDb->fetchQuery();
					
		if($this->libFunc->checkImageUpload("image"))
		{

			
			$fileUpload->source = $_FILES["image"]["tmp_name"];
			 $fileUpload->target = $this->imagePath."suppliers/".$_FILES["image"]["name"];
			$newName1 = $fileUpload->upload();
			if($newName1 != false)
				$image = $newName1;
			
			if(!empty($row_code[0]->vImage))
				$fileUpload->deleteFile($this->imagePath."suppliers/".$row_code[0]->vImage);
				
			$imagename="image";	
		}
		else
		{
			$image = $row_code[0]->vImage;
		}

		 $this->obDb->query="UPDATE ".SUPPLIERS." SET `vImage`='$image', `tmEditDate`='".time()."', `iAdminUser` ='".$_SESSION['uid']."' where iVendorid_PK ='".$this->request['id']."'";		
		$this->obDb->updateQuery();
		$this->libFunc->m_mosRedirect(SITE_URL."user/adminindex.php?action=supplier.uploadForm&status=1&id=".$this->request['id']);
	}#EF
}#CLASS ENDS
?>