<?php
/*
=======================================================================================
Copyright: TradingEye
Version: 7.1.1
=======================================================================================
*/
defined('_TEEXEC') or die;

include_once SITE_PATH."LanguagePacks/".LANGUAGE_PACK;
class c_packageInterface
{
#CONSTRUCTOR
	function c_packageInterface()
	{
		$this->libFunc=new c_libFunctions();
		$this->productTemplatePath=MODULES_PATH."ecom/templates/main/product/";
	}

	#FUNCTION TO DISPLAY PACKAGE
	function m_packageHome()
	{
		
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_PACKAGE_FILE",$this->packageTemplate);

		#SETTING ALL TEMPLATE BLOCKS
		$this->ObTpl->set_block("TPL_PACKAGE_FILE","TPL_ATTACHED_BLK", "attached_blk");
		$this->ObTpl->set_block("TPL_PACKAGE_FILE","TPL_BUTTON_BLK", "btn_blk");
		#SETTING TEMPLATE VARIABLE
		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	
		$this->ObTpl->set_var("TPL_SHOPURL",SITE_URL."ecom/");
				
		$this->ObTpl->set_var("attached_blk","");
		$this->ObTpl->set_var("btn_blk","");
		$this->ObTpl->set_var("TPL_VAR_MSG","");
		
		//defining language variables
		$this->ObTpl->set_var("LANG_VAR_PRODUCTPACKAGE",LANG_PRODUCTPACKAGES);
		$this->ObTpl->set_var("LANG_VAR_CREATEPACKAGE",LANG_CREATEPACKAGE);
		$this->ObTpl->set_var("LANG_VAR_RECORDSFOUND",LANG_RECORDSFOUND);
		$this->ObTpl->set_var("LANG_VAR_SEARCH",LANG_SEARCH);
		$this->ObTpl->set_var("LANG_VAR_ID",LANG_ID);
		$this->ObTpl->set_var("LANG_VAR_NAME",LANG_NAME);
		$this->ObTpl->set_var("LANG_VAR_PRODUCTSTXT",LANG_PRODUCTSTXT);
		$this->ObTpl->set_var("LANG_VAR_DISASSEMBLE",LANG_DISASSEMBLE);
		$this->ObTpl->set_var("LANG_VAR_EDITPACKAGE",LANG_EDITPACKAGE);
		$this->ObTpl->set_var("LANG_VAR_EDIT",LANG_EDIT);

		if(!isset($this->request['search']))
		{
			$this->request['search']="";
		}	
		
		#TO DISPLAY CURRENTLY ATTACHED ITEMS
		$query1 = "SELECT vSku,vTitle,iProdId_PK,iKit FROM ".PRODUCTS." WHERE iKit='1' OR iKit='2'  ";

		if(!empty($this->request['search']))
		{
			$query1.=" AND vTitle like '%".$this->request['search']."%' OR vSku like '%".$this->request['search']."%'";
			$this->ObTpl->set_var("TPL_VAR_LINK","<a href=".SITE_URL."ecom/adminindex.php?action=ec_package.home>View all</a>");
		}	
		else
		{
			$this->ObTpl->set_var("TPL_VAR_LINK","");
		}
	
		$this->obDb->query=$query1;
		$queryResult = $this->obDb->fetchQuery();
		$recordCount=$this->obDb->record_count;
		$this->ObTpl->set_var("TPL_VAR_RECORDCOUNT",$recordCount);
		if($recordCount>0)
		{
			#PARSING TPL_ITEMS_BLK
			for($j=0;$j<$recordCount;$j++)
			{
				$this->obDb->query= "SELECT iKitId_PK FROM  ".PRODUCTKITS."  WHERE iKitid='".$queryResult[$j]->iProdId_PK."'";
				$rs = $this->obDb->fetchQuery();
				$rCount=$this->obDb->record_count;
				$this->ObTpl->set_var("TPL_VAR_RCOUNT",$rCount);

				$this->obDb->query= "SELECT vOwnerType,iOwner_FK FROM  ".FUSIONS."  WHERE  iSubId_FK='".$queryResult[$j]->iProdId_PK."' AND vType='product'";
				$rs1 = $this->obDb->fetchQuery();
				
				$this->ObTpl->set_var("TPL_VAR_TYPE",$rs1[0]->vOwnerType);
				$this->ObTpl->set_var("TPL_VAR_OWNER",$rs1[0]->iOwner_FK);

				if($queryResult[$j]->iKit==1)
				{
					$this->ObTpl->set_var("TPL_VAR_CHECKED","checked=\"checked\"");
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_CHECKED","");
				}
				$this->ObTpl->set_var("TPL_VAR_SKU",$this->libFunc->m_displayContent($queryResult[$j]->vSku));
				$this->ObTpl->set_var("TPL_VAR_TITLE",$this->libFunc->m_displayContent($queryResult[$j]->vTitle));
				$this->ObTpl->set_var("TPL_VAR_ID",$queryResult[$j]->iProdId_PK);
				$this->ObTpl->set_var("TPL_VAR_KITID",$rs[0]->iKitId_PK);
				$this->ObTpl->parse("attached_blk","TPL_ATTACHED_BLK",true);
				$this->ObTpl->parse("btn_blk","TPL_BUTTON_BLK");
			}
		}
		else
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",MSG_NOPACKAGE);
		}
		
