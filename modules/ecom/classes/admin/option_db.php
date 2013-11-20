<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.1.0
=======================================================================================
*/
defined('_TEEXEC') or die;
class c_optionDb
{
	#CONSTRUCTOR
	function c_optionDb()
	{
		$this->libFunc=new c_libFunctions();
	}

	function m_ajaxgetAtrribute(){
	$string="";
	if (isset($this->request['attributeid']) && !isset($this->request['addnew']))
		{
			
			$this->obDb->query="SELECT A.*, AV.tValues FROM ".ATTRIBUTES." A, ".ATTRIBUTEVALUES." AV WHERE AV.iValueId_PK=".$this->request['attributeid']." AND iAttributesid_FK = iAttributesid_PK";
			$attribute = $this->obDb->fetchQuery();
			$name = explode("¬",$attribute[0]->vFieldname);
			$prefix = explode("¬",$attribute[0]->vPrefix);
			$suffix = explode("¬",$attribute[0]->vSuffix);
			$value = explode("¬",$attribute[0]->tValues);
			for ($i=0;$i<$attribute[0]->iFieldnumber;$i++)
			{	
			$string.="<tr>";
			$string.="<td class=\"first\"><label>".$name[$i]."</label></td>";
			$string.="<td><span>".$prefix[$i]." </label></span>";
			$string.="<input type=\"text\" name=\"attributevalue[]\" class=\"formField\" value =\"".$value[$i]."\"/>";
			$string.="<span> ".$suffix[$i]."</span></td>";
			$string.="</tr>";
			}
		}else{
			$string="";
			$this->obDb->query = "SELECT * FROM ".ATTRIBUTES." WHERE  iAttributesid_PK = ".$this->request['attributeid'];	
			$attribute = $this->obDb->fetchQuery();
			
			$name 	= explode("�",$attribute[0]->vFieldname);
			$prefix = explode("�",$attribute[0]->vPrefix);
			$suffix = explode("�",$attribute[0]->vSuffix);
			for ($i=0;$i<$attribute[0]->iFieldnumber;$i++)
			{	
			$string.="<tr>";
			$string.="<td class=\"first\"><label>".$name[$i]."</label></td>";
			$string.="<td><span>".$prefix[$i]." </label></span>";
			$string.="<input type=\"text\" name=\"attributevalue[]\" class=\"formField\" />";
			$string.="<span> ".$suffix[$i]."</span></td>";
			$string.="</tr>";
			}
		}	
	echo $string."~attribute";
	exit; 	
	}
	
	#INSERT NEW ATTRIBUTES
	function m_insertAttribute()
	{
		if (isset($_SESSION['fieldnumber'])){
		$fieldcount = $_SESSION['fieldnumber'];
		unset ($_SESSION['fieldnumber']);
		}
		$title= $this->request['title'];
		$fieldname="";
		$prefix="";
		$suffix="";
		$value="";
		for ($i=1;$i<$fieldcount+1;$i++)
		{
		$fieldname.= $this->request['fieldname'.$i]."�";	
		$prefix.= $this->request['prefix'.$i]."�";
		$suffix.= $this->request['suffix'.$i]."�";
		}
		$timeStamp=time();
		$this->obDb->query ="INSERT INTO ".ATTRIBUTES;
		$this->obDb->query.=" (`vAttributeTitle`,`iFieldnumber`,`vFieldname`,`vPrefix`,`vSuffix`) ";
		$this->obDb->query.=" VALUES ('".$this->libFunc->m_addToDB($title)."',";
		$this->obDb->query.="'".$this->libFunc->m_addToDB($fieldcount)."',";
		$this->obDb->query.="'".$this->libFunc->m_addToDB($fieldname)."',";
		$this->obDb->query.="'".$this->libFunc->m_addToDB($prefix)."',";
		$this->obDb->query.="'".$this->libFunc->m_addToDB($suffix)."')";
		
		$this->obDb->updateQuery();
	$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_option.dspAttributes");
	}
	
