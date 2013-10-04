<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.0.5
=======================================================================================
*/
include_once SITE_PATH."LanguagePacks/".LANGUAGE_PACK;
class c_userInterface
{
#CONSTRUCTOR
	function c_userInterface()
	{
		$this->err=0;
		$this->libFunc=new c_libFunctions();
	}
	#FUNCTION TO DISPLAY LOGIN FORM
	function m_loginForm()
	{
		#INTIALIZING TEMPLATES
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_USER_FILE", $this->userTemplate);
		$this->ObTpl->set_block("TPL_USER_FILE","TPL_MSG_BLK","msg_blk");
		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		$this->ObTpl->set_var("TPL_VAR_MSG","");
		$this->ObTpl->set_var("msg_blk","");
		$formUrl1=$this->libFunc->m_safeUrl(SITE_URL."user/index.php?action=user.checkLogin");
		$this->ObTpl->set_var("TPL_VAR_FORMURL1",$formUrl1);
	
		$formUrl2=$this->libFunc->m_safeUrl(SITE_URL."user/index.php?action=user.emailPass");
		$this->ObTpl->set_var("TPL_VAR_FORMURL2",$formUrl2);	

		$signupUrl=$this->libFunc->m_safeUrl(SITE_URL."user/index.php?action=user.signupForm");
		$this->ObTpl->set_var("TPL_VAR_SIGNUPURL",$signupUrl);	
		
		#defining Language Variables.
		$this->ObTpl->set_var("LANG_VAR_CUSTOMERLOGIN",LANG_CUSTOMERLOGIN);
		$this->ObTpl->set_var("LANG_VAR_NOTREGISTERED",LANG_NOTREGISTERED);
		$this->ObTpl->set_var("LANG_VAR_EMAILADDRESS",LANG_EMAILADDRESS);
		$this->ObTpl->set_var("LANG_VAR_PASSWORD",LANG_PASSWORD);
		$this->ObTpl->set_var("LANG_VAR_LOSTPASSWORD",LANG_LOSTPASSWORD);
		$this->ObTpl->set_var("LANG_VAR_CUSTOMERLOGIN",LANG_CUSTOMERLOGIN);
		$this->ObTpl->set_var("LANG_VAR_SENDPASSWORD",LANG_SENDPASSWORD);
		if($this->request["mode"] == "tc"){
			$this->ObTpl->set_var("TPL_VAR_MSG",MSG_TRADE_REGISTER);
			$this->ObTpl->parse("msg_blk","TPL_MSG_BLK");
		}
	
		if($this->err==1)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",$this->errMsg);
			$this->ObTpl->parse("msg_blk","TPL_MSG_BLK");
		}

