<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.0.5
=======================================================================================
*/
class c_supplierInterface
{
	#CONSTRUCTOR
	function c_supplierInterface()
	{
		$this->libFunc=new c_libFunctions();
		$this->errMsg="";
		$this->err=0;
	}

	#FUNCTION TO DISPLAY SUPPLIERS LISTING
	function m_dspSuppliers()
	{
		#INTIALIZING TEMPLATES
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_SUPPLIER_FILE", $this->supplierTemplate);
		$this->ObTpl->set_block("TPL_SUPPLIER_FILE","TPL_MAINSUPPLIER_BLK", "mainsupplier_blk");
		$this->ObTpl->set_block("TPL_MAINSUPPLIER_BLK","TPL_SUPPLIER_BLK", "supplier_blk");
		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		
		#defining language pack variables.
		$this->ObTpl->set_var("LANG_VAR_SUPPLIERS",LANG_SUPPLIERS);
		$this->ObTpl->set_var("LANG_VAR_ADDSUPP",LANG_ADDSUPP);
		$this->ObTpl->set_var("LANG_VAR_RECORDSFOUND",LANG_RECORDSFOUND);
		$this->ObTpl->set_var("LANG_VAR_SEARCH",LANG_SEARCH);
		$this->ObTpl->set_var("LANG_VAR_COMPANY",LANG_COMPANY);
		$this->ObTpl->set_var("LANG_VAR_COUNTY",LANG_COUNTYSTATE);
		$this->ObTpl->set_var("LANG_VAR_TELEPHONE",LANG_TELEPHONE);
		$this->ObTpl->set_var("LANG_VAR_CONTACT",LANG_CONTACT);
		$this->ObTpl->set_var("LANG_VAR_EDIT",LANG_EDIT);
		$this->ObTpl->set_var("LANG_VAR_DELETE",LANG_DELETE);
		
		$this->ObTpl->set_var("mainsupplier_blk","");
		if(!isset($this->request['searchtype']))
		{
			$this->request['searchtype']="";
		}
		$query= "SELECT *  FROM ".SUPPLIERS;
		if(isset($this->request['search']) && !empty($this->request['search']))
		{
			$this->ObTpl->set_var("TPL_VAR_SEARCHTEXT",$this->request['search']);

				$query.=" WHERE (vCompany LIKE '%".$this->request['search']."%')";
		}
		else
		{
			$this->ObTpl->set_var("TPL_VAR_SEARCHTEXT","");
		}
		$query.=" ORDER BY vCompany";
		$this->obDb->query=$query;
		$row_customer = $this->obDb->fetchQuery();
		$recordCount=$this->obDb->record_count;
		$this->ObTpl->set_var("TPL_VAR_RECORDCOUNT",$recordCount);
		$this->ObTpl->set_var("TPL_VAR_STATE","");
	
	if ($recordCount>0)
	{
		for($i=0;$i<$recordCount;$i++)
		{
			$this->ObTpl->set_var("TPL_VAR_ID",$row_customer[$i]->iVendorid_PK);
			$this->ObTpl->set_var("TPL_VAR_PHONE",$this->libFunc->m_displayContent($row_customer[$i]->vPhone));
			$this->ObTpl->set_var("TPL_VAR_EMAIL",$row_customer[$i]->vEmail);
			$this->ObTpl->set_var("TPL_VAR_COMPANY",$this->libFunc->m_displayContent($row_customer[$i]->vCompany));
			$this->ObTpl->set_var("TPL_VAR_CONTACT",$this->libFunc->m_displayContent($row_customer[$i]->vEmail));

			if($row_customer[$i]->vState>1	 )
			{
				$this->obDb->query = "SELECT vStateName FROM ".STATES." where iStateId_PK  = '".$row_customer[$i]->vState."'";
				$row_state = $this->obDb->fetchQuery();
				if(!empty($row_state[0]->vStateName))
				{
					$this->ObTpl->set_var("TPL_VAR_STATE",
					$this->libFunc->m_displayContent($row_state[0]->vStateName));
				}
			}
			else
			{
				if(!empty($row_customer[$i]->vStateName))
				{
					$this->ObTpl->set_var("TPL_VAR_STATE",
					$this->libFunc->m_displayContent($row_customer[$i]->vStateName));
				}
			}
			$this->obDb->query = "SELECT vCountryName FROM ".COUNTRY." where iCountryId_PK  = '".$row_customer[$i]->vCountry."' order by vCountryName";
			$row_country = $this->obDb->fetchQuery();
			if(!empty($row_country[0]->vCountryName))
			{
				$this->ObTpl->set_var("TPL_VAR_COUNTRY",
				$this->libFunc->m_displayContent($row_country[0]->vCountryName));
			}

	
			$this->ObTpl->parse("supplier_blk","TPL_SUPPLIER_BLK",true);
		}
	$this->ObTpl->parse("mainsupplier_blk","TPL_MAINSUPPLIER_BLK");
	}
		
		return($this->ObTpl->parse("return","TPL_SUPPLIER_FILE"));
	}