	#EDIT ATTRIBUTES
	function m_editAttribute(){
		
		$this->obDb->query="SELECT iFieldnumber FROM ".ATTRIBUTES." WHERE iAttributesid_PK='".$this->request['attributeid']."'";
		$count= $this->obDb->fetchQuery();
		$name="";
		$prefix="";
		$suffix="";
		$value="";
		for($i=1;$i<$count[0]->iFieldnumber+1;$i++){
			$name.=$this->request['fieldname'.$i]."�";
			$prefix.=$this->request['prefix'.$i]."�";
			$suffix.=$this->request['suffix'.$i]."�";
		}
		$this->obDb->query ="UPDATE ".ATTRIBUTES. " SET
				vAttributeTitle		=	'".$this->request['title']."',
				vFieldname			=	'".$name."',
				vPrefix		=	'".$prefix."',
				vSuffix		=	'".$suffix."'
				WHERE iAttributesid_PK = '".$this->request['attributeid']."'";
			$this->obDb->updateQuery();
	$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_option.dspAddattribute&optionid=".$this->request['attributeid']."&flag=edit");
	
	}
	
	function m_delAttribute(){
		$this->obDb->query="DELETE FROM ".ATTRIBUTES." WHERE iAttributesid_PK='".$this->request['attributeid']."'";
		$this->obDb->updateQuery();
		
		$this->obDb->query="DELETE FROM ".ATTRIBUTEVALUES." WHERE iAttributesid_FK='".$this->request['attributeid']."'";
		$this->obDb->updateQuery();
		
		$this->obDb->query="DELETE FROM ".PRODUCTATTRIBUTES." WHERE iAttributeid_FK='".$this->request['attributeid']."'";
		$this->obDb->updateQuery();
		
		$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_option.dspAttributes");	
		exit;
	}
	
	#INSERT NEW OPTIONS
	function m_insertOption($images="")
	{
		$timeStamp=time();
		
		#INSERTING TO OPTIONS
		$this->obDb->query ="INSERT INTO ".OPTIONS;
		$this->obDb->query.=" (`vName`,`vDescription`,`iState`, `tmBuildDate`, `iAdminUser`) ";
		$this->obDb->query.=" VALUES('".$this->libFunc->m_addToDB($this->request['name1'])."',";
		$this->obDb->query.="'".$this->libFunc->m_addToDB($this->request['description'])."',";
		if(isset($this->request['mandatory']) && !empty($this->request['mandatory'])){
			$this->obDb->query.="'1',";	
		}else{
			$this->obDb->query.="'0',";
		}
		$this->obDb->query.="'$timeStamp','".$_SESSION['uid']."')";
		$this->obDb->updateQuery();

		$optionId=$this->obDb->last_insert_id;
		$sort=1;
		#INSERTING TO OPTIONS VALUES
		if(isset($this->request['option_count']))
		{
			for($i=0;$i<$this->request['option_count'];$i++)
			{
				if(!isset($this->request['use_inventory'][$i]))
				{
					$this->request['use_inventory'][$i]="0";
				}
				if(!isset($this->request['backorder'][$i]))
				{
					$this->request['backorder'][$i]="0";
				}
				$this->obDb->query="INSERT INTO ".OPTIONVALUES." (`iOptionValueid_PK`,`iOptionid_FK`,`vOptSku`,`vItem`,";
				$this->obDb->query.="`fPrice`,`iInventory`,`iUseInventory`,`iBackorder`,`vImage`,`iSort`) ";
				$this->obDb->query.="	VALUES('','$optionId','".$this->libFunc->m_addToDB($this->request['sku'][$i])."',
				'".$this->libFunc->m_addToDB($this->request['item'][$i])."',";
				$this->obDb->query.="'".$this->libFunc->checkFloatValue($this->request['price'][$i])."',";
				$this->obDb->query.="'".$this->libFunc->checkWrongValue($this->request['inventory'][$i])."',";
				$this->obDb->query.="'".$this->libFunc->checkWrongValue($this->request['use_inventory'][$i])."',";
				$this->obDb->query.="'".$this->libFunc->checkWrongValue($this->request['backorder'][$i])."',";
				$this->obDb->query.="'".$images[$i]."','$sort')";

				$this->obDb->updateQuery();
				$sort++;
			}#endfor
		}#endif
		$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_option.home");	
		exit;
	}#end insert function


