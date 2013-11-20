<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.1.0
=======================================================================================
*/
defined('_TEEXEC') or die;
include_once SITE_PATH."LanguagePacks/".LANGUAGE_PACK;

class c_optionInterface
{
	#CONSTRUCTOR
	function c_optionInterface()
	{
		$this->err=0;
		$this->errMsg="";
		$this->libFunc=new c_libFunctions();
	}

	
	# METHOD TO DISPLAY Custom Option form	
	function m_showCustomOpt(){
		
		#DECLARYFING TEMPLATE FILE
		$this->ObTpl=new template();
		$this->ObTpl->set_file("optionCustom", $this->optionsTemplate);
		
		#INTIALIZING TEMPLATE BLOCKS		
		$this->ObTpl->set_block("optionCustom","TPL_MAINOPTIONS_BLK","mainoption_blk");		
		$this->ObTpl->set_block("TPL_MAINOPTIONS_BLK","TPL_CTMOPTIONS_BLK","dspctmoption_blk");
		$this->ObTpl->set_var("TPL_SHOPURL",SITE_URL."ecom/");
		
		$this->ObTpl->set_var("mainoption_blk","");
		$this->ObTpl->set_var("dspctmoption_blk","");
		
		#QUERY TO GET DEPARTMENTS UNDER SELECTED DEPARTMENT
		$query= "SELECT *  FROM ". CHOICES  ;
		if(isset($this->request['search']) && !empty($this->request['search']))
		{
			$query=$query." WHERE vName LIKE '%".$this->request['search']."%' OR vDescription LIKE '%".$this->request['search']."%'";
			$this->ObTpl->set_var("TPL_VAR_SEARCH",$this->request['search']);
			$this->ObTpl->set_var("TPL_VAR_VIEWALL","<a href='".SITE_URL."ecom/adminindex.php?action=ec_option.home'>View All</a>");
		}
		else
		{
			$this->ObTpl->set_var("TPL_VAR_SEARCH","");
			$this->ObTpl->set_var("TPL_VAR_VIEWALL","");
		}
		
		$query=$query." order by vName";
		$this->obDb->query=$query;	
		
		$resChoice=$this->obDb->fetchQuery();
		$varCount1=$this->obDb->record_count;
		$this->ObTpl->set_var("TPL_VAR_CURRENCY",CONST_CURRENCY);
		$this->ObTpl->set_var("TPL_TOTAL_RECORDS1",$varCount1);
		if($varCount1>0)
		{
			for($i=0;$i<$varCount1;$i++)
			{
				if($resChoice[$i]->iUseInventory==1)
				{
					$this->ObTpl->set_var("TPL_VAR_USTOCK","yes");
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_USTOCK","no");
				}	

				if(file_exists($this->imagePath."options/".$resChoice[$i]->vImage) && $resChoice[$i]->vImage!="")
				{
					$this->ObTpl->set_var("TPL_VAR_IMGLBL","Edit");
				}
				else
				{
						$this->ObTpl->set_var("TPL_VAR_IMGLBL","Add");
				}
				$this->ObTpl->set_var("TPL_VAR_CHOICEID",$resChoice[$i]->iChoiceid_PK);
				$this->ObTpl->set_var("TPL_VAR_CTITLE",$resChoice[$i]->vName);
				$this->ObTpl->set_var("TPL_VAR_TYPE",$resChoice[$i]->vType);
				$this->ObTpl->set_var("TPL_VAR_PRICE",$resChoice[$i]->fPrice);
				$this->ObTpl->set_var("TPL_VAR_MESSAGE","");
				$this->ObTpl->parse("dspctmoption_blk","TPL_CTMOPTIONS_BLK",true);	
			}
		$this->ObTpl->parse("mainoption_blk","TPL_MAINOPTIONS_BLK",true);
		}
		
		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);
		return($this->ObTpl->parse("return","optionCustom"));
	}	
	
	function m_showAddattribute()
	{
		$this->ObTpl=new template();
		$this->ObTpl->set_file("addattribute", $this->addattributeTemplate);
		
		$this->ObTpl->set_block("addattribute","TPL_ADDFIELDNUMBER_BLK","addfieldnumber_blk");
		$this->ObTpl->set_block("addattribute","TPL_ADDDETAILS_BLK","adddetails_blk");
		$this->ObTpl->set_block("TPL_ADDDETAILS_BLK","TPL_FIELDS_BLK","field_blk");
		
		$this->ObTpl->set_var("addfieldnumber_blk","");
		$this->ObTpl->set_var("adddetails_blk","");
		$this->ObTpl->set_var("field_blk","");
		
		$this->ObTpl->set_var("TPL_VAR_TITLE","");
		$this->ObTpl->set_var("TPL_VAR_NAME","");
		$this->ObTpl->set_var("TPL_VAR_PREFIX","");
		$this->ObTpl->set_var("TPL_VAR_SUFFIX","");
		$this->ObTpl->set_var("TPL_VAR_VALUE","");
		
		$this->ObTpl->set_var("TPL_VAR_SITEURL", SITE_URL);
		
		#SHOW BLANK FORM FOR ADDNEW
	if(isset($this->request['flag'])&& $this->request['flag']=='addnew') 
		{
			if (!isset($this->request['add'])){
			$this->ObTpl->set_var("TPL_VAR_ACTION",SITE_URL."ecom/adminindex.php?action=ec_option.dspAddattribute&flag=addnew");
			$this->ObTpl->parse("addfieldnumber_blk","TPL_ADDFIELDNUMBER_BLK");	
			}else {
				$_SESSION['fieldnumber']=$this->request['fieldnumber'];
				for ($i=0;$i<$this->request['fieldnumber'];$i++){
				$this->ObTpl->set_var("TPL_VAR_BOXNUMBER",$i+1);	
				$this->ObTpl->parse("field_blk","TPL_FIELDS_BLK",true);
				}
				$this->ObTpl->set_var("TPL_VAR_ACTION1",SITE_URL."ecom/adminindex.php?action=ec_option.addattribute");
				$this->ObTpl->set_var("TPL_VAR_BUTTONVALUE","Add New Product Attributes");
				
				
				$this->ObTpl->parse("adddetails_blk","TPL_ADDDETAILS_BLK");
			}
		}
		#SHOW FORM FOR EDIT
		if(isset($this->request['flag'])&& $this->request['flag']=='edit'){
			
			$this->obDb->query= "SELECT * FROM ".ATTRIBUTES. " WHERE iAttributesid_PK=".$this->request["optionid"];
			$attributerow = $this->obDb->fetchQuery();
			
			$this->ObTpl->set_var("TPL_VAR_ATTRIBUTEID",$attributerow[0]->iAttributesid_PK);
			$this->ObTpl->set_var("TPL_VAR_TITLE",$attributerow[0]->vAttributeTitle);
			$this->ObTpl->set_var("TPL_VAR_ACTION1",SITE_URL."ecom/adminindex.php?action=ec_option.addattribute");
			$this->ObTpl->parse("adddetails_blk","TPL_ADDDETAILS_BLK");
			
			$name = explode("�",$attributerow[0]->vFieldname);
			$prefix = explode("�",$attributerow[0]->vPrefix);
			$suffix = explode("�",$attributerow[0]->vSuffix);
			for ($i=0;$i<$attributerow[0]->iFieldnumber;$i++){
					$this->ObTpl->set_var("TPL_VAR_BOXNUMBER",$i+1);
					$this->ObTpl->set_var("TPL_VAR_NAME",$name[$i]);
					$this->ObTpl->set_var("TPL_VAR_PREFIX",$prefix[$i]);
					$this->ObTpl->set_var("TPL_VAR_SUFFIX",$suffix[$i]);
					$this->ObTpl->parse("field_blk","TPL_FIELDS_BLK",true);			
				}
			$this->ObTpl->set_var("TPL_VAR_BUTTONVALUE","Update Product Attributes");
			$this->ObTpl->set_var("TPL_VAR_ACTION1",SITE_URL."ecom/adminindex.php?action=ec_option.editattribute");
			$this->ObTpl->parse("adddetails_blk","TPL_ADDDETAILS_BLK");
		} 
	return($this->ObTpl->parse("return","addattribute"));
	}
	
	
	# DISPLAY ATTRIBUTE	FORM
	function m_showAttribute()
	{
	
		$this->ObTpl=new template();
		$this->ObTpl->set_file("attributes", $this->attributeTemplate);
		#INTIALIZING TEMPLATE BLOCKS
		$this->ObTpl->set_block("attributes","TPL_MAINATTRIBUTES_BLK","mainattributes_blk");
		$this->ObTpl->set_block("TPL_MAINATTRIBUTES_BLK","TPL_STDOPTIONS_BLK","dspstdoption_blk");
		$this->ObTpl->set_var("dspstdoption_blk","");	
		$this->ObTpl->set_var("TPL_ADD_ATTRIBUTE_URL",SITE_URL."ecom/adminindex.php?action=ec_option.dspAddattribute&flag=addnew");
		#DEFINING LANGUAGE VARIABLES
		$this->ObTpl->set_var("LANG_VAR_STANDARDOPTIONTXT",LANG_STANDARDOPTIONS);
		$this->ObTpl->set_var("LANG_VAR_ADDNEWOPTTXT",LANG_ADDNEWOPTION);
		$this->ObTpl->set_var("LANG_VAR_SEARCH",LANG_SEARCH);
		$this->ObTpl->set_var("LANG_VAR_OPTIONTITLE",LANG_OPTIONTITLE);
		$this->ObTpl->set_var("LANG_VAR_DESCRIPTION",LANG_DESCRIPTION);
		$this->ObTpl->set_var("LANG_VAR_EDIT",LANG_EDIT);
		$this->ObTpl->set_var("LANG_VAR_DELETE",LANG_DELETE);
		$this->ObTpl->set_var("LANG_VAR_RECORDSFOUND",LANG_RECORDSFOUND);
		$this->ObTpl->set_var("LANG_VAR_CUSTOMOPTIONS",LANG_CUSTOMOPTIONS);
		$this->ObTpl->set_var("LANG_VAR_NAME",LANG_NAME);
		$this->ObTpl->set_var("LANG_VAR_FORMTYPE",LANG_FORMTYPE);
		$this->ObTpl->set_var("LANG_VAR_IMAGE",LANG_IMAGE);
		$this->ObTpl->set_var("LANG_VAR_USINGSTOCK",LANG_USINGSTOCK);
		$this->ObTpl->set_var("LANG_VAR_ADDITIONALPRICE",LANG_ADDTLPRICE);
        
        $this->ObTpl->set_var("TPL_VAR_SITEURL", SITE_URL);
		#QUERY TO GET DEPARTMENTS UNDER SELECTED DEPARTMENT
		
		$this->ObTpl->set_var("mainattributes_blk","");
	
		
		$query= "SELECT vAttributeTitle,iFieldnumber,iAttributesid_PK FROM ".ATTRIBUTES  ;
				
		if(isset($this->request['search']) && !empty($this->request['search']))
		{
			$query=$query." WHERE vAttributeTitle LIKE '%".$this->request['search']."%' OR vDescription LIKE '%".$this->request['search']."%'";
			$this->ObTpl->set_var("TPL_VAR_SEARCH",$this->request['search']);
			$this->ObTpl->set_var("TPL_VAR_VIEWALL","<a href='".SITE_URL."ecom/adminindex.php?action=ec_option.home'>View All</a>");
		}
		else
		{
			$this->ObTpl->set_var("TPL_VAR_SEARCH","");
			$this->ObTpl->set_var("TPL_VAR_VIEWALL","");
		}
		$query=$query." order by vAttributeTitle";
		$this->obDb->query=$query;
		$resOption=$this->obDb->fetchQuery();
		$varCount=$this->obDb->record_count;
		$this->ObTpl->set_var("TPL_TOTAL_RECORDS",$varCount);
		
		if($varCount>0)
		{
			for($i=0;$i<$varCount;$i++)
			{
				$this->ObTpl->set_var("TPL_OPTION_ID",$resOption[$i]->iAttributesid_PK);
				$this->ObTpl->set_var("TPL_VAR_TITLE",$resOption[$i]->vAttributeTitle);
				$this->ObTpl->set_var("TPL_VAR_NO_FIELDS",$resOption[$i]->iFieldnumber);
				$this->ObTpl->set_var("TPL_SHOPURL",SITE_URL);
				$this->ObTpl->parse("dspstdoption_blk","TPL_STDOPTIONS_BLK",true);	
			}
		$this->ObTpl->parse("mainattributes_blk","TPL_MAINATTRIBUTES_BLK");
		
		}
		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);
	
		return($this->ObTpl->parse("return","attributes"));
	}	
	
	function m_showStandardOpt()
	{
		$this->ObTpl=new template();
		$this->ObTpl->set_file("optionStandard", $this->optionsTemplate);
		#INTIALIZING TEMPLATE BLOCKS
		$this->ObTpl->set_block("optionStandard","TPL_MAINOPTION_BLK","mainoption_blk");		
		$this->ObTpl->set_block("TPL_MAINOPTION_BLK","TPL_STDOPTIONS_BLK","dspstdoption_blk");
		
		$this->ObTpl->set_var("dspstdoption_blk","");
		$this->ObTpl->set_var("TPL_SHOPURL",SITE_URL."ecom/");
        $this->ObTpl->set_var("TPL_VAR_SITEURL", SITE_URL);
		
		//defining language variables
		$this->ObTpl->set_var("LANG_VAR_STANDARDOPTIONTXT",LANG_STANDARDOPTIONS);
		$this->ObTpl->set_var("LANG_VAR_ADDNEWOPTTXT",LANG_ADDNEWOPTION);
		$this->ObTpl->set_var("LANG_VAR_SEARCH",LANG_SEARCH);
		$this->ObTpl->set_var("LANG_VAR_OPTIONTITLE",LANG_OPTIONTITLE);
		$this->ObTpl->set_var("LANG_VAR_DESCRIPTION",LANG_DESCRIPTION);
		$this->ObTpl->set_var("LANG_VAR_EDIT",LANG_EDIT);
		$this->ObTpl->set_var("LANG_VAR_DELETE",LANG_DELETE);
		$this->ObTpl->set_var("LANG_VAR_RECORDSFOUND",LANG_RECORDSFOUND);
		$this->ObTpl->set_var("LANG_VAR_CUSTOMOPTIONS",LANG_CUSTOMOPTIONS);
		$this->ObTpl->set_var("LANG_VAR_NAME",LANG_NAME);
		$this->ObTpl->set_var("LANG_VAR_FORMTYPE",LANG_FORMTYPE);
		$this->ObTpl->set_var("LANG_VAR_IMAGE",LANG_IMAGE);
		$this->ObTpl->set_var("LANG_VAR_USINGSTOCK",LANG_USINGSTOCK);
		$this->ObTpl->set_var("LANG_VAR_ADDITIONALPRICE",LANG_ADDTLPRICE);
		$this->ObTpl->set_var("mainoption_blk","");
		
		
		#QUERY TO GET DEPARTMENTS UNDER SELECTED DEPARTMENT
		$query= "SELECT iOptionid_PK,vName ,vDescription,iState  FROM ".OPTIONS  ;
		if(isset($this->request['search']) && !empty($this->request['search']))
		{
			$query=$query." WHERE vName LIKE '%".$this->request['search']."%' OR vDescription LIKE '%".$this->request['search']."%'";
			$this->ObTpl->set_var("TPL_VAR_SEARCH",$this->request['search']);
			$this->ObTpl->set_var("TPL_VAR_VIEWALL","<a href='".SITE_URL."ecom/adminindex.php?action=ec_option.home'>View All</a>");
		}
		else
		{
			$this->ObTpl->set_var("TPL_VAR_SEARCH","");
			$this->ObTpl->set_var("TPL_VAR_VIEWALL","");
		}
		
		$query=$query." order by vName";
		$this->obDb->query=$query;
		
		$resOption=$this->obDb->fetchQuery();
		$varCount=$this->obDb->record_count;
		
		$this->ObTpl->set_var("TPL_TOTAL_RECORDS",$varCount);
		if($varCount>0)
		{
			for($i=0;$i<$varCount;$i++)
			{
				if($resOption[$i]->iState==1)
				{
					$this->ObTpl->set_var("TPL_VAR_CHECKED","checked=\"checked\"");
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_CHECKED","");
				}	
				$this->ObTpl->set_var("TPL_OPTION_ID",$resOption[$i]->iOptionid_PK);
				$this->ObTpl->set_var("TPL_VAR_TITLE",$resOption[$i]->vName);
				$this->ObTpl->set_var("TPL_VAR_DESC",$resOption[$i]->vDescription);
				$this->ObTpl->set_var("TPL_VAR_MESSAGE","");
				$this->ObTpl->parse("dspstdoption_blk","TPL_STDOPTIONS_BLK",true);	
			}
		$this->ObTpl->parse("mainoption_blk","TPL_MAINOPTION_BLK");
		}
		
		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);
		return($this->ObTpl->parse("return","optionStandard"));
	}	
	
	#FUNCTION TO DISPLAY OPTIONS
	function m_showOptions()
	{
		$this->ObTpl=new template();
		$this->ObTpl->set_file("optionHome", $this->optionsTemplate);				
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		
		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	
		return($this->ObTpl->parse("return","optionHome"));
	}
	
	#FUNCTION TO DISPLAY NUMBER OPTION FORM
	function m_formNumOptions()
	{
		#INTIALIZING TEMPLATES
		$this->ObTpl=new template();
		$this->ObTpl->set_file("numform",$this->optionNumTemplate);
		$this->ObTpl->set_var("GRAPHICSMAINPATH", GRAPHICS_PATH);
		$this->ObTpl->set_var("TPL_SHOPURL",SITE_URL."ecom/");
        $this->ObTpl->set_var("TPL_VAR_SITEURL", SITE_URL);
		
		//defining language variables
		$this->ObTpl->set_var("LANG_VAR_PRODUCTOPTIONS",LANG_PRODUCTOPTIONS);
		$this->ObTpl->set_var("LANG_VAR_ENTERNUM",LANG_ENTERNUMBER);
		$this->ObTpl->set_var("LANG_VAR_BUILDOPT",LANG_BUILDOPTION);

		#ASSIGNING FORM ACTION						
		$this->ObTpl->set_var("TPL_VAR_FORMURL", SITE_URL."ecom/adminindex.php?action=ec_option.stdOptForm");

		return($this->ObTpl->parse("return","numform"));
	}

	
	function m_showOptionForm()
	{
		$libFunc=new c_libFunctions();
		#INTIALIZING TEMPLATES
		$this->ObTpl=new template();
		$this->ObTpl->set_file("optionform", $this->optionTemplate);
		$this->ObTpl->set_block("optionform","TPL_OPTIONS_BLK","dspoptions_blk");
		$this->ObTpl->set_var("GRAPHICSMAINPATH", GRAPHICS_PATH);

		//defining language variables
		$this->ObTpl->set_var("LANG_VAR_STANDOPTTXT",LANG_STANDOPTBUILD);
		$this->ObTpl->set_var("LANG_VAR_DESCRIPTION",LANG_DESCRIPTION);
		$this->ObTpl->set_var("LANG_VAR_TITLE",LANG_TITLE);
		$this->ObTpl->set_var("LANG_VAR_ID",LANG_ID);
		$this->ObTpl->set_var("LANG_VAR_OPTIONNAME",LANG_OPTNAME);
		$this->ObTpl->set_var("LANG_VAR_ADDEDCOST",LANG_ADDEDCOST);
		$this->ObTpl->set_var("LANG_VAR_STOCK",LANG_STOCK);
		$this->ObTpl->set_var("LANG_VAR_STOCKLVL",LANG_STOCKLVL);
		$this->ObTpl->set_var("LANG_VAR_BACKORDER",LANG_BACKORDER);
		$this->ObTpl->set_var("LANG_VAR_CREATEOPTION",LANG_CREATEOPT);
		$this->ObTpl->set_var("LANG_VAR_MANDATORY",LANG_MANDATORY);
		$this->ObTpl->set_var("TPL_SHOPURL",SITE_URL."ecom/");
        $this->ObTpl->set_var("TPL_VAR_SITEURL", SITE_URL);
		/*CHECKING FOR POST VARIABLES
		IF VARIABLES ARE SET THEN ASSIGNING THEIR VALUE TO VARIABLE SAMEVARIABLE
		AS USED WHEN RETURNED FROM DATABASE
		THIS IS USED TO REMOVE REDUNDANCY AND USE SAME FORM FOR EDIT AND INSERT*/
		$this->request['option_count']=intval($this->request['option_count']);
		
		if(!isset($this->request['option_count']) || $this->request['option_count']<1)
		{
			$this->request['option_count']=1;
		}

		if(count($_POST) > 0)
		{
			if(isset($this->request["description"]))
				$row_option[0]->vDescription= $this->request["description"];
			if(isset($this->request["name"]))
				$row_option[0]->vName = $this->request["name"];
		}
		else
		{
			$row_option[0]->vDescription= "";
			$row_option[0]->vName = "";	
		
		}		
		
			$this->ObTpl->set_var("TPL_VAR_OPTIONID","");
			$this->ObTpl->set_var("TPL_VAR_MODE","Add");
			$this->ObTpl->set_var("TPL_VAR_SELECTED","checked");
			if(!isset($this->request['msg']))
			{
				$this->ObTpl->set_var("TPL_VAR_MSG","");
			}
			elseif($this->request['msg']==1)
			{
				$this->ObTpl->set_var("TPL_VAR_MSG",MSG_HEADER_EXIST);
			}		
			$this->ObTpl->set_var("TPL_VAR_BTNMESSAGE",LBL_ADD_MENU);

			$this->ObTpl->set_var("TPL_VAR_OPTIONCOUNT",$this->request['option_count']);
			if(isset($this->request['option_count']) && !empty($this->request['option_count']))
			{
				for($i=1;$i<=$this->request['option_count'];$i++)
				{
					$this->ObTpl->set_var("TPL_VAR_ID",$i);
					$this->ObTpl->parse("dspoptions_blk","TPL_OPTIONS_BLK",true);
				}
			}
			else
			{
				$this->ObTpl->set_var("dspoptions_blk","");
			}
		#ASSIGNING FORM ACTION						
		$this->ObTpl->set_var("TPL_VAR_FORMURL", SITE_URL."ecom/adminindex.php?action=ec_option.optionadd");

		#ASSIGNING FORM VARAIABLES
		return($this->ObTpl->parse("return","optionform"));
	}
	


	#FUNCTION TO VERIFY DATABASE UPDATION
	function m_verifyEditOption()
	{
		#VALIDATING EXISTING OPTION TITLE
		$this->obDb->query = "select iOptionid_PK  from ".OPTIONS." where vName  = '".$this->request['optname']."'";
		$row_code = $this->obDb->fetchQuery();
		if($this->obDb->record_count != 0)
		{
			if($row_code[0]->iOptionid_PK !=$this->request['optionid'])
			{
				return false;
			}
		}
		return true;
	}

	#FUNCTION TO VERIFY DATABASE UPDATION
	function m_verifyInsertOption()
	{
		$this->obDb->query = "select iOptionid_PK  from ".OPTIONS." where vName  = '".$this->request['name1']."'";
		$row_code = $this->obDb->fetchQuery();
		if($this->obDb->record_count != 0)
		{
			return false;
		}
		
		return true;
	}
	#FUNCTION TO VALIDATE IMAGE UPLOADED  FROM UPLOAD FORM
	function m_verifyImageUpload(){
		if(!$this->libFunc->m_validateUpload($this->request['image'])){
			$this->errMsg.=MSG_VALID_IMAGE."<br />";
			$this->err=1;
		}
		return $this->err;
	}
	function m_formEditOption()
	{
		$libFunc=new c_libFunctions();
		#INTIALIZING TEMPLATES
		$this->ObTpl=new template();
		$this->ObTpl->set_file("optionform", $this->optionTemplate);
		$this->ObTpl->set_block("optionform","TPL_OPTIONS_BLK","dspoptions_blk");
		$this->ObTpl->set_block("optionform","TPL_NEW_BLK","dspnewoption_blk");
		$this->ObTpl->set_block("optionform","TPL_LINK_BLK","dsplink_blk");
		
		//defining language variables
		$this->ObTpl->set_var("LANG_VAR_STANDOPTTXT",LANG_STANDOPTBUILD);
		$this->ObTpl->set_var("LANG_VAR_DESCRIPTION",LANG_DESCRIPTION);
		$this->ObTpl->set_var("LANG_VAR_TITLE",LANG_TITLE);
		$this->ObTpl->set_var("LANG_VAR_ID",LANG_ID);
		$this->ObTpl->set_var("LANG_VAR_OPTIONNAME",LANG_OPTNAME);
		$this->ObTpl->set_var("LANG_VAR_ADDEDCOST",LANG_ADDEDCOST);
		$this->ObTpl->set_var("LANG_VAR_STOCK",LANG_STOCK);
		$this->ObTpl->set_var("LANG_VAR_STOCKLVL",LANG_STOCKLVL);
		$this->ObTpl->set_var("LANG_VAR_BACKORDER",LANG_BACKORDER);
		$this->ObTpl->set_var("LANG_VAR_CREATEOPTION",LANG_CREATEOPT);
		$this->ObTpl->set_var("LANG_VAR_DELETE",LANG_DELETE);
		$this->ObTpl->set_var("LANG_VAR_IMAGE",LANG_IMAGE);
		$this->ObTpl->set_var("LANG_VAR_SORT",LANG_SORT);
		$this->ObTpl->set_var("LANG_VAR_ADDANOTHERROW",LANG_ADDANOTHERROW);
		$this->ObTpl->set_var("LANG_VAR_RETURNOPTBUILD",LANG_RETURNOPTBUILD);
		$this->ObTpl->set_var("LANG_VAR_RECORDSFOUND",LANG_RECORDSFOUND);
		$this->ObTpl->set_var("LANG_VAR_UPDATEOPT",LANG_UPDATEOPTION);
		$this->ObTpl->set_var("LANG_VAR_MANDATORY",LANG_MANDATORY);
        $this->ObTpl->set_var("TPL_VAR_SITEURL", SITE_URL);
        
		if(!isset($this->request['optionid']))
		{
			$this->request['optionid']="";
		}
		if(isset($this->request['addrow']) && $this->request['addrow']=="yes")
		{
			$this->obDb->query="SELECT max(iSort) as maxsort FROM  ".OPTIONVALUES." WHERE iOptionid_FK='".$this->request['optionid']."'";
			$rsSort=$this->obDb->fetchQuery();
			$sort=$rsSort[0]->maxsort+1;
			$this->ObTpl->set_var("TPL_VAR_SORTNUMNEW","$sort");
			$this->ObTpl->parse("dspnewoption_blk","TPL_NEW_BLK");
			$this->ObTpl->set_var("dsplink_blk","");
		}
		else
		{
			$this->ObTpl->parse("dsplink_blk","TPL_LINK_BLK");
			$this->ObTpl->set_var("dspnewoption_blk","");
		}
		$this->ObTpl->set_var("GRAPHICSMAINPATH", GRAPHICS_PATH);

		$this->ObTpl->set_var("TPL_SHOPURL",SITE_URL."ecom/");
		/*CHECKING FOR POST VARIABLES
		IF VARIABLES ARE SET THEN ASSIGNING THEIR VALUE TO VARIABLE SAMEVARIABLE
		AS USED WHEN RETURNED FROM DATABASE
		THIS THING IS USED TO REMOVE REDUNDANCY AND USE SAME FORM FOR EDIT AND INSERT*/

		if(count($_POST) > 0)
		{
			if(isset($this->request["description"]))
				$row_option[0]->vDescription= $this->request["description"];
			if(isset($this->request["name"]))
				$row_option[0]->vName = $this->request["name"];
		}
		else
		{
			$row_option[0]->vDescription= "";
			$row_option[0]->vName = "";	
		
		}		

		$this->obDb->query = "SELECT vName,vDescription,tmEditDate,iState from ".OPTIONS." where iOptionid_PK='".$this->request['optionid']."'";
		$rsOpt = $this->obDb->fetchQuery();

		$this->ObTpl->set_var("TPL_VAR_OPTNAME",$this->libFunc->m_displayContent($rsOpt[0]->vName));
		$this->ObTpl->set_var("TPL_VAR_OPTDESC",$this->libFunc->m_displayContent($rsOpt[0]->vDescription));

		if($rsOpt[0]->iState == "1"){
			$this->ObTpl->set_var("TPL_VAR_MANDATORY","checked");	
		}else{
			$this->ObTpl->set_var("TPL_VAR_MANDATORY","");
		}

		$this->obDb->query = "SELECT * FROM ".OPTIONVALUES." where iOptionid_FK='".$this->request['optionid']."' ORDER BY iSort";
		$rsOptValue = $this->obDb->fetchQuery();

		$this->ObTpl->set_var("TPL_VAR_OPTIONID",$this->request['optionid']);

		if(!isset($this->request['msg']))
		{
			$this->request["tmFormatEditDate"]=$libFunc->dateFormat($rsOpt[0]->tmEditDate);
			$this->ObTpl->set_var("TPL_VAR_MSG",LBL_LAST_UPDATE.$this->request['tmFormatEditDate']);
		}
		elseif($this->request['msg']==1)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",MSG_HEADER_EXIST);
		}		
		$rsOptValueCount=$this->obDb->record_count;
		$this->ObTpl->set_var("TPL_VAR_OPTIONCOUNT",$rsOptValueCount);
		if(isset($rsOptValueCount) && !empty($rsOptValueCount))
		{
			for($i=0;$i<$rsOptValueCount;$i++)
			{
				$this->ObTpl->set_var("TPL_VAR_ID",$rsOptValue[$i]->iOptionValueid_PK );
				$this->ObTpl->set_var("TPL_VAR_NAME",$this->libFunc->m_displayContent($rsOptValue[$i]->vItem));
				$this->ObTpl->set_var("TPL_VAR_PRICE",$rsOptValue[$i]->fPrice);
				$this->ObTpl->set_var("TPL_VAR_SORTNUM",$rsOptValue[$i]->iSort);
				$this->ObTpl->set_var("TPL_VAR_OPTSKU",$rsOptValue[$i]->vOptSku);

				$this->ObTpl->set_var("TPL_VAR_INVENTORY",$rsOptValue[$i]->iInventory);
				if($rsOptValue[$i]->iUseInventory==1)
				{
					$this->ObTpl->set_var("TPL_VAR_SELECTED1","checked");
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_SELECTED1","");
				}
				if($rsOptValue[$i]->iBackorder==1)
				{
					$this->ObTpl->set_var("TPL_VAR_SELECTED2","checked");
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_SELECTED2","");
				}
				if(file_exists($this->imagePath."options/".$rsOptValue[$i]->vImage) && $rsOptValue[$i]->vImage!="")
				{
					$this->ObTpl->set_var("TPL_VAR_IMGLBL","Edit");
				}
				else
				{
						$this->ObTpl->set_var("TPL_VAR_IMGLBL","Add");
				}
				$this->ObTpl->parse("dspoptions_blk","TPL_OPTIONS_BLK",true);
			}
		}
		else
		{
			
			$this->ObTpl->set_var("dspoptions_blk","");
		}

		#ASSIGNING FORM ACTION						
		$this->ObTpl->set_var("TPL_VAR_FORMURL", SITE_URL."ecom/adminindex.php?action=ec_option.optionedit");
		return($this->ObTpl->parse("return","optionform"));
	}


	#FUNCTION USED FOR UPLOADING IMAGES/FILES DURING EDIT PROCESS
	function m_uploadForm()
	{
		$obFile			=new FileUpload();
		$this->ObTpl	=new template();

		$this->ObTpl->set_file("Editor",$this->uploadTemplate);
		$this->ObTpl->set_block("Editor","TPL_IMAGE_BLK", "image_blk");
		$this->ObTpl->set_var("TPL_VAR_DELETELINK","");
		if(!isset($this->request['type']))
		{
			$this->request['type']="option";
		}

		if($this->request['type']=="choice")
		{
			$this->obDb->query = "select iChoiceid_PK,vImage from ".CHOICES." where iChoiceid_PK = ".$this->request['id'];
			$rsImage = $this->obDb->fetchQuery();
		}
		else
		{
			$this->obDb->query = "select iOptionValueid_PK,vItem,vImage from ".OPTIONVALUES." where iOptionValueid_PK = ".$this->request['id'];
			$rsImage = $this->obDb->fetchQuery();
		}

		if($this->libFunc->m_checkFileExist($rsImage[0]->vImage,"options") && $rsImage[0]->vImage!="")
		{
				$this->ObTpl->set_var("TPL_VAR_IMAGE",
				"<img src=".$this->imageUrl."options/".$rsImage[0]->vImage." alt='".$rsImage[0]->vImage."' width=100 height=100>");		
				$this->ObTpl->set_var("TPL_VAR_DELETELINK",	"<a href=".SITE_URL."ecom/adminindex.php?action=ec_option.uploadForm&id=".$this->request['id']."&type=".$this->request['type']."&delete=1>Delete</a>");		

				if(isset($this->request['delete']) && $this->request['delete']==1)
				{
					$source=$this->imagePath."options/".$rsImage[0]->vImage;
					$obFile->deleteFile($source);
					$this->ObTpl->set_var("TPL_VAR_IMAGE",$this->libFunc->m_noImage());
					$this->ObTpl->set_var("TPL_VAR_DELETELINK","");
					$this->request['msg']=1;
				}
		}
		else
		{
				$this->ObTpl->set_var("TPL_VAR_IMAGE",$this->libFunc->m_noImage());
		}
		
		$this->ObTpl->parse("image_blk","TPL_IMAGE_BLK");	
		$imgLabel="image";

		if(isset($this->request['status']))
		{
			$this->ObTpl->set_var("TPL_VAR_TOPMSG","".ucfirst($imgLabel)." has been 	Updated");			
		}
		else
		{
			$this->ObTpl->set_var("TPL_VAR_TOPMSG","");			
		}

		if(isset($this->request['msg'])) 
		{
			if($this->request['msg']==1)
			{
				$this->ObTpl->set_var("TPL_VAR_TOPMSG","<span class='adminDetail'>".MSG_IMGDELETE_SUCCESS."</span>");
			}
			elseif($this->request['msg']==2)
			{
				$this->ObTpl->set_var("TPL_VAR_TOPMSG","<span class='adminDetail'>".MSG_IMGDELETE_SUCCESS."</span>");
			}
			else
			{
				$this->ObTpl->set_var("TPL_VAR_TOPMSG","");
			}
		}elseif($this->err==1){
			$this->ObTpl->set_var("TPL_VAR_TOPMSG",$this->errMsg);
		}
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		$this->ObTpl->set_var("TPL_VAR_ID",$this->request['id']);			
		$this->ObTpl->set_var("FORMURL",SITE_URL."ecom/adminindex.php?action=ec_option.upload&id=".$this->request['id']."&type=".$this->request['type']);
		
		$this->ObTpl->pparse("return","Editor");
		exit;
	}

	#TO DISPLAY CUSTOM OPTIONS
	function m_customOptionForm()
	{
		$libFunc=new c_libFunctions();
		#INTIALIZING TEMPLATES
		$this->ObTpl=new template();
		$this->ObTpl->set_file("optionform", $this->optionTemplate);
		$this->ObTpl->set_block("optionform","IMG_BLK","dspimg_blk");
		$this->ObTpl->set_block("optionform","LINK_BLK","dsplink_blk");
		$this->ObTpl->set_var("GRAPHICSMAINPATH", GRAPHICS_PATH);

		$this->ObTpl->set_var("TPL_VAR_SITEURL", SITE_URL);
		$this->ObTpl->set_var("TPL_SHOPURL",SITE_URL."ecom/");
		$this->ObTpl->set_var("TPL_VAR_SEL1","");
		$this->ObTpl->set_var("TPL_VAR_SEL2","");
		$this->ObTpl->set_var("TPL_VAR_SEL3","");
		$this->ObTpl->set_var("TPL_VAR_SEL4","");
		
		//defining language variables
		$this->ObTpl->set_var("LANG_VAR_CUSTOMOPTTXT",LANG_CUSTOMOPTBUILD);
		$this->ObTpl->set_var("LANG_VAR_FIELDNAME",LANG_FIELDNAME);
		$this->ObTpl->set_var("LANG_VAR_DESCRIPTION",LANG_DESCRIPTION);
		$this->ObTpl->set_var("LANG_VAR_FIELDTYPE",LANG_TYPEOFTXT);
		$this->ObTpl->set_var("LANG_VAR_QUANTITYBOX",LANG_QUANTITYBOX);
		$this->ObTpl->set_var("LANG_VAR_INPUTBOX",LANG_INPUTBOX);
		$this->ObTpl->set_var("LANG_VAR_CHECKBOX",LANG_CHECKBOX);
		$this->ObTpl->set_var("LANG_VAR_TEXTAREA",LANG_TEXTAREA);
		$this->ObTpl->set_var("LANG_VAR_USESTOCK",LANG_USINGSTOCK);
		$this->ObTpl->set_var("LANG_VAR_ALLOWBACK",LANG_ALLOWBACKORDERS);
		$this->ObTpl->set_var("LANG_VAR_STOCKLVL",LANG_STOCKLVL);
		$this->ObTpl->set_var("LANG_VAR_ADDTLPRICE",LANG_ADDTLPRICE);
		$this->ObTpl->set_var("LANG_VAR_ONOFF",LANG_ONOFF);
		$this->ObTpl->set_var("LANG_VAR_OPTIMAGE",LANG_OPTIMAGE);
		/*CHECKING FOR POST VARIABLES
		IF VARIABLES ARE SET THEN ASSIGNING THEIR VALUE TO VARIABLE SAMEVARIABLE
		AS USED WHEN RETURNED FROM DATABASE
		THIS THING IS USED TO REMOVE REDUNDANCY AND USE SAME FORM FOR EDIT AND INSERT*/

		if(count($_POST) > 0)
		{
			if(isset($this->request["cname"]))
				$row_option[0]->vName= $this->request["cname"];
			if(isset($this->request["description"]))
				$row_option[0]->vDescription=$this->request["description"];
			if(isset($this->request["type"]))
				$row_option[0]->vType =$this->request["type"];
		
			if(isset($this->request["use_inventory"]))
				$row_option[0]->iUseInventory=$this->request["use_inventory"];

			if(isset($this->request["backorder"]))
				$row_option[0]->iBackorder=$this->request["backorder"];

			if(isset($this->request["inventory"]))
				$row_option[0]->iInventory=$this->request["inventory"];
			if(isset($this->request["price"]))
				$row_option[0]->fPrice=$this->request["price"];
			if(isset($this->request["state"]))
				$row_option[0]->iState =$this->request["state"];


			$row_option[0]->vImage = "";	

		}
		else
		{
			$row_option[0]->vName = "";
			$row_option[0]->vDescription="";
			$row_option[0]->vType="";
			$row_option[0]->iUseInventory="";
			$row_option[0]->iBackorder="";
			$row_option[0]->iInventory="";
			$row_option[0]->fPrice="";
			$row_option[0]->vImage = "";	
			$row_option[0]->iState="";
		}		
		#IF EDIT MODE SELECTED

		if(isset($this->request['choiceid']) && !empty($this->request['choiceid']))
		{
			$this->obDb->query = $this->obDb->query = "SELECT * FROM ".CHOICES." WHERE iChoiceid_PK ='".$this->request['choiceid']."'";
			$row_option=$this->obDb->fetchQuery();
			if(!isset($this->request['msg']) || empty($this->request['msg']))
			{
				$this->request["tmFormatEditDate"]=$libFunc->dateFormat($row_option[0]->tmEditDate);
				$this->ObTpl->set_var("TPL_VAR_MSG",LBL_LAST_UPDATE.$this->request['tmFormatEditDate']);
			}

			if($this->err==1)
			{
				$this->ObTpl->set_var("TPL_VAR_MSG",$this->errMsg);						
			}

			if($row_option[0]->vType=="input")
			{
				$this->ObTpl->set_var("TPL_VAR_SEL2","selected");
			}
			elseif($row_option[0]->vType=="checkbox")
			{
				$this->ObTpl->set_var("TPL_VAR_SEL3","selected");
			}
			elseif($row_option[0]->vType=="textarea")
			{
				$this->ObTpl->set_var("TPL_VAR_SEL4","selected");
			}
			else
			{
				$this->ObTpl->set_var("TPL_VAR_SEL1","selected");
			}
			if($row_option[0]->iUseInventory==1)
				{
					$this->ObTpl->set_var("TPL_VAR_SELECTED1","checked");
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_SELECTED1","");
				}	
			if($row_option[0]->iBackorder==1)
				{
					$this->ObTpl->set_var("TPL_VAR_SELECTED2","checked");
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_SELECTED2","");
				}	
			if($row_option[0]->iState==1)
				{
					$this->ObTpl->set_var("TPL_VAR_SELECTED3","checked");
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_SELECTED3","");
				}	
			$this->ObTpl->set_var("TPL_VAR_MODE","edit");

			$this->ObTpl->set_var("TPL_VAR_CHOICEID",$this->request['choiceid']);
			#HANDLING BLOCKS		
											
			$this->ObTpl->set_var("TPL_VAR_LBLBUTTON",LBL_EDIT_CHOICE);
			if(file_exists($this->imagePath."options/".$row_option[0]->vImage) && $row_option[0]->vImage!="")
			{
				$this->ObTpl->set_var("TPL_VAR_IMGLBL","Edit Image");
			}
			else
			{
					$this->ObTpl->set_var("TPL_VAR_IMGLBL","Add Image");
			}
			$this->ObTpl->set_var("dspimg_blk","");
			$this->ObTpl->parse("dsplink_blk","LINK_BLK");

		}	
		else #IF ADD
		{
			$this->ObTpl->set_var("TPL_VAR_CHOICEID","");
			$this->ObTpl->set_var("TPL_VAR_MODE","Add");
			$this->ObTpl->set_var("TPL_VAR_SELECTED1","");
			$this->ObTpl->set_var("TPL_VAR_SELECTED2","");
			$this->ObTpl->set_var("TPL_VAR_SELECTED3","checked");
			$this->ObTpl->set_var("TPL_VAR_SEL1","selected");
			$this->obDb->query = $this->obDb->query = "SELECT count(*) as totalCnt FROM ".CHOICES;
			$row_choice1=$this->obDb->fetchQuery();
			if(!isset($this->request['msg']))
			{
				$this->ObTpl->set_var("TPL_VAR_MSG","Total Records ".$row_choice1[0]->totalCnt);
			}
			elseif($this->err==1)
			{
				$this->ObTpl->set_var("TPL_VAR_MSG",$this->errMsg);						
			}	
			$this->ObTpl->set_var("TPL_VAR_LBLBUTTON",LBL_ADD_CHOICE);
			$this->ObTpl->set_var("dsplink_blk","");
			$this->ObTpl->parse("dspimg_blk","IMG_BLK");
		}	


		#ASSIGNING FORM ACTION						
		$this->ObTpl->set_var("TPL_VAR_FORMURL", SITE_URL."ecom/adminindex.php?action=ec_option.choiceadd");

		#ASSIGNING FORM VARAIABLES

		$this->ObTpl->set_var("TPL_VAR_TITLE", $libFunc->m_displayContent($row_option[0]->vName));
		$this->ObTpl->set_var("TPL_VAR_DESC", $libFunc->m_displayContent($row_option[0]->vDescription));
		$this->ObTpl->set_var("TPL_VAR_PRICE", $libFunc->m_displayContent($row_option[0]->fPrice));
		
		$this->ObTpl->set_var("TPL_VAR_INVENTORY", $libFunc->m_displayContent($row_option[0]->iInventory ));
		return($this->ObTpl->parse("return","optionform"));
	}


		#FUNCTION TO VERIFY DATABASE UPDATION
	function m_verifyEditChoice()
	{
		$this->errMsg="";
		if(empty($this->request['cname']))
		{
			$this->errMsg=MSG_CHOICENAME_EMPTY."<br />";
			$this->err=1;
		}
		$this->request['image']=$this->libFunc->ifSet($this->request,"image","");
		if(!$this->libFunc->m_validateUpload($this->request['image'])){
			$this->errMsg.=MSG_VALID_IMAGE."<br />";
			$this->err=1;
		}
		#VALIDATING EXISTING OPTION TITLE
		$this->obDb->query = "select iChoiceid_PK from ".CHOICES." where vName  = '".$this->request['cname']."'";
		$row_code = $this->obDb->fetchQuery();
		if($this->obDb->record_count != 0)
		{
			if($row_code[0]->iChoiceid_PK !=$this->request['choiceid'])
			{
				$this->errMsg.=MSG_TITLE_EXIST;
				$this->err=1;
			}
		}

		return $this->err;
	}

	#FUNCTION TO VERIFY DATABASE UPDATION
	function m_verifyInsertChoice()
	{
		$this->errMsg="";
		if(empty($this->request['cname']))
		{
			$this->errMsg=MSG_CHOICENAME_EMPTY."<br />";
			$this->err=1;
		}
		if(!$this->libFunc->m_validateUpload($this->request['image'])){
			$this->errMsg.=MSG_VALID_IMAGE."<br />";
			$this->err=1;
		}
		$this->obDb->query = "SELECT iChoiceid_PK FROM ".CHOICES." WHERE vName  = '".$this->request['cname']."'";
		$row_code = $this->obDb->fetchQuery();
		if($this->obDb->record_count != 0)
		{
			$this->errMsg.=MSG_TITLE_EXIST;
			$this->err=1;
		}

		return $this->err;
	}

}#CLASS END
?>