	#FUNCTION TO DISPLAY SUPPLIER FORM
	function m_dspSupplierForm()
	{
		#INTIALIZING TEMPLATES
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_SUPPLIER_FILE", $this->supplierTemplate);
		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		$this->ObTpl->set_block("TPL_SUPPLIER_FILE","DSPMSG_BLK", "msg_blk");
		$this->ObTpl->set_block("TPL_SUPPLIER_FILE","countryblk","countryblks");
		$this->ObTpl->set_block("TPL_SUPPLIER_FILE","BillCountry","nBillCountry");
		$this->ObTpl->set_block("TPL_SUPPLIER_FILE","stateblk","stateblks");
		$this->ObTpl->set_var("TPL_USERURL",SITE_URL."user/");
		$this->ObTpl->set_block("TPL_SUPPLIER_FILE","TPL_IMAGEBOX_BLK", "imagebox_blk");
		$this->ObTpl->set_block("TPL_SUPPLIER_FILE","TPL_IMAGELINK_BLK", "imagelink_blk");		
		$this->ObTpl->set_block("TPL_SUPPLIER_FILE","TPL_MSG_BLK", "message_blk");		
		
		$this->ObTpl->set_var("imagelink_blk","");
		$this->ObTpl->set_var("imagebox_blk","");
		$this->ObTpl->set_var("msg_blk","");
		$this->ObTpl->set_var("message_blk","");
		$this->ObTpl->set_var("countryblks","");
		
		#defining language pack variables.
		
		$this->ObTpl->set_var("LANG_VAR_SUPPLIEREDIT",LANG_SUPPLIEREDITOR);
		$this->ObTpl->set_var("LANG_VAR_COMPANYNAME",LANG_COMPANY);
		$this->ObTpl->set_var("LANG_VAR_ADDRESS1",LANG_ADDRESS1);
		$this->ObTpl->set_var("LANG_VAR_ADDRESS2",LANG_ADDRESS2);
		$this->ObTpl->set_var("LANG_VAR_CITY",LANG_CITY);
		$this->ObTpl->set_var("LANG_VAR_COUNTRY",LANG_COUNTRY);
		$this->ObTpl->set_var("LANG_VAR_COUNTY",LANG_COUNTYSTATE);
		$this->ObTpl->set_var("LANG_VAR_COUNTYOTHER",LANG_COUNTYSTATEOTHER);
		$this->ObTpl->set_var("LANG_VAR_POSTCODE",LANG_POSTCODE);
		$this->ObTpl->set_var("LANG_VAR_TELEPHONE",LANG_TELEPHONE);
		$this->ObTpl->set_var("LANG_VAR_TELEPHONE2",LANG_TELEPHONE2);
		$this->ObTpl->set_var("LANG_VAR_CONTACTNAME",LANG_CONTACTNAME);
		$this->ObTpl->set_var("LANG_VAR_CONTACTEMAIL",LANG_CONTACTEMAIL);
		$this->ObTpl->set_var("LANG_VAR_WEBSITE",LANG_WEBSITE);
		$this->ObTpl->set_var("LANG_VAR_COMMENTS",LANG_COMMENTS);
		$this->ObTpl->set_var("LANG_VAR_SUPPLIERLOGO",LANG_SUPPLIERLOGO);
		$this->ObTpl->set_var("LANG_VAR_STATUS",LANG_STATUS);
		
		
		$this->ObTpl->set_var("LANG_VAR_EDIT",LANG_EDIT);
		$this->ObTpl->set_var("LANG_VAR_DELETE",LANG_DELETE);
		
		#INTIALIZING VARIABLES
		$row_customer[0]->vEmail  = "";
		$row_customer[0]->vPassword  = "";
		$row_customer[0]->vPhone = "";
		$row_customer[0]->vPhone2 = "";
		$row_customer[0]->vCompany = "";
		$row_customer[0]->vAddress1 = "";
		$row_customer[0]->vAddress2 = "";
		$row_customer[0]->vState ="";
		$row_customer[0]->vStateName="";
		$row_customer[0]->vCity = "";
		$row_customer[0]->vCountry = "";	
		$row_customer[0]->vZip = "";	
		$row_customer[0]->vWebsite   = "";	
		$row_customer[0]->vComments  = "";
		$row_customer[0]->vContact  = "";
		$row_customer[0]->iStatus = "1";
		
		
		/*CHECKING FOR POST VARIABLES
		IF VARIABLES ARE SET THEN ASSIGNING THEIR VALUE TO VARIABLE SAMEVARIABLE
		AS USED WHEN RETURNED FROM DATABASE
		THIS THING IS USED TO REMOVE REDUNDANCY AND USE SAME FORM FOR EDIT AND INSERT*/
		
		if(count($_POST) > 0)
		{
			if(isset($this->request["company"]))
				$row_customer[0]->vCompany  = $this->request["company"];
			if(isset($this->request["email"]))
				$row_customer[0]->vEmail  = $this->request["email"];
			if(isset($this->request["address1"]))
				$row_customer[0]->vAddress1  = $this->request["address1"];
			if(isset($this->request["address2"]))
				$row_customer[0]->vAddress2  = $this->request["address2"];
			if(isset($this->request["city"]))
				$row_customer[0]->vCity = $this->request["city"];
			if(isset($this->request["bill_state_id"]))
				$row_customer[0]->vState = $this->request["bill_state_id"];	
			if(isset($this->request["bill_state"]))
				$row_customer[0]->vStateName  = $this->request["bill_state"];	
			if(isset($this->request["zip"]))
				$row_customer[0]->vZip  = $this->request["zip"];	
			if(isset($this->request["bill_country_id"]))
				$row_customer[0]->vCountry  = $this->request["bill_country_id"];	
			if(isset($this->request["phone"]))
				$row_customer[0]->vPhone = $this->request["phone"];	
				if(isset($this->request["phone1"]))
				$row_customer[0]->vPhone2 = $this->request["phone1"];	
			if(isset($this->request["contact"]))
				$row_customer[0]->vContact   = $this->request["contact"];	
			if(isset($this->request["website"]))
				$row_customer[0]->vWebsite   = $this->request["website"];	
			if(isset($this->request["comments"]))
				$row_customer[0]->vComments  = $this->request["comments"];	
			if(isset($this->request["image"]))
				$row_customer[0]->vImage  = $this->request["image"];	
			if(isset($this->request["status"]))
				$row_customer[0]->iStatus = $this->request["status"];	
			else
				$row_customer[0]->iStatus = "";
		}

		#IF EDIT MODE SELECTED
		if(!empty($this->request['id']))
		{
			$this->obDb->query = "SELECT vImage, tmEditDate, tmBuildDate FROM ".SUPPLIERS." WHERE iVendorid_PK  ='".$this->request['id']."'";
			$rowSup=$this->obDb->fetchQuery();
			if($this->err==1)
			{
				$this->ObTpl->set_var("TPL_VAR_MSG",$this->errMsg);
				$this->ObTpl->parse("msg_blk","DSPMSG_BLK");						
			}
			else{
				$this->request['id']=intval($this->request['id']);
				$this->obDb->query = "SELECT * FROM ".SUPPLIERS." WHERE iVendorid_PK  ='".$this->request['id']."'";
				$row_customer=$this->obDb->fetchQuery();
				$this->ObTpl->set_var("msg_blk","");
			}

			$this->ObTpl->set_var("TPL_VAR_MODE","edit");
			$this->ObTpl->set_var("TPL_VAR_ID",$this->request['id']);

			if($this->libFunc->m_checkFileExist($rowSup[0]->vImage, "suppliers"))
			{
				$this->ObTpl->set_var("TPL_LBL_IMAGE",LBL_EDIT_IMAGE);
			}
			else
			{
				$this->ObTpl->set_var("TPL_LBL_IMAGE",LBL_ADD_IMAGE);
			}

			if($rowSup[0]->tmEditDate)
			{
				$this->ObTpl->set_var("TPL_VAR_BUILDDATE","Last updated on: 				".$this->libFunc->dateFormat($rowSup[0]->tmEditDate));
			}
			else
			{
				$this->ObTpl->set_var("TPL_VAR_BUILDDATE","Build date:  ".$this->libFunc->dateFormat($rowSup[0]->tmBuildDate));
			}

			$this->ObTpl->parse("message_blk","TPL_MSG_BLK");	
			#HANDLING BLOCKS		
			$this->ObTpl->parse("imagelink_blk","TPL_IMAGELINK_BLK");	
			$this->ObTpl->set_var("TPL_VAR_BTNLBL",LBL_EDITSUP_BTN);
		}	
		else #IF ADD
		{
			$this->ObTpl->set_var("TPL_VAR_MODE","Add");
			$this->ObTpl->set_var("TPL_VAR_ID","");
			if($this->err==1)
			{
				$this->ObTpl->set_var("TPL_VAR_MSG",$this->errMsg);
				$this->ObTpl->parse("msg_blk","DSPMSG_BLK");						
			}	
		
			$this->ObTpl->parse("imagebox_blk","TPL_IMAGEBOX_BLK");	
			$this->ObTpl->set_var("TPL_VAR_BTNLBL",LBL_ADDSUP_BTN);
		}	

				
		#ASSIGNING FORM ACTION						
		$this->ObTpl->set_var("FORM_URL", SITE_URL."user/adminindex.php?action=supplier.updateSupplier");
		
		$this->obDb->query = "SELECT iStateId_PK, vStateName FROM ".STATES." ORDER BY vStateName";
		$row_state = $this->obDb->fetchQuery();
		$row_state_count = $this->obDb->record_count;
		
		$this->obDb->query = "SELECT iCountryId_PK, vCountryName, vShortName FROM  ".COUNTRY." ORDER BY iSortFlag,vCountryName";
		$row_country = $this->obDb->fetchQuery();
		$row_country_count = $this->obDb->record_count;

		# Loading billing country list		
		for($i=0;$i<$row_country_count;$i++)
		{
			$this->ObTpl->set_var("k", $row_country[$i]->iCountryId_PK);
			$this->ObTpl->parse('countryblks','countryblk',true);
			$this->ObTpl->set_var("TPL_COUNTRY_VALUE", $row_country[$i]->iCountryId_PK);
			
			if($row_customer[0]->vCountry> 0)
			{
				if($row_customer[0]->vCountry == $row_country[$i]->iCountryId_PK)
					$this->ObTpl->set_var("BILL_COUNTRY_SELECT", "selected");
				else
					$this->ObTpl->set_var("BILL_COUNTRY_SELECT", "");
			}
			else
			{
					$row_customer[0]->vCountry = $row_country[$i]->iCountryId_PK;
					if($row_country[$i]->iCountryId_PK==251)
					{
						$this->ObTpl->set_var("BILL_COUNTRY_SELECT", "selected");
					}	
			}	

			$this->ObTpl->set_var("TPL_COUNTRY_NAME",$this->libFunc->m_displayContent($row_country[$i]->vCountryName));
			$this->ObTpl->parse("nBillCountry","BillCountry",true);
		}
		
			
		$this->ObTpl->set_var('selbillcountid',$row_customer[0]->vCountry);

		if($row_customer[0]->vState != '')
			$this->ObTpl->set_var('selbillstateid',$row_customer[0]->vState);
		else
			$this->ObTpl->set_var('selbillstateid',0);
		
			
		
		# Loading the state list here
		$this->obDb->query = "SELECT C.iCountryId_PK as cid,S.iStateId_PK as sid,S.vStateName as statename FROM ".COUNTRY." C,".STATES." S WHERE S.iCountryId_FK=C.iCountryId_PK ORDER BY C.vCountryName,S.vStateName";
		$cRes = $this->obDb->fetchQuery();
		$country_count = $this->obDb->record_count;

		if($country_count == 0)
		{
			$this->ObTpl->set_var("countryblks", "");
			$this->ObTpl->set_var("stateblks", "");
		}
		else
		{
		$loopid=0;
			for($i=0;$i<$country_count;$i++)
			{
				if($cRes[$i]->cid==$loopid)
				{
					$stateCnt++;
				}
				else
				{
					$loopid=$cRes[$i]->cid;
					$stateCnt=0;
				}
				$this->ObTpl->set_var("i", $cRes[$i]->cid);
				$this->ObTpl->set_var("j", $stateCnt);
				$this->ObTpl->set_var("stateName", $this->libFunc->m_displayContent($cRes[$i]->statename));
				$this->ObTpl->set_var("stateVal",$cRes[$i]->sid);
				$this->ObTpl->parse('stateblks','stateblk',true);
			}
		}

		#ASSIGNING FORM VARAIABLES

		$this->ObTpl->set_var("TPL_VAR_CONTACT", $this->libFunc->m_displayContent($row_customer[0]->vContact));
		$this->ObTpl->set_var("TPL_VAR_EMAIL", $this->libFunc->m_displayContent($row_customer[0]->vEmail));

		$this->ObTpl->set_var("TPL_VAR_ADDRESS1",$this->libFunc->m_displayContent($row_customer[0]->vAddress1 ));
		$this->ObTpl->set_var("TPL_VAR_ADDRESS2", $this->libFunc->m_displayContent($row_customer[0]->vAddress2 ));
		$this->ObTpl->set_var("TPL_VAR_CITY",$this->libFunc->m_displayContent($row_customer[0]->vCity));

		$this->ObTpl->set_var("TPL_VAR_STATE",
			$this->libFunc->m_displayContent($row_customer[0]->vState ));
		if($row_customer[0]->vState>1)
		{
			$this->ObTpl->set_var("BILL_STATE","");
		}
		else
		{
			$this->ObTpl->set_var("BILL_STATE",
			$this->libFunc->m_displayContent($row_customer[0]->vStateName));
		}
		$this->ObTpl->set_var("TPL_VAR_COUNTRY",
			$this->libFunc->m_displayContent($row_customer[0]->vCountry ));
		$this->ObTpl->set_var("TPL_VAR_ZIP",
			$this->libFunc->m_displayContent($row_customer[0]->vZip));
		$this->ObTpl->set_var("TPL_VAR_COMPANY",
			$this->libFunc->m_displayContent($row_customer[0]->vCompany));
		$this->ObTpl->set_var("TPL_VAR_PHONE",
			$this->libFunc->m_displayContent($row_customer[0]->vPhone));
		$this->ObTpl->set_var("TPL_VAR_PHONE1",
			$this->libFunc->m_displayContent($row_customer[0]->vPhone2));
		$this->ObTpl->set_var("TPL_VAR_HOMEPAGE",
			$this->libFunc->m_displayContent($row_customer[0]->vWebsite));
		$this->ObTpl->set_var("TPL_VAR_COMMENTS",
			$this->libFunc->m_displayContent($row_customer[0]->vComments));
		if($row_customer[0]->iStatus==1)
		{
			$this->ObTpl->set_var("TPL_VAR_STATUS","checked='checked'");
		}
		else
		{
			$this->ObTpl->set_var("TPL_VAR_STATUS","");					
		}	
		
		return($this->ObTpl->parse("return","TPL_SUPPLIER_FILE"));
	}

