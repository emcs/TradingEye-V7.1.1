<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.1.0
=======================================================================================
*/
defined('_TEEXEC') or die;
class c_giftWrapInterface
{
#CONSTRUCTOR
	function  c_giftWrapInterface()
	{
		$this->err=0;
		$this->errMsg="";
		$this->libFunc=new c_libFunctions();
	}

#FUNCTION TO DISPLAY All AVAILABLE GIFTWRAPS
	function m_dspGiftWrap()
	{
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_GIFTWRAP_FILE",$this->giftWrapTemplate);

		#SETTING ALL TEMPLATE BLOCKS
		$this->ObTpl->set_block("TPL_GIFTWRAP_FILE","TPL_GIFTWRAP_BLK", "giftwrap_blk");
		$this->ObTpl->set_block("TPL_GIFTWRAP_FILE","TPL_BUTTON_BLK", "button_blk");
		$this->ObTpl->set_block("TPL_GIFTWRAP_FILE","TPL_MESSAGE_BLK", "message_blk");
		$this->ObTpl->set_block("TPL_GIFTWRAP_FILE","TPL_MSG_BLK1", "msg_blk1");
		#SETTING TEMPLATE VARIABLE
		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	
		$this->ObTpl->set_var("TPL_VAR_SALESURL",SITE_URL."sales/");
		$this->ObTpl->set_var("TPL_VAR_CURRENCY",CONST_CURRENCY);
		#INTAILIZING ***
		$this->ObTpl->set_var("giftwrap_blk","");	
		$this->ObTpl->set_var("button_blk","");
		$this->ObTpl->set_var("message_blk","");	
		$this->ObTpl->set_var("msg_blk1","");	

		$this->request['msg']=$this->libFunc->ifSet($this->request,"msg");
		$this->ObTpl->set_var("TPL_VAR_MESSAGE","");

		#DATABASE QUERY
		$this->obDb->query = "SELECT *  FROM ".GIFTWRAPS." ORDER BY iSort";
		$queryResult = $this->obDb->fetchQuery();
		$recordCount=$this->obDb->record_count;
		$this->ObTpl->set_var("TPL_VAR_MSG",$recordCount." records found");
		if($this->request['msg']==1)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",MSG_GIFTWRAP_INSERTED);
			$this->ObTpl->parse("msg_blk1","TPL_MSG_BLK1");
		}
		elseif($this->request['msg']==2)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",MSG_GIFTWRAP_UPDATED);
			$this->ObTpl->parse("msg_blk1","TPL_MSG_BLK1");
		}
		elseif($this->request['msg']==3)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",MSG_GIFTWRAP_DELETED);
			$this->ObTpl->parse("msg_blk1","TPL_MSG_BLK1");
		}

		if($recordCount>0)
		{
			#PARSING DISCOUNT BLOCK
			for($j=0;$j<$recordCount;$j++)
			{		
				$this->ObTpl->set_var("TPL_VAR_ID",$queryResult[$j]->iGiftwrapid_PK);
				$str =$this->libFunc->m_displayContent($queryResult[$j]->vTitle);
				$str=str_replace("'","\'",$str);

				$this->ObTpl->set_var("TPL_VAR_CODE1", $str);
				$this->ObTpl->set_var("TPL_VAR_TITLE",$this->libFunc->m_displayContent($queryResult[$j]->vTitle));
				$this->ObTpl->set_var("TPL_VAR_SORT",$queryResult[$j]->iSort);
				$this->ObTpl->set_var("TPL_VAR_PRICE",number_format($queryResult[$j]->fPrice ,2));

				if(empty($queryResult[$j]->vImage) || !file_exists($this->imagePath."giftwrap/".$queryResult[$j]->vImage))
				{
					$this->ObTpl->set_var("TPL_VAR_IMAGE","No Image");
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_IMAGE","Image");
				}
				if($queryResult[$j]->iState==1)
				{
					$this->ObTpl->set_var("TPL_VAR_CHECKED","checked=\"checked\"");
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_CHECKED","");
				}

				$this->ObTpl->parse("giftwrap_blk","TPL_GIFTWRAP_BLK",true);
			}
			$this->ObTpl->parse("button_blk","TPL_BUTTON_BLK");
		}
		else
		{
			$this->ObTpl->set_var("TPL_VAR_MESSAGE",MSG_NOGIFTWRAP);
			$this->ObTpl->parse("message_blk","TPL_MESSAGE_BLK");
		}
	
		return($this->ObTpl->parse("return","TPL_GIFTWRAP_FILE"));
	}


	#FUNCTION TO BUILD PACKAGE
	function m_giftWrapBuilder()
	{
		$libFunc=new c_libFunctions();
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_GIFTWRAP_FILE",$this->giftWrapTemplate);
		$this->ObTpl->set_block("TPL_GIFTWRAP_FILE","TPL_MSG_BLK", "msg_blk");
		$this->ObTpl->set_block("TPL_GIFTWRAP_FILE","DSPIMAGEBOX_BLK", "imagebox_blk");
		$this->ObTpl->set_block("TPL_GIFTWRAP_FILE","DSPIMAGELINK_BLK", "imagelink_blk");
		#SETTING TEMPLATE VARIABLE
		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	
		$this->ObTpl->set_var("TPL_VAR_SALESURL",SITE_URL."sales/");
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);

		#INTIALIZING
		$this->ObTpl->set_var("msg_blk","");
		$this->ObTpl->set_var("imagebox_blk","");
		$this->ObTpl->set_var("imagelink_blk","");
		$this->ObTpl->set_var("TPL_VAR_MSG","");
		$giftWrapRs[0]->vTitle		    ="";
		$giftWrapRs[0]->vImage 		="";
		$giftWrapRs[0]->vDescription  ="";
		$giftWrapRs[0]->fPrice			="";
		$giftWrapRs[0]->vImageLarge	="";
		$giftWrapRs[0]->iState			="1";

			
		if($this->err==1)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",$this->errMsg);
			$this->ObTpl->parse("msg_blk","TPL_MSG_BLK");
		}
	
		if(isset($_POST))
		{
			if(isset($this->request['title']))
				$giftWrapRs[0]->vTitle=$this->request['title'];
		
			if(isset($this->request['description']))
				$giftWrapRs[0]->vDescription=$this->request['description'];
			if(isset($this->request['price']))
				$giftWrapRs[0]->fPrice=$this->request['price'];
			if(isset($this->request['image']))
				$giftWrapRs[0]->vImage=$this->request['image'];
			if(isset($this->request['image_large']))
				$giftWrapRs[0]->vImageLarge =$this->request['image_large'];
		}
		#START DISPLAY MODULES
		if(isset($this->request['id']) && !empty($this->request['id']) && is_numeric($this->request['id']))
		{
			if($this->err==0)
			{
				#DATABASE QUERY
				$this->obDb->query = "SELECT *  FROM ".GIFTWRAPS." WHERE iGiftwrapid_PK='".$this->request['id']."'";
				$giftWrapRs = $this->obDb->fetchQuery();
				
				$this->ObTpl->set_var("TPL_VAR_MSG","Build Date ".$this->libFunc->dateFormat1($giftWrapRs[0]->tmBuildDate));
			}
			$this->ObTpl->set_var("TPL_VAR_MODE","edit");
			$this->ObTpl->set_var("TPL_VAR_ID",$this->request['id']);
			if(empty($giftWrapRs[0]->vImage) || !file_exists($this->imagePath."giftwrap/".$giftWrapRs[0]->vImage))
			{
				$this->ObTpl->set_var("LBL_LINK1",LBL_ADDIMAGE);
			}
			else
			{
				$this->ObTpl->set_var("LBL_LINK1",LBL_EDITIMAGE);
			}
			if(empty($giftWrapRs[0]->vImageLarge) || !file_exists($this->imagePath."giftwrap/".$giftWrapRs[0]->vImageLarge))
			{
				$this->ObTpl->set_var("LBL_LINK2",LBL_ADDIMAGE);
			}
			else
			{
				$this->ObTpl->set_var("LBL_LINK2",LBL_EDITIMAGE);
			}
			$this->ObTpl->parse("imagelink_blk","DSPIMAGELINK_BLK");
			$this->ObTpl->set_var("TPL_VAR_BTNLBL",LBL_EDITGIFTWRAP_BTN);
		}
		else
		{
			$this->ObTpl->set_var("TPL_VAR_ID","");
			$this->ObTpl->set_var("TPL_VAR_MODE","add");
			$this->ObTpl->parse("imagebox_blk","DSPIMAGEBOX_BLK");
			$this->ObTpl->set_var("TPL_VAR_BTNLBL",LBL_ADDGIFTWRAP_BTN);
		}
		#DISPLAYING MESSAGES
		if(isset($this->request['msg']) && $this->request['msg']==1)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",MSG_GIFTWRAP_UPDATED);
			$this->ObTpl->parse("msg_blk","TPL_MSG_BLK");
		}
		$this->ObTpl->set_var("TPL_VAR_TITLE",$this->libFunc->m_displayContent($giftWrapRs[0]->vTitle));
		$this->ObTpl->set_var("TPL_VAR_DESCRIPTION",$this->libFunc->m_displayContent($giftWrapRs[0]->vDescription));
		$this->ObTpl->set_var("TPL_VAR_PRICE",number_format($giftWrapRs[0]->fPrice,2));
		
		if($giftWrapRs[0]->iState==1)
		{
			$giftWrapRs[0]->iState="checked";
		}
		else
		{
			$giftWrapRs[0]->iState="";
		}
		$this->ObTpl->set_var("TPL_VAR_STATE",$giftWrapRs[0]->iState);
		
		return($this->ObTpl->parse("return","TPL_GIFTWRAP_FILE"));
	}
	