	#FUNCTION TO UPDATE DEPARTMENT
	function m_updateOption()
	{
		$fileUpload = new FileUpload();
		$timeStamp=time();

		#UPDATING OPTIONS
		$this->obDb->query="UPDATE ".OPTIONS." SET ";
		$this->obDb->query.="`vName`='".$this->libFunc->m_addToDB($this->request['optname'])."',";
		$this->obDb->query.="`vDescription`='".$this->libFunc->m_addToDB($this->request['optdescription'])."',";
		if(isset($this->request['mandatory']) && !empty($this->request['mandatory'])){
			$this->obDb->query.="`iState`='1',";
		}else{
			$this->obDb->query.="`iState`='0',";
		}
		$this->obDb->query.="`tmEditDate`='$timeStamp',`iAdminUser`='".$_SESSION['uid']."'";
		$this->obDb->query.=" WHERE iOptionid_PK='".$this->request['optionid']."'";
		$this->obDb->updateQuery();
		if(isset($this->request['itemid']))
		{
			$cnt=count($this->request['itemid']);
			for($i=0;$i<$cnt;$i++)
			{
				$itemid=$this->request['itemid'][$i];
				if(!isset($this->request['use_inventory'][$itemid]))
				{
					$this->request['use_inventory'][$itemid]="0";
				}
				if(!isset($this->request['backorder'][$itemid]))
				{
					$this->request['backorder'][$itemid]="0";
				}
				if(is_numeric($this->request['price'][$itemid]))
				{
					$tempprice = $this->request['price'][$itemid];
				}
				else
				{
					$tempprice = 0;
				}
				$this->obDb->query ="UPDATE ".OPTIONVALUES." SET ";
				$this->obDb->query.="`vOptSku`='".$this->libFunc->m_addToDB($this->request['sku'][$itemid])."',";
				$this->obDb->query.="`vItem`='".$this->libFunc->m_addToDB($this->request['item'][$itemid])."',";
				$this->obDb->query.="`fPrice`='".$tempprice."',";
				$this->obDb->query.="`iInventory`='".$this->libFunc->checkWrongValue($this->request['inventory'][$itemid])."',";
				$this->obDb->query.="`iUseInventory`='".$this->libFunc->checkWrongValue($this->request['use_inventory'][$itemid])."',";
				$this->obDb->query.="`iBackorder`='".$this->libFunc->checkWrongValue($this->request['backorder'][$itemid])."',";
				$this->obDb->query.="`iSort`='".$this->libFunc->checkWrongValue($this->request['sort'][$itemid])."' "; 
				$this->obDb->query.=" WHERE iOptionValueid_PK='".$this->request['itemid'][$i]."'"; 
				$this->obDb->updateQuery();
			}#endfor
		}#endif
		if(isset($this->request['addnew']))
		{	
			$this->obDb->query ="SELECT max(iSort) as maxsort FROM  ".OPTIONVALUES;
			$this->obDb->query.=" WHERE iOptionid_FK='".$this->request['optionid']."'";
			$rsSort=$this->obDb->fetchQuery();
			$sort=$rsSort[0]->maxsort+1;
	
			#INSERTING TO NEW OPTIONS VALUES
			if(!isset($this->request['use_inventorynew']))
			{
				$this->request['use_inventorynew']="";
			}
			if(!isset($this->request['backordernew']))
			{
				$this->request['backordernew']="";
			}
			$this->obDb->query ="INSERT INTO ".OPTIONVALUES." (`iOptionValueid_PK`,`iOptionid_FK`,`vOptSku`,`vItem`,";
			$this->obDb->query.="`fPrice`,`iInventory`,`iUseInventory`,`iBackorder`,`iSort`) ";
			$this->obDb->query.=" VALUES('','".$this->request['optionid']."',";
            $this->obDb->query.="'".$this->libFunc->m_addToDB($this->request['skunew'])."',";
			$this->obDb->query.="'".$this->libFunc->m_addToDB($this->request['itemnew'])."',";
			if(is_numeric($this->request['pricenew']))
			{
			$this->obDb->query.="'".$this->request['pricenew']."',";
			}
			else
			{
			$this->obDb->query.="'" . 0 ."',";
			}
			$this->obDb->query.="'".$this->libFunc->checkWrongValue($this->request['inventorynew'])."',";
			$this->obDb->query.="'".$this->libFunc->checkWrongValue($this->request['use_inventorynew'])."',";
			$this->obDb->query.="'".$this->libFunc->checkWrongValue($this->request['backordernew'])."',";
			$this->obDb->query.="'".$this->libFunc->checkWrongValue($sort)."')";
			$this->obDb->updateQuery();
		}	
		if(isset($this->request['del']))
		{
			$del=$this->request['del'];
			foreach($del as $delid=>$delValue)
			{
				$this->obDb->query = "SELECT vImage FROM ".OPTIONVALUES; 
				$this->obDb->query.=" WHERE iOptionValueid_PK ='".$delid."'";
				$rsImage = $this->obDb->fetchQuery();
				$rsCount = $this->obDb->record_count;	
				if($rsCount>0)
				{
					if($this->libFunc->m_checkFileExist($rsImage[0]->vImage,"options"))
					{
						$fileUpload->deleteFile($this->imagePath."options/".$rsImage[0]->vImage);
					}
					$this->obDb->query="DELETE FROM ".OPTIONVALUES." WHERE iOptionValueid_PK='".$delid."'"; 
					$this->obDb->updateQuery();
				}
			}
		}
		$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_option.editForm&optionid=".$this->request['optionid']);	
		exit;
	}#end update option function