	#FUNCTION TO VERIFY DATABASE UPDATION
	function m_verifyEditSupplier()
	{
				#15-05-07
		if($this->libFunc->m_isNull($this->request['company'])){
			$this->errMsg.=MSG_COMPANY_EMPTY."<br />";
			$this->err=1;
		}
		if($this->libFunc->m_isNull($this->request['address1'])){
			$this->errMsg.=MSG_ADDRESS_EMPTY."<br />";
			$this->err=1;
		}
		if($this->libFunc->m_isNull($this->request['city'])){
			$this->errMsg.=MSG_CITY_EMPTY."<br />";
			$this->err=1;
		}
		if($this->libFunc->m_isNull($this->request['zip'])){
			$this->errMsg.=MSG_ZIP_EMPTY."<br />";
			$this->err=1;
		}
		if($this->libFunc->m_isNull($this->request['phone'])){
			$this->errMsg.=MSG_PHONE_EMPTY."<br />";
			$this->err=1;
		}

		#VALIDATING EXISTING OPTION TITLE
		$this->obDb->query = "SELECT iVendorid_PK FROM ".SUPPLIERS." where vCompany  = '".$this->request['company']."'";
		$row_code = $this->obDb->fetchQuery();
		if($this->obDb->record_count != 0)
		{
			if($row_code[0]->iVendorid_PK !=$this->request['id'])
			{
				$this->errMsg.=MSG_COMPANY_EXIST."<br />";
				$this->err=1;
			}
		}
		return $this->err;
	}

