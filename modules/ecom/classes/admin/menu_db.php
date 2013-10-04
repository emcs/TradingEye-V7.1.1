<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.0.5
=======================================================================================
*/
class c_menuDb
{
	#CONSTRUCTOR
	function c_menuDb()
	{
		$this->libFunc=new c_libFunctions();
	}
	# INSERT MENU HEADER
	function m_insertMenuHeader()
	{
		$timeStamp=time();
		$state=$this->libFunc->ifSet($this->request,"state");
	
		#FILE UPLOADING START
		if($this->libFunc->checkImageUpload("image"))
		{
			$fileUpload = new FileUpload();
			$fileUpload->source = $_FILES["image"]["tmp_name"];
			$fileUpload->target = $this->imagePath."menu/".$_FILES["image"]["name"];
			$newName1 = $fileUpload->upload();
			if($newName1 != false)
				$image = $newName1;
		}
		else
		{
			$image = "";
		}	

		#FILE UPLOADING END
		$this->obDb->query="SELECT max(iSort) as maxsort FROM  ".MENUHEADERS;
		$rsSort=$this->obDb->fetchQuery();
		$rsSort[0]->maxsort++;
		#INSERTING TO DEPARTMENTS
		$this->obDb->query="INSERT INTO ".MENUHEADERS."
		(`iHeaderid_PK`, `vHeader`, `vImage`,`iState`,`iSort`,`tmBuildDate`,tmEditDate, `iAdminUser`) VALUES('',
		'".$this->libFunc->m_addToDB($this->request['header'])."',
		'$image',
		'".$state."',
		'".$rsSort[0]->maxsort."',
		'$timeStamp','$timeStamp',
		'".$_SESSION['uid']."')";
		$this->obDb->execQry($this->obDb->query);
		$subObjId=mysql_insert_id();

		$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_menu.show&msg=1");	
	}


	#FUNCTION TO UPDATE DEPARTMENT
	function m_updateMenuHeader()
	{
		$timestamp=time();		
		$state=$this->libFunc->ifSet($this->request,"state");
		
		#FILE UPLOADING START
		if($this->libFunc->checkImageUpload("image"))
		{
			$fileUpload = new FileUpload();
			$fileUpload->source = $_FILES["image"]["tmp_name"];
			$fileUpload->target = $this->imagePath."menu/".$_FILES["image"]["name"];
			$newName1 = $fileUpload->upload();
			if($newName1 != false)
				$image = $newName1;
		}
		else
		{
			$image = "";
		}	

		#CHECK FOR DEPARTMENT ID URL TEMPER
		 $this->obDb->query = "select iHeaderid_PK from ".MENUHEADERS." where iHeaderid_PK = '".$this->request['headerid']."'";
		$row_code = $this->obDb->fetchQuery();
		if($this->obDb->record_count == 0)
		{
			$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_menu.show&msg=2");	
		}
		else
		{
			$query="UPDATE ".MENUHEADERS." SET `vHeader`='".$this->request['header']."',";

			if(!empty($image))
				$query.="`vImage`='".$image."',";

			$query.="`iState`='".$state."',`tmEditDate`='".$timestamp."',`iAdminUser` ='".$_SESSION['uid']."'
			where iHeaderid_PK=".$this->request['headerid'];
			$this->obDb->query=$query;
			$this->obDb->execQry($this->obDb->query);
		
			$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_menu.show&msg=3");	
		}
	}


	function m_updateHomeMenuHeader()
	{
		$sort=$this->request['sort'];
		if(isset($this->request['state']))
		{
			$state=$this->request['state'];
		}

		$this->obDb->query="UPDATE ".MENUHEADERS." set
			 `iState`='0'";
		$this->obDb->updateQuery();
		foreach($sort as $sortid=>$sortValue)
		{
			$this->obDb->query="UPDATE ".MENUHEADERS." set
			 `iSort`='$sortValue' where iHeaderid_PK ='$sortid'";
			$this->obDb->updateQuery();
		}

		if(isset($state))
		{
			foreach($state as $stateid=>$stateValue)
			{
				$this->obDb->query="UPDATE ".MENUHEADERS." set
				 `iState`='$stateValue' where  iHeaderid_PK ='$stateid'";
				$this->obDb->updateQuery();
			}
		}
		$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_menu.show");	
	}	