		#END DISPLAY CURRENTLY ATTACHED ITEMS
		return($this->ObTpl->parse("return","TPL_PACKAGE_FILE"));
	}


#FUNCTION TO BUILD PACKAGE
	function m_packageBuild()
	{
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_PACKAGE_FILE",$this->packageTemplate);

		#SETTING ALL TEMPLATE BLOCKS
		$this->ObTpl->set_block("TPL_PACKAGE_FILE","TPL_DEPARTMENT_BLK", "dept_blk");
		$this->ObTpl->set_block("TPL_PACKAGE_FILE","TPL_ITEMS_BLK", "items_blk");
		$this->ObTpl->set_block("TPL_PACKAGE_FILE","TPL_MAIN_BLK", "main_blk");
		$this->ObTpl->set_block("TPL_MAIN_BLK","TPL_ATTACHED_BLK", "attached_blk");
		#INTIALIZING VARIABLES
		if(!isset($this->request['owner']))
		{
			$this->request['owner']="0";
		}
		if(!isset($this->request['type']))
		{
			$this->request['type']="product";
		}
		if(!isset($this->request['otype']))
		{
			$this->request['otype']="department";
		}
		if(!isset($this->request['kitid']))
		{
			$this->request['kitid']="";
		}

		#SETTING TEMPLATE VARIABLE
		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	
		$this->ObTpl->set_var("TPL_SHOPURL",SITE_URL."ecom/");
		$this->ObTpl->set_var("TPL_VAR_OWNER",$this->request['owner']);
		$this->ObTpl->set_var("TPL_VAR_TYPE",$this->request['type']);
		$this->ObTpl->set_var("TPL_VAR_OTYPE",$this->request['otype']);
		$this->ObTpl->set_var("TPL_VAR_KITID",$this->request['kitid']);
		
		//defining language variables
		$this->ObTpl->set_var("LANG_VAR_BUILDPACKAGE",LANG_BUILDPACKAGE);
		$this->ObTpl->set_var("LANG_VAR_CURRENTPACKAGE",LANG_CURRENTPACKAGE);
		$this->ObTpl->set_var("LANG_VAR_CODE",LANG_CODE);
		$this->ObTpl->set_var("LANG_VAR_PRODUCT",LANG_PRODUCTSTXT);
		$this->ObTpl->set_var("LANG_VAR_QTY",LANG_QTYTXT);
		$this->ObTpl->set_var("LANG_VAR_SORT",LANG_SORT);
		$this->ObTpl->set_var("LANG_VAR_REMOVE",LANG_REMOVE);
		$this->ObTpl->set_var("LANG_VAR_HOME",LANG_HOME);
		$this->ObTpl->set_var("LANG_VAR_ALLORPHAN",LANG_ALLORPHAN);
		$this->ObTpl->set_var("LANG_VAR_RETURNPACK",LANG_RETURNTOPACK);
		$this->ObTpl->set_var("LANG_VAR_VIEWITEMS",LANG_VIEWITEMS);
		$this->ObTpl->set_var("LANG_VAR_UPDATEPACKAGE",LANG_UPDATEPACKAGE);
		#START DISPLAY DEPARETMENT BLOCK
		$this->obDb->query = "SELECT vTitle,iDeptId_PK FROM ".DEPARTMENTS.", ".FUSIONS."  WHERE iDeptId_PK=iSubId_FK AND vType='department'";
		$deptResult = $this->obDb->fetchQuery();
		 $recordCount=$this->obDb->record_count;
		#PARSING DEPARTMENT BLOCK
		$this->ObTpl->set_var("SELECTED1","selected");
		
		
		if($recordCount>0)
		{
			for($i=0;$i<$recordCount;$i++)
			{
				$_SESSION['dspTitle']="";		
				 $this->ObTpl->set_var("TPL_VAR_TITLE",$this->m_getTitle($deptResult[$i]->iDeptId_PK,'department'));
				$this->ObTpl->set_var("TPL_VAR_ID",$deptResult[$i]->iDeptId_PK);
				if(isset($this->request['postOwner']) && $this->request['postOwner'] == $deptResult[$i]->iDeptId_PK)
				{
					$this->ObTpl->set_var("SELECTED1","");
					$this->ObTpl->set_var("SELECTED2","selected");
				}
				else
				{
					$this->ObTpl->set_var("SELECTED2","");
				}
				
				$this->ObTpl->parse("dept_blk","TPL_DEPARTMENT_BLK",true);
			}
		}
		else
		{
			$this->ObTpl->set_var("dept_blk","");
		}
		#END DISPLAY DEPARETMENT BLOCK

		#START DISPLAY PRODUCT BLOCK
		#IF TYPE IS CONTENT
		if(isset($this->request['postOwner']))#PRODUCT
		{#FOR ORPHAN PRODUCT
			if($this->request['postOwner']=="orphan")
			{
				 $this->obDb->query= "SELECT vTitle,fusionid,iProdId_PK FROM ".PRODUCTS." LEFT JOIN ".FUSIONS." ON iProdId_PK = iSubId_FK " ;
				$queryResult = $this->obDb->fetchQuery();
				$recordCount=$this->obDb->record_count;
				
				if($recordCount>0)
				{
					#PARSING TPL_ITEMS_BLK
					for($j=0;$j<$recordCount;$j++)
					{
						if(empty($queryResult[$j]->fusionid))
						{
							$this->ObTpl->set_var("TPL_VAR_TITLE",$this->libFunc->m_displayContent($queryResult[$j]->vTitle));
							$this->ObTpl->set_var("TPL_VAR_ID",$queryResult[$j]->iProdId_PK);
							$this->ObTpl->parse("items_blk","TPL_ITEMS_BLK",true);
						}
					}
				}
				else
				{
						$this->ObTpl->set_var("items_blk","");
				}
			}
			else
			{#IF OTHER THAN ORPHAN
				$query = "SELECT vTitle,iProdId_PK FROM ".PRODUCTS.", ".FUSIONS."  WHERE iProdId_PK=iSubId_FK AND iOwner_FK='".$this->request['postOwner']."' AND vOwnerType='department' AND vType='".$this->request['type']."'";
				$this->obDb->query=$query;
				$queryResult = $this->obDb->fetchQuery();
				$recordCount=$this->obDb->record_count;
				if($recordCount>0)
				{
					#PARSING TPL_ITEMS_BLK
					for($j=0;$j<$recordCount;$j++)
					{
						$this->ObTpl->set_var("TPL_VAR_TITLE",$this->libFunc->m_displayContent($queryResult[$j]->vTitle));
						$this->ObTpl->set_var("TPL_VAR_ID",$queryResult[$j]->iProdId_PK);
						$this->ObTpl->parse("items_blk","TPL_ITEMS_BLK",true);
					}
				}
				else
				{
						$this->ObTpl->set_var("items_blk","");
				}
			}
			$this->ObTpl->set_var("TPL_VAR_POSTOWNER",$this->request['postOwner']);
		}
		else#POST OWNER NOT SET
		{
			$this->ObTpl->set_var("items_blk","");
			$this->ObTpl->set_var("TPL_VAR_POSTOWNER","");
		}

		$this->obDb->query="SELECT vTitle FROM ".PRODUCTS." WHERE iProdId_PK='".$this->request['kitid']."'";
		$rs = $this->obDb->fetchQuery();
		if(!empty($rs[0]->vTitle))
		{
			$this->ObTpl->set_var("TPL_VAR_HEADTITLE",$this->libFunc->m_displayContent($rs[0]->vTitle));
		}
		else
		{
			$this->ObTpl->set_var("TPL_VAR_HEADTITLE","");
		}
			
		#TO DISPLAY CURRENTLY ATTACHED ITEMS
		$query1 = "SELECT vSku,vTitle,iProdId_PK,iKitId_PK,iSort,iQty  FROM ".PRODUCTS.", ".PRODUCTKITS."  WHERE iProdId_PK=iProdId_FK AND iKitId='".$this->request['kitid']."' order by iSort";
		$this->obDb->query=$query1;
		$queryResult = $this->obDb->fetchQuery();
		$recordCount=$this->obDb->record_count;
		if($recordCount>0)
		{
			#PARSING TPL_ITEMS_BLK
			for($j=0;$j<$recordCount;$j++)
			{
			
					$this->ObTpl->set_var("TPL_VAR_QTY",$queryResult[$j]->iQty);
					$this->ObTpl->set_var("TPL_VAR_SORT",$queryResult[$j]->iSort);
					$this->ObTpl->set_var("TPL_VAR_SKU",$this->libFunc->m_displayContent($queryResult[$j]->vSku));
					$this->ObTpl->set_var("TPL_VAR_TITLE",$this->libFunc->m_displayContent($queryResult[$j]->vTitle));
					$str=str_replace("'","\'",$this->libFunc->m_displayContent($queryResult[$j]->vTitle));
					$this->ObTpl->set_var("TPL_VAR_TITLE1",$str);
									
					$this->ObTpl->set_var("TPL_VAR_PID",$queryResult[$j]->iProdId_PK);
					$this->ObTpl->set_var("TPL_VAR_KID",$queryResult[$j]->iKitId_PK);
					$this->ObTpl->parse("attached_blk","TPL_ATTACHED_BLK",true);
			
			}

			$this->ObTpl->parse("main_blk","TPL_MAIN_BLK");
		}
		else
		{
				$this->ObTpl->set_var("attached_blk","");
				$this->ObTpl->parse("main_blk","TPL_MAIN_BLK");
		}
		#END DISPLAY CURRENTLY ATTACHED ITEMS
		$this->ObTpl->set_var("TPL_VAR_KITID",$this->request['kitid']);
		if(empty($this->request['kitid']))
		{
			$this->ObTpl->set_var("main_blk","");
			$this->ObTpl->set_var("TPL_BTNLBL",LBL_BUILDPACK);
		}
		else
		{
			$this->ObTpl->set_var("TPL_BTNLBL",LBL_ADDTOPACK);
		}
		return($this->ObTpl->parse("return","TPL_PACKAGE_FILE"));
	}
	
	function m_getTitle($ownerid,$type)
	{
		if($ownerid!=0)
		{
			$this->obDb->query = "SELECT vTitle,iOwner_FK,vOwnerType FROM ".DEPARTMENTS." D ,".FUSIONS." F WHERE iDeptid_PK=iSubId_FK and iSubId_FK=".$ownerid." AND vtype='".$type."'" ;
			$row = $this->obDb->fetchQuery();
			if($this->obDb->record_count != 0)
			{
				$_SESSION['dspTitle']=" /".$this->libFunc->m_displayContent($row[0]->vTitle).$_SESSION['dspTitle'];
				$this->m_getTitle($row[0]->iOwner_FK,$row[0]->vOwnerType);
			}
		}

			return $_SESSION['dspTitle'];
			
	}

		#FUNCTION TO DISAMBLE PACKAGE
	function m_packageDisamble()
	{
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_PACKAGE_FILE",$this->packageTemplate);
		$this->ObTpl->set_block("TPL_PACKAGE_FILE","TPL_TEMPLATE_BLK","template_blk");
		$this->obDb->query="SELECT vTitle,vTemplate FROM ".PRODUCTS." WHERE iProdId_PK='".$this->request['kitid']."'";
		$rs = $this->obDb->fetchQuery();
		if (is_dir($this->productTemplatePath)) 
		{
			if ($dh = opendir($this->productTemplatePath))
			{			
				while (($templateName = readdir($dh)) !== false) 
				{
					if($templateName!="." && $templateName!="..")
					{
						if($templateName==$rs[0]->vTemplate)
						{
							$this->ObTpl->set_var("SELTEMPLATE","selected");
						}
						else
						{
							$this->ObTpl->set_var("SELTEMPLATE","");
						}
						$this->ObTpl->set_var("TPL_VAR_TEMPLATENAME",$templateName);
						$this->ObTpl->parse("template_blk","TPL_TEMPLATE_BLK",true);
					}
				}
				closedir($dh);
			}
		}
		if(!isset($this->request['kitid']))
		{
			$this->request['kitid']="";
		}
		
		if(isset($this->request['msg'])  && $this->request['msg']==1)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG","<font class=message>".MSG_TPL_SELECT."</font>");
		}
		else
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",LBL_SELECT_TEMPLATE);
		}

		#SETTING TEMPLATE VARIABLE
		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	
		$this->ObTpl->set_var("TPL_SHOPURL",SITE_URL."ecom/");
		$this->ObTpl->set_var("TPL_VAR_KITID",$this->request['kitid']);
		$this->ObTpl->set_var("TPL_VAR_PNAME",$this->libFunc->m_displayContent($rs[0]->vTitle));
		
		//defining language variables
		$this->ObTpl->set_var("TPL_VAR_PRODUCTDISS",LANG_PRODUCTDISASSEMBLE);
		$this->ObTpl->set_var("TPL_VAR_PACKAGEITEM",LANG_PACKAGEITEM);
		$this->ObTpl->set_var("TPL_VAR_DISSMESSAGE",LANG_DISSMESSAGE);
		$this->ObTpl->set_var("TPL_VAR_UNKIT",LANG_UNKIT);
		$this->ObTpl->set_var("TPL_VAR_CANCEL",LANG_CANCEL);
		
		return($this->ObTpl->parse("return","TPL_PACKAGE_FILE"));
	}#EF
}#EC
?>