	#FUNCTION TO VERIFY DATABASE UPDATION
	function m_verifyInsertSupplier()
	{
		#15-05-07
		if($this->libFunc->m_isNull($this->request['company'])){
			$this->errMsg.=MSG_COMPANY_EMPTY."<br />";
			$this->err=1;
		}
		if($this->libFunc->m_isNull($this->request['address1'])){
			$this->errMsg.=MSG_ADDRESS_EMPTY."<br />";
			$this->err=1;
		}
		if($this->libFunc->m_isNull($this->request['city'])){
			$this->errMsg.=MSG_CITY_EMPTY."<br />";
			$this->err=1;
		}
		if($this->libFunc->m_isNull($this->request['zip'])){
			$this->errMsg.=MSG_ZIP_EMPTY."<br />";
			$this->err=1;
		}
		if($this->libFunc->m_isNull($this->request['phone'])){
			$this->errMsg.=MSG_PHONE_EMPTY."<br />";
			$this->err=1;
		}
		if(!$this->libFunc->m_validateUpload($this->request['image'])){
			$this->errMsg.=MSG_VALID_IMAGE."<br />";
			$this->err=1;
		}
		#VALIDATING EXISTING OPTION TITLE
		$this->obDb->query = "SELECT iVendorid_PK FROM ".SUPPLIERS." WHERE ";
		$this->obDb->query.="	vCompany  = '".$this->request['company']."'";
		$row_code = $this->obDb->fetchQuery();
		if($this->obDb->record_count != 0)
		{
			$this->errMsg.=MSG_COMPANY_EXIST."<br />";
			$this->err=1;
		}
		
		return $this->err;
	}