	# function to delete an MENU
	function m_deleteMenu()
	{
		if(isset($this->request['headerid']) && !empty($this->request['headerid']))
		{
			
			$this->obDb->query = "SELECT vImage,iSort from ".MENUHEADERS." where iHeaderid_PK =".$this->request['headerid'];
			$rs = $this->obDb->fetchQuery();
			$num_rows = $this->obDb->record_count;	
			if($num_rows==1)
			{
				#DELETING IMAGES 
				if(!empty($rs[0]->vImage) && file_exists($this->imagePath."menu/".$rs[0]->vImage))
					@unlink($this->imagePath."menu/".$rs[0]->vImage);
				
				#DELETING MENUHEADER
				$this->obDb->query = "DELETE FROM ".MENUHEADERS." WHERE iHeaderid_PK =".$this->request['headerid'];
				$this->obDb->updateQuery();
				$this->m_updateSort($rs[0]->iSort);
				$this->obDb->query = "DELETE FROM ".MENUITEMS." WHERE iHeaderid_FK =".$this->request['headerid'];
				$this->obDb->updateQuery();
			}
		}
			
		$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_menu.show");	
	}

	function m_updateSort($delid)
	{
		$this->obDb->query = "UPDATE ".MENUHEADERS." SET iSort=iSort-1 where iSort >'$delid'";
		$this->obDb->updateQuery();
	}

	#INSERT NEW ITEM IN MENU
	function m_insertMenuItem()
	{
		$timeStamp=time();
		if(!isset($this->request['state']))
				{
					$state="";																
				}
			else
				{
					$state=$this->request['state'];															
				}

		
			#FILE UPLOADING START
			if($this->libFunc->checkImageUpload("image"))
			{
				$fileUpload = new FileUpload();
				$fileUpload->source = $_FILES["image"]["tmp_name"];
				$fileUpload->target = $this->imagePath."menu/".$_FILES["image"]["name"];
				$newName1 = $fileUpload->upload();
				if($newName1 != false)
					$image = $newName1;
			}
			else
			{
				$image = "";
			}	

			#FILE UPLOADING END
			$this->obDb->query="SELECT max(iSort) as maxsort FROM  ".MENUITEMS." WHERE iHeaderid_FK=".$this->request['headerid'];
			$rsSort=$this->obDb->fetchQuery();
			$sort=$rsSort[0]->maxsort+1;
			#INSERTING TO DEPARTMENTS
			$this->obDb->query="INSERT INTO ".MENUITEMS."
			(`iMenuItemsId`,`iHeaderid_FK`,`vItemtitle`,`vLink`,`vHrefAttributes`,`iMethod`, `vImage`,`iState`,`iSort`,
				 `tmBuildDate`,tmEditDate, `iAdminUser`) 
				values('','".$this->request['headerid']."',
				'".$this->libFunc->m_addToDB($this->request['item_title'])."',
				'".$this->libFunc->m_addToDB($this->request['link'])."',
				'".$this->libFunc->m_addToDB($this->request['href_attributes'])."',
			 	'".$this->libFunc->m_addToDB($this->request['method'])."',
				'$image',
				'".$state."',
				'".$sort."',
				'$timeStamp',
				'$timeStamp',
				'".$_SESSION['uid']."')";
				$this->obDb->execQry($this->obDb->query);
				$subObjId=mysql_insert_id();
				$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_menu.viewItems&headerid=".$this->request['headerid']);	
	}


	#FUNCTION TO UPDATE DEPARTMENT
	function m_updateMenuItem()
	{
		$timestamp=time();		
		if(!isset($this->request['state']))
		{
			$state="";																
		}
		else
		{
			$state=$this->request['state'];															
		}
		#FILE UPLOADING START
		if($this->libFunc->checkImageUpload("image"))
		{
			$fileUpload = new FileUpload();
			$fileUpload->source = $_FILES["image"]["tmp_name"];
			$fileUpload->target = $this->imagePath."menu/".$_FILES["image"]["name"];
			$newName1 = $fileUpload->upload();
			if($newName1 != false)
				$image = $newName1;
		}
		else
		{
			$image = "";
		}	


		#CHECK FOR DEPARTMENT ID URL TEMPER
		 $this->obDb->query = "SELECT iMenuItemsId from ".MENUITEMS." where iMenuItemsId = '".$this->request['itemid']."'";
		$row_code = $this->obDb->fetchQuery();
		if($this->obDb->record_count == 0)
		{
			$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_menu.show&msg=2");	
		}
		else
		{
			$query="UPDATE ".MENUITEMS." SET 
			`vItemtitle`='".$this->request['item_title']."',
			`vLink`='".$this->request['link']."',
            `iMethod`='".$this->request['method']."',	
			`vHrefAttributes`='".$this->request['href_attributes']."',";

			if(!empty($image))
				$query.="`vImage`='".$image."',";

			$query.="
			`iState`='".$state."',
			`tmEditDate`='".$timestamp."',
			`iAdminUser` ='".$_SESSION['uid']."'
			where iMenuItemsId =".$this->request['itemid'];
			$this->obDb->query=$query;
			$this->obDb->execQry($this->obDb->query);
			$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_menu.viewItems&headerid=".$this->request['headerid']."&msg=3");	
		}
	}