	#function to delete an option
	function m_deleteOption()
	{
		$fileUpload = new FileUpload();
		if(isset($this->request['optionid']) && !empty($this->request['optionid']))
		{
			$this->obDb->query = "SELECT vImage FROM ".OPTIONVALUES; 
			$this->obDb->query.=" WHERE iOptionId_FK ='".$this->request['optionid']."'";
			$rsImage = $this->obDb->fetchQuery();
			$rsCount = $this->obDb->record_count;	
			if($rsCount>0)
			{
				for($i=0;$i<$rsCount;$i++)
				{
					if($this->libFunc->m_checkFileExist($rsImage[$i]->vImage,"options"))
					{
							$fileUpload->deleteFile($this->imagePath."options/".$rsImage[$i]->vImage);
					}
				}
			}				
			#DELETING MENUHEADER
			$this->obDb->query = "DELETE FROM ".OPTIONS." WHERE iOptionId_PK =".$this->request['optionid'];
			$this->obDb->updateQuery();
			$this->obDb->query = "DELETE FROM ".OPTIONVALUES." WHERE iOptionId_FK =".$this->request['optionid'];
			$this->obDb->updateQuery();
		}
		$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_option.home");	
		exit;		
	}

	function m_uploadImage()
	{
		$fileUpload = new FileUpload();
		if($this->request['type']=="choice")
		{
			$this->obDb->query ="SELECT iChoiceid_PK,vImage FROM ".CHOICES;
			$this->obDb->query.=" WHERE iChoiceid_PK=".$this->request['id'];
			$row_code				= $this->obDb->fetchQuery();
		}
		else
		{
			$this->obDb->query ="SELECT iOptionid_FK,iOptionValueid_PK,vItem,vImage FROM ".OPTIONVALUES;
			$this->obDb->query.=" WHERE iOptionValueid_PK = ".$this->request['id'];
			$row_code				= $this->obDb->fetchQuery();
		}
						
		if($this->libFunc->checkImageUpload("image"))
		{
			$fileUpload->source	 = $_FILES["image"]["tmp_name"];
			$fileUpload->target	 = $this->imagePath."options/".$_FILES["image"]["name"];
			$newName1				 = $fileUpload->upload();

			//$fileUpload->resampleImage ($this->imagePath."options/".$newName1,50,50,75);
			if($newName1 != false)
				$image = $newName1;
			
			if($this->libFunc->m_checkFileExist($row_code[0]->vImage,"options"))
			{
				$fileUpload->deleteFile($this->imagePath."options/".$row_code[0]->vImage);
			}				
			$imagename="image";	
		}
		else
		{
			$image=$row_code[0]->vImage;
		}

		if($this->request['type']=="choice")
		{
			$this->obDb->query="UPDATE ".CHOICES." SET `vImage`='$image' ,";
			$this->obDb->query.="`tmEditDate`='".time()."', `iAdminUser` ='".$_SESSION['uid']."'";
			$this->obDb->query.=" WHERE iChoiceid_PK ='".$this->request['id']."'";	
		}
		else
		{
			$this->obDb->query="UPDATE ".OPTIONVALUES." SET `vImage`='$image'";
			$this->obDb->query.="WHERE iOptionValueid_PK ='".$this->request['id']."'";		
			$this->obDb->updateQuery();

			$this->obDb->query="UPDATE ".OPTIONS." SET `tmEditDate`='".time()."',";
			$this->obDb->query.="`iAdminUser` ='".$_SESSION['uid']."'";
			$this->obDb->query.=" WHERE iOptionId_PK ='".$row_code[0]->iOptionid_FK."'";		
		}
		$this->obDb->updateQuery();
		$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_option.uploadForm&status=1&id=".$this->request['id']."&type=".$this->request['type']);
		exit;
	}	


