<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.1.0
=======================================================================================
*/
defined('_TEEXEC') or die;
class c_settingsInterface
{
	#CONSTRUCTOR
	function c_settingsInterface()
	{
		$this->subTotal		=0;
		$this->pageTplPath	=MODULES_PATH."default/templates/admin/";
		$this->pageTplFile	="pager.tpl.htm";
		$this->pageSize		="10";
		$this->err			=0;
		$this->LanguagePath=SITE_PATH."LanguagePacks/";
		$this->libFunc			=new c_libFunctions();
	}

	#FUNCTION TO DISPLAY SHOP SETTING HOMEPAGE
	function m_dspHome()
	{
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_SETTING_FILE", $this->settingsTemplate);

		#INTIALIZING TEMPLATE BLOCKS
		$this->ObTpl->set_var("GRAPHICSMAINPATH", GRAPHICS_PATH);
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		#INTIALIZING BLOCKS
		return($this->ObTpl->parse("return","TPL_SETTING_FILE"));
	}
	
	#FUNCTION TO SHOW THE ANALYTICS PAGE:
	function m_dspAnalytics () {
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_SETTING_FILE", $this->settingsTemplate);

		#INTIALIZING TEMPLATE BLOCKS
		$this->ObTpl->set_var("GRAPHICSMAINPATH", GRAPHICS_PATH);
		$this->ObTpl->set_var("TPL_VAR_SITEURL", SITE_URL);
		$this->ObTpl->set_var("TPL_VAR_TRACKINGCODE", $this->libFunc->m_displayContent(ANALYTICSCODE));
		#INTIALIZING BLOCKS
		
		return($this->ObTpl->parse("return", "TPL_SETTING_FILE"));
	}