	function m_updateHomeMenuItem()
	{
		$sort=$this->request['sort'];
		if(isset($this->request['state']))
		{
		 $state=$this->request['state'];
		}

		$this->obDb->query="UPDATE ".MENUITEMS." set
			 `iState`='0' WHERE iHeaderid_FK=".$this->request['headerid'];
		$this->obDb->updateQuery();
		foreach($sort as $sortid=>$sortValue)
		{
			$this->obDb->query="UPDATE ".MENUITEMS." set
			 `iSort`='$sortValue' where iMenuItemsId ='$sortid'";
			$this->obDb->updateQuery();
		}

		if(isset($state))
		{
			foreach($state as $stateid=>$stateValue)
			{
				$this->obDb->query="UPDATE ".MENUITEMS." set
				 `iState`='$stateValue' where  iMenuItemsId ='$stateid'";
				$this->obDb->updateQuery();
			}
		}
		$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_menu.viewItems&headerid=".$this->request['headerid']);	
	}	

	


	# function to delete an department,product,article	
	function m_deleteItem()
	{
		if(isset($this->request['itemid']) && !empty($this->request['itemid']))
		{
			
			$this->obDb->query = "select vImage,iSort from ".MENUITEMS." where iMenuItemsId =".$this->request['itemid'];
			$rs = $this->obDb->fetchQuery();
			$num_rows = $this->obDb->record_count;	
			if($num_rows==1)
			{
				#DELETING IMAGES 
				if(!empty($rs[0]->vImage) && file_exists($this->imagePath."menu/".$rs[0]->vImage))
					@unlink($this->imagePath."menu/".$rs[0]->vImage);
				
				#DELETING MENUHEADER
				$this->obDb->query = "DELETE FROM ".MENUITEMS." WHERE iMenuItemsId =".$this->request['itemid'];
				$this->obDb->updateQuery();
				$this->m_itemSort($rs[0]->iSort);
			}
		}			
		$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_menu.viewItems&headerid=".$this->request['headerid']);	
	}

	#FUNCTION TO RESORT MENUITEMS
	function m_itemSort($delid)
	{
		$this->obDb->query = "UPDATE ".MENUITEMS." SET iSort=iSort-1 where iSort >'$delid' AND iHeaderid_FK=".$this->request['headerid'];
		$this->obDb->updateQuery();
	}

	#FUNCTION TO UPLAD IMAGE
	function m_uploadImage()
	{
		$fileUpload = new FileUpload();
		if($this->request['type']=="menu")
		{
			$this->obDb->query = "SELECT vImage from ".MENUITEMS." WHERE iMenuItemsId=".$this->request['id'];
			$rsImage = $this->obDb->fetchQuery();
		}
		else
		{
			$this->obDb->query = "SELECT vImage from ".MENUHEADERS." WHERE iHeaderid_PK=".$this->request['id'];
			$rsImage = $this->obDb->fetchQuery();
		}
						
		if($this->libFunc->checkImageUpload("image"))
		{
			$fileUpload->source = $_FILES["image"]["tmp_name"];
			 $fileUpload->target = $this->imagePath."menu/".$_FILES["image"]["name"];
			$newName1 = $fileUpload->upload();
			if($newName1 != false)
				$image = $newName1;
			
			if(!empty($rsImage[0]->vImage))
				$fileUpload->deleteFile($this->imagePath."menu/".$rsImage[0]->vImage);
				
			$imagename="image";	
		}
		else
		{
			$image = $rsImage[0]->vImage;
		}
		if($this->request['type']=="menu")
		{
			$this->obDb->query="UPDATE ".MENUITEMS." SET `vImage`='$image' ,`tmEditDate`='".time()."', `iAdminUser` ='".$_SESSION['uid']."'  WHERE iMenuItemsId  ='".$this->request['id']."'";	
		}
		else
		{
			 $this->obDb->query="UPDATE ".MENUHEADERS." SET `vImage`='$image',`tmEditDate`='".time()."', `iAdminUser` ='".$_SESSION['uid']."' WHERE iHeaderid_PK ='".$this->request['id']."'";		
		}
		$this->obDb->updateQuery();
		$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_menu.uploadForm&status=1&id=".$this->request['id']."&type=".$this->request['type']);
	}	#EF UPLOAD
}#CLASS ENDS
?>