	#INSERT NEW CHOICE
	function m_insertChoice()
	{
		$timeStamp=time();
		$fileUpload = new FileUpload();
		
		$this->request['use_inventory']	=$this->libFunc->ifSet($this->request,"use_inventory","0");
		$this->request['backorder']		=$this->libFunc->ifSet($this->request,"backorder","0");
		$this->request['state']			=$this->libFunc->ifSet($this->request,"state","0");
		$image							="";
		
		if($this->libFunc->checkImageUpload("image"))
		{
			$fileUpload->source = $_FILES["image"]["tmp_name"];
			$fileUpload->target = $this->imagePath."options/".$_FILES["image"]["name"];
			$newName1 = $fileUpload->upload();
			if($newName1 != false)
				$image = $newName1;
			$imagename="image";	
		}

		#INSERTING TO OPTIONS
		$this->obDb->query ="INSERT INTO ".CHOICES." (`vName`,`vDescription`,`iInventory`,";
		$this->obDb->query.="`fPrice`,`vType`,`iUseInventory`,`iBackorder`,`iState`,`vImage`,";
		$this->obDb->query.="`tmBuildDate`, `iAdminUser`) VALUES( ";			$this->obDb->query.="'".$this->libFunc->m_addToDB($this->request['cname'])."',";
		$this->obDb->query.="'".$this->libFunc->m_addToDB($this->request['description'])."',";
		$this->obDb->query.="'".$this->libFunc->checkWrongValue($this->request['inventory'])."',";
		$this->obDb->query.="'".$this->libFunc->checkFloatValue($this->request['price'])."',";
		$this->obDb->query.="'".$this->request['type']."',";
		$this->obDb->query.="'".$this->request['use_inventory']."','".$this->request['backorder']."',";
		$this->obDb->query.="'".$this->request['state']."','".$image."','$timeStamp','".$_SESSION['uid']."')";
		$this->obDb->updateQuery();
		$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_option.home");	
		exit;
	}#ef


	#FUNCTION TO UPDATE DEPARTMENT
	function m_updateChoice()
	{
		$timeStamp=time();

		$this->request['use_inventory']	=$this->libFunc->ifSet($this->request,"use_inventory","0");
		$this->request['backorder']		=$this->libFunc->ifSet($this->request,"backorder","0");
		$this->request['state']				=$this->libFunc->ifSet($this->request,"state","0");

		#INSERTING TO OPTIONS
		$this->obDb->query ="UPDATE ".CHOICES." SET ";
		$this->obDb->query.="`vName`='".$this->libFunc->m_addToDB($this->request['cname'])."',";
		$this->obDb->query.="`vDescription`='".$this->libFunc->m_addToDB($this->request['description'])."',";
		$this->obDb->query.="`fPrice`='".$this->libFunc->checkFloatValue($this->request['price'])."',";
		$this->obDb->query.="`iInventory`='".$this->libFunc->checkWrongValue($this->request['inventory'])."',";
		$this->obDb->query.="`iUseInventory`='".$this->request['use_inventory']."',"; 
		$this->obDb->query.="`iBackorder`='".$this->request['backorder']."',";
		$this->obDb->query.="`iState`='".$this->request['state']."',`vType`='".$this->request['type']."', ";
		$this->obDb->query.="`tmEditDate`='$timeStamp', `iAdminUser`='".$_SESSION['uid']."'";
		$this->obDb->query.=" WHERE iChoiceid_PK ='".$this->request['choiceid']."'";
		$this->obDb->updateQuery();
		$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_option.editChoice&choiceid=".$this->request['choiceid']);	
		exit;
	}