		if(isset($this->request['msg']) && $this->request['msg']==1)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",MSG_PASS_SENT);
			$this->ObTpl->parse("msg_blk","TPL_MSG_BLK");
		}
		elseif(isset($this->request['msg']) && $this->request['msg']==2)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",MSG_PASS_NOSENT);
			$this->ObTpl->parse("msg_blk","TPL_MSG_BLK");
		}
		elseif(isset($this->request['msg']) && $this->request['msg']==3)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG","Password Changed.");
			$this->ObTpl->parse("msg_blk","TPL_MSG_BLK");
		}
		if(!isset($_COOKIE['email']))
		{
			$this->ObTpl->set_var("TPL_VAR_EMAIL",'email address');
		}
		else
		{
			$this->ObTpl->set_var("TPL_VAR_EMAIL",$_COOKIE['email']);
		}

		if(!isset($_COOKIE['password']))
		{
			$this->ObTpl->set_var("TPL_VAR_PASS",'password');
		}
		else
		{
			$this->ObTpl->set_var("TPL_VAR_PASS",$_COOKIE['password']);
		}
		return($this->ObTpl->parse("return","TPL_USER_FILE"));
	}#END FUNCTION

	#FUNCTION TO CHECK VALID LOGIN INFO
	function m_checkLogin()
	{
		$this->obDb->query= "select vFirstName,iCustmerid_PK  FROM ".CUSTOMERS." WHERE vEmail = '".$this->request['email']."' AND vPassword=PASSWORD('".$this->request['password']."') AND iRegistered=1 ";
		$qryRs = $this->obDb->fetchQuery();
		$rCount=$this->obDb->record_count;

		if($rCount==1) 
		{
			$_SESSION['username']=$qryRs[0]->vFirstName;
			$_SESSION['userid']=$qryRs[0]->iCustmerid_PK;
			if(isset($this->request['save_info']))
			{
				setcookie("email",$this->request['email']);
				setcookie("password",$this->request['password']);
			}
			else
			{
				setcookie("email","");
				setcookie("password","");
			}
		}
		else
		{	
			$this->err=1;
			$this->errMsg=MSG_INVALID_USER;
		}
		return $this->err;
	}#END FUNCTION


	#FUNCTION TO DISPLAY ACCOUNT INFORMATION
	function m_dspUserAccount()
	{
		if(!isset($_SESSION['userid']) || $_SESSION['userid']=="")
		{
			#URL TEMPER
			$this->libFunc->m_mosRedirect($this->m_safeUrl(SITE_URL."user/index.php?action=user.loginForm"));
			exit;
		}
		else
		{
			
			#INTIALIZING TEMPLATES
			$this->ObTpl=new template();
			$this->ObTpl->set_var("TPL_VAR_MSG","");
			$this->ObTpl->set_file("TPL_USER_FILE", $this->userTemplate);
			$this->ObTpl->set_block("TPL_USER_FILE","TPL_CURRENTINFO_BLK","current_blk");
			$this->ObTpl->set_block("TPL_CURRENTINFO_BLK","TPL_ORDERLISTMAIN_BLK","orderlistmain_blk");
			$this->ObTpl->set_block("TPL_ORDERLISTMAIN_BLK","TPL_ORDERLIST_BLK","orderlist_blk");
			$this->ObTpl->set_block("TPL_USER_FILE","TPL_EDITACCOUNT_BLK","edit_blk");
			$this->ObTpl->set_block("TPL_USER_FILE","TPL_CHANGEPASS_BLK","changepass_blk");
			$this->ObTpl->set_block("TPL_USER_FILE","countryblk","countryblks");
			$this->ObTpl->set_block("TPL_EDITACCOUNT_BLK","BillCountry","nBillCountry");
			$this->ObTpl->set_block("TPL_EDITACCOUNT_BLK","TPL_NEWSLETTER_BLK","news_blk");
			$this->ObTpl->set_block("TPL_USER_FILE","stateblk","stateblks");
			#INTIALIZING
			$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	
			$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);

			$this->ObTpl->set_var("news_blk","");
			$this->ObTpl->set_var("current_blk","");
			$this->ObTpl->set_var("edit_blk","");
			$this->ObTpl->set_var("changepass_blk","");
			$this->ObTpl->set_var("orderlistmain_blk","");
			$this->ObTpl->set_var("orderlist_blk","");
			$this->ObTpl->set_var("BILL_STATE","");
			$this->ObTpl->set_var("TPL_VAR_STATE","");
			$this->ObTpl->set_var("TPL_VAR_STATENAME","");

			$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo;&nbsp;My Account");

			$accountUrl=$this->libFunc->m_safeUrl(SITE_URL."user/index.php?action=user.home");
			if(isset($this->request['msg']) && $this->request['msg']==1)
			{
				$this->ObTpl->set_var("TPL_VAR_MSG",MSG_PASSWORD_CHANGED);
			}
			if($this->err==1)
			{
				$this->ObTpl->set_var("TPL_VAR_MSG",$this->errMsg);
			}
			
			#SAFE URLS
			$updateUrl=SITE_URL."user/index.php?action=user.home&mode=editDetails";
			$this->ObTpl->set_var("TPL_VAR_UPDATEURL",$this->libFunc->m_safeUrl($updateUrl));
			$changePassUrl=SITE_URL."user/index.php?action=user.home&mode=changePass";
			$this->ObTpl->set_var("TPL_VAR_CHANGEPASSURL",$this->libFunc->m_safeUrl($changePassUrl));
			$logoutUrl=SITE_URL."user/index.php?action=user.logout";
			$this->ObTpl->set_var("TPL_VAR_LOGOUTURL",$this->libFunc->m_safeUrl($logoutUrl));
			$reportsUrl=SITE_URL."user/index.php?action=user.home&mode=reports";
			$this->ObTpl->set_var("TPL_VAR_REPORTS_URL",$this->libFunc->m_safeUrl($reportsUrl));


			#QUERY DATABASE
			$this->obDb->query = "SELECT * FROM ".CUSTOMERS." where iCustmerid_PK = '".$_SESSION['userid']."'";
			$row_customer = $this->obDb->fetchQuery();
			$recordCount=$this->obDb->record_count;
			if($recordCount!=1)
			{
				$this->libFunc->m_sessionUnregister("userid");
				$this->libFunc->m_sessionUnregister("username");
				$retUrl=$this->libFunc->m_safeUrl(SITE_URL."user/index.php?action=user.home");
				$_SESSION['referer']=$retUrl;
				$siteUrl=SITE_URL."user/index.php?action=user.loginForm";
				$this->libFunc->m_mosRedirect($this->libFunc->m_safeUrl($siteUrl));
				exit;
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
				
				if($row_customer[0]->vCountry> 0)
				{
					if($row_customer[0]->vCountry == $row_country[$i]->iCountryId_PK)
						$this->ObTpl->set_var("BILL_COUNTRY_SELECT", "selected=\"selected\"");
					else
						$this->ObTpl->set_var("BILL_COUNTRY_SELECT", "");
				}
				else
				{
					$row_customer[0]->vCountry = $row_country[$i]->iCountryId_PK;
					if($row_country[$i]->iCountryId_PK==251)
					{
						$this->ObTpl->set_var("BILL_COUNTRY_SELECT", "selected=\"selected\"");
					}	
				}	
				$this->ObTpl->set_var("TPL_COUNTRY_NAME",$this->libFunc->m_displayContent($row_country[$i]->vCountryName));
				$this->ObTpl->parse("nBillCountry","BillCountry",true);
			}
			if(isset($row_customer[0]->vCountry) && $row_customer[0]->vCountry != '')
				$this->ObTpl->set_var('selbillcountid',$row_customer[0]->vCountry);
			else
				$this->ObTpl->set_var('selbillcountid',"251");
			if(isset($row_customer[0]->vState) && $row_customer[0]->vState != '')
				$this->ObTpl->set_var('selbillstateid',$row_customer[0]->vState);
			else
				$this->ObTpl->set_var('selbillstateid',"0");
			
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
					$this->ObTpl->set_var("stateName",$cRes[$i]->statename);
					$this->ObTpl->set_var("stateVal",$cRes[$i]->sid);
					$this->ObTpl->parse('stateblks','stateblk',true);
				}
			}
			$this->ObTpl->set_var("TPL_VAR_FNAME", $this->libFunc->m_displayContent($row_customer[0]->vFirstName));
			$this->ObTpl->set_var("TPL_VAR_LNAME", $this->libFunc->m_displayContent($row_customer[0]->vLastName));
			$this->ObTpl->set_var("TPL_VAR_EMAIL", $this->libFunc->m_displayContent($row_customer[0]->vEmail));
			$this->ObTpl->set_var("TPL_VAR_PASS", $this->libFunc->m_displayContent($row_customer[0]->vPassword));
			$this->ObTpl->set_var("TPL_VAR_ADDRESS1", $this->libFunc->m_displayContent($row_customer[0]->vAddress1 ));
			$this->ObTpl->set_var("TPL_VAR_ADDRESS2", $this->libFunc->m_displayContent($row_customer[0]->vAddress2 ));
			$this->ObTpl->set_var("TPL_VAR_CITY", $this->libFunc->m_displayContent($row_customer[0]->vCity));
			if($row_customer[0]->vState>1)
			{
				$this->obDb->query = "SELECT vStateName FROM ".STATES." where iStateId_PK  = '".$row_customer[0]->vState."'";
				$row_state = $this->obDb->fetchQuery();
				$this->ObTpl->set_var("TPL_VAR_STATE",
				$this->libFunc->m_displayContent($row_state[0]->vStateName));
			}
			else
			{
				$this->ObTpl->set_var("TPL_VAR_STATENAME",
				$this->libFunc->m_displayContent($row_customer[0]->vStateName));
			}
			$this->obDb->query = "SELECT vCountryName FROM ".COUNTRY." where iCountryId_PK  = '".$row_customer[0]->vCountry."' order by vCountryName";
				$row_country = $this->obDb->fetchQuery();
			$this->ObTpl->set_var("TPL_VAR_COUNTRY",
				$this->libFunc->m_displayContent($row_country[0]->vCountryName));
			$this->ObTpl->set_var("TPL_VAR_ZIP",
				$this->libFunc->m_displayContent($row_customer[0]->vZip));
			$this->ObTpl->set_var("TPL_VAR_COMPANY",
				$this->libFunc->m_displayContent($row_customer[0]->vCompany));
			$this->ObTpl->set_var("TPL_VAR_PHONE",
				$this->libFunc->m_displayContent($row_customer[0]->vPhone));
			$this->ObTpl->set_var("TPL_VAR_HOMEPAGE",
				$this->libFunc->m_displayContent($row_customer[0]->vHomePage));
			if($row_customer[0]->iMailList==1)
			{
				$maillist="HTML";
			}
			elseif($row_customer[0]->iMailList==2)
			{
				$maillist="Plain text ";
			}
			else
			{
				$maillist="None";
			}

			if(MAIL_LIST==1)
			{
				$this->ObTpl->set_var("TPL_VAR_NEWSLETTER",$maillist);
			}
			else
			{
					$this->ObTpl->set_var("TPL_VAR_NEWSLETTER",$maillist."(opt-out)");
			}

			$this->ObTpl->set_var("TPL_VAR_SIGNUPDATE",
			$this->libFunc->dateFormat1($row_customer[0]->tmSignupDate));

			if($row_customer[0]->iMailList==1)
			{
				$this->ObTpl->set_var("TPL_VAR_CHECK1","selected=\"selected\"");
			}
			elseif($row_customer[0]->iMailList==2)
			{
				$this->ObTpl->set_var("TPL_VAR_CHECK2","selected=\"selected\"");
			}
			else
			{
				$this->ObTpl->set_var("TPL_VAR_CHECK3","selected=\"selected\"");
			}

			if(!isset($this->request['mode']) || empty($this->request['mode']))
			{
				$this->m_displayInvoiceList();
				$this->ObTpl->parse("current_blk","TPL_CURRENTINFO_BLK");
			}
			elseif($this->request['mode']=="editDetails")
			{
				#DISPLAY EDIT ACCOUNT FORM
				$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo;&nbsp;<a href=".$accountUrl.">My Account</a>&nbsp;&raquo;&nbsp;Update Information");
				if(MAIL_LIST==1)
				{
					$this->ObTpl->parse("news_blk","TPL_NEWSLETTER_BLK");
				}
				$this->ObTpl->parse("edit_blk","TPL_EDITACCOUNT_BLK");
			}
			elseif($this->request['mode']=="changePass")
			{
				#DISPLAY CHANGEPASS FORM
				$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo;&nbsp;<a href=".$accountUrl.">My Account</a>&nbsp;&raquo;&nbsp;Change Password");
				$this->ObTpl->parse("changepass_blk","TPL_CHANGEPASS_BLK");
			}
			else
			{
				$this->m_displayInvoiceList();
				$this->ObTpl->parse("current_blk","TPL_CURRENTINFO_BLK");
			}
		
			return($this->ObTpl->parse("return","TPL_USER_FILE"));
		}#END ELSE LOOP
	
	}#END FUNCTION

	#FUNCTION TO DISPLAY INVOICES
	function m_displayInvoiceList()
	{
		#QUERY ORDER TABLE
		$this->obDb->query = "SELECT iOrderid_PK,iInvoice,tmOrderDate,iOrderStatus FROM ".ORDERS." WHERE	 iCustomerid_FK= '".$_SESSION['userid']."' ORDER BY tmOrderDate DESC";
		$rsOrders = $this->obDb->fetchQuery();
		if($this->obDb->record_count>0)
		{
			for($i=0;$i<$this->obDb->record_count;$i++)
			{
				$this->ObTpl->set_var("TPL_VAR_STATUS","Incomplete");
				if($rsOrders[$i]->iOrderStatus==1){
					$this->ObTpl->set_var("TPL_VAR_STATUS","Complete");
				}

				$this->ObTpl->set_var("TPL_VAR_ORDERDATE", $this->libFunc->dateFormat2($rsOrders[$i]->tmOrderDate));
				$this->ObTpl->set_var("TPL_VAR_INVOICE", $this->libFunc->m_displayContent($rsOrders[$i]->iInvoice));
				$this->ObTpl->set_var("TPL_DETAILS_LINK", $this->libFunc->m_safeUrl(SITE_SAFEURL."ecom/index.php?action=checkout.receipt&mode=".$rsOrders[$i]->iOrderid_PK));

				$this->ObTpl->parse("orderlist_blk","TPL_ORDERLIST_BLK",true);
			}
			$this->ObTpl->parse("orderlistmain_blk","TPL_ORDERLISTMAIN_BLK");
		}
	}


	#FUNCTION TO DISPLAY USER SIGNUP FORM
	function m_dspSignupForm()
	{
		#INTIALIZING TEMPLATES
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_USER_FILE", $this->userTemplate);
		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		$this->ObTpl->set_block("TPL_USER_FILE","TPL_NEWSLETTER_BLK","news_blk");
	//	$this->ObTpl->set_block("TPL_USER_FILE","DSPMSG_BLK", "msg_blk");
		$this->ObTpl->set_block("TPL_USER_FILE","TPL_CAPTCHA_BLK","captcha_blk");
		$this->ObTpl->set_var("captcha_blk","");
		$this->ObTpl->set_block("TPL_USER_FILE","countryblk","countryblks");
		$this->ObTpl->set_block("TPL_USER_FILE","BillCountry","nBillCountry");
		$this->ObTpl->set_block("TPL_USER_FILE","stateblk","stateblks");
		$this->ObTpl->set_var("TPL_USERURL",SITE_URL."user/");
		$this->ObTpl->set_var("TPL_VAR_CPASS","");
		$this->ObTpl->set_var("news_blk","");
		$this->ObTpl->set_var("TPL_VAR_MSG","");
		$this->ObTpl->set_var("TPL_VAR_CHECK1","");
		$this->ObTpl->set_var("TPL_VAR_CHECK2","");
		$this->ObTpl->set_var("TPL_VAR_CHECK3","");

		#INTIALIZING
		$row_customer[0]->vFirstName  = "";
		$row_customer[0]->vLastName  ="";
		$row_customer[0]->vEmail  = "";
		$row_customer[0]->vPassword  = "";
		$row_customer[0]->vPhone  = "";
		$row_customer[0]->vCompany = "";
		$row_customer[0]->vAddress1 = "";
		$row_customer[0]->vAddress2 = "";
		$row_customer[0]->vState ="";
		$row_customer[0]->vStateName="";
		$row_customer[0]->vCity = "";
		$row_customer[0]->vCountry = "";	
		$row_customer[0]->vZip = "";	
		$row_customer[0]->vHomePage  = "";	
		$row_customer[0]->fMemberPoints = "";
		$row_customer[0]->iMailList = "1";
		$row_customer[0]->iStatus = "1";


		/*CHECKING FOR POST VARIABLES
		IF VARIABLES ARE SET THEN ASSIGNING THEIR VALUE TO VARIABLE SAMEVARIABLE
		AS USED WHEN RETURNED FROM DATABASE
		THIS THING IS USED TO REMOVE REDUNDANCY AND USE SAME FORM FOR EDIT AND INSERT*/

		if(count($_POST) > 0)
		{
			if(isset($this->request["first_name"]))
				$row_customer[0]->vFirstName  = $this->request["first_name"];
			if(isset($this->request["password"]))
				$row_customer[0]->vPassword  = $this->request["password"];
			$this->ObTpl->set_var("TPL_VAR_CPASS", $this->libFunc->m_displayContent($this->request["verify_pw"]));
			if(isset($this->request["last_name"]))
				$row_customer[0]->vLastName  = $this->request["last_name"];
	
			if(isset($this->request["company"]))
				$row_customer[0]->vCompany  = $this->request["company"];
			if(isset($this->request["txtemail"]))
				$row_customer[0]->vEmail  = $this->request["txtemail"];
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
			if(isset($this->request["homepage"]))
				$row_customer[0]->vHomePage  = $this->request["homepage"];	
			if(isset($this->request["mail_list"]))
				$row_customer[0]->iMailList  = $this->request["mail_list"];	
			if(isset($this->request["member_points"]))
				$row_customer[0]->fMemberPoints  = $this->request["member_points"];	
			if(isset($this->request["iStatus"]))
				$row_customer[0]->iStatus = $this->request["status"];	
			else
				$row_customer[0]->iStatus = "";
		}
	
		#DISPLAYING MESSAGES
		if($this->err==1)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",$this->errMsg);
		}


		#IF EDIT MODE SELECTED

		#ASSIGNING FORM ACTION						
		$this->ObTpl->set_var("FORM_URL", SITE_URL."user/adminindex.php?action=user.updateUser");
		
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
					$this->ObTpl->set_var("BILL_COUNTRY_SELECT", "selected=\"selected\"");
				else
					$this->ObTpl->set_var("BILL_COUNTRY_SELECT", "");
			}
			else
			{
					$row_customer[0]->vCountry = SELECTED_COUNTRY;
					//echo SELECTED_COUNTRY;

					if($row_country[$i]->iCountryId_PK==$row_customer[0]->vCountry)
					{
						$this->ObTpl->set_var("BILL_COUNTRY_SELECT","selected=\"selected\"");
					}
					else
					{
						$this->ObTpl->set_var("BILL_COUNTRY_SELECT", "");
					}
			}	

			$this->ObTpl->set_var("TPL_COUNTRY_NAME",$this->libFunc->m_displayContent($row_country[$i]->vCountryName));
			$this->ObTpl->parse("nBillCountry","BillCountry",true);
		}
		
		if(isset($row_customer[0]->vCountry) && $row_customer[0]->vCountry != '')	
			$this->ObTpl->set_var('selbillcountid',$row_customer[0]->vCountry);
		else
			$this->ObTpl->set_var('selbillcountid',"251");


		if(isset($row_customer[0]->vState) && $row_customer[0]->vState != '')
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
				$this->ObTpl->set_var("stateName",$cRes[$i]->statename);
				$this->ObTpl->set_var("stateVal",$cRes[$i]->sid);
				$this->ObTpl->parse('stateblks','stateblk',true);
			}
		}


		#ASSIGNING FORM VARAIABLES

		$this->ObTpl->set_var("TPL_VAR_FNAME", $this->libFunc->m_displayContent($row_customer[0]->vFirstName));
		$this->ObTpl->set_var("TPL_VAR_LNAME", $this->libFunc->m_displayContent($row_customer[0]->vLastName));
		$this->ObTpl->set_var("TPL_VAR_EMAIL", $this->libFunc->m_displayContent($row_customer[0]->vEmail));
		$this->ObTpl->set_var("TPL_VAR_PASS", $this->libFunc->m_displayContent($row_customer[0]->vPassword));
		$this->ObTpl->set_var("TPL_VAR_ADDRESS1", $this->libFunc->m_displayContent($row_customer[0]->vAddress1 ));
		$this->ObTpl->set_var("TPL_VAR_ADDRESS2", $this->libFunc->m_displayContent($row_customer[0]->vAddress2 ));
		$this->ObTpl->set_var("TPL_VAR_CITY", $this->libFunc->m_displayContent($row_customer[0]->vCity));

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
		$this->ObTpl->set_var("TPL_VAR_HOMEPAGE",
			$this->libFunc->m_displayContent($row_customer[0]->vHomePage));
		
		if(CAPTCHA_REGISTRATION){
			$this->ObTpl->parse("captcha_blk","TPL_CAPTCHA_BLK",true);
		}

		if(MAIL_LIST==1)
		{
			if($row_customer[0]->iMailList==1)
			{
				$this->ObTpl->set_var("TPL_VAR_CHECK1","selected=\"selected\"");
			}
			elseif($row_customer[0]->iMailList==2)
			{
				$this->ObTpl->set_var("TPL_VAR_CHECK2","selected=\"selected\"");
			}
			else
			{
				$this->ObTpl->set_var("TPL_VAR_CHECK3","selected=\"selected\"");
			}
			$this->ObTpl->parse('news_blk','TPL_NEWSLETTER_BLK');
		}
		
		return $this->ObTpl->parse("return","TPL_USER_FILE");
	}

	#FUNCTION TO VERIFY DATABASE UPDATION
		function m_verifyEditPass()
	{
		$this->errMsg=MSG_HEAD."<br>";
		if(empty($this->request['password']))
		{
			$this->err=1;
			$this->errMsg.=MSG_PASS_EMPTY."<br>";
		}
		if(empty($this->request['verify_pw']))
		{
			$this->err=1;
			$this->errMsg.=MSG_VERIFYPASS_EMPTY."<br>";
		}
		if($this->request['password']!=$this->request['verify_pw'])
		{
			$this->err=1;
			$this->errMsg.=MSG_PASS_NOTMATCHED."<br>";
		}
		return $this->err;
	}
	function m_verifyEditUser()
	{
		$this->errMsg=MSG_HEAD."<br>";
		if(empty($this->request['first_name']))
		{
			$this->err=1;
			$this->errMsg.=MSG_FIRSTNAME_EMPTY."<br>";
		}
		if(empty($this->request['last_name']))
		{
			$this->err=1;
			$this->errMsg.=MSG_LASTNAME_EMPTY."<br>";
		}
		if(empty($this->request['address1']))
		{
			$this->err=1;
			$this->errMsg.=MSG_ADDRESS1_EMPTY."<br>";
		}

		if(empty($this->request['city']))
		{
			$this->err=1;
			$this->errMsg.=MSG_CITY_EMPTY."<br>";
		}
		if(empty($this->request['zip']))
		{
			$this->err=1;
			$this->errMsg.=MSG_ZIP_EMPTY."<br>";
		}
		if(empty($this->request['phone']))
		{
			$this->err=1;
			$this->errMsg.=MSG_PHONE_EMPTY."<br>";
		}

		return $this->err;
	}

	#FUNCTION TO VERIFY DATABASE UPDATION
	function m_verifyInsertUser()
	{
		$this->errMsg=MSG_HEAD."<br />";
		$data = implode(",",$this->request);
		if(CAPTCHA_REGISTRATION){
			if($_SESSION['image_auth_string'] != $this->request['cap_key']){
				$this->err=1;
				$this->errMsg.=MSG_INVALID_CAP_KEY."<br />";
			}
		}
		if(!$this->libFunc->m_validEmailData($data)){
			$this->err=1;
			$this->errMsg.=MSG_INVALID_EMAILDATA."<br />";
		}
		if(empty($this->request['first_name']))
		{
			$this->err=1;
			$this->errMsg.=MSG_FIRSTNAME_EMPTY."<br />";
		}
		if(empty($this->request['last_name']))
		{
			$this->err=1;
			$this->errMsg.=MSG_LASTNAME_EMPTY."<br>";
		}
		if(empty($this->request['txtemail']))
		{
			$this->err=1;
			$this->errMsg.=MSG_EMAIL_EMPTY."<br>";
		}
		if(!$this->libFunc->m_validateEmail($this->request['txtemail']))
		{
			$this->err=1;
			$this->errMsg.=MSG_INVALID_EMAILADDR."<br />";
		}
		if(empty($this->request['address1']))
		{
			$this->err=1;
			$this->errMsg.=MSG_ADDRESS1_EMPTY."<br>";
		}

		if(empty($this->request['city']))
		{
			$this->err=1;
			$this->errMsg.=MSG_CITY_EMPTY."<br>";
		}
		if(empty($this->request['zip']))
		{
			$this->err=1;
			$this->errMsg.=MSG_ZIP_EMPTY."<br>";
		}
		if(empty($this->request['phone']))
		{
			$this->err=1;
			$this->errMsg.=MSG_PHONE_EMPTY."<br>";
		}
		#VALIDATING EXISTING OPTION TITLE
		//adding status check to allow for registration of a account that has already ordered once. Status signifies it was a auto generated account and shouldnt be used or is a yet to be verified account. Should consider making a seperate flag for account signalling that it is a autogenerated account.
		$this->obDb->query = "SELECT iCustmerid_PK FROM ".CUSTOMERS." where vEmail = '".$this->request['txtemail']."' AND iRegistered='1'";
		$row_code = $this->obDb->fetchQuery();
		if($this->obDb->record_count != 0)
		{
			$this->err=1;
			$this->errMsg.=MSG_EMAIL_EXIST."<br>";
		}
		
		return $this->err;
	}
	
	function m_sendPassword()
	{
		$this->obDb->query= "select iCustmerid_PK,vFirstName,vEmail,tmSignupDate FROM ".CUSTOMERS." WHERE vEmail = '".$this->request['email']."' AND iRegistered='1'";
		$qryResult = $this->obDb->fetchQuery();
		$rCount=$this->obDb->record_count;
		if(isset($this->cart))
		{
			$action="ecom/index.php?action=checkout.loginForm";
		}
		else
		{
			$action="user/index.php?action=user.loginForm";
		}
		if($rCount>0) 
		{
		$requesttime = time();
		$recoveryid = md5($qryResult[0]->iCustmerid_PK . $qryResult[0]->vFirstName . $qryResult[0]->vEmail . $qryResult[0]->tmSignupDate . $requesttime);
		$this->obDb->query="UPDATE " . CUSTOMERS . " SET vRecovery='" . $recoveryid . "',tRequestTime='" . $requesttime . "' WHERE iCustmerid_PK='" . $qryResult[0]->iCustmerid_PK . "' AND iRegistered='1'";
		$this->obDb->updateQuery();
	//	$uniqID=uniqid (3);
			$message ="Hi ".$this->libFunc->m_displayContent($qryResult[0]->vFirstName);
			$message .="<br><br>You requested to reset your login details for Username:&nbsp;".$qryResult[0]->vEmail;
			$message .="<br><br>You can do so by visiting this <a href='".SITE_URL."user/index.php?action=user.recover&id=". $recoveryid . "'>link</a>.";
			$message .="<br>If the link is not clickable, copy and paste this url into your browser: " . SITE_URL."user/index.php?action=user.recover&id=". $recoveryid;
			$message .="<br>You must click the above password within 24 hours of your request or the link will be deactivated.";
			$message .="<br><br>If you didn't request to reset your password, then please disregard this message.";
			$message .="<br><br>Best Regards,";
			$message .="<br><a href='".SITE_URL."'>".SITE_NAME."</a>";
			$obMail = new htmlMimeMail();
			$obMail->setReturnPath(ADMIN_EMAIL);
			$obMail->setFrom(SITE_NAME."<".ADMIN_EMAIL.">");
			$obMail->setSubject("Login details from ".SITE_NAME);
			$obMail->setCrlf("\n"); //to handle mails in Outlook Express
			$htmlcontent=$message;
			$txtcontent=preg_replace("/<([^>]+)>/","",preg_replace("/<br(\/{0,1})>/","\r\n",$message));
			$obMail->setHtml($htmlcontent,$txtcontent);
			$obMail->buildMessage();
			$result = $obMail->send(array($qryResult[0]->vEmail));
			$retUrl=$this->libFunc->m_safeUrl(SITE_URL.$action."&mode=sent&msg=1");
			$this->libFunc->m_mosRedirect($retUrl);
			exit;
		}
		else
		{	
			$retUrl=$this->libFunc->m_safeUrl(SITE_URL.$action."&mode=lost&msg=2");
			$this->libFunc->m_mosRedirect($retUrl);
			exit;
		}
	
	}
	
	function m_reset_Password()
	{
		$this->obDb->query= "select iCustmerid_PK,tRequestTime FROM ".CUSTOMERS." WHERE vRecovery = '".$this->request['id']."' AND iRegistered='1'";
		$qryResult = $this->obDb->fetchQuery();
		$rCount=$this->obDb->record_count;
		if($rCount>0 && $qryResult[0]->tRequestTime + 86400 > time()) 
		{
			$this->ObTpl=new template();
			$this->ObTpl->set_file("TPL_USER_FILE", $this->userTemplate);
			$_SESSION['id'] = $this->request['id'];
			return($this->ObTpl->parse("return","TPL_USER_FILE"));
		}
		else
		{
			$retUrl=$this->libFunc->m_safeUrl(SITE_URL);
			$this->libFunc->m_mosRedirect($retUrl);
			exit;
		}
	}
		function m_save_new_Password()
		{
			$this->obDb->query= "select iCustmerid_PK,tRequestTime FROM ".CUSTOMERS." WHERE vRecovery = '".$_SESSION['id']."' AND iRegistered='1'";
			$qryResult = $this->obDb->fetchQuery();
			$rCount=$this->obDb->record_count;
			if($rCount>0 && $qryResult[0]->tRequestTime + 86400 > time()) 
			{
				$this->obDb->query="UPDATE ".CUSTOMERS." SET vPassword=PASSWORD('".$this->request['txtpassword']."'),vRecovery='',tRequestTime='' WHERE vRecovery ='".$_SESSION['id']."'";
				$this->obDb->updateQuery();
				$retUrl=$this->libFunc->m_safeUrl(SITE_URL."user/index.php?action=user.home&mode=password&msg=3");
				$this->libFunc->m_mosRedirect($retUrl);
				exit;
			}
			else
			{
				$retUrl=$this->libFunc->m_safeUrl(SITE_URL);
				$this->libFunc->m_mosRedirect($retUrl);
				exit;
			}
		}

}#END CLASS
?>