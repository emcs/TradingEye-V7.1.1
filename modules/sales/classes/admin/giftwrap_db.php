<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.0.5
=======================================================================================
*/
class c_giftWrapDb
{
	#CONSTRUCTOR
	function c_giftWrapDb()
	{
		$this->libFunc=new c_libFunctions();
	}

	#FUNCTION ADD NEW GIFTWRAP
	function m_insertGiftWrap()
	{
		$timeStamp=time();
		
		#FILE UPLOADING START
		if($this->libFunc->checkFileUpload("image"))
		{
			$fileUpload = new FileUpload();
			$fileUpload->source = $_FILES["image"]["tmp_name"];
			$fileUpload->target = $this->imagePath."giftwrap/".$_FILES["image"]["name"];
			$newName1 = $fileUpload->upload();
			if($newName1 != false)
				$image = $newName1;
		}
		else
		{
			$image ="";
		}	
		if($this->libFunc->checkFileUpload("image_large"))
		{
			$fileUpload = new FileUpload();
			$fileUpload->source = $_FILES["image_large"]["tmp_name"];
			$fileUpload->target = $this->imagePath."giftwrap/".$_FILES["image_large"]["name"];
			$newName2 = $fileUpload->upload();
			if($newName2 != false)
				$image_large = $newName2;
		}
		else
		{
			$image_large = "";
		}	

		$this->obDb->query = "INSERT INTO  ".GIFTWRAPS." SET  
		vTitle		 		='".$this->libFunc->m_addToDB($this->request['title'])."', 
		vDescription 	='".$this->libFunc->m_addToDB($this->request['description'])."', 
		fPrice			 	='".$this->libFunc->checkFloatValue($this->request['price'])."', 
		vImage		 	='".$image."', 
		vImageLarge 	='".$image_large."', 
		iState			='1', 
		tmBuildDate		='".$timeStamp."'" ;
		$this->obDb->updateQuery();
		 $lastid=$this->obDb->last_insert_id;
		$this->obDb->query="select MAX(iSort) AS MaxSort from ".GIFTWRAPS;
		$res = $this->obDb->fetchQuery();
		$sort=$res[0]->MaxSort+1;
		
		$this->obDb->query="UPDATE ".GIFTWRAPS." SET iSort='".$sort."' WHERE  iGiftwrapid_PK= '".$lastid."'";
		$res = $this->obDb->updateQuery();
		$this->libFunc->m_mosRedirect(SITE_URL."sales/adminindex.php?action=promotions.giftwrap.home&msg=1");	
	}
	
	function m_updateGiftWrap()
	{
		$timeStamp=time();

		$this->request['state']=$this->libFunc->ifSet($this->request,"state");
		$this->obDb->query = "UPDATE ".GIFTWRAPS." SET  
		vTitle		 		='".$this->libFunc->m_addToDB($this->request['title'])."', 
		vDescription 	='".$this->libFunc->m_addToDB($this->request['description'])."', 
		fPrice			 	='".$this->libFunc->checkFloatValue($this->request['price'])."', 
		tmEditDate		='".$timeStamp."'
		WHERE iGiftwrapid_PK ='".$this->request['id']."'"; ;

		$this->obDb->updateQuery();
		$this->libFunc->m_mosRedirect(SITE_URL."sales/adminindex.php?action=promotions.giftwrap.dspForm&msg=1&id=".$this->request['id']);	
	}

	# FUNTION TO DELETE GIFTWRAP
	function m_giftWrapDelete()
	{
		if(isset($this->request['id']) && !empty($this->request['id']))
		{			
				$this->obDb->query = "DELETE FROM ".GIFTWRAPS." WHERE  iGiftwrapid_PK  =".$this->request['id'];
				$this->obDb->updateQuery();
		}
		$this->libFunc->m_mosRedirect(SITE_URL."sales/adminindex.php?action=promotions.giftwrap.home&msg=3");	
	}
	
	#FUNCTION WILL UPDATE HOME( STATE FIELD)
	function m_updateHome()
	{
		if(isset($this->request['state']))
		{
			$state=$this->request['state'];
		}
		if(isset($this->request['sort']))
		{
			$sort=$this->request['sort'];
		}
		$this->obDb->query="UPDATE ".GIFTWRAPS." set `iState`='0'";
		$this->obDb->updateQuery();

		if(isset($state))
		{
			foreach($state as $stateid=>$stateValue)
			{
				$this->obDb->query="UPDATE ".GIFTWRAPS." set
				 `iState`='$stateValue' where iGiftwrapid_PK='$stateid'";
				$this->obDb->updateQuery();
			}
		}
		if(isset($sort))
		{
			foreach($sort as $sortid=>$sortValue)
			{
				$this->obDb->query="UPDATE ".GIFTWRAPS." set
				 `iSort`='$sortValue' where iGiftwrapid_PK='$sortid'";
				$this->obDb->updateQuery();
			}
		}
		$this->libFunc->m_mosRedirect(SITE_URL."sales/adminindex.php?action=promotions.giftwrap.home&msg=2");	
	}	#EF

	#FUNCTION TO UPLOAD IMAGE
	function m_uploadImage()
	{
		$fileUpload = new FileUpload();
		$name=$this->request['img'];
		if($this->libFunc->checkImageUpload("image"))
		{
				$fileUpload->source = $_FILES["image"]["tmp_name"];
				$fileUpload->target = $this->imagePath."giftwrap/".$_FILES["image"]["name"];
				$newName1 = $fileUpload->upload();
				$this->obDb->query="UPDATE ".GIFTWRAPS." set
				 $name='$newName1' where iGiftwrapid_PK='".$this->request['id']."'";
				$this->obDb->updateQuery();
		}
		else
		{
			$this->libFunc->m_mosRedirect(SITE_URL."sales/adminindex.php?action=promotions.giftwrap.uploadForm&msg=2&img=".$this->request['img']."&id=".$this->request['id']);
		}
		$this->libFunc->m_mosRedirect(SITE_URL."sales/adminindex.php?action=promotions.giftwrap.uploadForm&msg=1&img=".$this->request['img']."&id=".$this->request['id']);
	}#EF	
}#CLASS ENDS
?>