#FUNCTION TO VERIFY DATABASE UPDATION
	function m_verifyEdit()
	{
		$this->errMsg="";
		if(empty($this->request['title']))
		{
			$this->err=1;
			$this->errMsg=MSG_GIFTWRAP_EMPTY."<br>";
		}
		
		#VALIDATING EXISTING OPTION TITLE
		$this->obDb->query = "SELECT iGiftwrapid_PK  FROM ".GIFTWRAPS." where vTitle  = '".$this->request['title']."'";
		$row_code = $this->obDb->fetchQuery();
		if($this->obDb->record_count != 0)
		{
			if($row_code[0]->iGiftwrapid_PK !=$this->request['id'])
			{
				$this->err=1;
				$this->errMsg.=MSG_GIFTWRAP_EXIST."<br>";
			}
		}
		return $this->err;
	}

	#FUNCTION TO VERIFY DATABASE UPDATION
	function m_verifyInsert()
	{
		$this->errMsg="";
	
		if(empty($this->request['title']))
		{
			$this->err=1;
			$this->errMsg=MSG_GIFTWRAP_EMPTY."<br>";
		}
		
		#VALIDATING EXISTING OPTION TITLE
		$this->obDb->query = "SELECT iGiftwrapid_PK  FROM ".GIFTWRAPS." where vTitle  = '".$this->request['title']."'";
		$row_code = $this->obDb->fetchQuery();
		if($this->obDb->record_count != 0)
		{
			$this->err=1;
			$this->errMsg.=MSG_GIFTWRAP_EXIST."<br>";
		}
		return $this->err;
	}#END INSERT USER

	#FUNCTION TO UPLOAD GIFTWRAP IMAGE
	function m_uploadForm()
	{
		$obFile			=new FileUpload();
		$this->ObTpl	=new template();
		$this->ObTpl->set_file("TPL_EDITOR_FILE",$this->browseTemplate);
		$this->ObTpl->set_var("GRAPHICSMAINPATH", GRAPHICS_PATH);
		
		$this->ObTpl->set_var("TPL_VAR_DELETELINK","");
		$this->ObTpl->set_var("TPL_VAR_TOPMSG","");
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		$this->ObTpl->set_var("FORMURL",SITE_URL."sales/adminindex.php?action=promotions.giftwrap.upload");

		$this->ObTpl->set_var("TPL_VAR_ID",$this->request['id']);
		$this->obDb->query = "SELECT vImage,vImageLarge  FROM ".GIFTWRAPS." WHERE iGiftwrapid_PK='".$this->request['id']."'";
		$rsImage = $this->obDb->fetchQuery();
		if($this->request['img']=="vImage")
		{
			if($this->libFunc->m_checkFileExist($rsImage[0]->vImage,"giftwrap") && !empty($rsImage[0]->vImage))
			{
				$this->ObTpl->set_var("TPL_VAR_IMAGE","<img src=".$this->imageUrl."giftwrap/".$rsImage[0]->vImage." width='100' height='100'>");
				$this->ObTpl->set_var("TPL_VAR_DELETELINK",	"<a href=".SITE_URL."sales/adminindex.php?action=promotions.giftwrap.uploadForm&id=".$this->request['id']."&img=".$this->request['img']."&delete=1>Delete</a>");		
				if(isset($this->request['delete']) && $this->request['delete']==1)
				{
					$source=$this->imagePath."giftwrap/".$rsImage[0]->vImage;
					$obFile->deleteFile($source);
					$this->ObTpl->set_var("TPL_VAR_IMAGE",$this->libFunc->m_noImage());
					$this->ObTpl->set_var("TPL_VAR_DELETELINK","");
					$this->request['msg']=3;
				}
			}
			else
			{
				$this->ObTpl->set_var("TPL_VAR_IMAGE",$this->libFunc->m_noImage());
			}
		}
		elseif($this->request['img']=="vImageLarge")
		{
			if($this->libFunc->m_checkFileExist($rsImage[0]->vImageLarge,"giftwrap") && !empty($rsImage[0]->vImageLarge))
			{
				$this->ObTpl->set_var("TPL_VAR_IMAGE","<img src=".$this->imageUrl."giftwrap/".$rsImage[0]->vImageLarge." width='100' height='100'>");
				$this->ObTpl->set_var("TPL_VAR_DELETELINK",	"<a href=".SITE_URL."sales/adminindex.php?action=promotions.giftwrap.uploadForm&id=".$this->request['id']."&img=".$this->request['img']."&delete=1>Delete</a>");		
				if(isset($this->request['delete']) && $this->request['delete']==1)
				{
					$source=$this->imagePath."giftwrap/".$rsImage[0]->vImageLarge;
					$obFile->deleteFile($source);
					$this->ObTpl->set_var("TPL_VAR_IMAGE",$this->libFunc->m_noImage());
					$this->ObTpl->set_var("TPL_VAR_DELETELINK","");
					$this->request['msg']=3;
				}
			}
			else
			{
				$this->ObTpl->set_var("TPL_VAR_IMAGE",$this->libFunc->m_noImage());
			}
		}
		if(isset($this->request['msg']) && $this->request['msg']==1)
		{
			$this->ObTpl->set_var("TPL_VAR_TOPMSG","<span class='adminDetail'>".MSG_IMAGE_UPLOADED."</span>");
		}
		elseif(isset($this->request['msg']) && $this->request['msg']==2)
		{
			$this->ObTpl->set_var("TPL_VAR_TOPMSG","<span class='adminDetail'>".MSG_IMAGE_NOTUPLOADED."</span>");
		}
		elseif(isset($this->request['msg']) && $this->request['msg']==3)
		{
			$this->ObTpl->set_var("TPL_VAR_TOPMSG","<span class='adminDetail'>".MSG_IMGDELETE_SUCCESS."</span>");
		}
		else
		{
			$this->ObTpl->set_var("TPL_VAR_TOPMSG","Update Image");
		}
		$this->ObTpl->set_var("TPL_VAR_IMG",$this->request['img']);
		$this->ObTpl->pparse("return","TPL_EDITOR_FILE");
		exit;
	}#END UPLOAD FUNCTION
}
?>