	#FUNCTION TO UPLOAD SUPPLIER IMAGE
	function m_uploadForm()
	{
		$obFile			=new FileUpload();
		$this->ObTpl	=new template();

		$this->ObTpl->set_file("Editor",$this->uploadTemplate);
		$this->ObTpl->set_block("Editor","TPL_IMAGE_BLK", "image_blk");
		$this->ObTpl->set_var("image_blk","");
		$this->ObTpl->set_var("TPL_VAR_DELETELINK","");

		$this->obDb->query = "select iVendorid_PK,vImage from ".SUPPLIERS." where iVendorid_PK = ".$this->request['id'];
		$rsImage = $this->obDb->fetchQuery();
	
		if($this->libFunc->m_checkFileExist($rsImage[0]->vImage,"suppliers") && $rsImage[0]->vImage!="")
		{
			$this->ObTpl->set_var("TPL_VAR_IMAGE","<img src=".$this->imageUrl."suppliers/".$rsImage[0]->vImage." alt='No Image' width=100 height=100>");	
				
			$this->ObTpl->set_var("TPL_VAR_DELETELINK",	"<a href=".SITE_URL."user/adminindex.php?action=supplier.uploadForm&id=".$this->request['id']."&delete=1>Delete</a>");		
			if(isset($this->request['delete']) && $this->request['delete']==1)
			{
				$source=$this->imagePath."suppliers/".$rsImage[0]->vImage;
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

		$imgLabel="Supplier's Logo";
		if(isset($this->request['status']))
		{
		$this->ObTpl->set_var("TPL_VAR_TOPMSG","".ucfirst($imgLabel)." has been Updated");			
		}
		else
		{
			$this->ObTpl->set_var("TPL_VAR_TOPMSG","");			
		}
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		$this->ObTpl->set_var("TPL_VAR_ID",$this->request['id']);		
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
		$this->ObTpl->set_var("FORMURL",SITE_URL."user/adminindex.php?action=supplier.upload&id=".$this->request['id']);
		
		$this->ObTpl->pparse("return","Editor");
		exit;
	}#EF

	#FUNCTION TO VALIDATE IMAGE UPLOADED  FROM UPLOAD FORM
	function m_verifyImageUpload(){
		if(!$this->libFunc->m_validateUpload($this->request['image'])){
			$this->errMsg.=MSG_VALID_IMAGE."<br />";
			$this->err=1;
		}
		return $this->err;
	}
}#END CLASS
?>