	# function to delete an option
	function m_deleteChoice()
	{
		$obFile=new FileUpload ();
		if(isset($this->request['choiceid']) && !empty($this->request['choiceid']))
		{
			$this->obDb->query ="SELECT vImage FROM ".CHOICES;
			$this->obDb->query.=" WHERE iChoiceid_PK  ='".$this->request['choiceid']."'";
			$rsImage = $this->obDb->fetchQuery();
			$rsCount = $this->obDb->record_count;	
			if($rsCount>0)
			{
				#DELETING IMAGES 
				if($this->libFunc->m_checkFileExist($rsImage[0]->vImage,"options"))
				{
					$obFile->deleteFile($this->imagePath."options/".$rsImage[0]->vImage);
				}	
				#DELETING CHOICE
				$this->obDb->query = "DELETE FROM ".CHOICES." WHERE iChoiceid_PK  ='".$this->request['choiceid']."'";
				$this->obDb->updateQuery();
			}
		}
		$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_option.home");	
		exit;		
	}
	
	# function to upload images while adding the product options
	
	function m_uploadImages()
	{	
		$fileUpload = new FileUpload();
			foreach($_FILES["image"]["name"] as $key=>$val){
				if($_FILES["image"]["name"][$key]!="")
				{	
					$fileUpload->source	 = $_FILES["image"]["tmp_name"][$key];
					$fileUpload->target	 = $this->imagePath."options/".$_FILES["image"]["name"][$key];
					$image[] = $fileUpload->upload();
				}else{
					$image[] = 0;
				}
		}
		return $image;
	}
	
/*	
	
	function m_insertAttribute()
	{
		
	$timeStamp=time();
		
		#INSERTING TO OPTIONS
		$this->obDb->query ="INSERT INTO ".ATTRIBUTES;
		$this->obDb->query.=" (`vName`,`vDescription`,`tmBuildDate`, `iAdminUser`) ";
		$this->obDb->query.=" VALUES('".$this->libFunc->m_addToDB($this->request['name1'])."',";
		$this->obDb->query.="'".$this->libFunc->m_addToDB($this->request['description'])."',";
		$this->obDb->query.="'$timeStamp','".$_SESSION['uid']."')";
		$this->obDb->updateQuery();

		$optionId=$this->obDb->last_insert_id;
		$sort=1;
		#INSERTING TO OPTIONS VALUES
		if(isset($this->request['option_count']))
		{
			for($i=0;$i<$this->request['option_count'];$i++)
			{
				if(!isset($this->request['use_inventory'][$i]))
				{
					$this->request['use_inventory'][$i]="0";
				}
				if(!isset($this->request['backorder'][$i]))
				{
					$this->request['backorder'][$i]="0";
				}
				$this->obDb->query="INSERT INTO ".OPTIONVALUES." (`iOptionValueid_PK`,`iOptionid_FK`,`vOptSku`,`vItem`,";
				$this->obDb->query.="`fPrice`,`iInventory`,`iUseInventory`,`iBackorder`,`iSort`) ";
				$this->obDb->query.="	VALUES('','$optionId','".$this->libFunc->m_addToDB($this->request['sku'][$i])."',
				'".$this->libFunc->m_addToDB($this->request['item'][$i])."',";
				$this->obDb->query.="'".$this->libFunc->checkFloatValue($this->request['price'][$i])."',";
				$this->obDb->query.="'".$this->libFunc->checkWrongValue($this->request['inventory'][$i])."',";
				$this->obDb->query.="'".$this->libFunc->checkWrongValue($this->request['use_inventory'][$i])."',";
				$this->obDb->query.="'".$this->libFunc->checkWrongValue($this->request['backorder'][$i])."','$sort')";
				$this->obDb->updateQuery();
				$sort++;
			}#endfor
		}#endif
		$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_option.home");	
		exit;	
		
	}
*/	
}#CLASS ENDS
?>