	#FUNCTION TO DISPLAY COMPANY SETTING
	function m_formCompanyInfo()
	{
		#INTIALIZING TEMPLATES
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_SETTING_FILE", $this->settingsTemplate);
		$this->ObTpl->set_block("TPL_SETTING_FILE","countryblk","countryblks");
		$this->ObTpl->set_block("TPL_SETTING_FILE","BillCountry","nBillCountry");
		$this->ObTpl->set_block("TPL_SETTING_FILE","stateblk","stateblks");
		$this->ObTpl->set_block("TPL_SETTING_FILE","TPL_MSG_BLK","msgblk");
		$this->ObTpl->set_var("GRAPHICSMAINPATH", GRAPHICS_PATH);
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		$this->ObTpl->set_var("msgblk","");
		$this->ObTpl->set_var("BILL_STATE"," ");

		#MESSAGE HANDLE
		if(isset($this->request['msg']) && $this->request['msg']==1)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",MSG_CDETAILS_UPDATED);
			$this->ObTpl->parse("msgblk","TPL_MSG_BLK");
		}		
		if($this->err==1)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",$this->errMsg);
			$this->ObTpl->parse("msgblk","TPL_MSG_BLK");
		}

		$this->obDb->query = "SELECT * FROM ".COMPANYSETTINGS;
		$row_setting=$this->obDb->fetchQuery();
		#ASSIGNING FORM VARAIABLES
		$this->ObTpl->set_var("TPL_VAR_COMPANY",$this->libFunc->m_displayContent($row_setting[0]->vCname));
		$this->ObTpl->set_var("TPL_VAR_ADDRESS",$this->libFunc->m_displayContent($row_setting[0]->vAddress));
		$this->ObTpl->set_var("TPL_VAR_CITY",$this->libFunc->m_displayContent($row_setting[0]->vCity));
		$this->ObTpl->set_var("TPL_VAR_ZIP",$this->libFunc->m_displayContent($row_setting[0]->vZip));
		$this->ObTpl->set_var("TPL_VAR_PHONE",$this->libFunc->m_displayContent($row_setting[0]->vPhone));
		$this->ObTpl->set_var("TPL_VAR_FAX",$this->libFunc->m_displayContent($row_setting[0]->vFax));
		$this->ObTpl->set_var("TPL_VAR_FREEPHONE",$this->libFunc->m_displayContent($row_setting[0]->vFreePhone));		
		$this->ObTpl->set_var("TPL_VAR_VATNUMBER",$this->libFunc->m_displayContent($row_setting[0]->vVatNumber));		
		$this->ObTpl->set_var("TPL_VAR_RNUMBER",$this->libFunc->m_displayContent($row_setting[0]->vRNumber));
		$this->ObTpl->set_var("TPL_VAR_SLOGAN",$this->libFunc->m_displayContent($row_setting[0]->vSlogan));
		
	
		if (!$this->libFunc->m_isNull($row_setting[0]->vLogo)) 
		{
			$this->ObTpl->set_var("TPL_VAR_LOGO", "<img src='".SITE_URL."images/company/".$row_setting[0]->vLogo."' alt ='Company Logo' />");

		}else {
			$this->ObTpl->set_var("TPL_VAR_LOGO", "No Image Uploaded");
		}
		
		
		
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
			
			if($row_setting[0]->vCountry> 0)
			{
				if($row_setting[0]->vCountry == $row_country[$i]->iCountryId_PK)
					$this->ObTpl->set_var("BILL_COUNTRY_SELECT", "selected");
				else
					$this->ObTpl->set_var("BILL_COUNTRY_SELECT", "");
			}
			else
			{
					$row_setting[0]->vCountry = $row_country[$i]->iCountryId_PK;
					if($row_country[$i]->iCountryId_PK==251)
					{
						$this->ObTpl->set_var("BILL_COUNTRY_SELECT", "selected");
					}	
			}	
		$this->ObTpl->set_var("TPL_COUNTRY_NAME",$this->libFunc->m_displayContent($row_country[$i]->vCountryName));
			$this->ObTpl->parse("nBillCountry","BillCountry",true);
		}
		
			
		$this->ObTpl->set_var('selbillcountid',$row_setting[0]->vCountry);

		if($row_setting[0]->vState != '')
		{
			$this->ObTpl->set_var('selbillstateid',$this->libFunc->m_displayContent($row_setting[0]->vState));
		}
		else
		{
			$this->ObTpl->set_var('selbillstateid',0);
			$this->ObTpl->set_var('BILL_STATE',$this->libFunc->m_displayContent($row_setting[0]->vStateName));
		}
		
		
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
				$this->ObTpl->set_var("stateName",$this->libFunc->m_displayContent($cRes[$i]->statename));
				$this->ObTpl->set_var("stateVal",$cRes[$i]->sid);
				$this->ObTpl->parse('stateblks','stateblk',true);
			}
		}
		return($this->ObTpl->parse("return","TPL_SETTING_FILE"));
	}
	
	
	function m_formCompanyInfoHome()
	{
		#INTIALIZING TEMPLATES
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_SETTING_FILE", $this->settingsTemplate);
		$this->ObTpl->set_var("GRAPHICSMAINPATH", GRAPHICS_PATH);
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);


		$this->obDb->query = "SELECT * FROM ".COMPANYSETTINGS;
		$row_setting=$this->obDb->fetchQuery();
		$row_count = $this->obDb->record_count;
		
		#ASSIGNING FORM VARAIABLES
		$this->ObTpl->set_var("TPL_VAR_COMPANY",$this->libFunc->m_displayContent($row_setting[0]->vCname));
		$this->ObTpl->set_var("TPL_VAR_ADDRESS",$this->libFunc->m_displayContent($row_setting[0]->vAddress));
		$this->ObTpl->set_var("TPL_VAR_CITY",$this->libFunc->m_displayContent($row_setting[0]->vCity));
		$this->ObTpl->set_var("TPL_VAR_ZIP",$this->libFunc->m_displayContent($row_setting[0]->vZip));
		$this->ObTpl->set_var("TPL_VAR_PHONE",$this->libFunc->m_displayContent($row_setting[0]->vPhone));
		$this->ObTpl->set_var("TPL_VAR_FAX",$this->libFunc->m_displayContent($row_setting[0]->vFax));
		$this->ObTpl->set_var("TPL_VAR_FREEPHONE",$this->libFunc->m_displayContent($row_setting[0]->vFreePhone));	
		$this->ObTpl->set_var("TPL_VAR_VATNUMBER",$this->libFunc->m_displayContent($row_setting[0]->vVatNumber));		
		$this->ObTpl->set_var("TPL_VAR_RNUMBER",$this->libFunc->m_displayContent($row_setting[0]->vRNumber));
		$this->ObTpl->set_var("TPL_VAR_SLOGAN",$this->libFunc->m_displayContent($row_setting[0]->vSlogan));
		
		if (!$this->libFunc->m_isNull($row_setting[0]->vLogo)) {
			$this->ObTpl->set_var("TPL_VAR_LOGO", "<img src='".SITE_URL."images/company/".$row_setting[0]->vLogo."' alt ='Company Logo' />");
		}else {
			$this->ObTpl->set_var("TPL_VAR_LOGO", "No Image Uploaded");
		}
		
		
		if (!$this->libFunc->m_isNull($row_setting[0]->vState)) 
		{
			$this->obDb->query = "SELECT vStateName FROM ".STATES." WHERE iStateId_PK = ".$row_setting[0]->vState;
			$row_state = $this->obDb->fetchQuery();
			$this->ObTpl->set_var("TPL_STATE_NAME",$this->libFunc->m_displayContent($row_state[0]->vStateName));
		}else {
			$this->ObTpl->set_var("TPL_STATE_NAME","");
		}
		
		if (!$this->libFunc->m_isNull($row_setting[0]->vCountry)) 
		{
			$this->obDb->query = "SELECT vCountryName FROM  ".COUNTRY." WHERE iCountryId_PK =".$row_setting[0]->vCountry;
			$row_country = $this->obDb->fetchQuery();
			$this->ObTpl->set_var("TPL_COUNTRY_NAME",$this->libFunc->m_displayContent($row_country[0]->vCountryName));	
		}else {
			$this->ObTpl->set_var("TPL_COUNTRY_NAME","");
		}
		
		return($this->ObTpl->parse("return","TPL_SETTING_FILE"));
		
	}

	#FUNCTION TO VALIDATE COMPANY SETTING
	function valiadateCompanyInfo()
	{
		$this->errMsg="";
		if(empty($this->request['storeName']))
		{
			$this->err=1;
			$this->errMsg.=MSG_CNAME_EMPTY."<br>";
		}
		if(empty($this->request['storeAddress']))
		{
			$this->err=1;
			$this->errMsg.=MSG_CADDRESS_EMPTY."<br>";
		}
		if(empty($this->request['storeCity']))
		{
			$this->err=1;
			$this->errMsg.=MSG_CCITY_EMPTY."<br>";
		}
		if(empty($this->request['storeZip']))
		{
			$this->err=1;
			$this->errMsg.=MSG_CZIP_EMPTY."<br>";
		}
		if(empty($this->request['storePhone']))
		{
			$this->err=1;
			$this->errMsg.=MSG_CPHONE_EMPTY."<br>";
		}
		return $this->err;
	}

	function valiadateOrderInfo()
	{
		$this->errMsg="";
		
		return $this->err;
	}
	
	
	function displayIt($value)
	{
		if($value==1)
		{
			return "checked='checked'";
		}
		else
		{
			return "";
		}
	}

	#FUNCTION TO DISPLAY ORDER SETTINGS
	function m_orderSettings()
	{
		#INTIALIZING TEMPLATES
		$this->ObTpl=new template();
		$this->ObTpl->set_var("GRAPHICSMAINPATH", GRAPHICS_PATH);
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		$this->ObTpl->set_file("TPL_SETTING_FILE", $this->settingsTemplate);

		$this->ObTpl->set_block("TPL_SETTING_FILE","TPL_DSPMSG_BLK","dspmsg_blk");
	
		$this->ObTpl->set_var("dspmsg_blk","");

		$this->ObTpl->set_var("INCREASE_CHECKED","");		
		$this->ObTpl->set_var("ORIGINAL_CHECKED","checked='checked'");		
		$this->ObTpl->set_var("DECREASE_CHECKED","");	
		$this->ObTpl->set_var("TPL_VAR_PERCENT","");
		
		if(isset($this->request['msg']) && $this->request['msg']==1)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",MSG_ORDERDETAILS_UPDATED);
			$this->ObTpl->parse("dspmsg_blk","TPL_DSPMSG_BLK");
		}	

	
		$this->obDb->query = "SELECT vDatatype,vSmalltext,nNumberdata FROM ".SITESETTINGS;
		$row_setting=$this->obDb->fetchQuery();
		$rCount=$this->obDb->record_count;
		
		for($i=0;$i<$rCount;$i++)
		{
			switch($row_setting[$i]->vDatatype)
			{
				case "cartAlternateShipping":
				$this->ObTpl->set_var("TPL_VAR_ENABLEADDRESS",$this->displayIt($row_setting[$i]->nNumberdata));
				break;
				case "cartDefaultShipping":
					$this->ObTpl->set_var("TPL_VAR_POSTAGE",$this->libFunc->m_displayContent($row_setting[$i]->vSmalltext));
				break;
				case "vatbaserate":
					$this->ObTpl->set_var("TPL_VAR_VATBASERATE",$row_setting[$i]->nNumberdata);
				break;
				case "incvat":
					$this->ObTpl->set_var("TPL_VAR_INCVAT",$this->displayIt($row_setting[$i]->nNumberdata));
				break;
                case "wholesale":
					$this->ObTpl->set_var("ENABLE_WHOLESALE", $this->displayIt($row_setting[$i]->nNumberdata));
				break;
				case "netgross":
					$this->ObTpl->set_var("TPL_VAR_NETGROSS",$this->displayIt($row_setting[$i]->nNumberdata));
				break;
				case "cartOrderEmail":
					$this->ObTpl->set_var("TPL_VAR_ORDERMAIL",$row_setting[$i]->vSmalltext);
				break;
				case "cartInfoEmail":
					$this->ObTpl->set_var("TPL_VAR_INFOMAIL",$row_setting[$i]->vSmalltext);
				break;
				#MODIFIED ON 19-03-07 BY NSI
				case "cartWirelessEmail":
					$this->ObTpl->set_var("TPL_VAR_WIRELESS",$row_setting[$i]->vSmalltext);
				break;
				#MODIFIED ON 12-04-07 BY NSI
				case "cartPayCC":
					$this->ObTpl->set_var("ENABLE_CREDIT",$this->displayIt($row_setting[$i]->nNumberdata));
				break;
				case "cartPayCCp":
					$this->ObTpl->set_var("ENABLE_CREDIT_PHONE",$this->displayIt($row_setting[$i]->nNumberdata));
				break;
				case "cartPayEFT":
					$this->ObTpl->set_var("ENABLE_ELECTRONIC_FUNDS",$this->displayIt($row_setting[$i]->nNumberdata));
				break;
				case "cartPayMail":
					$this->ObTpl->set_var("ENABLE_CREDITPHONE",$this->displayIt($row_setting[$i]->nNumberdata));
				break;

				case "cartPayCOD":
					$this->ObTpl->set_var("COD",number_format($row_setting[$i]->vSmalltext,2));
				break;
				case "cartCCTypeVisa":
					$this->ObTpl->set_var("ENABLE_VISA",$this->displayIt($row_setting[$i]->nNumberdata));
				break;
				case "cartCCTypeVisaDelta":
					$this->ObTpl->set_var("ENABLE_VISADELTA",$this->displayIt($row_setting[$i]->nNumberdata));
				break;
				case "cartCCTypeVisaElectron":
					$this->ObTpl->set_var("ENABLE_VISAELECTRON",$this->displayIt($row_setting[$i]->nNumberdata));
				break;
				case "cartCCTypeMC":
					$this->ObTpl->set_var("ENABLE_MASTERCARD",$this->displayIt($row_setting[$i]->nNumberdata));
				break;
				case "cartCCTypeAmex":
					$this->ObTpl->set_var("ENABLE_AMEX",$this->displayIt($row_setting[$i]->nNumberdata));
				break;
				case "cartCCTypeDiscover":
					$this->ObTpl->set_var("ENABLE_DISCOVER",$this->displayIt($row_setting[$i]->nNumberdata));
				break;
				case "cartCCTypeDiners":
					$this->ObTpl->set_var("ENABLE_DINNER",$this->displayIt($row_setting[$i]->nNumberdata));
				break;
				case "cartCCTypeSolo":
					$this->ObTpl->set_var("ENABLE_SOLO",$this->displayIt($row_setting[$i]->nNumberdata));
				break;
				case "cartCCTypeSwitch":
					$this->ObTpl->set_var("ENABLE_SWITCH",$this->displayIt($row_setting[$i]->nNumberdata));
				break;
				case "cartCCTypeMaestro":
					$this->ObTpl->set_var("ENABLE_MASTERO",$this->displayIt($row_setting[$i]->nNumberdata));
				break;
				case "rrptext":
					$this->ObTpl->set_var("TPL_VAR_RRPTEXT",$row_setting[$i]->vSmalltext);
				break;
				case "vTaxName":
					$this->ObTpl->set_var("TPL_VAR_TAXNAME",$row_setting[$i]->vSmalltext);
				break;
				case "postagevatonoff":
				$this->ObTpl->set_var("TPL_VAR_POSTAGEVATOPTION",$this->displayIt($row_setting[$i]->nNumberdata));
				break;
				case "IncVatTextFlag":
				$this->ObTpl->set_var("TPL_VAR_INCVATTEXTCHECK",$this->displayIt($row_setting[$i]->nNumberdata));
				break;
				case "hidenovat":
				$this->ObTpl->set_var("TPL_VAR_HIDENOVAT",$this->displayIt($row_setting[$i]->nNumberdata));
				break;
				case "minordertotal":
				$this->ObTpl->set_var("TPL_VAR_MINORDER",number_format($row_setting[$i]->nNumberdata));
				break;
				case "marginpercent":
				$this->ObTpl->set_var("TPL_VAR_PERCENT",number_format($row_setting[$i]->nNumberdata));
				break;
			}#END SWITCH
		}#END FOR LOOP
		switch (MARGINSTATUS)
		{
			case "increase":
				$this->ObTpl->set_var("INCREASE_CHECKED","checked='checked'");
			break;
			case "decrease":
				$this->ObTpl->set_var("DECREASE_CHECKED","checked='checked'");
			break;
			default:
				$this->ObTpl->set_var("ORIGINAL_CHECKED","checked='checked'");
			break;
						
		}
		#ASSIGNING FORM VARAIABLES
		return($this->ObTpl->parse("return","TPL_SETTING_FILE"));
	}

	#FUNCTION TO DISPLAY DESIGN SETTINGS
	function m_dspDesignSettings(){
		#INTIALIZING TEMPLATES
		$this->ObTpl=new template();
		$this->ObTpl->set_var("GRAPHICSMAINPATH", GRAPHICS_PATH);
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		$this->ObTpl->set_file("TPL_SETTING_FILE", $this->settingsTemplate);

		$this->ObTpl->set_block("TPL_SETTING_FILE","TPL_DSPMSG_BLK","dspmsg_blk");
		$this->ObTpl->set_var("dspmsg_blk","");
		$this->ObTpl->set_block("TPL_SETTING_FILE","TPL_HOME_LAYOUT_BLK", "hTPL_HOME_LAYOUT_BLK");
		$this->ObTpl->set_block("TPL_SETTING_FILE","TPL_MAIN_LAYOUT_BLK", "hTPL_MAIN_LAYOUT_BLK");
		
		$this->ObTpl->set_var("TPL_VAR_THUMBIMGWIDTH","");
		$this->ObTpl->set_var("TPL_VAR_THUMBIMGHEIGHT","");
		$this->ObTpl->set_var("TPL_VAR_LARGEIMGWIDTH","");
		$this->ObTpl->set_var("TPL_VAR_LARGEIMGHEIGHT","");

		if(isset($this->request['msg']) && $this->request['msg']==1)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",MSG_DESIGNSETTINGS_UPDATED);
			$this->ObTpl->parse("dspmsg_blk","TPL_DSPMSG_BLK");
		}		
		$this->ObTpl->set_var("TPL_VAR_TREEMENU",$this->displayIt(TREE_MENU));

		$this->obDb->query = "SELECT * FROM ".SITESETTINGS;
		$row_setting=$this->obDb->fetchQuery();
		$rCount=$this->obDb->record_count;
		for($i=0;$i<$rCount;$i++)
		{
			switch($row_setting[$i]->vDatatype)
			{
			
				case "iTreeMenu":
					$this->ObTpl->set_var("TPL_VAR_TREEMENU", $this->displayIt($row_setting[$i]->nNumberdata));
				break;
				# Image Upload Settings
				case "imgUploadJPGCompression":
					$this->ObTpl->set_var("TPL_VAR_JPGCOMPRESSION",number_format($row_setting[$i]->nNumberdata));
				break;
				case "imgUploadSmallWidth":
					$this->ObTpl->set_var("TPL_VAR_SMIMAGEWIDTH",number_format($row_setting[$i]->nNumberdata));
				break;
				case "imgUploadSmallHeight":
					$this->ObTpl->set_var("TPL_VAR_SMIMAGEHEIGHT",number_format($row_setting[$i]->nNumberdata));
				break;
				case "imgUploadMediumWidth":
					$this->ObTpl->set_var("TPL_VAR_MDIMAGEWIDTH",number_format($row_setting[$i]->nNumberdata));
				break;
				case "imgUploadMediumHeight":
					$this->ObTpl->set_var("TPL_VAR_MDIMAGEHEIGHT",number_format($row_setting[$i]->nNumberdata));
				break;
				case "imgUploadLargeWidth":
					$this->ObTpl->set_var("TPL_VAR_LGIMAGEWIDTH",number_format($row_setting[$i]->nNumberdata));
				break;
				case "imgUploadLargeHeight":
					$this->ObTpl->set_var("TPL_VAR_LGIMAGEHEIGHT",number_format($row_setting[$i]->nNumberdata));
				break;
				case "imgUploadDeptSmallWidth":
					$this->ObTpl->set_var("TPL_VAR_DEPTSMIMAGEWIDTH",number_format($row_setting[$i]->nNumberdata));
				break;
				case "imgUploadDeptSmallHeight":
					$this->ObTpl->set_var("TPL_VAR_DEPTSMIMAGEHEIGHT",number_format($row_setting[$i]->nNumberdata));
				break;
				case "imgUploadDeptMediumWidth":
					$this->ObTpl->set_var("TPL_VAR_DEPTMDIMAGEWIDTH",number_format($row_setting[$i]->nNumberdata));
				break;
				case "imgUploadDeptMediumHeight":
					$this->ObTpl->set_var("TPL_VAR_DEPTMDIMAGEHEIGHT",number_format($row_setting[$i]->nNumberdata));
				break;
				case "imgUploadContentSmallWidth":
					$this->ObTpl->set_var("TPL_VAR_CONTENTSMIMAGEWIDTH",number_format($row_setting[$i]->nNumberdata));
				break;
				case "imgUploadContentSmallHeight":
					$this->ObTpl->set_var("TPL_VAR_CONTENTSMIMAGEHEIGHT",number_format($row_setting[$i]->nNumberdata));
				break;
				case "imgGalleryThumbnailWidth":
					$this->ObTpl->set_var("TPL_VAR_THUMBIMGWIDTH",number_format($row_setting[$i]->nNumberdata));
				break;
				case "imgGalleryThumbnailHeight":
					$this->ObTpl->set_var("TPL_VAR_THUMBIMGHEIGHT",number_format($row_setting[$i]->nNumberdata));
				break;
				case "imgGalleryLargeWidth":
					$this->ObTpl->set_var("TPL_VAR_LARGEIMGWIDTH",number_format($row_setting[$i]->nNumberdata));
				break;
				case "imgGalleryLargeHeight":
					$this->ObTpl->set_var("TPL_VAR_LARGEIMGHEIGHT",number_format($row_setting[$i]->nNumberdata));
				break;
				
				
				#NUMBER OF PRODUCTS PER PAGE (department or product page) 								
				case "deptlimit":
					$this->ObTpl->set_var("TPL_VAR_IMGPERPAGE",number_format($row_setting[$i]->vSmalltext));
				break;
				case "homeLayout":
					if (is_dir(MODULES_PATH."default/templates/main/layout/")) {
						if ($dh = opendir(MODULES_PATH."default/templates/main/layout/")) {			
							while (($templateName = readdir($dh)) !== false) {
								if($templateName!="." && $templateName!="..") {
									if($templateName==$row_setting[$i]->vSmalltext)	{
										$this->ObTpl->set_var("SEL_HOME_LAYOUT","selected");
									}
									else{
										$this->ObTpl->set_var("SEL_HOME_LAYOUT","");
									}
									$this->ObTpl->set_var("TPL_VAR_HOME_LAYOUT",$templateName);
									$this->ObTpl->parse("hTPL_HOME_LAYOUT_BLK","TPL_HOME_LAYOUT_BLK",true);
								}// end of templateName
							 }// end of while
							closedir($dh);
						}// end of if
					}// end of if
				break;
				case "mainLayout":
					if (is_dir(MODULES_PATH."default/templates/main/layout/")) {
						if ($dh = opendir(MODULES_PATH."default/templates/main/layout/")) {			
							while (($templateName = readdir($dh)) !== false) {
								if($templateName!="." && $templateName!="..") {
									if($templateName==$row_setting[$i]->vSmalltext)	{
										$this->ObTpl->set_var("SELLAYOUT","selected");
									}
									else{
										$this->ObTpl->set_var("SELLAYOUT","");
									}
									$this->ObTpl->set_var("TPL_VAR_MAIN_LAYOUT",$templateName);
									$this->ObTpl->parse("hTPL_MAIN_LAYOUT_BLK","TPL_MAIN_LAYOUT_BLK",true);
								}// end of templateName
							 }// end of while
							closedir($dh);
						}// end of if
					}// end of if
				break;
				
			}#switch
		}#for loop
		
		return($this->ObTpl->parse("return","TPL_SETTING_FILE"));		
	}

	#FUNCTION TO DISPLAY FEATURE SETTING	
	function m_featureSettings() {
		#INTIALIZING TEMPLATES
		
		$this->ObTpl=new template();
		$this->ObTpl->set_var("GRAPHICSMAINPATH", GRAPHICS_PATH);
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		$this->ObTpl->set_file("TPL_SETTING_FILE", $this->settingsTemplate);

		$this->ObTpl->set_block("TPL_SETTING_FILE","TPL_DSPMSG_BLK","dspmsg_blk");
		$this->ObTpl->set_block("TPL_SETTING_FILE","TPL_LANG_BLK","lang_blk");
		$this->ObTpl->set_var("dspmsg_blk","");
		$this->ObTpl->set_var("lang_blk","");

		if(isset($this->request['msg']) && $this->request['msg']==1)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",MSG_FEATURESETTINGS_UPDATED);
			$this->ObTpl->parse("dspmsg_blk","TPL_DSPMSG_BLK");
		}		
		$this->obDb->query = "SELECT * FROM ".SITESETTINGS;
		$row_setting=$this->obDb->fetchQuery();
		$rCount=$this->obDb->record_count;
		for($i=0;$i<$rCount;$i++)
		{
			switch($row_setting[$i]->vDatatype)
			{			
				case "inventory":
					$this->ObTpl->set_var("TPL_VAR_ENABLEINVENTORY", $this->displayIt($row_setting[$i]->vSmalltext));
				break;
				case "rssarticles":
					if( !empty($row_setting[$i]->vSmalltext) && ($row_setting[$i]->vSmalltext > 1 ) ){
						$row_setting[$i]->vSmalltext = 1 ;
					}
					$this->ObTpl->set_var("TPL_VAR_ENABLEARTICLESRSS", $this->displayIt($row_setting[$i]->vSmalltext));
				break;
				case "rssproducts":
					if( !empty($row_setting[$i]->vSmalltext) && ($row_setting[$i]->vSmalltext > 1 ) ){
						$row_setting[$i]->vSmalltext = 1 ;
					}
					$this->ObTpl->set_var("TPL_VAR_ENABLEPRODUCTRSS", $this->displayIt($row_setting[$i]->vSmalltext));
				break;
				case "topsellers":
					$this->ObTpl->set_var("TPL_VAR_ENABLETOPSELLERS", $this->displayIt($row_setting[$i]->vSmalltext));
				break;
				case "shopbybrand":
					$this->ObTpl->set_var("TPL_VAR_ENABLESHOPBYBRAND", $this->displayIt($row_setting[$i]->vSmalltext));
				break;
				case "recent":
					$this->ObTpl->set_var("TPL_VAR_ENABLERECENT", $this->displayIt($row_setting[$i]->vSmalltext));
				break;
				case "customerReviews":
				$this->ObTpl->set_var("TPL_VAR_ENABLEREVIEWS", $this->displayIt($row_setting[$i]->vSmalltext));
				break;
				case "wishlist":
				$this->ObTpl->set_var("TPL_VAR_ENABLEWISHLIST",$this->displayIt($row_setting[$i]->vSmalltext));
				break;
				case "usecompare":
				$this->ObTpl->set_var("TPL_VAR_ENABLECOMPARE",$this->displayIt($row_setting[$i]->vSmalltext));
				break;
				case "cartGiftWrapping":
					$this->ObTpl->set_var("ENABLE_GIFTWRAP",$this->displayIt($row_setting[$i]->vSmalltext));
				break;
				case "newsletternav":
					$this->ObTpl->set_var("ENABLE_NEWSLETTERNAV",$this->displayIt($row_setting[$i]->vSmalltext));
				break;
				case "captcha_registration":
					$this->ObTpl->set_var("CAPTCHA_REGISTRATION",$this->displayIt($row_setting[$i]->vSmalltext));
				break;
				case "captcha_contactus":
					$this->ObTpl->set_var("CAPTCHA_CONTACTUS",$this->displayIt($row_setting[$i]->vSmalltext));
				break;
				case "SpecialPostage":
					$this->ObTpl->set_var("TPL_VAR_SPECIALD",$this->displayIt($row_setting[$i]->nNumberdata));
				break;
				case "cartMailList":
					$this->ObTpl->set_var("ENABLE_NEWSLETTER",$this->displayIt($row_setting[$i]->vSmalltext));
				break;
				case "dropshipFeature":
					$this->ObTpl->set_var("ENABLE_DROPSHIP",$this->displayIt($row_setting[$i]->vSmalltext));
				break;
				case "membership":
					$this->ObTpl->set_var("TPL_VAR_ENABLEMEMBERSHIP",$this->displayIt(OFFERMPOINT));
				break;				
				case "Language":
						
						if (is_dir($this->LanguagePath)) 
						{
							if ($dh = opendir($this->LanguagePath))
							{			
								while (($templateName = readdir($dh)) !== false) 
								{
									if($templateName!="." && $templateName!="..") {
										if(preg_match("/([\.htm|html|tpl|tpl.html|tpl.htm])$/",$templateName)){
											if($templateName==$row_setting[$i]->vSmalltext)
											{
												$this->ObTpl->set_var("SELLANG","selected");
											}
											else
											{
												$this->ObTpl->set_var("SELLANG","");
											}
											$this->ObTpl->set_var("TPL_VAR_LANGNAME",$templateName);
											$this->ObTpl->parse("lang_blk","TPL_LANG_BLK",true);
										}
									}
								}
								closedir($dh);
							}
						}
				break;
				case "analyticsCode":
					$this->ObTpl->set_var("TPL_VAR_TRACKINGCODE",$this->libFunc->m_displayContent($row_setting[$i]->tLargetext));
				break;
			
			
			}#END SWITCH
		}#END FOR LOOP
		$this->ObTpl->set_var("TPL_VAR_ENABLERECENT_LIMIT", RVP_LIMIT);
	return($this->ObTpl->parse("return","TPL_SETTING_FILE"));
	}#END FUNCTION


	#FUNCTION TO DISPLAY PAYMENT SETTING
	function m_paymentSettings()
	{
		#INTIALIZING TEMPLATES
		$this->ObTpl=new template();
		$this->ObTpl->set_var("GRAPHICSMAINPATH", GRAPHICS_PATH);
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		$this->ObTpl->set_file("TPL_SETTING_FILE", $this->settingsTemplate);
		$this->ObTpl->set_block("TPL_SETTING_FILE","TPL_DSPMSG_BLK","dspmsg_blk");

		if($this->err==1){
			$this->ObTpl->set_var("TPL_VAR_MSG",$this->errMsg);
			$this->ObTpl->parse("dspmsg_blk","TPL_DSPMSG_BLK");
		}else{		
			$this->ObTpl->set_var("dspmsg_blk","");
		}

		$this->ObTpl->set_var("TPL_VAR_SELECTED_METHOD", SELECTED_PAYMENTGATEWAY);	
		$this->ObTpl->set_var("ENABLE_GATEWAYTESTMODE", $this->displayIt(GATEWAY_TESTMODE));	
		$this->ObTpl->set_var("TPL_VAR_PAYPAL_ID", PAYPAL_ID);
		$this->ObTpl->set_var("TPL_VAR_PAYPAL_CURRENCY",PAYMENT_CURRENCY);
		$this->ObTpl->set_var("TPL_VAR_AUTHORIZEPAYMENT_LOGIN", AUTHORIZEPAYMENT_LOGIN);
		$this->ObTpl->set_var("TPL_VAR_AUTHORIZEPAYMENT_TYPE", AUTHORIZEPAYMENT_TYPE);
		$this->ObTpl->set_var("TPL_VAR_AUTHORIZEPAYMENT_KEY", AUTHORIZEPAYMENT_KEY);
		
		if(!empty($this->request['txtprotxVendor'])){
			$protx_vendor=$this->libFunc->m_displayContent1($this->request['txtprotxVendor']);
			$protx_apply_avs_cv2=$this->libFunc->m_displayContent1($this->request['txtprotxApplyAVSCV2']);
			$protx_3d_secure_status=$this->libFunc->m_displayContent1($this->request['txtprotx3DSecureStatus']);
		}
		else{
			$protx_vendor=$this->libFunc->m_displayContent1(PROTX_VENDOR);
			$protx_apply_avs_cv2=$this->libFunc->m_displayContent1(PROTX_APPLY_AVS_CV2);
			$protx_3d_secure_status=$this->libFunc->m_displayContent1(PROTX_3D_SECURE_STATUS);
		}

		$this->ObTpl->set_var("TPL_VAR_PROTX_VENDOR", $protx_vendor);
		$this->ObTpl->set_var("TPL_VAR_APPLY_AVS_CV2",$protx_apply_avs_cv2);
		$this->ObTpl->set_var("TPL_VAR_3D_SECURE_STATUS",$protx_3d_secure_status);
		
		//PAYPAL DIRECT PAYMENTS
		
		$this->ObTpl->set_var("TPL_VAR_PAYPALAPI_USERNAME", PAYPALAPI_USERNAME);
		$this->ObTpl->set_var("TPL_VAR_PAYPALAPI_PASSWORD", PAYPALAPI_PASSWORD);
		$this->ObTpl->set_var("TPL_VAR_PAYPALAPI_SIGNATURE", PAYPALAPI_SIGNATURE);
		$this->ObTpl->set_var("TPL_VAR_PAYPALAPI_ENDPOINT", PAYPALAPI_ENDPOINT);
		
				
		//Implementing secpay
		$this->ObTpl->set_var("TPL_VAR_SECPAY_MERCHANT", SECPAY_MERCHANT);
		$this->ObTpl->set_var("TPL_VAR_SECPAY_REMOTEPASSWORD", SECPAY_REMOTEPASSWORD);
		$this->ObTpl->set_var("TPL_VAR_SECPAY_DIGESTKEY", SECPAY_DIGESTKEY);
		
		//Implemented Verisign
		$this->ObTpl->set_var("TPL_VAR_VERISIGN_PARTNER", VERISIGN_PARTNER);
		$this->ObTpl->set_var("TPL_VAR_VERISIGN_LOGIN", VERISIGN_LOGIN);
		$this->ObTpl->set_var("TPL_VAR_VERISIGN_USER", VERISIGN_USER);
		$this->ObTpl->set_var("TPL_VAR_VERISIGN_PASSWORD", VERISIGN_PASSWORD);	
		
		//Implemented HSBC
		$this->ObTpl->set_var("TPL_VAR_HSBC_KEY", HSBC_KEY);
		$this->ObTpl->set_var("TPL_VAR_HSBC_STOREID", HSBC_STOREID);
		$this->ObTpl->set_var("TPL_VAR_HSBC_CURRENCY", HSBC_CURRENCY);

		//mplemented BARCLAYS
		$this->ObTpl->set_var("TPL_VAR_BARCLAYS_CLIENTID", BARCLAYS_CLIENTID);
		$this->ObTpl->set_var("TPL_VAR_BARCLAYS_PASSWORD", BARCLAYS_PASSWORD);
		
		#NSI: 01-05-2008-Implement SecureTrading offline
		$this->ObTpl->set_var("TPL_VAR_ST_SITEREF", STREFERENCE);
		$this->ObTpl->set_var("TPL_VAR_ST_CLIENTID", SECURETRADING_CLIENTID);
		$this->ObTpl->set_var("TPL_VAR_ST_PASSWORD", SECURETRADING_PASSWORD);
		
		//-Implemented SecureTrading
   		$this->ObTpl->set_var("TPL_VAR_SECURETRADING_MERCHANTID", SECURETRADING_MERCHANTID);
		$this->ObTpl->set_var("TPL_VAR_SECURETRADING_CURRENCY", SECURETRADING_CURRENCY);

		if(isset($this->request['msg']) && $this->request['msg']==1)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",MSG_PAYMENTDETAILS_UPDATED);
			$this->ObTpl->parse("dspmsg_blk","TPL_DSPMSG_BLK");
		}		
        
        #(BEGIN) SAGEPAY INTEGRATION 
		$this->ObTpl->set_var("TPL_VAR_SAGEVENDORNAME", SAGE_VENDORNAME);
		$this->ObTpl->set_var("TPL_VAR_ENCRYPTIONPASSWORD", SAGE_ENCRYPTEDPASSWORD);
		$this->ObTpl->set_var("TPL_VAR_SAGETRANSACTIONTYPE", SAGE_TRANSACTIONTYPE);
		$this->ObTpl->set_var("TPL_VAR_SAGECURRENCY", SAGE_CURRENCY);
        #(END) SAGEPAY INTEGRATION
		
		//-Implemented Propay
		//Propay Gateway Integration: Starts
   		$this->ObTpl->set_var("TPL_VAR_PROPAY_ACCNUMBER", PROPAY_ACCNUMBER);
		$this->ObTpl->set_var("TPL_VAR_PROPAY_CERTSTRING", PROPAY_CERTSTRING);
		
		if(PROPAY_CANADA == "1"){
			$this->ObTpl->set_var("TPL_VAR_PROPAY_CANADA", "checked='checked'");
		}
		//Propay Gateway Integration: Ends
		
		// (BEGIN)CardPay payment Integration 
		$this->ObTpl->set_var("TPL_VAR_CS_ID",CS_MERCHANT_ID);	
		$this->ObTpl->set_var("TPL_VAR_CS_PASS",CS_MERCHANT_PASS);	
		$this->ObTpl->set_var("TPL_VAR_CS_DOMAIN",CS_GATEWAY_DOMAIN);	
		$this->ObTpl->set_var("TPL_VAR_CS_PORT",CS_GATEWAY_PORT);	
		$this->ObTpl->set_var("TPL_VAR_CS_SECRET",CS_SECRET_KEY);	
		$this->ObTpl->set_var("TPL_VAR_CS_CURRENCY",CS_CURRENCY);	
		
		$this->ObTpl->set_var("TPL_VAR_CSR_ID",CSr_MERCHANT_ID);
		$this->ObTpl->set_var("TPL_VAR_CSR_PASS",CSr_MERCHANT_PASS);
		$this->ObTpl->set_var("TPL_VAR_CSR_SECRET",CSr_KEY);
		$this->ObTpl->set_var("TPL_VAR_CSR_DOMAIN",CSr_DOMAIN);
		$this->ObTpl->set_var("TPL_VAR_CSR_RESULTS",CSr_RESULTS_DISPLAY);
		$this->ObTpl->set_var("TPL_VAR_CSR_CV2",CSr_CV2_MANDATORY);
		$this->ObTpl->set_var("TPL_VAR_CSR_CURRENCY",CSr_CURRENCY);
		// (END) Cardpay payment Integration

		return($this->ObTpl->parse("return","TPL_SETTING_FILE"));
	}

	#FUNCTION TO DISPLAY METATAGS EDIT FORM
	function m_metaTagEditor()
	{

		#INTIALIZING TEMPLATES
		$this->ObTpl=new template();
		$this->ObTpl->set_var("GRAPHICSMAINPATH", GRAPHICS_PATH);
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		$this->ObTpl->set_file("TPL_SETTING_FILE", $this->metaTemplate);

		$this->ObTpl->set_block("TPL_SETTING_FILE","TPL_DSPMSG_BLK","dspmsg_blk");
		$this->ObTpl->set_var("dspmsg_blk","");

		if(isset($this->request['msg']) && $this->request['msg']==1)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",MSG_METASETTINGS_UPDATED);
			$this->ObTpl->parse("dspmsg_blk","TPL_DSPMSG_BLK");
		}		

		$this->obDb->query = "SELECT vDatatype,tLargetext FROM ".SITESETTINGS;
		$row_setting=$this->obDb->fetchQuery();
		$rCount=$this->obDb->record_count;
		
		for($i=0;$i<$rCount;$i++)
		{
			switch($row_setting[$i]->vDatatype)
			{
				case "metatitle":
					$this->ObTpl->set_var("TPL_VAR_METATITLE",$this->libFunc->m_displayContent($row_setting[$i]->tLargetext));
				break;
				case "metadescription":
				$this->ObTpl->set_var("TPL_VAR_METADESC",$this->libFunc->m_displayContent($row_setting[$i]->tLargetext));
				break;
				case "metakeyword":
				$this->ObTpl->set_var("TPL_VAR_METAKEY",$this->libFunc->m_displayContent($row_setting[$i]->tLargetext));
				break;
			}#END SWITCH
		}#END FOR LOOP
		return($this->ObTpl->parse("return","TPL_SETTING_FILE"));
	}

	#FUNCTION TO DISPLAY TEXT EDITOR
	function m_textEditor()	{

		#INTIALIZING TEMPLATES
		$this->ObTpl=new template();
		$this->ObTpl->set_var("GRAPHICSMAINPATH", GRAPHICS_PATH);
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		$this->ObTpl->set_file("TPL_SETTING_FILE", $this->settingsTemplate);
		$this->ObTpl->set_block("TPL_SETTING_FILE","TPL_DSPMSG_BLK","dspmsg_blk");
		$this->ObTpl->set_block("TPL_SETTING_FILE","TPL_GLOBALSEO_BLK","globalseo_blk");
		
		$this->ObTpl->set_var("dspmsg_blk","");
		$this->ObTpl->set_var("globalseo_blk","");
		
		if(isset($this->request['msg']) && $this->request['msg']==1)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",TPL_VAR_UPDATESUCCESS);
			$this->ObTpl->parse("dspmsg_blk","TPL_DSPMSG_BLK");
		}		
		
		if($this->request['which']=="index_body"){
			$this->ObTpl->parse("globalseo_blk","TPL_GLOBALSEO_BLK");
		}
		
		$this->obDb->query = "SELECT vSmalltext,tLargetext FROM ".SITESETTINGS." WHERE vDatatype='".$this->request['which']."'";
		$row_setting=$this->obDb->fetchQuery();
		$rCount=$this->obDb->record_count;
		
		#SETTING UP FCK EDITOR			
		$oFCKeditor = new CKEditor();
		$oFCKeditor->basePath = '../ckeditor/';
		$oFCKeditor->Value=$this->libFunc->m_displayCms($row_setting[0]->tLargetext);

		$oFCKeditor->Height="300";
		$oFCKeditor->ToolbarSet="Default";
		
		$this->ObTpl->set_var("TPL_VAR_TYPE",$this->request['which']);
		$this->ObTpl->set_var("TPL_VAR_HEADING",$this->libFunc->m_displayContent($row_setting[0]->vSmalltext));
		$this->ObTpl->set_var("cmsEditor","<textarea id='TextEditor' name='content'>" . $this->libFunc->m_displayCms($row_setting[0]->tLargetext) . "</textarea><script type='text/javascript'>CKEDITOR.replace('TextEditor',{removePlugins : 'save'});</script>");
		
		//--		
		if(isset($this->request['msg']) && $this->request['msg']==1)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",MSG_METASETTINGS_UPDATED);
			$this->ObTpl->parse("dspmsg_blk","TPL_DSPMSG_BLK");
		}		

		$this->obDb->query = "SELECT vDatatype,tLargetext FROM ".SITESETTINGS;
		$row_setting=$this->obDb->fetchQuery();
		$rCount=$this->obDb->record_count;
		
		for($i=0;$i<$rCount;$i++)
		{
			switch($row_setting[$i]->vDatatype)
			{
				case "metatitle":
					$this->ObTpl->set_var("TPL_VAR_METATITLE",$this->libFunc->m_displayContent($row_setting[$i]->tLargetext));
				break;
				case "metadescription":
				$this->ObTpl->set_var("TPL_VAR_METADESC",$this->libFunc->m_displayContent($row_setting[$i]->tLargetext));
				break;
				case "metakeyword":
				$this->ObTpl->set_var("TPL_VAR_METAKEY",$this->libFunc->m_displayContent($row_setting[$i]->tLargetext));
				break;
			}#END SWITCH
		}#END FOR LOOP
		//--
		
		
		return($this->ObTpl->parse("return","TPL_SETTING_FILE"));
	}#END FUNCTION
	
	#FUNCTION TO DISPLAY TEXTAREAS HOME
	function m_textAreas()
	{
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_SETTING_FILE", $this->settingsTemplate);

		#INTIALIZING TEMPLATE BLOCKS
		$this->ObTpl->set_var("GRAPHICSMAINPATH", GRAPHICS_PATH);
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		#INTIALIZING BLOCKS
		return($this->ObTpl->parse("return","TPL_SETTING_FILE"));
	}

	#FUNCTION TO DISPLAY POSTAL & PACKAGES HOME
	function m_postageHome()
	{
				  		
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_SETTING_FILE", $this->settingsTemplate);
		$this->ObTpl->set_block("TPL_SETTING_FILE","TPL_METHODS_BLK","dspmethods_blk");
		#INTIALIZING TEMPLATE BLOCKS
		$this->ObTpl->set_var("GRAPHICSMAINPATH", GRAPHICS_PATH);
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		#INTIALIZING BLOCKS
		$this->ObTpl->set_var("dspmethods_blk","");
		$this->obDb->query="SELECT * FROM ".POSTAGE." WHERE vKey IN ('special','pweight')";
		$resSpecial=$this->obDb->fetchQuery();
	
		$this->ObTpl->set_var("TPL_VAR_SPCID",$resSpecial[0]->iPostId_PK);
		$this->ObTpl->set_var("TPL_VAR_SPCDESCRIPTION",$resSpecial[0]->vDescription);
		$this->ObTpl->set_var("TPL_VAR_SPCNAME",$resSpecial[0]->vMethodName);
		$this->ObTpl->set_var("TPL_VAR_SPCKEY",$resSpecial[0]->vKey);

		if($resSpecial[0]->iStatus==1)
		{
			$this->ObTpl->set_var("TPL_VAR_SPECIALSTATUS","checked");
		}
		else
		{
			$this->ObTpl->set_var("TPL_VAR_SPECIALSTATUS","");
		}
		$this->ObTpl->set_var("ENABLE_SPECIALPOSTAGE",$this->displayIt(SPECIAL_POSTAGE));
		$this->ObTpl->set_var("TPL_VAR_WEIGHTID",$resSpecial[1]->iPostId_PK);
		$this->ObTpl->set_var("TPL_VAR_WEIGHTDESCRIPTION",$resSpecial[1]->vDescription);
		$this->ObTpl->set_var("TPL_VAR_WEIGHTNAME",$resSpecial[1]->vMethodName);
		$this->ObTpl->set_var("TPL_VAR_WEIGHTKEY",$resSpecial[1]->vKey);

		if($resSpecial[1]->iStatus==1)
		{
			$this->ObTpl->set_var("TPL_VAR_WEIGHTSTATUS","checked");
		}
		else
		{
			$this->ObTpl->set_var("TPL_VAR_WEIGHTSTATUS","");
		}

		$this->obDb->query="SELECT * FROM ".POSTAGE." WHERE vKey NOT IN ('special','pweight')";
		$resPostage=$this->obDb->fetchQuery();
		$rsCount=$this->obDb->record_count;
		for($i=0;$i<$rsCount;$i++)
		{
			$this->ObTpl->set_var("TPL_VAR_COUNT",$i+1);
			$this->ObTpl->set_var("TPL_VAR_ID",$resPostage[$i]->iPostId_PK);
			$this->ObTpl->set_var("TPL_VAR_DESCRIPTION",$resPostage[$i]->vDescription);
			$this->ObTpl->set_var("TPL_VAR_NAME",$resPostage[$i]->vMethodName);
			$this->ObTpl->set_var("TPL_VAR_KEY",$resPostage[$i]->vKey);
			if($resPostage[$i]->iStatus==1)
			{
				$this->ObTpl->set_var("TPL_VAR_STATUS","checked");
			}
			else
			{
				$this->ObTpl->set_var("TPL_VAR_STATUS","");
			}
			$this->ObTpl->parse("dspmethods_blk","TPL_METHODS_BLK",true);
		}


		return($this->ObTpl->parse("return","TPL_SETTING_FILE"));
	}
	
    #FUNCTION TO DISPLAY SETUP COST PAGE FOR INTERNATIONAL POSTAGE  
    function m_zoneSetupcostHome()
    {
        $this->ObTpl=new template();
        $this->ObTpl->set_file("TPL_SETUPCOST_FILE",$this->settingsTemplate);
        $this->ObTpl->set_var("GRAPHICSMAINPATH", GRAPHICS_PATH);
        $this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
        #SETTING BLOCKS
        
        $this->ObTpl->set_block("TPL_SETUPCOST_FILE","TPL_ZONECOSTSETUP_BLK","zonecostsetup_blk");
        $this->ObTpl->set_block("TPL_ZONECOSTSETUP_BLK","TPL_MAINSETUP_BLK","mainsetup_blk");
        $this->ObTpl->set_block("TPL_MAINSETUP_BLK","TPL_RANGELIST_BLK","rangelist_blk");
        $this->ObTpl->set_block("TPL_RANGELIST_BLK","TPL_DELETELINK_BLK","deletelink_blk");
        
        
        
        $this->ObTpl->set_var("mainsetup_blk","");
        $this->ObTpl->set_var("rangelist_blk","");
        $this->ObTpl->set_var("deletelink_blk","");
        
        $this->ObTpl->set_var("TPL_VAR_MINWEIGHTEDIT","");
        $this->ObTpl->set_var("TPL_VAR_MAXWEIGHTEDIT","");
        $this->ObTpl->set_var("TPL_VAR_COSTEDIT","");
        $this->ObTpl->set_var("TPL_VAR_SPECIALDELIVERYCOSTEDIT","");
        $this->ObTpl->set_var("TPL_VAR_CURRENCY",CONST_CURRENCY); //locloc
        
        $this->ObTpl->set_var("TPL_VAR_ERRORMSG","");
        
        $this->obDb->query = "SELECT vZonename,vCountryId FROM ".POSTAGEZONE." WHERE iZoneId=".$this->request['id']; 
        $zonerow = $this->obDb->fetchQuery();
        
        $this->obDb->query = "SELECT vCountryName FROM ".COUNTRY." WHERE iCountryId_PK IN (".$zonerow[0]->vCountryId.")";
        $countryarray=$this->obDb->fetchQuery();
        $countrycount = $this->obDb->record_count;
        
        $countriesname = "";    
        if ($zonerow[0]->vZonename!='Rest of the World')
        {
            for ($k=0;$k<$countrycount;$k++)
            {
            $countriesname.= $countryarray[$k]->vCountryName.", ";      
            }
        
        $countriesname=substr_replace($countriesname ,"",-2);
        }else {
            $countriesname= "Other countries";
        }
        
        $this->ObTpl->set_var("TPL_VAR_ZONEID",$this->request['id']);
        $this->ObTpl->set_var("TPL_VAR_ZONENAME",$zonerow[0]->vZonename);
        $this->ObTpl->set_var("TPL_VAR_COUNTRIES",$countriesname);
                
        $this->obDb->query = "SELECT * FROM ".POSTAGEZONEDETAILS. " WHERE iZoneId='".$this->request['id']."' ORDER BY fMinWeight ";
        
        $rangerow = $this->obDb->fetchQuery();
        $rangecount = $this->obDb->record_count;
        
        
        if ($rangecount==0){
            $this->ObTpl->set_var("TPL_VAR_AUTOMINWEIGHT","0");
        }
        else {
            $this->ObTpl->set_var("TPL_VAR_AUTOMINWEIGHT",$rangerow[$rangecount-1]->fMaxWeight+1);  
        }
        
        for ($i=0;$i<$rangecount;$i++)
        {
                $this->ObTpl->set_var("TPL_VAR_MINWEIGHT",$rangerow[$i]->fMinweight);
                $this->ObTpl->set_var("TPL_VAR_MAXWEIGHT",$rangerow[$i]->fMaxWeight);
                $this->ObTpl->set_var("TPL_VAR_COST",$rangerow[$i]->fCost);
                $this->ObTpl->set_var("TPL_VAR_SPECIALDELIVERYCOST",$rangerow[$i]->fSpecialDelivery);
                $this->ObTpl->set_var("TPL_VAR_RANGEID",$rangerow[$i]->iRangeId);
                
                if ($i==$rangecount-1){
                    $this->ObTpl->parse("deletelink_blk","TPL_DELETELINK_BLK");
                }
                
                $this->ObTpl->parse("rangelist_blk","TPL_RANGELIST_BLK",true);
        } 
        
        if (isset($this->request['edit'])){
            $this->ObTpl->set_var("TPL_VAR_ACTIONURL",SITE_URL."admin/adminindex.php?action=settings.updaterange&rangeid=".$this->request['rangeid']);
            
            $this->obDb->query = "SELECT * FROM ".POSTAGEZONEDETAILS. " WHERE iZoneId='".$this->request['id']."' AND iRangeId='".$this->request['rangeid']."'";
            $rangerowedit = $this->obDb->fetchQuery();
                    
            $this->ObTpl->set_var("TPL_VAR_AUTOMINWEIGHT",$rangerowedit[0]->fMinweight);
            $this->ObTpl->set_var("TPL_VAR_MAXWEIGHTEDIT",$rangerowedit[0]->fMaxWeight);
            $this->ObTpl->set_var("TPL_VAR_COSTEDIT",$rangerowedit[0]->fCost);
            $this->ObTpl->set_var("TPL_VAR_SPECIALDELIVERYCOSTEDIT",$rangerowedit[0]->fSpecialDelivery);
            $this->ObTpl->set_var("TPL_VAR_BUTTON","Save Update");
            
        }else {
            $this->ObTpl->set_var("TPL_VAR_ACTIONURL",SITE_URL."admin/adminindex.php?action=settings.addrange");
            $this->ObTpl->set_var("TPL_VAR_BUTTON","Add Range");
        }
        
        $this->ObTpl->parse("mainsetup_blk","TPL_MAINSETUP_BLK");
    
    return($this->ObTpl->parse("zonecostsetup_blk","TPL_ZONECOSTSETUP_BLK"));
    }
	
    #FUNCTION TO DISPLAY SETUP COST PAGE FOR CITY POSTAGE  
    function m_citySetupcostHome()
    {
        $this->ObTpl=new template();
        $this->ObTpl->set_file("TPL_SETUPCOST_FILE",$this->settingsTemplate);
        $this->ObTpl->set_var("GRAPHICSMAINPATH", GRAPHICS_PATH);
        $this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
        #SETTING BLOCKS
        
        $this->ObTpl->set_block("TPL_SETUPCOST_FILE","TPL_CITYCOSTSETUP_BLK","citycostsetup_blk");
        $this->ObTpl->set_block("TPL_CITYCOSTSETUP_BLK","TPL_MAINSETUP_BLK","mainsetup_blk");
        $this->ObTpl->set_block("TPL_MAINSETUP_BLK","TPL_RANGELIST_BLK","rangelist_blk");
        $this->ObTpl->set_block("TPL_RANGELIST_BLK","TPL_DELETELINK_BLK","deletelink_blk");
        
        
        
        $this->ObTpl->set_var("mainsetup_blk","");
        $this->ObTpl->set_var("rangelist_blk","");
        $this->ObTpl->set_var("deletelink_blk","");
        
        $this->ObTpl->set_var("TPL_VAR_MINWEIGHTEDIT","");
        $this->ObTpl->set_var("TPL_VAR_MAXWEIGHTEDIT","");
        $this->ObTpl->set_var("TPL_VAR_COSTEDIT","");
        $this->ObTpl->set_var("TPL_VAR_SPECIALDELIVERYCOSTEDIT","");
        $this->ObTpl->set_var("TPL_VAR_CURRENCY",CONST_CURRENCY); //locloc
        
        $this->ObTpl->set_var("TPL_VAR_ERRORMSG","");
        
        $this->obDb->query = "SELECT vCountryId,vStateId FROM ".POSTAGECITY." WHERE iCityId=".$this->request['id']; 
        $cityrow = $this->obDb->fetchQuery();
        
        $this->obDb->query = "SELECT vCountryName FROM ".COUNTRY." WHERE iCountryId_PK = '".$cityrow[0]->vCountryId."'";
        $countryarray=$this->obDb->fetchQuery();
        
        $this->obDb->query = "SELECT vStateName FROM ".STATES." WHERE iStateId_PK = '".$cityrow[0]->vStateId."'";
        $statearray=$this->obDb->fetchQuery();
 
        if ($cityrow[0]->vCountryId=='0') {
            $this->ObTpl->set_var("TPL_VAR_STATENAME","Any");
            $this->ObTpl->set_var("TPL_VAR_COUNTRY","Any");
        } elseif ($cityrow[0]->vStateId == '0') {      
            $this->ObTpl->set_var("TPL_VAR_STATENAME","Any");
            $this->ObTpl->set_var("TPL_VAR_COUNTRY",$countryarray[0]->vCountryName);
        } else {    
            $this->ObTpl->set_var("TPL_VAR_STATENAME",$statearray[0]->vStateName);
            $this->ObTpl->set_var("TPL_VAR_COUNTRY",$countryarray[0]->vCountryName);
        }
        
        $this->ObTpl->set_var("TPL_VAR_CITYID",$this->request['id']);
        
                
        $this->obDb->query = "SELECT * FROM ".POSTAGECITYDETAILS. " WHERE fCityId='".$this->request['id']."' ORDER BY fMinWeight ";
        
        $rangerow = $this->obDb->fetchQuery();
        $rangecount = $this->obDb->record_count;
        
        
        if ($rangecount==0){
            $this->ObTpl->set_var("TPL_VAR_AUTOMINWEIGHT","0");
        }
        else {
            $this->ObTpl->set_var("TPL_VAR_AUTOMINWEIGHT",$rangerow[$rangecount-1]->fMaxWeight+1);  
        }
        
        for ($i=0;$i<$rangecount;$i++)
        {
                $this->ObTpl->set_var("TPL_VAR_MINWEIGHT",$rangerow[$i]->fMinweight);
                $this->ObTpl->set_var("TPL_VAR_MAXWEIGHT",$rangerow[$i]->fMaxWeight);
                $this->ObTpl->set_var("TPL_VAR_COST",$rangerow[$i]->fCost);
                $this->ObTpl->set_var("TPL_VAR_SPECIALDELIVERYCOST",$rangerow[$i]->fSpecialDelivery);
                $this->ObTpl->set_var("TPL_VAR_RANGEID",$rangerow[$i]->iRangeId);
                
                if ($i==$rangecount-1){
                    $this->ObTpl->parse("deletelink_blk","TPL_DELETELINK_BLK");
                }
                
                $this->ObTpl->parse("rangelist_blk","TPL_RANGELIST_BLK",true);
        } 
        
        if (isset($this->request['edit'])){
            $this->ObTpl->set_var("TPL_VAR_ACTIONURL",SITE_URL."admin/adminindex.php?action=settings.updatecityrange&rangeid=".$this->request['rangeid']);
            
            $this->obDb->query = "SELECT * FROM ".POSTAGECITYDETAILS. " WHERE fCityId='".$this->request['id']."' AND iRangeId='".$this->request['rangeid']."'";
            $rangerowedit = $this->obDb->fetchQuery();
                    
            $this->ObTpl->set_var("TPL_VAR_AUTOMINWEIGHT",$rangerowedit[0]->fMinweight);
            $this->ObTpl->set_var("TPL_VAR_MAXWEIGHTEDIT",$rangerowedit[0]->fMaxWeight);
            $this->ObTpl->set_var("TPL_VAR_COSTEDIT",$rangerowedit[0]->fCost);
            $this->ObTpl->set_var("TPL_VAR_SPECIALDELIVERYCOSTEDIT",$rangerowedit[0]->fSpecialDelivery);
            $this->ObTpl->set_var("TPL_VAR_BUTTON","Save Update");
            
        }else {
            $this->ObTpl->set_var("TPL_VAR_ACTIONURL",SITE_URL."admin/adminindex.php?action=settings.addcityrange");
            $this->ObTpl->set_var("TPL_VAR_BUTTON","Add Range");
        }
        
        $this->ObTpl->parse("mainsetup_blk","TPL_MAINSETUP_BLK");
    
    return($this->ObTpl->parse("citycostsetup_blk","TPL_CITYCOSTSETUP_BLK"));
    }

	#FUNCTION TO DISPLAY EDIT FORM FOR ALL POSTAGE DETAILS
	function m_postageEditor()
	{
		#INTIALIZING TEMPLATES
		$this->ObTpl=new template();
	
		$this->ObTpl->set_file("TPL_SETTING_FILE", $this->settingsTemplate);
		$this->ObTpl->set_var("GRAPHICSMAINPATH", GRAPHICS_PATH);
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		$this->ObTpl->set_var("TPL_VAR_CURRENCY",CONST_CURRENCY);
		#SETTING BLOCKS
		$this->ObTpl->set_block("TPL_SETTING_FILE","TPL_FLATRATE_BLK","flatrate_blk");
		$this->ObTpl->set_block("TPL_SETTING_FILE","TPL_PERCENTAGE_BLK","percentage_blk");
		$this->ObTpl->set_block("TPL_SETTING_FILE","TPL_PERITEM_BLK","peritem_blk");
		$this->ObTpl->set_block("TPL_SETTING_FILE","TPL_POSTAGECODES_BLK","codes_blk");
		$this->ObTpl->set_block("TPL_POSTAGECODES_BLK","TPL_CODE_BLK","dspcodes_blk");
		
		$this->ObTpl->set_block("TPL_SETTING_FILE","TPL_ZONE_BLK","zone_blk");
		$this->ObTpl->set_block("TPL_ZONE_BLK","TPL_COUNTRIES_BLK","coutries_blk");
		$this->ObTpl->set_block("TPL_ZONE_BLK","TPL_ZONELIST_BLK","zonelist_blk");
		$this->ObTpl->set_block("TPL_ZONE_BLK","TPL_RESTOFTHEWORLD_BLK","restoftheworld_blk");
        
        $this->ObTpl->set_block("TPL_SETTING_FILE","TPL_CITY_BLK","city_blk");
        $this->ObTpl->set_block("TPL_CITY_BLK","TPL_COUNTRIES_BLK","countries_blk");
        $this->ObTpl->set_block("TPL_CITY_BLK","TPL_STATES_BLK","states_blk");
        $this->ObTpl->set_block("TPL_CITY_BLK","TPL_CITYLIST_BLK","citylist_blk");
        $this->ObTpl->set_block("TPL_CITY_BLK","TPL_RESTOFTHEWORLD_BLK","restoftheworld_blk");
        $this->ObTpl->set_block("TPL_CITY_BLK","TPL_RESTOFTHECOUNTRY_BLK","restofthecountry_blk");
        $this->ObTpl->set_block("TPL_CITY_BLK","TPL_RESTOFTHESTATE_BLK","restofthestate_blk");
			
		$this->ObTpl->set_block("TPL_SETTING_FILE","TPL_POSTAGERANGES_BLK","range_blk");
		$this->ObTpl->set_block("TPL_POSTAGERANGES_BLK","TPL_RANGE_BLK","dsprange_blk");
		$this->ObTpl->set_block("TPL_SETTING_FILE","TPL_SPECIALRATE_BLK","special_blk");
		$this->ObTpl->set_block("TPL_SPECIALRATE_BLK","TPL_SPECIAL_BLK","dspspecial_blk");
		$this->ObTpl->set_block("TPL_SETTING_FILE","TPL_POSTAGEBYWEIGHT_BLK","pweight_blk");
		
		#INTIALIZING
		$this->ObTpl->set_var("flatrate_blk","");
		$this->ObTpl->set_var("percentage_blk","");
		$this->ObTpl->set_var("peritem_blk","");
		$this->ObTpl->set_var("codes_blk","");
		$this->ObTpl->set_var("range_blk","");
		$this->ObTpl->set_var("special_blk","");
		$this->ObTpl->set_var("dspcodes_blk","");
		$this->ObTpl->set_var("dsprange_blk","");
		$this->ObTpl->set_var("dspspecial_blk","");
		$this->ObTpl->set_var("pweight_blk","");
		$this->ObTpl->set_var("zone_blk","");
		$this->ObTpl->set_var("zonelist_blk","");
		$this->ObTpl->set_var("restoftheworld_blk","");
        $this->ObTpl->set_var("city_blk", "");
        $this->ObTpl->set_var("citylist_blk", "");
        $this->ObTpl->set_var("restoftheworld_blk", "");
        $this->ObTpl->set_var("restofthecountry_blk", "");
        $this->ObTpl->set_var("restofthestate_blk", "");
		
		$this->ObTpl->set_var("TPL_VAR_ERRORMSG","");
		$this->ObTpl->set_var("TPL_VAR_SELECTED","");
		
		$this->request['mode']=$this->libFunc->ifSet($this->request,'mode',"");

		#DB QUERY
		$this->obDb->query="SELECT D.vDescription,vField1,vField2,vField3,fBaseRate,iDefaultHighest,iPostDescId_PK  FROM ".POSTAGE." P, ".POSTAGEDETAILS." D WHERE iPostId_PK=iPostId_FK AND  vKey='".$this->request['mode']."' ORDER BY iPostDescId_PK";
	
		$resPostage=$this->obDb->fetchQuery();
		$rsCount=$this->obDb->record_count;
		switch($this->request['mode']){
				case "flat":
					$this->ObTpl->set_var("TPL_VAR_FLATRATE",$resPostage[0]->vField1);
					$this->ObTpl->parse("flatrate_blk","TPL_FLATRATE_BLK");
				break;
				case "percent":
					$this->ObTpl->set_var("TPL_VAR_BASERATE",$resPostage[0]->fBaseRate);
				$this->ObTpl->set_var("TPL_VAR_PERCENTRATE",$resPostage[0]->vField1);
					$this->ObTpl->parse("percentage_blk","TPL_PERCENTAGE_BLK");
				break;
				case "peritem":
					$this->ObTpl->set_var("TPL_VAR_FIRSTITEM",number_format($resPostage[0]->vField1,2));
					$this->ObTpl->set_var("TPL_VAR_ADDITIONAL",number_format($resPostage[0]->vField2,2));
					$this->ObTpl->parse("peritem_blk","TPL_PERITEM_BLK");
				break;
			
				case "codes":
					$this->ObTpl->set_var("TPL_VAR_BASERATE",$resPostage[0]->fBaseRate);
				    if($resPostage[0]->iDefaultHighest){
					   $this->ObTpl->set_var('TPL_VAR_SEL_DEFAULTHIGHEST','checked');
					}else{
					   $this->ObTpl->set_var('TPL_VAR_SEL_DEFAULTHIGHEST','');
					}
					for($i=0;$i<$rsCount;$i++)
					{
						$this->ObTpl->set_var("TPL_VAR_COUNT",$i+1);
						$this->ObTpl->set_var("TPL_VAR_ID",$resPostage[$i]->iPostDescId_PK);
						$this->ObTpl->set_var("TPL_VAR_DESCRIPTION",$resPostage[$i]->vDescription);
						$this->ObTpl->set_var("TPL_VAR_FIELD1",round($resPostage[$i]->vField1));
						$this->ObTpl->set_var("TPL_VAR_FIELD2",number_format($resPostage[$i]->vField2,2));
						$this->ObTpl->set_var("TPL_VAR_FIELD3",$resPostage[$i]->vField3);
						$this->ObTpl->parse("dspcodes_blk","TPL_CODE_BLK",true);
					}
					$this->ObTpl->parse("codes_blk","TPL_POSTAGECODES_BLK");
				break;
				case "range":
					$this->ObTpl->set_var("TPL_VAR_BASERATE",$resPostage[0]->fBaseRate);
					for($i=0;$i<$rsCount;$i++)
					{
						$this->ObTpl->set_var("TPL_VAR_COUNT",$i+1);
						$this->ObTpl->set_var("TPL_VAR_ID",$resPostage[$i]->iPostDescId_PK  );
						$this->ObTpl->set_var("TPL_VAR_DESCRIPTION",$resPostage[$i]->vDescription);
						$this->ObTpl->set_var("TPL_VAR_FIELD1",$resPostage[$i]->vField1);
						$this->ObTpl->set_var("TPL_VAR_FIELD2",$resPostage[$i]->vField2);
						$this->ObTpl->set_var("TPL_VAR_FIELD3",number_format($resPostage[$i]->vField3,2));
						$this->ObTpl->parse("dsprange_blk","TPL_RANGE_BLK",true);
					}
					$this->ObTpl->parse("range_blk","TPL_POSTAGERANGES_BLK");
				break;
				case "special":
					$this->ObTpl->set_var("TPL_VAR_BASERATE",$resPostage[0]->fBaseRate);
					for($i=0;$i<$rsCount;$i++)
					{
						$this->ObTpl->set_var("TPL_VAR_COUNT",$i+1);
						$this->ObTpl->set_var("TPL_VAR_ID",$resPostage[$i]->iPostDescId_PK  );
						$this->ObTpl->set_var("TPL_VAR_DESCRIPTION",$resPostage[$i]->vDescription);
						$this->ObTpl->set_var("TPL_VAR_FIELD1",number_format($resPostage[$i]->vField1,2));
						$this->ObTpl->set_var("TPL_VAR_FIELD2",number_format($resPostage[$i]->vField2,2));
						$this->ObTpl->set_var("TPL_VAR_FIELD3",number_format($resPostage[$i]->vField3,2));
						$this->ObTpl->parse("dspspecial_blk","TPL_SPECIAL_BLK",true);
					}
					$this->ObTpl->parse("special_blk","TPL_SPECIALRATE_BLK");
				break;
				case "pweight":
				$this->ObTpl->set_var("TPL_VAR_POSTAGEBY_WEIGHTRATE",$resPostage[0]->vField1);
				$this->ObTpl->parse("pweight_blk","TPL_POSTAGEBYWEIGHT_BLK");
				break;
				
				case "zones":
				$this->dsp_postagehome();
				break;
                
                case "cities":
                $this->dsp_citySetupHome();
                break;
				
				default:
					$this->libFunc->m_mosRedirect(SITE_URL."admin/adminindex.php?action=settings.postageHome");
				break;
			}#END 

		return($this->ObTpl->parse("return","TPL_SETTING_FILE"));
	}#END FUNCTION
	
    #FUNCTION TO DISPLAY CITY POSTAGE HOME
    function dsp_citySetuphome() // for edit and add new
    {



        if (isset($_SESSION['postageerror']))
        {
          switch( $_SESSION['postageerror'])
            {
                case 1:
                $this->ObTpl->set_var("TPL_VAR_ERRORMSG","City name already exists for this state.");    
                unset ($_SESSION['postageerror']);
                break;
            
                case 2:
                $this->ObTpl->set_var("TPL_VAR_ERRORMSG","Please select the country associate with this.");    
                unset ($_SESSION['postageerror']);
                break;
                
                case 3:
                $this->ObTpl->set_var("TPL_VAR_ERRORMSG","Please input a City.");   
                unset ($_SESSION['postageerror']);
                break;
                
                case 4:
                $this->ObTpl->set_var("TPL_VAR_ERRORMSG","A rest of world price has already been set.");   
                unset ($_SESSION['postageerror']);
                break;
                
                case 5:
                $this->ObTpl->set_var("TPL_VAR_ERRORMSG","A rest of country price has already been set.");   
                unset ($_SESSION['postageerror']);
                break;
                
                case 6:
                $this->ObTpl->set_var("TPL_VAR_ERRORMSG","A rest of state price has already been set.");   
                unset ($_SESSION['postageerror']);
                break;
            }
        }
    
        $this->obDb->query = "SELECT iCountryId_PK, vCountryName, vShortName FROM  ".COUNTRY." ORDER BY iSortFlag,vShortName";
        
        $row_country = $this->obDb->fetchQuery();
        $row_country_count = $this->obDb->record_count;
        
        $this->obDb->query = "SELECT iStateId_PK, vStateName, vShortName FROM ".STATES." ORDER BY iStateId_PK";
        $row_state = $this->obDb->fetchQuery();
        $row_state_count = $this->obDb->record_count;
    // If in edit mode, find which countries existing in this zone
        if (isset($this->request['edit'])){

        $this->obDb->query= "SELECT * FROM ".POSTAGECITY." WHERE iCityId=".$this->request['id'];
        
        $selectedcity = $this->obDb->fetchQuery();
        $selectedcountries = explode(",",$selectedcity[0]->vCountryId);
        $selectedstate = explode(",",$selectedcity[0]->vStateId);
        }
    // to display states list
    if (isset($this->request['edit'])) {
        $this->obDb->query = "SELECT iStateId_PK, vStateName, vShortName FROM ".STATES." WHERE iStateId_PK = '".$selectedcity[0]->vStateId."'";
        $info = $this->obDb->fetchQuery();
        $this->ObTpl->set_var("TPL_VAR_STATENAME",$info[0]->vStateName); 
        $this->ObTpl->set_var("TPL_VAR_STATEID",$selectedcity[0]->vStateId);
        $this->ObTpl->parse("states_blk","TPL_STATES_BLK",true);
    }
        for ($j=0;$j<$row_state_count;$j++)  
                {
                    if (isset($this->request['edit']))  // if in edit mode, highlight existing countries
                    {
                       for($k=0;$k<count($selectedstate);$k++)
                        {
                             if ($selectedstate[$k]==$row_state[$j]->iStateId_PK)
                             {
                             $exclude = "true";     
                             }
                        }
                    }#end if
                    if (empty($exclude)) {
                    $this->ObTpl->set_var("TPL_VAR_STATENAME",$row_state[$j]->vStateName); 
                    $this->ObTpl->set_var("TPL_VAR_STATEID",$row_state[$j]->iStateId_PK);
                    $this->ObTpl->parse("states_blk","TPL_STATES_BLK",true);
                    }
                    $exclude = "";
                }
                
    // to display counties list
            for ($j=0;$j<$row_country_count;$j++)  
                {
                    $this->ObTpl->set_var ("TPL_VAR_SELECTED","");
                    if (isset($this->request['edit']))  // if in edit mode, highlight existing countries
                    {
                       for($k=0;$k<count($selectedcountries);$k++)
                        {
                             if ($selectedcountries[$k]==$row_country[$j]->iCountryId_PK)
                             {
                             $this->ObTpl->set_var ("TPL_VAR_SELECTED","SELECTED");     
                             }
                        }
                    }#end if
                    $this->ObTpl->set_var("TPL_VAR_COUTRYNAME",$row_country[$j]->vCountryName); 
                    $this->ObTpl->set_var("TPL_VAR_COUNTRYID",$row_country[$j]->iCountryId_PK);
                    $this->ObTpl->parse("countries_blk","TPL_COUNTRIES_BLK",true);
                }
    //------------------------------------------- TO DISPLAY THE LIST OF CITIES ADDED
        $this->obDb->query = "SELECT * FROM  ".POSTAGECITY." ORDER BY iCityId ASC"; 
        $postagecity = $this->obDb->fetchQuery();
        $postagecitycount = $this->obDb->record_count;
        $restofworld = 0;
    if ($postagecitycount>0){
        
        for ($i=0;$i<$postagecitycount;$i++)
        {
        $this->obDb->query = "SELECT vCountryName FROM ".COUNTRY." WHERE iCountryId_PK IN (".$postagecity[$i]->vCountryId.")";
    
        $countryshort = $this->obDb->fetchQuery();
        $countryshortcount = $this->obDb->record_count;
        $shortname = "";    
            
            for ($k=0;$k<$countryshortcount;$k++)
            {
            $shortname.= $countryshort[$k]->vCountryName.", ";      
            }
        $shortname=substr_replace($shortname ,"",-2);
        
        
        if ($postagecity[$i]->vStateId=='0'){
        $restofworld = 1;
        }
        
        $this->obDb->query = "SELECT vStateName FROM ".STATES." WHERE `iStateId_PK` = '".$postagecity[$i]->vStateId."'";
        $stateinfo = $this->obDb->fetchQuery();
        
        if ($postagecity[$i]->vStateId == "0") {
            $this->ObTpl->set_var("TPL_VAR_STATENAME","Any");
            $this->ObTpl->set_var("TPL_VAR_COUNTRYLIST",$shortname);
        } elseif ($postagecity[$i]->vCountryId == "0") {
            $this->ObTpl->set_var("TPL_VAR_STATENAME","Any");
            $this->ObTpl->set_var("TPL_VAR_COUNTRYLIST","Any");
        } else { 
            $this->ObTpl->set_var("TPL_VAR_STATENAME",$stateinfo[0]->vStateName);
            $this->ObTpl->set_var("TPL_VAR_COUNTRYLIST",$shortname);
        }
        $this->ObTpl->set_var("TPL_VAR_CITYID",$postagecity[$i]->iCityId);
        $this->ObTpl->parse("citylist_blk","TPL_CITYLIST_BLK",true);
        }
    }
    
    if ( $restofworld == 1){
    $this->ObTpl->parse("restoftheworld_blk","");
    } else {
    $this->ObTpl->parse("restoftheworld_blk","TPL_RESTOFTHEWORLD_BLK");
    }
    
    $this->ObTpl->parse("restofthecountry_blk","TPL_RESTOFTHECOUNTRY_BLK");
    //$this->ObTpl->parse("restofthestate_blk","TPL_RESTOFTHESTATE_BLK");
      $this->ObTpl->parse("restofthestate_blk","");
       //$this->ObTpl->parse("restofthecountry_blk","");
    //------------------------------------------------------------------------------------- 
        if(!isset($this->request['edit'])){
            $this->ObTpl->set_var("TPL_VAR_ACTION",SITE_URL."admin/adminindex.php?action=settings.addCity");
            $this->ObTpl->set_var("TPL_VAR_CITYTEXT","");
            $this->ObTpl->set_var("TPL_VAR_MODE","Add State");
        }else {
            $this->ObTpl->set_var("TPL_VAR_ACTION",SITE_URL."admin/adminindex.php?action=settings.addCity&update=1&id=".$this->request['id']);
            $this->ObTpl->set_var("TPL_VAR_MODE","Update State");
        }
        $this->ObTpl->parse("city_blk","TPL_CITY_BLK"); 
    }# END FUNCTION
    
        #FUNCTION TO DISPLAY INTERNATION POSTAGE HOME
    function dsp_postagehome() // for edit and add new
    {

        if (isset($_SESSION['postageerror']))
        {
          switch( $_SESSION['postageerror'])
            {
                case 1:
                $this->ObTpl->set_var("TPL_VAR_ERRORMSG","Zone name already exist");    
                unset ($_SESSION['postageerror']);
                break;
            
                case 2:
                $this->ObTpl->set_var("TPL_VAR_ERRORMSG","Please select contries associate with this zone");    
                unset ($_SESSION['postageerror']);
                break;
                
                case 3:
                $this->ObTpl->set_var("TPL_VAR_ERRORMSG","Please input a zone name");   
                unset ($_SESSION['postageerror']);
                break;
                
                case 4: 
                $this->ObTpl->set_var("TPL_VAR_ERRORMSG","Some of these countries already have been associated with an existing zone"); 
                unset ($_SESSION['postageerror']);
                break;
            
                case 5: 
                $this->ObTpl->set_var("TPL_VAR_ERRORMSG","You must create manually at least 1 zone before using this function");    
                unset ($_SESSION['postageerror']);
                break;
            }
        }
    
        $this->obDb->query = "SELECT iCountryId_PK, vCountryName, vShortName FROM  ".COUNTRY." ORDER BY iSortFlag,vShortName";
        
        $row_country = $this->obDb->fetchQuery();
        $row_country_count = $this->obDb->record_count;
        
    // If in edit mode, find which countries existing in this zone
        if (isset($this->request['edit'])){

        $this->obDb->query= "SELECT * FROM ".POSTAGEZONE." WHERE iZoneId=".$this->request['id'];
        
        $selectedzone = $this->obDb->fetchQuery();
        $selectedcountries = explode(",",$selectedzone[0]->vCountryId);
        }
    // to display countries list
        for ($j=0;$j<$row_country_count;$j++)  
                {
                    $this->ObTpl->set_var ("TPL_VAR_SELECTED","");
                    if (isset($this->request['edit']))  // if in edit mode, highlight existing countries
                    {
                       for($k=0;$k<count($selectedcountries);$k++)
                        {
                             if ($selectedcountries[$k]==$row_country[$j]->iCountryId_PK)
                             {
                             $this->ObTpl->set_var ("TPL_VAR_SELECTED","SELECTED");     
                             }
                        }
                    }#end if
                    $this->ObTpl->set_var("TPL_VAR_COUTRYNAME",$row_country[$j]->vCountryName); 
                    $this->ObTpl->set_var("TPL_VAR_COUNTRYID",$row_country[$j]->iCountryId_PK);
                    $this->ObTpl->parse("coutries_blk","TPL_COUNTRIES_BLK",true);
                }
        
    //------------------------------------------- TO DISPLAY THE LIST OF ZONES ADDED
        $this->obDb->query = "SELECT * FROM  ".POSTAGEZONE." ORDER BY iZoneId ASC"; 
        $postagezone = $this->obDb->fetchQuery();
        $postagezonecount = $this->obDb->record_count;
        $restofworld = 0;
    if ($postagezonecount>0){
        $restofworld = 0;
        for ($i=0;$i<$postagezonecount;$i++)
        {
        $this->obDb->query = "SELECT vCountryName FROM ".COUNTRY." WHERE iCountryId_PK IN (".$postagezone[$i]->vCountryId.")";
    
        $countryshort = $this->obDb->fetchQuery();
        $countryshortcount = $this->obDb->record_count;
        $shortname = "";    
            
            for ($k=0;$k<$countryshortcount;$k++)
            {
            $shortname.= $countryshort[$k]->vCountryName.", ";      
            }
        $shortname=substr_replace($shortname ,"",-2);
        
        
        if ($postagezone[$i]->vZonename=='Rest of the World'){
        $restofworld = 1;
        $shortname = "Other countries"; 
        }
        $this->ObTpl->set_var("TPL_VAR_ZONENAME",$postagezone[$i]->vZonename);  
        $this->ObTpl->set_var("TPL_VAR_COUNTRYLIST",$shortname);
        $this->ObTpl->set_var("TPL_VAR_ZONEID",$postagezone[$i]->iZoneId);
        
        $this->ObTpl->parse("zonelist_blk","TPL_ZONELIST_BLK",true);
        }
    }
    
    if ($restofworld ==1)
    {
    $this->ObTpl->parse("restoftheworld_blk","");   
    }else{
    $this->ObTpl->parse("restoftheworld_blk","TPL_RESTOFTHEWORLD_BLK");
    }   
    //------------------------------------------------------------------------------------- 
        if(!isset($this->request['edit'])){
            $this->ObTpl->set_var("TPL_VAR_ACTION",SITE_URL."admin/adminindex.php?action=settings.addZone");
            $this->ObTpl->set_var("TPL_VAR_ZONETEXT","");
            $this->ObTpl->set_var("TPL_VAR_MODE","Add Zone");
        }else {
            $this->obDb->query = "SELECT vZonename FROM ".POSTAGEZONE." WHERE iZoneId=".$this->request['id'];
            $zonename= $this->obDb->fetchQuery();
            $this->ObTpl->set_var("TPL_VAR_ZONETEXT",$zonename[0]->vZonename);
            $this->ObTpl->set_var("TPL_VAR_ACTION",SITE_URL."admin/adminindex.php?action=settings.addZone&update=1&id=".$this->request['id']);
            $this->ObTpl->set_var("TPL_VAR_MODE","Update Zone");
        }
        $this->ObTpl->parse("zone_blk","TPL_ZONE_BLK"); 
    }# END FUNCTION
	
	
	function getValue($val,$case)
	{
		if($_POST)
		{
			$val=$this->request[$case];
			
		}
		return $val;
	}

	#FUNCTION TO DISPLAY SYSTEM SETTINGS
	function m_systemSettings()
	{
		#INTIALIZING TEMPLATES
		$this->ObTpl=new template();
		$this->ObTpl->set_var("GRAPHICSMAINPATH", GRAPHICS_PATH);
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		$this->ObTpl->set_file("TPL_SETTING_FILE", $this->settingsTemplate);
		$this->ObTpl->set_var("TPL_VAR_MSG","");
		
		$this->ObTpl->set_block("TPL_SETTING_FILE","TPL_DSPMSG_BLK","dspmsg_blk");
		
		$this->ObTpl->set_var("dspmsg_blk","");
		$this->ObTpl->set_block("TPL_SETTING_FILE","COMPUTER_ENCODING_BLK","COMPUTER_ENCODING_BLKs");
		$this->ObTpl->set_var("COMPUTER_ENCODING_BLKs",'');

		if(isset($this->request['msg']))
		{
			if($this->request['msg']==1)
			{
				$this->ObTpl->set_var("TPL_VAR_MSG",MSG_FILE_OPEN);
				$this->ObTpl->parse("dspmsg_blk","TPL_DSPMSG_BLK");
			}
			elseif($this->request['msg']==2)
			{
				$this->ObTpl->set_var("TPL_VAR_MSG",MSG_FILE_NOWRITE);
				$this->ObTpl->parse("dspmsg_blk","TPL_DSPMSG_BLK");
			}
			elseif($this->request['msg']==3)
			{
				$this->ObTpl->set_var("TPL_VAR_MSG",MSG_FILE_NOWRITABLE);
				$this->ObTpl->parse("dspmsg_blk","TPL_DSPMSG_BLK");
			}
			elseif($this->request['msg']==4)
			{
				$this->ObTpl->set_var("TPL_VAR_MSG",MSG_SYSTEMSETTING_UPDATED);
				$this->ObTpl->parse("dspmsg_blk","TPL_DSPMSG_BLK");
			}
		}
		if($this->err==1)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",$this->errMsg);
			$this->ObTpl->parse("dspmsg_blk","TPL_DSPMSG_BLK");
		}


		$this->obDb->query = "SELECT * FROM ".SITESETTINGS;
		$row_setting=$this->obDb->fetchQuery();
		$rCount=$this->obDb->record_count;
		
		for($i=0;$i<$rCount;$i++)
		{
			switch($row_setting[$i]->vDatatype)
			{
				
				case "dbServer":
					$this->ObTpl->set_var("TPL_VAR_DBSERVER",$this->getValue($row_setting[$i]->vSmalltext,"dbServer"));
				break;
				case "dsn":
					$this->ObTpl->set_var("TPL_VAR_DSN",$this->getValue($row_setting[$i]->vSmalltext,"dsn"));
				break;
				case "dbType":
					$this->ObTpl->set_var("TPL_VAR_DBTYPE",$this->getValue($row_setting[$i]->vSmalltext,"dbType"));
				break;
				case "dbPrefix":
					$this->ObTpl->set_var("TPL_VAR_DBPREFIX",$this->libFunc->m_displayContent($this->getValue($row_setting[$i]->vSmalltext,"dbPrefix")));
				break;
				case "dbUserName":
					$this->ObTpl->set_var("TPL_VAR_USERNAME",$this->libFunc->m_displayContent($this->getValue($row_setting[$i]->vSmalltext,"dbUserName")));
				break;
				
				case "dbPassword":
					$this->ObTpl->set_var("TPL_VAR_PASSWORD",$this->getValue($row_setting[$i]->vSmalltext,"dbPassword"));
				break;
				case "SITEURL":
				$this->ObTpl->set_var("TPL_VAR_URLROOT",$this->getValue($row_setting[$i]->vSmalltext,"SITEURL"));
				break;

				case "SITEPATH":
				$row_setting[$i]->vSmalltext=addslashes($row_setting[$i]->vSmalltext);
				$this->ObTpl->set_var("TPL_VAR_SITEDIRECTORY",$this->libFunc->m_displayContent($this->getValue($row_setting[$i]->vSmalltext,"SITEPATH")));
				break;
	
				case "LicenseKey":
					$this->ObTpl->set_var("TPL_VAR_LICENSE", $this->libFunc->m_displayContent($this->getValue($row_setting[$i]->vSmalltext,"newlicense")));
				break;
	
				case "ADMINEMAIL":
					$this->ObTpl->set_var("TPL_VAR_ADMINEMAIL",$this->libFunc->m_displayContent($this->getValue($row_setting[$i]->vSmalltext,"ADMINEMAIL")));
				break;
				case "CURRENCY":
					$this->ObTpl->set_var("TPL_VAR_CURRENCY",$this->libFunc->m_displayContent($this->getValue($row_setting[$i]->vSmalltext,"CURRENCY")));
				break;

				case "cartSecureServer":
					$this->ObTpl->set_var("CART_SECURE_SERVER",$this->getValue($row_setting[$i]->vSmalltext,"cartSecureServer"));
				break;

				case "systemstate":
					$this->ObTpl->set_var("TPL_VAR_SYSTEM",$this->displayIt($row_setting[$i]->vSmalltext));
				break;
				case "SMTP_AUTH":
				if(!isset($this->request['SMTP_USERNAME'])){
					$smtpAuth=$row_setting[$i]->vSmalltext;
				}else{
					$smtpAuth=$this->libFunc->ifSet($this->request,"SMTP_AUTH",0);
				}

				$this->ObTpl->set_var("TPL_VAR_SMTP_AUTH",$this->displayIt($smtpAuth));
				break;
				case "SMTP_USERNAME":
				$this->ObTpl->set_var("TPL_VAR_SMTP_USERNAME",$this->libFunc->m_displayContent($this->getValue($row_setting[$i]->vSmalltext,"SMTP_USERNAME")));
				break;
				case "SMTP_PASSWORD":
				$this->ObTpl->set_var("TPL_VAR_SMTP_PASSWORD",$this->libFunc->m_displayContent($this->getValue($row_setting[$i]->vSmalltext,"SMTP_PASSWORD")));
				break;
				case "SMTP_HOST":
				$this->ObTpl->set_var("TPL_VAR_SMTP_HOST",$this->libFunc->m_displayContent($this->getValue($row_setting[$i]->vSmalltext,"SMTP_HOST")));
				break;
				case "cencoding":					//$this->ObTpl->set_var("TPL_VAR_CENCODING",$this->libFunc->m_displayContent($row_setting[$i]->vSmalltext));
				$computerEncoding=unserialize(COMPUTER_ENCODING);
				$this->libFunc->multi_chk_or_selectbox($this->ObTpl,'COMPUTER_ENCODING_BLK',$computerEncoding,$row_setting[$i]->vSmalltext,'selected');
				break;
			
			}#END SWITCH
		}#END FOR LOOP
	
		#ASSIGNING FORM VARAIABLES
		
		return($this->ObTpl->parse("return","TPL_SETTING_FILE"));
	}#end FUNCTION
	
	function valiadateSystemInfo()
	{
		$this->errMsg="";
		$libFunc=new c_libFunctions();
		
		if(empty($this->request['dbServer']))
		{
			$this->err=1;
			$this->errMsg.=MSG_DBSERVER_EMPTY."<br>";
		}
		if(empty($this->request['dbUserName']))
		{
			$this->err=1;
			$this->errMsg.=MSG_USERNAME_EMPTY."<br>";
		}
		if(empty($this->request['dbPassword']))
		{
			$this->err=1;
			$this->errMsg.=MSG_PASSWORD_EMPTY."<br>";
		}
		if(empty($this->request['dsn']))
		{
			$this->err=1;
			$this->errMsg.=MSG_DBNAME_EMPTY."<br>";
		}
		#INTIALIZING VALUES
		define("DATABASE_HOSTTEST",$this->request['dbServer']);
		define("DATABASE_USERNAMETEST",$this->request['dbUserName']);
		define("DATABASE_PASSWORDTEST",$this->request['dbPassword']);
		define("DATABASE_NAMETEST",$this->request['dsn']);
		define("DATABASE_PORTTEST","3306");
		
		$comFunc=new c_commonFunctions();
		$comFunc->db_host = DATABASE_HOSTTEST;
		$comFunc->db_user = DATABASE_USERNAMETEST;
		$comFunc->db_password = DATABASE_PASSWORDTEST;
		$comFunc->db_port = DATABASE_PORTTEST;
		$comFunc->db_name = DATABASE_NAMETEST;
		$returnValue=$comFunc->checkDatabase();
		if($returnValue!=1)
		{
			$this->err=1;
			$this->errMsg.=$returnValue."<br>";
		}
		
		
		if(empty($this->request['SITEURL']))
		{
			$this->err=1;
			$this->errMsg.=MSG_SITEURL_EMPTY."<br>";
		}
		if(empty($this->request['SITEPATH']))
		{
			$this->err=1;
			$this->errMsg.=MSG_SITETITLE_EMPTY."<br>";
		}
		if(empty($this->request['ADMINEMAIL']))
		{
			$this->err=1;
			$this->errMsg.=MSG_ADMINEMAIL_EMPTY."<br>";
		}
		if(empty($this->request['CURRENCY']))
		{
			$this->err=1;
			$this->errMsg.=MSG_CURRENCY_EMPTY."<br>";
		}
		if(!is_dir($this->libFunc->path_converter($this->request['SITEPATH'])))
		{
			$this->err=1;
			$this->errMsg.=MSG_NOTDIR."<br>";
		}
		if(isset($this->request['SMTP_AUTH']) && $this->request['SMTP_AUTH']=='1' && (empty($this->request['SMTP_USERNAME']) || empty($this->request['SMTP_PASSWORD']) || empty($this->request['SMTP_HOST']))){
			$this->err=1;
			$_errMsg="";
			if(empty($this->request['SMTP_HOST']))
				$_errMsg=MSG_SMTP_HOST_EMPTY;
			if(empty($this->request['SMTP_PASSWORD']))
				$_errMsg=MSG_SMTP_PASSWORD_EMPTY;
			if(empty($this->request['SMTP_USERNAME']))
				$_errMsg=MSG_SMTP_USERNAME_EMPTY;
			
			$this->errMsg.=$_errMsg."<br>";
		}
		
		$this->request['newlicense']=$this->libFunc->ifSet($this->request,'newlicense');
		$this->license=new licenseCheck($this->obDb, $this->libFunc);
		$licenseinfo = $this->license->DolicenseCheck($this->request['newlicense']);
		if(empty($licenseinfo)){
			die("LICENSE ERROR. LICENSE FUNCTION HAS BEEN REMOVED!");
		} else {
			if($licenseinfo['status'] == "Active")
			{
				
			}
			elseif ($licenseinfo['status'] == "Invalid")
			{
				$this->errMsg = $this->errMsg." Your license key is invalid. <br />";
				$this->err=1;
			}
			elseif ($licenseinfo['status'] == "Expired")
			{
				$this->errMsg = $this->errMsg." Your license key has expired. <br />";
				$this->err=1;
			}
			elseif ($licenseinfo['status'] == "Suspended")
			{
				$this->errMsg = $this->errMsg." Your license key has been suspended. <br />";
				$this->err=1;
			}
			else
			{
				$this->errMsg = $this->errMsg." Your license key is invalid. <br />";
				$this->err=1;
			}
			if($this->err !=1)
			{
				 $this->request['LicenseKey'] = $this->request['newlicense'];
			}
		}		
		return $this->err;
	}
	// payment setting server side vaildations
	function valiadatePaymentInfo(){
		$this->errMsg="";

		$libFunc=new c_libFunctions();

		$flag_protx_avscv2=$this->request['txtprotxApplyAVSCV2']==='0' || $this->request['txtprotxApplyAVSCV2']==='1' || $this->request['txtprotxApplyAVSCV2']==='2' || $this->request['txtprotxApplyAVSCV2']==='3';

		$flag_protx3dsecure =$this->request['txtprotx3DSecureStatus'] === '0' || $this->request['txtprotx3DSecureStatus'] ==='1' || $this->request['txtprotx3DSecureStatus'] === '2' || $this->request['txtprotx3DSecureStatus'] ==='3';

		if(!empty($this->request['txtprotxVendor'])  && (!$flag_protx_avscv2 || !$flag_protx3dsecure)){
			$this->err=1;
			$_errMsg="";
			if(!$flag_protx3dsecure)
				$_errMsg=MSG_3DSECURESTATUS_EMPTY;
			if(!$flag_protx_avscv2)
				$_errMsg=MSG_APPLYAVSCV2_EMPTY;
			$this->errMsg.=$_errMsg."<br>";
		}
		return $this->err;
	}

}
?>