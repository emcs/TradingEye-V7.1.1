<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.0.5
=======================================================================================
*/
include_once SITE_PATH."LanguagePacks/".LANGUAGE_PACK;
class c_billShipInfo
{
#CONSTRUCTOR
	function c_billShipInfo()
	{
		$this->err=0;
		$this->errMsg="";
		$this->libFunc=new c_libFunctions();

	}
	#FUNCTION TO DISPLAY LOGIN FORM
	function m_checkoutLoginForm()
	{
		#INTIALIZING TEMPLATES
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_LOGIN_FILE", $this->loginTemplate);
		$this->ObTpl->set_block("TPL_LOGIN_FILE","TPL_CUSTOMER_BLK","customer_blk");
		$this->ObTpl->set_block("TPL_LOGIN_FILE","TPL_LOSTPASS_BLK","lostpass_blk");
		$this->ObTpl->set_block("TPL_CUSTOMER_BLK","TPL_MSG_BLK","msg_blk");

		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		$this->ObTpl->set_var("TPL_VAR_MSG","");

		$this->ObTpl->set_var("customer_blk","");
		$this->ObTpl->set_var("lostpass_blk","");
		$this->ObTpl->set_var("msg_blk","");
		
		#defining Language Variables.
		$this->ObTpl->set_var("LANG_VAR_CHECOUTLOGIN",LANG_CHECKOUTLOGIN);
		$this->ObTpl->set_var("LANG_VAR_NOTREGISTEREDMESS",LANG_NOTREGMESSAGE);
		$this->ObTpl->set_var("LANG_VAR_NEWCUSTOMERS",LANG_NEWCUSTOMERS);
		$this->ObTpl->set_var("LANG_VAR_EMAILADDRESS",LANG_EMAILADDRESS);
		$this->ObTpl->set_var("LANG_VAR_IFREGISTERED",LANG_IFREGISTEREDMESS);
		$this->ObTpl->set_var("LANG_VAR_REGISTEREDCUS",LANG_REGISTEREDCUS);
		$this->ObTpl->set_var("LANG_VAR_PASSWORD",LANG_PASSWORD);
		$this->ObTpl->set_var("LANG_VAR_CUSTOMERLOGIN",LANG_CUSTOMERLOGIN);
		$this->ObTpl->set_var("LANG_VAR_SAVELOGIN",LANG_SAVEDETAILS);
		$this->ObTpl->set_var("LANG_VAR_FORGOTPASSWORD",LANG_LOSTPASSWORD);
		$this->ObTpl->set_var("LANG_VAR_SENDPASSWORD",LANG_SENDPASSWORD);
		$this->ObTpl->set_var("LANG_VAR_LOSTPASSWORDMESS",LANG_ENTEREMAILMESS);
		$this->ObTpl->set_var("LANG_VAR_CONTINUE",LANG_CONTINUE);
		$this->ObTpl->set_var("LANG_VAR_PROCEEDNEW",LANG_PROCEEDMESS);
		

		if($this->err==1)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",$this->errMsg);
			$this->ObTpl->parse("msg_blk","TPL_MSG_BLK");
		}
		if(isset($this->request['mode']) && $this->request['mode']=='lost')
		{
			$formUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=checkout.lost");
			$this->ObTpl->set_var("TPL_VAR_FORMURL2",$formUrl);
			$linkUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=checkout.loginForm");
			$this->ObTpl->set_var("TPL_VAR_LINKNEW",$linkUrl);	
			if(isset($this->request['msg']) && $this->request['msg']==2)
			{
				$this->ObTpl->set_var("TPL_VAR_MSG",MSG_PASS_NOSENT);
				$this->ObTpl->parse("msg_blk","TPL_MSG_BLK");
			}
			$this->ObTpl->parse("lostpass_blk","TPL_LOSTPASS_BLK");
		}
		else
		{
			$formUrl=$this->libFunc->m_safeUrl(SITE_SAFEURL."ecom/index.php?action=checkout.login");
			$this->ObTpl->set_var("TPL_VAR_FORMURL",$formUrl);
			$formUrl1=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=checkout.login");
			$this->ObTpl->set_var("TPL_VAR_FORMURL1",$formUrl1);	$linkUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=checkout.loginForm&mode=lost");
			$this->ObTpl->set_var("TPL_VAR_LINKLOST",$linkUrl);
			if(isset($this->request['msg']) && $this->request['msg']==1)
			{
					$this->ObTpl->set_var("TPL_VAR_MSG",MSG_PASS_SENT);
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
			$this->ObTpl->set_var("SESSIONID",SESSIONID);
			$this->ObTpl->parse("customer_blk","TPL_CUSTOMER_BLK");
		}
		return($this->ObTpl->parse("return","TPL_LOGIN_FILE"));
	}#END FUNCTION

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
				setcookie("email",$this->request['email'],time()+60*60*24*30 , "/");
				setcookie("password",$this->request['password'],time()+60*60*24*30, "/");
			}

			if(isset($_SESSION['referer']))
			{
				$this->libFunc->m_mosRedirect($_SESSION['referer']);
			}
			else
			{
				$retUrl=$this->libFunc->m_safeUrl(SITE_SAFEURL."ecom/index.php?action=checkout.billing&mode=".SESSIONID);
				$this->libFunc->m_mosRedirect($retUrl);
			}
		}
		else
		{	
			$this->err=1;
			$this->errMsg=MSG_INVALID_USER;
		}
		return $this->err;
	}#END FUNCTION
	
	#FUNCTION TO VALIDATE EMAIL ENTER BY UNREGISTERED USER
	function m_valiadateEmail()
	{
		$this->obDb->query= "SELECT iCustmerid_PK  FROM ".CUSTOMERS." WHERE vEmail = '".$this->request['email']."' AND iRegistered='1'";
		$qryRs = $this->obDb->fetchQuery();
		$rCount=$this->obDb->record_count;
		if($rCount>0) 
		{
			return 1;
		}
		return 0;
	}

	#FUNCTION TO DISPLAY USER FORM
	function m_billShipInfoForm()
	{
		$libFunc=new c_libFunctions();
		$this->libFunc->obDb=$this->obDb;
		
		#INTIALIZING TEMPLATES
		$this->ObTpl=new template();
		$this->ObTpl->set_var("TPL_VAR_JAVASCRIPTS",file_get_contents(SITE_PATH."jscript/checkout.js"));
		if(isset($this->request['member_points']) && $this->request['member_points'] == "yes") 
		{
			$this->ObTpl->set_var("TPL_VAR_MEMPOINTS","<input type=\"hidden\" name=\"member_points\" value=\"".$this->request['member_points']."\"/>");	
			$_SESSION['useMemberPoints'] = "yes";
		}
		else
		{
			$this->ObTpl->set_var("TPL_VAR_MEMPOINTS","<input type=\"hidden\" name=\"member_points\" value=\"no\"/>");
			$_SESSION['useMemberPoints'] = "no";
		}
		$this->ObTpl->set_file("TPL_USER_FILE", $this->billShipTemplate);
		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_SAFEURL);
		$this->ObTpl->set_var("TPL_VAR_PTYPE",DEFAULT_POSTAGE_METHOD);
		$this->ObTpl->set_block("TPL_USER_FILE","TPL_PASSWORD_BLK","password_blk");
		$this->ObTpl->set_block("TPL_USER_FILE","TPL_ALTSHIP_BLK","altship_blk");
		$this->ObTpl->set_block("TPL_USER_FILE","TPL_VAT_WELCOMEMSG","welcome_blk");
		$this->ObTpl->set_block("TPL_USER_FILE","countryblk","countryblks");
		$this->ObTpl->set_block("TPL_USER_FILE","BillCountry","nBillCountry");
		$this->ObTpl->set_block("TPL_ALTSHIP_BLK","ShipCountry","nShipCountry");
	
		$this->ObTpl->set_block("TPL_USER_FILE","TPL_SPECIALRATE_BLK","specialrate_blk");
		
		$this->ObTpl->set_block("TPL_USER_FILE","stateblk","stateblks");
		$this->ObTpl->set_block("TPL_USER_FILE","TPL_MPOINTS_BLK","memberpoint_blk");


		//Payment Blocks
		
		#SETTING BLOCKS	
		
		$this->ObTpl->set_block("TPL_USER_FILE","TPL_CREDITDEBITMAIN_BLK","creditmain_blk");
		$this->ObTpl->set_block("TPL_CREDITDEBITMAIN_BLK","TPL_CREDITDEBITCARDS_BLK","credit_blk");
		$this->ObTpl->set_block("TPL_CREDITDEBITMAIN_BLK","TPL_CARDBYPHONE_BLK","creditbyphone_blk");
		//////////////////////////////
			//cardsave redirect blk
		$this->ObTpl->set_block("TPL_CREDITDEBITMAIN_BLK","TPL_CARDSAVEREDIRECT_BLK","cardsaveredirect_blk");
			//cardsave start date blk
      	$this->ObTpl->set_block("TPL_CREDITDEBITCARDS_BLK","TPL_SC_STARTDATE_BLK","cardsave_start_date_blk");
		
		/////////////////////////////
		
		$this->ObTpl->set_block("TPL_SC_STARTDATE_BLK","TPL_CSCARDYEAR_BLK","cs_startyear_blk");
		
		$this->ObTpl->set_var("TPL_VAR_COMPPHONE",$this->libFunc->m_displayContent(SITE_PHONE)); 
		if(SELECTED_PAYMENTGATEWAY==VERISIGN){
		$this->ObTpl->set_block("TPL_CREDITDEBITCARDS_BLK","TPL_CARDSTYPE_BLK","creditcardtype_blk");
			$this->ObTpl->set_block("TPL_CARDSTYPE_BLK","TPL_CARDS_BLK","creditcard_blk");
		}else{
			$this->ObTpl->set_block("TPL_CREDITDEBITCARDS_BLK","TPL_CARDS_BLK","creditcard_blk");
		}
		$this->ObTpl->set_block("TPL_CREDITDEBITCARDS_BLK","TPL_CARDYEAR_BLK","credityear_blk");
		$this->ObTpl->set_block("TPL_CREDITDEBITCARDS_BLK","TPL_SOLOCARDS_BLK","solocard_blk");
		$this->ObTpl->set_block("TPL_SOLOCARDS_BLK","TPL_SOLOCARDYEAR_BLK","soloyear_blk");
		$this->ObTpl->set_block("TPL_USER_FILE","TPL_ECHECK_BLK","echeck_blk");
		$this->ObTpl->set_block("TPL_USER_FILE","TPL_NOMETHOD_BLK1","nomethod_blk1");
		$this->ObTpl->set_block("TPL_USER_FILE","TPL_NOMETHOD_BLK2","nomethod_blk2");
		$this->ObTpl->set_block("TPL_USER_FILE","TPL_PAYPAL_BLK","paypal_blk");
		$this->ObTpl->set_block("TPL_USER_FILE","TPL_SECPAY_BLK","secpay_blk");
		$this->ObTpl->set_block("TPL_USER_FILE","TPL_WORLDPAY_BLK","worldpay_blk");
		$this->ObTpl->set_block("TPL_USER_FILE","TPL_STOFF_BLK","stoff_blk"); //
		$this->ObTpl->set_block("TPL_USER_FILE","TPL_OTHER_BLK","other_blk");
		$this->ObTpl->set_block("TPL_USER_FILE","TPL_COD_BLK","cod_blk");
		$this->ObTpl->set_block("TPL_USER_FILE","TPL_NEWSLETTER_BLK","news_blk");
		//End Payment blocks
    
        #(BEGIN) SAGE PAY INTERGRATION
        $this->ObTpl->set_block("TPL_USER_FILE","TPL_SAGEPAYFORM_BLK","sagepayform_blk");
    	#(END) SAGE PAY INTERGRATION

		$this->ObTpl->set_block("TPL_USER_FILE","TPL_FREEPOST_BLK","freepost_blk");

		//Select postage block
		$this->ObTpl->set_block("TPL_SPECIALRATE_BLK","TPL_DEFAULTPOSTAGE_BLK","default_postage_blk");
		$this->ObTpl->set_block("TPL_SPECIALRATE_BLK","TPL_POSTAGE_BLK","postage_blk");
		$this->ObTpl->set_block("TPL_POSTAGE_BLK","TPL_SPECIALPOSTAGE_BLK","special_postage_blk");
		//End Select postage block
		
		#INTIALIZING BLOCKS
		$this->ObTpl->set_var("password_blk","");
		$this->ObTpl->set_var("default_postage_blk","");
		$this->ObTpl->set_var("special_postage_blk","");
		$this->ObTpl->set_var("postage_blk","");
		$this->ObTpl->set_var("specialrate_blk","");
		$this->ObTpl->set_var("altship_blk","");
		$this->ObTpl->set_var("welcome_blk","");
		$this->ObTpl->set_var("countryblks","");
		$this->ObTpl->set_var("stoff_blk","");
		$this->ObTpl->set_var("nBillCountry","");
		$this->ObTpl->set_var("nShipCountry","");
		$this->ObTpl->set_var("stateblks","");
		$this->ObTpl->set_var("memberpoint_blk","");
		$this->ObTpl->set_var("TPL_VAR_MSG","");
		$this->ObTpl->set_var("TPL_VAR_NAME","");
        
        #(BEGIN) SAGE PAY INTERGRATION
		$this->ObTpl->set_var("sagepayform_blk","");
		#(END) SAGE PAY INTERGRATION
		$this->ObTpl->set_var("freepost_blk","");
		//#INTIALIZING PAYMENT BLOCKS
		$this->ObTpl->set_var("hsbc_blk","");
		$this->ObTpl->set_var("barclay_blk","");
		$this->ObTpl->set_var("creditmain_blk","");
		$this->ObTpl->set_var("creditcardtype_blk","");
		$this->ObTpl->set_var("worldpay_blk","");
		$this->ObTpl->set_var("secpay_blk","");
		$this->ObTpl->set_var("nomethod_blk1","");
		$this->ObTpl->set_var("nomethod_blk2","");
		$this->ObTpl->set_var("credit_blk","");
		$this->ObTpl->set_var("creditbyphone_blk","");
		$this->ObTpl->set_var("cardsaveredirect_blk","");
		$this->ObTpl->set_var("creditcard_blk","");
		$this->ObTpl->set_var("credityear_blk","");
		$this->ObTpl->set_var("solocard_blk","");
		$this->ObTpl->set_var("soloyear_blk","");
		$this->ObTpl->set_var("echeck_blk","");
		$this->ObTpl->set_var("paypal_blk","");
		$this->ObTpl->set_var("echeck_blk","");
		$this->ObTpl->set_var("other_blk","");
		$this->ObTpl->set_var("cod_blk","");
		$this->ObTpl->set_var("news_blk","");
		$this->ObTpl->set_var("TPL_VAR_MSG","");
		$this->ObTpl->set_var("TPL_VAR_VATNUMBER","");
		$this->ObTpl->set_var("TPL_VAR_REGISTERNUMBER","");
		$this->ObTpl->set_var("TPL_VAR_POSTAGEMETHOD","");
		$this->ObTpl->set_var("TPL_VAR_DISPLAYPRICE","");
		$this->ObTpl->set_var("cs_startyear_blk","");
		$this->ObTpl->set_var("TPL_SC_STARTDATE_BLK","");
		$this->ObTpl->set_var("cardsave_start_date_blk","");
		
		 
		// End
		
		//defining language variables
		$this->ObTpl->set_var("LANG_VAR_BILLINGADDRESS",LANG_BILLINGDELIVERY);
		$this->ObTpl->set_var("LANG_VAR_REGMESS",LANG_REGISTRATIONMESS);
		$this->ObTpl->set_var("LANG_VAR_REGSENTANCE",LANG_REGSENTANCE);
		$this->ObTpl->set_var("LANG_VAR_PASSWORD",LANG_PASSWORD);
		$this->ObTpl->set_var("LANG_VAR_CONFIRMPASSWORD",LANG_CONFIRMPASS);
		$this->ObTpl->set_var("LANG_VAR_BILLINGADDRESS",LANG_BILLINGADDRESS);
		$this->ObTpl->set_var("LANG_VAR_COMPANY",LANG_COMPANY);
		$this->ObTpl->set_var("LANG_VAR_FIRSTNAME",LANG_FIRSTNAME);
		$this->ObTpl->set_var("LANG_VAR_LASTNAME",LANG_LASTNAME);
		$this->ObTpl->set_var("LANG_VAR_EMAILADDRESS",LANG_EMAILADDRESS);
		$this->ObTpl->set_var("LANG_VAR_ADDRESS1",LANG_ADDRESS1);
		$this->ObTpl->set_var("LANG_VAR_ADDRESS2",LANG_ADDRESS2);
		$this->ObTpl->set_var("LANG_VAR_CITY",LANG_CITY);
		$this->ObTpl->set_var("LANG_VAR_COUNTRY",LANG_COUNTRY);
		$this->ObTpl->set_var("LANG_VAR_COUNTY",LANG_COUNTYSTATE);
		$this->ObTpl->set_var("LANG_VAR_COUNTYOTHER",LANG_COUNTYSTATEOTHER);
		$this->ObTpl->set_var("LANG_VAR_POSTCODE",LANG_POSTCODE);
		$this->ObTpl->set_var("LANG_VAR_TELEPHONE",LANG_TELEPHONE);
		$this->ObTpl->set_var("LANG_VAR_WEBSITE",LANG_WEBSITE);
		$this->ObTpl->set_var("LANG_VAR_FULLNAME",LANG_FULLNAME);
		$this->ObTpl->set_var("LANG_VAR_SAMEASBILL",LANG_SAMEASBILLING);
		$this->ObTpl->set_var("LANG_VAR_DELIVERYADDRESS",LANG_DELIVERYADDRESS);
		$this->ObTpl->set_var("LANG_VAR_YOURPOINTS",LANG_YOURMEMBERPOITNS);
		$this->ObTpl->set_var("LANG_VAR_WOULDYOU",LANG_WOULDYOU);
		$this->ObTpl->set_var("LANG_VAR_YOUHAVE",LANG_YOUHAVE);
		$this->ObTpl->set_var("LANG_VAR_ACCUMULATED",LANG_ACCUMULATED);
		$this->ObTpl->set_var("LANG_VAR_INHISTORY",LANG_INHISTORY);
		if(isset($_SESSION['useMemberPoints']) && $_SESSION['useMemberPoints']=="yes")
		{
		$this->ObTpl->set_var("LANG_VAR_YES","Yes");
		}
		elseif(isset($_SESSION['useMemberPoints']) && $_SESSION['useMemberPoints']=="no")
		{
		$this->ObTpl->set_var("LANG_VAR_YES","No");
		}
		$this->ObTpl->set_var("LANG_VAR_IAGREE",LANG_IAGREE);
		$this->ObTpl->set_var("LANG_VAR_TERMSCONDITIONS",LANG_TERMSCONDITIONS);
		$this->ObTpl->set_var("LANG_VAR_ALLFIELDS",LANG_ALLFIELDS);
		$this->ObTpl->set_var("LANG_VAR_REQUIRED",LANG_ALLREQUIRED);
		$this->ObTpl->set_var("LANG_VAR_IFYOUARENOT",LANG_IFYOUARENOT);
		$this->ObTpl->set_var("LANG_VAR_SIGNINREGMESS",LANG_SIGNINREGISTERMESS);
		$this->ObTpl->set_var("LANG_VAR_CONTINUE",LANG_CONTINUE);
		
		//defining language variables for Payment
		$this->ObTpl->set_var("LANG_VAR_CARDSAVEREDIRECT","Cardsave redirect/hosted 

method");		
		$this->ObTpl->set_var("LANG_VAR_CS_STARTDATE","Start Date");
		$this->ObTpl->set_var("LANG_VAR_CS_MONTH","Start Month");
		$this->ObTpl->set_var("LANG_VAR_CS_YEAR",LANG_YEAR);

		
		
		$this->ObTpl->set_var("LANG_VAR_PAYMENTMETHOD",LANG_PAYMENTMETHOD);
		$this->ObTpl->set_var("LANG_VAR_CREDITDEBIT",LANG_CREDITDEBIT);
		$this->ObTpl->set_var("LANG_VAR_CARDHOLDERNAME",LANG_CARDHOLDERNAME);
		$this->ObTpl->set_var("LANG_VAR_SELECTCARDTYPE",LANG_CARDTYPE);
		$this->ObTpl->set_var("LANG_VAR_SELECTCARDTYPE2",LANG_CARDTYPE2);
		$this->ObTpl->set_var("LANG_VAR_CREDITDEBITNUM",LANG_CREDITDEBITNUMBER);
		$this->ObTpl->set_var("LANG_VAR_EXPIRY",LANG_EXPIRY);
		$this->ObTpl->set_var("LANG_VAR_MONTH",LANG_MONTH);
		$this->ObTpl->set_var("LANG_VAR_YEAR",LANG_YEAR);
		$this->ObTpl->set_var("LANG_VAR_3DIGIT",LANG_3DIGIT);
		$this->ObTpl->set_var("LANG_VAR_CREDITDEBITPHONE",LANG_CREDITDEBITPHONE);
		$this->ObTpl->set_var("LANG_VAR_ONLY",LANG_ONLY);
		$this->ObTpl->set_var("LANG_VAR_ISSUENUMBER",LANG_ISSUENUMBER);
		$this->ObTpl->set_var("LANG_VAR_STARTDATE",LANG_STARTDATE);
		$this->ObTpl->set_var("LANG_VAR_PAYBY",LANG_PAYBY);
		$this->ObTpl->set_var("LANG_VAR_ACCOUNTNUM",LANG_ACCOUNTNUMBER);
		$this->ObTpl->set_var("LANG_VAR_ACCOUNT",LANG_ACCOUNT);
		$this->ObTpl->set_var("LANG_VAR_PAYUSING",LANG_IWOULDLIKE);
		$this->ObTpl->set_var("LANG_VAR_OTHERPAYMETH",LANG_OTHERPAYMETH);
		$this->ObTpl->set_var("LANG_VAR_PAYBYCHEQUE",LANG_PAYBYCHEQUE);
		$this->ObTpl->set_var("LANG_VAR_SENDPAYMENTTO",LANG_SENDPAYMENT);
		$this->ObTpl->set_var("LANG_VAR_CASHONDELMESS",LANG_CASHONDELIVERYMESS);
		$this->ObTpl->set_var("LANG_VAR_CHEQUEPAYMESS",LANG_CHEQUEPAYMESS);
		$this->ObTpl->set_var("LANG_VAR_DISCOUNTHEADER",LANG_DISCCODEHEAD);
		$this->ObTpl->set_var("LANG_VAR_DISCCODETXT",LANG_DISCOUNTCODETEXT);
		$this->ObTpl->set_var("LANG_VAR_DISCCERTTXT",LANG_DISCOUNTCERTTEXT);
		$this->ObTpl->set_var("LANG_VAR_SPECIALREQ",LANG_SPECIALREQ);
		$this->ObTpl->set_var("LANG_VAR_SIGNUPNEWS",LANG_SIGNUPNEWS);
		$this->ObTpl->set_var("LANG_VAR_EMAILTXT",LANG_EMAILTXT);
		$this->ObTpl->set_var("LANG_VAR_PLAINEMAILTXT",LANG_PLAINEMAIL);
		$this->ObTpl->set_var("LANG_VAR_NOTSURETXT",LANG_NOTSURE);
		$this->ObTpl->set_var("LANG_VAR_NOTHANKSTXT",LANG_NOTHANKS);
		$this->ObTpl->set_var("LANG_VAR_REVIEWTXT",LANG_REVIEWTXT);
		$this->ObTpl->set_var("LANG_VAR_REVIEWORDER",LANG_REVIEWORDER);
		if(isset($_SESSION['cardsave_error']) && $_SESSION['cardsave_error']!=''){
			$this->ObTpl->set_var("TPL_VAR_MSG",$_SESSION['cardsave_error']);
		} 
		if(isset($_SESSION['Message']) && $_SESSION['Message']!='' && !isset($_SESSION['cardsave_error'])){
			$this->ObTpl->set_var("TPL_VAR_MSG",$_SESSION['Message']);
		}
		$_SESSION['cardsave_error']="";
		if(isset($_SESSION['paypaldirecterr']) && $_SESSION['paypaldirecterr']==1)
        {
           $this->err==1;
           $this->errMsg = urldecode($_SESSION['paypaldirectMsg']);
           unset($_SESSION['paypaldirecterr']);
           unset($_SESSION['paypaldirectMsg']);
        }

		if(isset($_SESSION['discountCode']) && !empty($_SESSION['discountCode']))
		{
			$this->ObTpl->set_var("TPL_VAR_DISCOUNTCODE","<input type='hidden' name='discount' value='".$_SESSION['discountCode']."'/>");
		}
		else
		{
			$this->ObTpl->set_var("TPL_VAR_DISCOUNTCODE","");
		}
		if(isset($_SESSION['giftCertCode']) && !empty($_SESSION['giftCertCode']))
		{
			$this->ObTpl->set_var("TPL_VAR_GIFTCERTCODE","<input type='hidden' name='giftcert' value='".$_SESSION['giftCertCode']."'/>");
		}
		else
		{
			$this->ObTpl->set_var("TPL_VAR_GIFTCERTCODE","");
		}
		
        if($this->err==1)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",$this->errMsg);	
		}
		elseif(!empty($this->errMsg))
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",$this->errMsg);	
		}
		
		//Propay Gateway Integration: Starts
		if($_SESSION['pro'] != ""){
			$this->ObTpl->set_var("TPL_VAR_MSG",$_SESSION['pro']);
			$_SESSION['pro'] = "";
		}
		//Propay Gateway Integration: Ends

		if(isset($_SESSION['userid']))	{
			$this->ObTpl->set_var("TPL_VAR_NAME",$this->libFunc->m_displayContent($this->libFunc->m_getName($_SESSION['userid'])));
			$this->ObTpl->parse("welcome_blk","TPL_VAT_WELCOMEMSG");
		}

		
		$logoutUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=checkout.logout"); 
		$this->ObTpl->set_var("TPL_VAR_LOGOUTURL",$logoutUrl);

		$formUrl=$this->libFunc->m_safeUrl(SITE_SAFEURL."ecom/index.php?action=checkout.addBillShipInfo");
		$this->ObTpl->set_var("TPL_VAR_FORMURL",$formUrl);
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
		$row_customer[0]->iMailList = "";
		$row_customer[0]->iStatus = "1";

		$this->ObTpl->set_var("nBillCountry","");
		$this->ObTpl->set_var("nShipCountry","");

		#DISPLAYING MESSAGES
		if($this->err==1)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",$this->errMsg);
		}	

		/*CHECKING FOR POST VARIABLES
		IF VARIABLES ARE SET THEN ASSIGNING THEIR VALUE TO VARIABLE SAMEVARIABLE
		AS USED WHEN RETURNED FROM DATABASE
		THIS THING IS USED TO REMOVE REDUNDANCY AND USE SAME FORM FOR EDIT AND INSERT*/
	
		if(isset($_POST['first_name']))
		{
			$row_customer[0]->vPassword=$this->libFunc->ifSet($this->request,'txtpassword',' ');
			$row_customer[0]->vFirstName=$this->libFunc->ifSet($this->request,'first_name',' ');
			$row_customer[0]->vLastName=$this->libFunc->ifSet($this->request,'last_name','');
			$row_customer[0]->vCompany=$this->libFunc->ifSet($this->request,'company','');
			$row_customer[0]->vEmail=$this->libFunc->ifSet($this->request,'email',' ');
			$row_customer[0]->vAddress1=$this->libFunc->ifSet($this->request,'address1','');
			$row_customer[0]->vAddress2=$this->libFunc->ifSet($this->request,'address2','');
			$row_customer[0]->vCity=$this->libFunc->ifSet($this->request,'city',' ');
			$row_customer[0]->vState=$this->libFunc->ifSet($this->request,'bill_state_id','');
			$row_customer[0]->vStateName=$this->libFunc->ifSet($this->request,'bill_state',' ');
			$row_customer[0]->vZip=$this->libFunc->ifSet($this->request,'zip',' ');
			$row_customer[0]->vCountry=$this->libFunc->ifSet($this->request,'bill_country_id',' ');
			$row_customer[0]->vPhone=$this->libFunc->ifSet($this->request,'phone',' ');
			$row_customer[0]->vHomePage=$this->libFunc->ifSet($this->request,'homepage',' ');
			$this->ObTpl->set_var("TPL_VAR_MODE","Add");
			
			if(!isset($_SESSION['userid']))
			{
				$this->ObTpl->parse("password_blk","TPL_PASSWORD_BLK");
			}
		}
		elseif(isset($_SESSION['userid']) && $_SESSION['userid'] !=0)		#IF EDIT MODE SELECTED
		{
			$this->obDb->query = "SELECT * FROM ".CUSTOMERS." WHERE iCustmerid_PK  ='".$_SESSION['userid']."'";
			$row_customer=$this->obDb->fetchQuery();
			$recordCount=$this->obDb->record_count;
			if($recordCount!=1)
			{
				$this->libFunc->m_sessionUnregister("userid");
				$this->libFunc->m_sessionUnregister("username");
				$retUrl=$this->libFunc->m_safeUrl(SITE_SAFEURL."ecom/index.php?action=checkout.billing");
				$_SESSION['referer']=$retUrl;
				$siteUrl=SITE_URL."user/index.php?action=user.loginForm";
				$this->libFunc->m_mosRedirect($this->libFunc->m_safeUrl($siteUrl));
				exit;
			}		
			$this->ObTpl->set_var("TPL_VAR_READONLY","readonly");

			$this->ObTpl->set_var("TPL_VAR_MODE","edit");
		}	
		elseif(isset($_SESSION['customer']) )
		{
			
			$row_customer[0]->vPassword=$_SESSION['txtpassword'];
			$row_customer[0]->vFirstName=$_SESSION['first_name'];
			$row_customer[0]->vLastName=$_SESSION['last_name'];
			$row_customer[0]->vEmail=$_SESSION['email'];
			$row_customer[0]->vAddress1=$_SESSION['address1'];
			$row_customer[0]->vAddress2=$_SESSION['address2'];
			$row_customer[0]->vCity=$_SESSION['city'];	
			$row_customer[0]->vState=$_SESSION['bill_state_id'];
			$row_customer[0]->vStateName=$_SESSION['bill_state'];
			$row_customer[0]->vCountry=$_SESSION['bill_country_id'];
			$row_customer[0]->vZip=$_SESSION['zip'];
			$row_customer[0]->vCompany=$_SESSION['company'];	
			$row_customer[0]->vPhone=$_SESSION['phone'];
			$row_customer[0]->vHomePage=$_SESSION['homepage'];
			$this->ObTpl->set_var("TPL_VAR_READONLY","");
			$this->ObTpl->set_var("TPL_VAR_MODE","edit");
		}
		else #IF ADD
		{
			$row_customer[0]->vEmail=$this->request['email'];
			//$this->ObTpl->set_var("TPL_VAR_READONLY","readonly");
			$this->ObTpl->set_var("TPL_VAR_MODE","Add");
		}	

		if(!isset($_SESSION['userid']))
		{
			$this->ObTpl->parse("password_blk","TPL_PASSWORD_BLK");
		}


		#ASSIGNING FORM ACTION						
		$this->ObTpl->set_var("FORM_URL", SITE_SAFEURL."user/adminindex.php?action=user.updateUser");
		
		$this->obDb->query = "SELECT iStateId_PK, vStateName FROM ".STATES." ORDER BY vStateName";
		$row_state = $this->obDb->fetchQuery();
		$row_state_count = $this->obDb->record_count;
		
		$this->obDb->query = "SELECT iCountryId_PK, vCountryName, vShortName FROM  ".COUNTRY." WHERE iStatus = 1 ORDER BY iSortFlag,vCountryName";
		$row_country = $this->obDb->fetchQuery();
		$row_country_count = $this->obDb->record_count;
		
		$billStateId=$this->libFunc->m_displayContent($row_customer[0]->vState);
		if(isset($row_customer[0]->vCountry) && $row_customer[0]->vCountry>0){
			$billCountry=$row_customer[0]->vCountry;
		}else{
			$billCountry=SELECTED_COUNTRY;
		}
		
		$shipCountry=$this->libFunc->ifSet($_SESSION,'ship_country_id',$billCountry);
		$shipState=$this->libFunc->ifSet($_SESSION,'ship_state_id',$billStateId);

		#Loading billing country list		
		for($i=0;$i<$row_country_count;$i++)
		{
			$this->ObTpl->set_var("k", $row_country[$i]->iCountryId_PK);
			$this->ObTpl->parse('countryblks','countryblk',true);
			$this->ObTpl->set_var("TPL_COUNTRY_VALUE", $row_country[$i]->iCountryId_PK);
			
			if($billCountry == $row_country[$i]->iCountryId_PK)
				$this->ObTpl->set_var("BILL_COUNTRY_SELECT", "selected");
			else
				$this->ObTpl->set_var("BILL_COUNTRY_SELECT",'');
			if($shipCountry == $row_country[$i]->iCountryId_PK)
				$this->ObTpl->set_var("SHIP_COUNTRY_SELECT", "selected");
			else
				$this->ObTpl->set_var("SHIP_COUNTRY_SELECT",'');
			$this->ObTpl->set_var("TPL_COUNTRY_NAME",$this->libFunc->m_displayContent($row_country[$i]->vCountryName));
			$this->ObTpl->parse("nBillCountry","BillCountry",true);
			$this->ObTpl->parse("nShipCountry","ShipCountry",true);
		}
		
		if($row_customer[0]->vCountry != ''){	
			$this->ObTpl->set_var('selbillcountid',$row_customer[0]->vCountry);
		}else{
			$this->ObTpl->set_var('selbillcountid',SELECTED_COUNTRY);
		}

		$this->ObTpl->set_var('selshipcountid',$shipCountry);

		if($row_customer[0]->vState != '')
			$this->ObTpl->set_var('selbillstateid',$row_customer[0]->vState);
		else
			$this->ObTpl->set_var('selbillstateid',0);
		

		if($shipState!= '')
			$this->ObTpl->set_var('selshipstateid',$shipState);
		else
			$this->ObTpl->set_var('selshipstateid',0);
		
			
		
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

		
		#ASSIGNING FORM VARAIABLES
		$fName=$row_customer[0]->vFirstName;
		$lName=$row_customer[0]->vLastName;
		$address1=$row_customer[0]->vAddress1;
		$address2=$row_customer[0]->vAddress2;
		$billCity=$row_customer[0]->vCity;
		$billStateName=$row_customer[0]->vStateName;
		$phone=$row_customer[0]->vPhone;
		$zip=$row_customer[0]->vZip;

		$this->ObTpl->set_var("TPL_VAR_FNAME",$this->libFunc->m_displayContent($fName));
		$this->ObTpl->set_var("TPL_VAR_LNAME",$this->libFunc->m_displayContent($lName));
		$this->ObTpl->set_var("TPL_VAR_EMAIL", $this->libFunc->m_displayContent($row_customer[0]->vEmail));
		$this->ObTpl->set_var("TPL_VAR_PASS", $this->libFunc->m_displayContent($row_customer[0]->vPassword));
		$this->ObTpl->set_var("TPL_VAR_ADDRESS1",$this->libFunc->m_displayContent($address1));
		$this->ObTpl->set_var("TPL_VAR_ADDRESS2",$this->libFunc->m_displayContent($address2));
		$this->ObTpl->set_var("TPL_VAR_CITY",$this->libFunc->m_displayContent($billCity));

		//$this->ObTpl->set_var("TPL_VAR_STATE",$stateid);
		if($row_customer[0]->vState>1)
			{
				$this->ObTpl->set_var("BILL_STATE","");
			}
			else
			{
				$this->ObTpl->set_var("BILL_STATE",$this->libFunc->m_displayContent($billStateName));
			}
	//	$this->ObTpl->set_var("TPL_VAR_COUNTRY",$country);
		$this->ObTpl->set_var("TPL_VAR_ZIP",$this->libFunc->m_displayContent($zip));
		$this->ObTpl->set_var("TPL_VAR_COMPANY",
			$this->libFunc->m_displayContent($row_customer[0]->vCompany));
		$this->ObTpl->set_var("TPL_VAR_PHONE",$this->libFunc->m_displayContent($phone));
		$this->ObTpl->set_var("TPL_VAR_HOMEPAGE",
		$this->libFunc->m_displayContent($row_customer[0]->vHomePage));

		if(OFFERMPOINT==1 && $row_customer[0]->fMemberPoints>0)
			{
				//$this->memPoints=MPOINTVALUE*$this->subTotal;
				$this->ObTpl->set_var("TPL_VAR_MPOINTS",number_format($row_customer[0]->fMemberPoints,0));
				$this->ObTpl->parse("memberpoint_blk","TPL_MPOINTS_BLK");	
			}
	
		#HISTORY MEMBER POINTS
		$_SESSION['memberPoints']=$row_customer[0]->fMemberPoints;

		//Added 5/5/12
		$this->ObTpl->set_var("TPL_VAR_MPOINTS_VALUE",$_SESSION['memberPointsValue']);
		$this->ObTpl->set_var("TPL_VAR_DISCOUNT_VALUE",$_SESSION['DiscountCodeValue']);
		$this->ObTpl->set_var("TPL_VAR_GIFTCERT_VALUE",$_SESSION['GiftCertCodeValue']);
		
		
		$name=$fName." ".$lName;

		$this->ObTpl->set_var("TPL_VAR_ALTFNAME",$this->libFunc->m_displayContent($this->libFunc->ifSet($_SESSION,'alt_fName',$fName)));
		$this->ObTpl->set_var("TPL_VAR_ALTLNAME",$this->libFunc->m_displayContent($this->libFunc->ifSet($_SESSION,'alt_lName',$lName)));
		$this->ObTpl->set_var("TPL_VAR_ALTADDR1",$this->libFunc->m_displayContent($this->libFunc->ifSet($_SESSION,'alt_address1',$address1)));
		$this->ObTpl->set_var("TPL_VAR_ALTADDR2",$this->libFunc->m_displayContent($this->libFunc->ifSet($_SESSION,'alt_address2',$address2)));
		$this->ObTpl->set_var("TPL_VAR_ALTCITY",$this->libFunc->m_displayContent($this->libFunc->ifSet($_SESSION,'alt_city',$billCity)));
		
		$this->ObTpl->set_var("SHIP_STATE",$this->libFunc->m_displayContent($this->libFunc->ifSet($_SESSION,'ship_state',$billStateName)));
		$this->ObTpl->set_var("TPL_VAR_ALTZIP",$this->libFunc->m_displayContent($this->libFunc->ifSet($_SESSION,'alt_zip',$zip)));
		$this->ObTpl->set_var("TPL_VAR_ALTPHONE",$this->libFunc->m_displayContent($this->libFunc->ifSet($_SESSION,'alt_phone',$phone)));
		if(DELIVERY_ADDRESS==1)
		{
			$this->ObTpl->parse("altship_blk","TPL_ALTSHIP_BLK");
		}
		
// Select postage start
	if(!isset($_SESSION['freeShip']) || $_SESSION['freeShip']!=1)
		{
		
			if(!isset($_SESSION['defPostageMethod']) || !isset($_SESSION['defPostagePrice']))
			{
				$retUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=ecom.viewcart");
				//$this->libFunc->m_mosRedirect($retUrl);	
				die("Postage Price not set or is zero and there is no Free Postage. Postage Price: " . $_SESSION['defPostagePrice'] . "Method: " . $_SESSION['defPostageMethod']);
			}
		
			$this->ObTpl->set_var("TPL_VAR_DEFAULT_POSTAGEMETHOD",$_SESSION['defPostageMethod']);
			$this->ObTpl->set_var("TPL_VAR_DEFAULT_POSTAGEPRICE",number_format($_SESSION['defPostagePrice'],2));
			$_SESSION['postageoptions'][0] = $_SESSION['defPostagePrice'];
			//--
			if($_SESSION['zoneSpecialDelivery'] >0 && DEFAULT_POSTAGE_METHOD=='zones')
            {
                $postagePrice=$_SESSION['zoneSpecialDelivery'];
                $this->ObTpl->set_var("TPL_VAR_DISPLAYPRICE",number_format($postagePrice,2));
                $this->ObTpl->set_var("TPL_VAR_POSTAGEMETHOD","Special Delivery");
                
                $this->ObTpl->set_var("TPL_VAR_DEFAULT_POSTAGEMETHOD",$_SESSION['defPostageMethod']);
                $this->ObTpl->set_var("TPL_VAR_DEFAULT_POSTAGEPRICE",$_SESSION['postagePrice']);
                
                
                $this->ObTpl->set_var("TPL_VAR_METHODID","1");
                $this->ObTpl->set_var("TPL_VAR_POSTAGEPRICE",$_SESSION['postagePrice']);
				$_SESSION['postageoptions'][1] = $_SESSION['postagePrice'];
                $this->ObTpl->parse("postage_blk","TPL_POSTAGE_BLK");
                
                $this->ObTpl->set_var("TPL_VAR_METHODID","2");
                $this->ObTpl->set_var("TPL_VAR_POSTAGEPRICE",number_format($postagePrice,2));
				$_SESSION['postageoptions'][2] = $_SESSION['postagePrice'];
                $this->ObTpl->parse("postage_blk","TPL_POSTAGE_BLK");
                                    
                $this->ObTpl->parse("default_postage_blk","TPL_DEFAULTPOSTAGE_BLK");            
                //$this->ObTpl->parse("special_postage_blk","TPL_SPECIALPOSTAGE_BLK");
                $this->ObTpl->parse("specialrate_blk","TPL_SPECIALRATE_BLK",true);
            }elseif($_SESSION['citySpecialDelivery'] >0 && DEFAULT_POSTAGE_METHOD=='cities')
            {
                $postagePrice=$_SESSION['citySpecialDelivery'];
                $this->ObTpl->set_var("TPL_VAR_DISPLAYPRICE",number_format($postagePrice,2));
                $this->ObTpl->set_var("TPL_VAR_POSTAGEMETHOD","Special Delivery");
                
                $this->ObTpl->set_var("TPL_VAR_DEFAULT_POSTAGEMETHOD",$_SESSION['defPostageMethod']);
                $this->ObTpl->set_var("TPL_VAR_DEFAULT_POSTAGEPRICE",$_SESSION['postagePrice']);
                
                
                $this->ObTpl->set_var("TPL_VAR_METHODID","1");
                $this->ObTpl->set_var("TPL_VAR_POSTAGEPRICE",$_SESSION['postagePrice']);
				$_SESSION['postageoptions'][1] = $_SESSION['postagePrice'];
                $this->ObTpl->parse("postage_blk","TPL_POSTAGE_BLK");
                
                $this->ObTpl->set_var("TPL_VAR_METHODID","2");
                $this->ObTpl->set_var("TPL_VAR_POSTAGEPRICE",number_format($postagePrice,2));
				$_SESSION['postageoptions'][2] = $_SESSION['postagePrice'];
                $this->ObTpl->parse("postage_blk","TPL_POSTAGE_BLK");
                                    
                $this->ObTpl->parse("default_postage_blk","TPL_DEFAULTPOSTAGE_BLK");            
                //$this->ObTpl->parse("special_postage_blk","TPL_SPECIALPOSTAGE_BLK");
                $this->ObTpl->parse("specialrate_blk","TPL_SPECIALRATE_BLK",true);
            }elseif(DEFAULT_POSTAGE_METHOD=='zones' && $_SESSION['zoneSpecialDelivery']==0)
            {
            $this->ObTpl->set_var("TPL_VAR_DEFAULT_POSTAGEPRICE",number_format($_SESSION['postagePrice'],2));
            $this->ObTpl->parse("default_postage_blk","TPL_DEFAULTPOSTAGE_BLK");            
            }elseif(DEFAULT_POSTAGE_METHOD=='cities' && $_SESSION['cotySpecialDelivery']==0)
            {
            $this->ObTpl->set_var("TPL_VAR_DEFAULT_POSTAGEPRICE",number_format($_SESSION['postagePrice'],2));
            $this->ObTpl->parse("default_postage_blk","TPL_DEFAULTPOSTAGE_BLK");            
            }    
			//--
			#IF SPECIAL POSTAGE IS NOT ENABLED THE DEFAULT POSTAGE OPTION WILL BE DISPLAYED 
			#OTHERWISE DEFAULT RATES WILL BE ADDED TO SPECIAL
			if(!SPECIAL_POSTAGE){
				$this->ObTpl->parse("default_postage_blk","TPL_DEFAULTPOSTAGE_BLK");
			}else{
				$this->ObTpl->parse("default_postage_blk","TPL_DEFAULTPOSTAGE_BLK");
                $this->ObTpl->parse("special_postage_blk","TPL_SPECIALPOSTAGE_BLK");
			}

			$this->obDb->query ="SELECT vField1,vField2,iPostDescId_PK,PD.vDescription FROM  ".POSTAGE." P,".POSTAGEDETAILS." PD WHERE iPostId_PK=iPostId_FK AND vKey='special' AND iStatus='1'";
			$rsPostage=$this->obDb->fetchQuery();
			$rsCount=$this->obDb->record_count;
			if($rsCount>0 && SPECIAL_POSTAGE)
			{
				for($j=0;$j<$rsCount;$j++)
				{
					$this->ObTpl->set_var("TPL_VAR_METHODID",$rsPostage[$j]->iPostDescId_PK);
					$this->ObTpl->set_var("TPL_VAR_POSTAGEMETHOD",$rsPostage[$j]->vDescription);
					#REASON FOR SUBTRACT 1 is additional after first 
					$addtional=$_SESSION['totalQty']-1;
					if($addtional>0)
					{
						$postagePrice=$rsPostage[$j]->vField1+($rsPostage[$j]->vField2*$addtional);
					}
					else
					{
						$postagePrice=$rsPostage[$j]->vField1;
					}
					$this->ObTpl->set_var("TPL_VAR_DISPLAYPRICE",number_format($postagePrice,2));
					if(SPECIAL_POSTAGE){
						$this->ObTpl->set_var("TPL_VAR_SPECIAL_POSTAGEPRICE",$rsPostage[$j]->vField2);
						//Changed Special Postage to no longer add postage to special postage price
						//$postagePrice=$postagePrice+$_SESSION['defPostagePrice'];
					}
					$this->ObTpl->set_var("TPL_VAR_POSTAGEPRICE",$postagePrice);
				$_SESSION['postageoptions'][$rsPostage[$j]->iPostDescId_PK] = $postagePrice;
					$this->ObTpl->parse("postage_blk","TPL_POSTAGE_BLK",true);
				}
			}else			
			if($_SESSION['zoneSpecialDelivery']==0 || !SPECIAL_POSTAGE)
			{
			$_SESSION['postageId']='0';
			$_SESSION['postageMethod']=$_SESSION['defPostageMethod'];
			$_SESSION['postagePrice']=$_SESSION['defPostagePrice'];
			$_SESSION['postageoptions'][0] = $_SESSION['defPostagePrice'];
			$this->ObTpl->set_var("postage_blk","");	
			}
		$this->ObTpl->parse("specialrate_blk","TPL_SPECIALRATE_BLK");
        }
		elseif(SPECIAL_POSTAGE)
		{
		
			$this->ObTpl->set_var("TPL_VAR_DEFAULT_POSTAGEMETHOD",DEFAULT_POSTAGE_NAME);
			$this->ObTpl->set_var("TPL_VAR_DEFAULT_POSTAGEPRICE",number_format(0,2));
				$this->ObTpl->parse("default_postage_blk","TPL_DEFAULTPOSTAGE_BLK");
                $this->ObTpl->parse("special_postage_blk","TPL_SPECIALPOSTAGE_BLK");

			$this->obDb->query ="SELECT vField1,vField2,iPostDescId_PK,PD.vDescription FROM  ".POSTAGE." P,".POSTAGEDETAILS." PD WHERE iPostId_PK=iPostId_FK AND vKey='special' AND iStatus='1'";
			$rsPostage=$this->obDb->fetchQuery();
			$rsCount=$this->obDb->record_count;
			if($rsCount>0 && SPECIAL_POSTAGE)
			{
				for($j=0;$j<$rsCount;$j++)
				{
					$this->ObTpl->set_var("TPL_VAR_METHODID",$rsPostage[$j]->iPostDescId_PK);
					$this->ObTpl->set_var("TPL_VAR_POSTAGEMETHOD",$rsPostage[$j]->vDescription);
					$postagePrice=0;
					$this->ObTpl->set_var("TPL_VAR_DISPLAYPRICE",number_format($postagePrice,2));
					if(SPECIAL_POSTAGE){
						$this->ObTpl->set_var("TPL_VAR_SPECIAL_POSTAGEPRICE",number_format($postagePrice,2));
					}
					$this->ObTpl->set_var("TPL_VAR_POSTAGEPRICE",$postagePrice);
					$_SESSION['postageoptions'][$rsPostage[$j]->iPostDescId_PK] = $postagePrice;
					$this->ObTpl->parse("postage_blk","TPL_POSTAGE_BLK",true);
				}
			}else			
			if($_SESSION['zoneSpecialDelivery']==0 || !SPECIAL_POSTAGE)
			{
			$_SESSION['postageId']='0';
			$_SESSION['postageMethod']=$_SESSION['defPostageMethod'];
			$_SESSION['postagePrice']=0;
			$_SESSION['postageoptions'][0] = 0;
			$this->ObTpl->set_var("postage_blk","");	
			}
		$this->ObTpl->parse("specialrate_blk","TPL_SPECIALRATE_BLK");
		}
// End Select postage
	
			
		//Payment code start
		$blkactive=0;
		#HANDLING MESSAGES
		if($this->err==1)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",$this->errMsg);	
		}
		elseif(!empty($this->errMsg))
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",$this->errMsg);	
		}
		# Return back from secureTrading payment gateway
		$this->request['payMethod']=$this->libFunc->ifSet($this->request,'payMethod','');
		if($this->request['payMethod']==='securetrading'){
			$this->request['stauthcode']=$this->libFunc->ifSet($this->request,'stauthcode','0');

			$this->obDb->query="UPDATE ".ORDERS." SET v3DSecureStatus='{$this->request['stauthcode']}' where iOrderid_PK={$_SESSION['order_id']}";
			$this->obDb->updateQuery();	
			if($this->request['stauthcode']=='DECLINE') {
				$this->ObTpl->set_var("TPL_VAR_MSG",'The order is Declined.');
			}else{
				$retUrl=$this->libFunc->m_safeUrl(SITE_SAFEURL."ecom/index.php?action=checkout.process&mode=".$_SESSION['order_id']);
				$this->libFunc->m_mosRedirect($retUrl);		
			}
		}#End of securetrading payment gateway


		#FORM URL
		$formUrl=$this->libFunc->m_safeUrl(SITE_SAFEURL."ecom/index.php?action=checkout.review");
		$this->ObTpl->set_var("TPL_VAR_FORMURL",$formUrl);
		#CHECKING FOR MAESTRO CARDS
		if(MAESTRO) {
			$this->solo=1;
			$this->ObTpl->set_var("TPL_VAR_NAME","Maestro");
			$this->ObTpl->set_var("TPL_VAR_VALUE","SWITCH");
			$this->ObTpl->parse("creditcard_blk","TPL_CARDS_BLK",true);
		}
		#CHECKING FOR SWITCH CARDS
		if(SWITCHCARD) {
			$this->solo=1;
			$this->ObTpl->set_var("TPL_VAR_NAME","Switch");
			$this->ObTpl->set_var("TPL_VAR_VALUE","SWITCH");
			$this->ObTpl->parse("creditcard_blk","TPL_CARDS_BLK",true);
		}
		#CHECKING FOR SOLO CARDS
		if(SOLO) {
			$this->solo=1;
			$this->ObTpl->set_var("TPL_VAR_NAME","Solo");
			$this->ObTpl->set_var("TPL_VAR_VALUE","SOLO");
			$this->ObTpl->parse("creditcard_blk","TPL_CARDS_BLK",true);
		}
		#CHECKING FOR VISA CARDS
		if(VISA) {
			$this->ObTpl->set_var("TPL_VAR_NAME","Visa");
			$this->ObTpl->set_var("TPL_VAR_VALUE","VISA");
			$this->ObTpl->parse("creditcard_blk","TPL_CARDS_BLK",true);
		}
		#CHECKING FOR DELTA CARDS
		if(VISA_DELTA) {
			$this->ObTpl->set_var("TPL_VAR_NAME","Visa Debit");
			$this->ObTpl->set_var("TPL_VAR_VALUE","DELTA");
			$this->ObTpl->parse("creditcard_blk","TPL_CARDS_BLK",true);
		}
		#CHECKING FOR ELECTRON CARDS
		if(VISA_ELECTRON) {
			$this->ObTpl->set_var("TPL_VAR_NAME","Visa Electron");
			$this->ObTpl->set_var("TPL_VAR_VALUE","UKE");
			$this->ObTpl->parse("creditcard_blk","TPL_CARDS_BLK",true);
		}
		if(MASTERCARD) {
			$this->ObTpl->set_var("TPL_VAR_NAME","Master Card");
			$this->ObTpl->set_var("TPL_VAR_VALUE","MC");
			$this->ObTpl->parse("creditcard_blk","TPL_CARDS_BLK",true);
		}
		#CHECKING FOR AMEX CARDS
		if(AMEX) {
			$this->ObTpl->set_var("TPL_VAR_NAME","American Express");
			$this->ObTpl->set_var("TPL_VAR_VALUE","AMEX");
			$this->ObTpl->parse("creditcard_blk","TPL_CARDS_BLK",true);
		}
		#CHECKING FOR DinnersClub CARDS
		if(DINERS_CLUB) {
			$this->ObTpl->set_var("TPL_VAR_NAME","Diners Club");
			$this->ObTpl->set_var("TPL_VAR_VALUE","DinersClub");
			$this->ObTpl->parse("creditcard_blk","TPL_CARDS_BLK",true);
		}
		#CHECKING FOR Discover CARDS
		if(DISCOVER) {
			$this->ObTpl->set_var("TPL_VAR_NAME","Discover");
			$this->ObTpl->set_var("TPL_VAR_VALUE","DISCOVER");
			$this->ObTpl->parse("creditcard_blk","TPL_CARDS_BLK",true);
		}

	
	#DB QUERY
	 	$this->obDb->query ="SELECT vDatatype,vSmalltext,nNumberdata FROM  ".SITESETTINGS." WHERE vDatatype='cartPayCC' OR vDatatype='cartPayCCp' OR vDatatype='cartPayEFT' OR vDatatype='cartPayCOD' OR vDatatype='cartPayMail' OR vDatatype='cartMailList' OR vDatatype='CSMerchantID' OR vDatatype='CSPass' OR vDatatype='CSBaseURL' OR vDatatype='CSPort' OR vDatatype='CSRMerchantID' OR vDatatype='CSRCallback' OR vDatatype='CSRPreshared' OR vDatatype='CSRMerchantPass' or vDatatype='paymentCurrency' ORDER BY iSettingid";
				
				
				
		$rsSettings=$this->obDb->fetchQuery();
		$rcount=$this->obDb->record_count;
		for($i=0;$i<$rcount;$i++)	{
			if($rsSettings[$i]->vDatatype=='CSMerchantID' && $rsSettings[$i]->vSmalltext!=''){
				$this->ObTpl->parse("cardsave_start_date_blk","TPL_SC_STARTDATE_BLK");
				$startYear=date('Y')-5;
				for($k=$startYear;$k<date('Y')+10;$k++)
				{
					$this->ObTpl->set_var("TPL_VAR_CS_YEAR",$k);
					$this->ObTpl->parse("cs_startyear_blk","TPL_CSCARDYEAR_BLK",true);
				}
			}
			#CHECKING FOR CARDSAVE
			
			if($rsSettings[$i]->vDatatype=='CSRMerchantID' && 

$rsSettings[$i]->vSmalltext!=''){
				

$this->ObTpl->parse("cardsaveredirect_blk","TPL_CARDSAVEREDIRECT_BLK");
			
			
			if($rsSettings[$i]->vDatatype=='paymentCurrency'){
				$_SESSION['paymentCurrency'] = $rsSettings[$i]->vSmalltext;
					
			}
			
			if($rsSettings[$i]->vDatatype=='CSRMerchantID'){
			

$this->ObTpl->set_var("TPL_VAR_MERCHANTID",$rsSettings[$i]->vSmalltext);			
			}
			if($rsSettings[$i]->vDatatype=='CSRCallback'){
			

$this->ObTpl->set_var("TPL_VAR_CSRCALLBACK",$rsSettings[$i]->vSmalltext);			
			}
			if($rsSettings[$i]->vDatatype=='CSRPreshared'){
			

$this->ObTpl->set_var("TPL_VAR_PERSHAREDKEY",$rsSettings[$i]->vSmalltext);			
			}
			if($rsSettings[$i]->vDatatype=='CSRMerchantPass'){
			

$this->ObTpl->set_var("TPL_VAR_CSRPASSWORD",$rsSettings[$i]->vSmalltext);			
			}
			}
			//// FOR CS METHOD
			if($rsSettings[$i]->vDatatype=='CSMerchantID'){
			

$this->ObTpl->set_var("TPL_VAR_CSMERCHANTID",$rsSettings[$i]->vSmalltext);			
			}
			if($rsSettings[$i]->vDatatype=='CSPass'){
			

$this->ObTpl->set_var("TPL_VAR_CSPASSWORD",$rsSettings[$i]->vSmalltext);			
			}
			if($rsSettings[$i]->vDatatype=='CSBaseURL'){
			

$this->ObTpl->set_var("TPL_VAR_CSBASEURL",$rsSettings[$i]->vSmalltext);			
			}
			if($rsSettings[$i]->vDatatype=='CSPort'){
			$this->ObTpl->set_var("TPL_VAR_CSPORT",$rsSettings[$i]->vSmalltext);	

		
			}
			if(!isset($_SESSION['NextFormMode'])){
				$_SESSION['NextFormMode'] = 'PAYMENT_FORM';
			}
			

$this->ObTpl->set_var("TPL_VAR_NEXTFORMMODE",$_SESSION['NextFormMode']);
			#CHECKING FOR CARDSAVE
			
			#CHECKING FOR CREDIT CARDS AND PAYPAL DIRECT
			#CREDITCARDS MUST BE ENABLED FOR PAYPAL DIRECT TO WORK, PAYPAL DIRECT WILL OVERRIDE IF SETTINGS ARE SET
			if($rsSettings[$i]->vDatatype=="cartPayCC" && $rsSettings[$i]->nNumberdata==1)
			{
				$startYear=date('Y');
				for($j=$startYear;$j<$startYear+6;$j++)
				{
					$this->ObTpl->set_var("TPL_VAR_YEAR",$j);
					$this->ObTpl->parse("credityear_blk","TPL_CARDYEAR_BLK",true);
				}
				#CHECKING FOR PAYPAL DIRECT
				if(PAYPALAPI_USERNAME!="" && PAYPALAPI_SIGNATURE!="" && PAYPALAPI_PASSWORD!="" && PAYPALAPI_ENDPOINT!="" && PAYMENT_CURRENCY!="")
				{
					$this->ObTpl->set_var("TPL_VAR_CC_VALUE","paypaldirect");
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_CC_VALUE","cc");
				}
				$this->ObTpl->parse("credit_blk","TPL_CREDITDEBITCARDS_BLK");
				$this->credit=1;
			}
			#CHECKING FOR CARD ON PHONE
			if($rsSettings[$i]->vDatatype=="cartPayCCp" && $rsSettings[$i]->nNumberdata==1)
			{
				$this->credit=1;
				$this->ObTpl->parse("creditbyphone_blk","TPL_CARDBYPHONE_BLK");
			}
			if($this->credit==1)
			{
				$blkactive=1;
				$this->ObTpl->parse("creditmain_blk","TPL_CREDITDEBITMAIN_BLK");
			}

			
			if($this->solo==1)
			{
				$this->ObTpl->set_var("soloyear_blk","");
				$startYear=date('Y')-5;
				for($k=$startYear;$k<date('Y')+10;$k++)
				{
					$this->ObTpl->set_var("TPL_VAR_YEAR",$k);
					$this->ObTpl->parse("soloyear_blk","TPL_SOLOCARDYEAR_BLK",true);
				}
				$this->ObTpl->parse("solocard_blk","TPL_SOLOCARDS_BLK");			
			}	

			#CHECKING FOR EFT
			if($rsSettings[$i]->vDatatype=="cartPayEFT" && $rsSettings[$i]->nNumberdata==1)
			{
				$blkactive=1;
				$this->ObTpl->parse("echeck_blk","TPL_ECHECK_BLK");
			}

			if($rsSettings[$i]->vDatatype=="cartPayCOD")
			{
				if($rsSettings[$i]->vSmalltext>0)
				{
					$this->codPrice=$rsSettings[$i]->vSmalltext;
					$this->ObTpl->set_var("TPL_VAR_CODPRICE",$this->codPrice);
					$this->ObTpl->parse("cod_blk","TPL_COD_BLK");	
					
				}
			}	
		
			#CHECKING FOR CHEQUE/COD
			if($rsSettings[$i]->vDatatype=="cartPayMail" && $rsSettings[$i]->nNumberdata==1)
			{
				$blkactive=1;
				$this->obDb->query ="SELECT vAddress,vCity,vZip,vState,vStateName,vCountry FROM  ".COMPANYSETTINGS;
				$rsCompany=$this->obDb->fetchQuery();
				$this->ObTpl->set_var("TPL_VAR_COMPANYCHEQUE",$this->libFunc->m_displayContent(SITE_NAME));
				$this->ObTpl->set_var("TPL_VAR_ADDRESSCHEQUE",nl2br($this->libFunc->m_displayContent($rsCompany[0]->vAddress)));
				$this->ObTpl->set_var("TPL_VAR_CITYCHEQUE",$this->libFunc->m_displayContent($rsCompany[0]->vCity));
				$this->ObTpl->set_var("TPL_VAR_ZIPCHEQUE",$this->libFunc->m_displayContent($rsCompany[0]->vZip));
				if($rsCompany[0]->vState>1)
				{
					$this->obDb->query = "SELECT vStateName FROM ".STATES." where iStateId_PK  = '".$rsCompany[0]->vState."'";
					$row_state = $this->obDb->fetchQuery();
					$this->ObTpl->set_var("TPL_VAR_STATE",
					$this->libFunc->m_displayContent($row_state[0]->vStateName));
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_STATE",
					$this->libFunc->m_displayContent($rsCompany[0]->vStateName));
				}
				$this->obDb->query = "SELECT vCountryName FROM ".COUNTRY." where iCountryId_PK  = '".$rsCompany[0]->vCountry."'";
				$row_country = $this->obDb->fetchQuery();
				$this->ObTpl->set_var("TPL_VAR_COUNTRY",
				$this->libFunc->m_displayContent($row_country[0]->vCountryName));
				
				if(trim(COMPANY_VATNUMBER)!="")
				{
					$this->ObTpl->set_var("TPL_VAR_VATNUMBER", VAT_TAX_TEXT." Registration No.: ".COMPANY_VATNUMBER."<br />");
				}

				if(trim(COMPANY_REGISTERNUMBER)!="")
				{
					$this->ObTpl->set_var("TPL_VAR_REGISTERNUMBER","Company Registration No.: ".COMPANY_REGISTERNUMBER."<br />");
				}

				
				$this->ObTpl->parse("other_blk","TPL_OTHER_BLK");
			}

			
			#CHECKING FOR PAYPAL
			if(PAYPAL_ID!="" && PAYMENT_CURRENCY!="")
			{
				$blkactive=1;
				$this->ObTpl->set_var("TPL_VAR_PAYPAL_VALUE","paypal");
				$this->ObTpl->parse("paypal_blk","TPL_PAYPAL_BLK");
			}
			#CHECKING FOR SECPAY
			if(SECPAY_MERCHANT!="" && SECPAY_REMOTEPASSWORD!="" && SECPAY_DIGESTKEY!="")
			{
				$blkactive=1;
				$this->ObTpl->parse("secpay_blk","TPL_SECPAY_BLK");
			}
			#07-05-07
			if(WORLDPAY_INSTID!=""){
				$blkactive=1;
				$this->ObTpl->parse("worldpay_blk","TPL_WORLDPAY_BLK");
			}
			
            #(BEGIN) SAGE PAY INTERGRATION
             if (SAGE_VENDORNAME!="" && SAGE_ENCRYPTEDPASSWORD!="" && SAGE_TRANSACTIONTYPE!="" && SAGE_CURRENCY!="" )
			{
				$blkactive=1;
				$this->ObTpl->parse("sagepayform_blk","TPL_SAGEPAYFORM_BLK");
			}
            #(BEGIN) SAGE PAY INTERGRATION
            
			if($_SESSION['postagedropdown'] == "1"){
				$this->ObTpl->parse("freepost_blk","TPL_FREEPOST_BLK");
			}elseif(DEFAULT_POSTAGE_METHOD!='zones' || DEFAULT_POSTAGE_METHOD!='cities'){
				$this->ObTpl->parse("freepost_blk","TPL_FREEPOST_BLK");
			}

			if(SECURETRADING_CLIENTID!="" && SECURETRADING_PASSWORD!=""){
				$blkactive=1;
				$this->ObTpl->parse("stoff_blk","TPL_STOFF_BLK");
			}
			
			#08-05-07
			if(HSBC_KEY!="" && HSBC_STOREID!=""){
				$blkactive=1;
				$this->ObTpl->parse("hsbc_blk","TPL_HSBC_BLK");
			}
			#08-05-07
			if(BARCLAYS_CLIENTID!="" && BARCLAYS_PASSWORD!=""){
				$blkactive=1;
				$this->ObTpl->parse("barclay_blk","TPL_BARCLAY_BLK");
			}
			#CHECKING FOR NEWSLETTER
            if($rsSettings[$i]->vDatatype=="cartMailList" && $rsSettings[$i]->vSmalltext==1)
			{
				$this->ObTpl->parse("news_blk","TPL_NEWSLETTER_BLK");
			}
		}

		if($blkactive==0)
		{
			$this->ObTpl->parse("nomethod_blk1","TPL_NOMETHOD_BLK1");
			$this->ObTpl->parse("nomethod_blk2","TPL_NOMETHOD_BLK2");
		}

		return $this->ObTpl->parse("return","TPL_USER_FILE");
	}
	
	

	#FUNCTION TO VERIFY DATABASE UPDATION
	function m_verifyBillShipAdd()
	{
		
		$data = implode(",",$this->request);
		if(!$this->libFunc->m_validEmailData($data)){
			$this->err=1;
			$this->errMsg.=MSG_INVALID_EMAILDATA."<br />";
		}
				/* Server side Credit Card Validations. Starts */
		$this->startMsg=1;
		$currentYear=date('Y');
		$currentMonth=date('m');
		if(!isset($this->request['paymethod']))
		{
			$this->err=1;
			$this->errMsg.="Please choose a payment method.<br>";
		}
		if(!isset($this->request['cc_type']))
		{
			$this->request['cc_type']="";
		}
		switch($this->request['paymethod'])
		{
			case "cc":
			
				if(empty($this->request['cc_type']))
				{
					$this->err=1;
					$this->errMsg.=MSG_CCTYPE_EMPTY."<br>";
				}
			
			if($this->request['cc_type']=='SWITCH' || $this->request['cc_type']=='SOLO')
			{
				if(empty($this->request['issuenumber']))
				{
					if(empty($this->request['cc_start_month']) || empty($this->request['cc_start_year']))
					{
						$this->err=1;
						$this->errMsg.=MSG_ISSUENOANDDATE_EMPTY."<br>";
					}
					else
					{
						if($this->request['cc_start_year']>$currentYear)
						{
							$this->startMsg=0;
							$this->err=1;
							$this->errMsg.=MSG_CCSTARTDATE_INVALID."<br>";
						}
						elseif($this->request['cc_start_year']==$currentYear)
						{
							if($this->request['cc_start_month'] != $currentMonth){
							if($this->request['cc_start_month'] > $currentMonth)
							{
								$this->startMsg=0;
								$this->err=1;
								$this->errMsg.=MSG_CCSTARTDATE_INVALID."<br>";
							}
							}
						}
					}
				}
			}
			if(empty($this->request['cc_number']))
			{
				$this->err=1;
				$this->errMsg.=MSG_CCNUM_EMPTY."<br>";
			}
			if(empty($this->request['cc_month']))
			{
				$this->err=1;
				$this->errMsg.=MSG_CCMONTH_EMPTY."<br>";
			}
			if(empty($this->request['cc_year']))
			{
				$this->err=1;
				$this->errMsg.=MSG_CCYEAR_EMPTY."<br>";
			}

			if($this->request['cc_year']<$currentYear)
			{
				$this->err=1;
				$this->errMsg.=MSG_CCEXPDATE_INVALID."<br>";
			}
			elseif($this->request['cc_year']==$currentYear)
			{
				if($this->request['cc_month']<$currentMonth)
				{
					$this->err=1;
					$this->errMsg.=MSG_CCEXPDATE_INVALID."<br>";
				}
			}
			if($this->startMsg==1 && (!empty($this->request['cc_start_month']) || !empty($this->request['cc_start_year'])))
			{
				if($this->request['cc_start_year']>$currentYear)
				{
					$this->err=1;
					$this->errMsg.=MSG_CCSTARTDATE_INVALID."<br>";
				}
				elseif($this->request['cc_start_year']==$currentYear)
				{
					if($this->request['cc_start_month'] != $currentMonth){
					if($this->request['cc_start_month'] > $currentMonth)
					{
						$this->err=1;
						$this->errMsg.=MSG_CCSTARTDATE_INVALID."<br>";
					}
					}
				}
			}

			if(empty($this->request['cv2']))
			{
				$this->err=1;
				$this->errMsg.=MSG_CV2_EMPTY."<br>";
			}
			break;
			case "eft":
			if(empty($this->request['acct']))
			{
				$this->err=1;
				$this->errMsg.=MSG_ACCT_EMPTY."<br>";
			}
			if(empty($this->request['aba']))
			{
				$this->err=1;
				$this->errMsg.=MSG_ABA_EMPTY."<br>";
			}
			break;
		}

		/* Credit Card Validations. Ends */
		if(empty($this->request['first_name']))
		{
			$this->err=1;
			$this->errMsg=MSG_FIRSTNAME_EMPTY."<br>";
		}
		if(empty($this->request['last_name']))
		{
			$this->err=1;
			$this->errMsg.=MSG_LASTNAME_EMPTY."<br>";
		}
        if(empty($this->request['paymethod']))
		{
			
			$this->err=1;
			$this->errMsg.=MSG_NOPAYMENT."<br>";
		}
		if(empty($this->request['email']))
		{
			$this->err=1;
			$this->errMsg.=MSG_EMAIL_EMPTY."<br>";
		}
		if(!$this->libFunc->m_validateEmail($this->request['email']))
		{
			$this->err=1;
			$this->errMsg.=MSG_INVALID_EMAILADDR."<br />";
		}
		if(isset($this->request['txtpassword']))
		{
			if(!empty($this->request['txtpassword']))
			{
				if(empty($this->request['verify_pw']))
				{
					$this->err=1;
					$this->errMsg.=MSG_VERIFYPASS_EMPTY."<br>";
				}
				if($this->request['txtpassword']!=$this->request['verify_pw'])
				{
					$this->err=1;
					$this->errMsg.=MSG_PASS_NOTMATCHED."<br>";
				}
				if($this->m_valiadateEmail()==1){
					$this->err=1;
					$this->errMsg.="Email address already in use. Please login to continue with this email address.<br>";
				}
			}
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
		if(empty($this->request['phone']))
		{
			$this->err=1;
			$this->errMsg.=MSG_PHONE_EMPTY."<br>";
		}
		if(empty($this->request['zip']))
		{
			$this->err=1;
			$this->errMsg.=MSG_ZIP_EMPTY."<br>";
		}
		if(DELIVERY_ADDRESS==1 && $this->request['alt_ship']!=1) 
		{
			//if(empty($this->request['alt_name']))
			//{
				//$this->err=1;
				//$this->errMsg.=MSG_ALTNAME_EMPTY."<br>";
			//}
		
			if(empty($this->request['alt_address1']))
			{
				$this->err=1;
				$this->errMsg.=MSG_ALTADDRESS1_EMPTY."<br>";
			}

			if(empty($this->request['alt_city']))
			{
				$this->err=1;
				$this->errMsg.=MSG_ALTCITY_EMPTY."<br>";
			}
			if(empty($this->request['alt_zip']))
			{
				$this->err=1;
				$this->errMsg.=MSG_ALTZIP_EMPTY."<br>";
			}
			if(empty($this->request['alt_phone']))
			{
				$this->err=1;
				$this->errMsg.=MSG_ALTPHONE_EMPTY."<br>";
			}
		}
			if(SPECIAL_POSTAGE == 1)
			{
				if(isset($this->request['ship_id']) || !empty($this->request['ship_id']))
				{
				}
				else
				{
				$this->err=1;
				$this->errMsg.="Please choose a postage method.<br>";
				}
			}
                $returnstring = $this->errMsg;
		return $returnstring;
	}

	
	function m_saveBillShipInfo() {
		
			//CARDSAVE
			if($this->request['paymethod']=='cs_redirect'){
				$_SESSION['MerchantID']=$this->request['MerchantID'];
				$_SESSION['CSRPreshared']=$this->request['CSRPreshared'];
				$_SESSION['CSRMerchantPass']=$this->request['CSRMerchantPass'];
				$_SESSION['CallbackURL']=$this->request['CallbackURL'];
				$_SESSION['CV2Mandatory']=$this->request['CV2Mandatory'];
				$_SESSION['Address1Mandatory']=$this->request['Address1Mandatory'];
				$_SESSION['CityMandatory']=$this->request['CityMandatory'];
				$_SESSION['PostCodeMandatory']=$this->request['PostCodeMandatory'];
				$_SESSION['StateMandatory']=$this->request['StateMandatory'];
				$_SESSION['CountryMandatory']=$this->request['CountryMandatory'];
				///////
				$_SESSION['CSMerchantID']=$this->request['CSMerchantID'];
				$_SESSION['CSPass']=$this->request['CSPass'];
				$_SESSION['CSBaseURL']=$this->request['CSBaseURL'];
				$_SESSION['CSPort']=$this->request['CSPort'];
			}
			$libFunc=new c_libFunctions();
			$comFunc=new c_commonFunctions();
			$comFunc->obDb=$this->obDb;	
			
			$m=$this->request['ship_id'];
			$_SESSION['postageId']=$m;
			$_SESSION['postageMethod']=$this->request['ship_method'][$m];

			$_SESSION['postagePrice']=$_SESSION['postageoptions'][$_SESSION['postageId']];
			$_SESSION['payMethod'] = $this->request['paymethod'];
			if($_SESSION['payMethod'] == "cod"){
				$_SESSION['codPrice'] = $this->request['codprice'];
			}else{
				$_SESSION['codPrice'] = "";
			}
			if(isset($this->request['mail_list'])){
				 $_SESSION['mail_list'] = $this->request['mail_list'];
			}else{
				$_SESSION['mail_list'] = "";
			}
			//Handling Discounts
				$_SESSION['discountCode']=$this->request['discount'];
				$this->discountPrice=$comFunc->m_calculateDiscount($this->request['discount']);
			
			//Handling Gift certficates
				$_SESSION['giftCertCode']=$this->request['giftcert'];
				$this->giftCertPrice=$comFunc->m_calculateGiftCertPrice($this->request['giftcert']);
			
			
				 // Begin Card Holder Protx Modification 
                if($this->libFunc->ifSet($this->request,'cardholder_name',''))
                {
                    $_SESSION['cardholder_name']=$this->request['cardholder_name'];
                }
                else
                {
                  $_SESSION['cardholder_name']=$this->libFunc->ifSet($_SESSION,'cardholder_name','');
                }
			// End Card Holder Protx Modification 
		if($this->libFunc->ifSet($this->request,'cc_number',''))
		{
			$_SESSION['cc_number']=$this->request['cc_number'];
		}
		else
		{
			$_SESSION['cc_number']=$this->libFunc->ifSet($_SESSION,'cc_number','');
		}
		
		if($this->libFunc->ifSet($this->request,'cc_type',''))
		{
			$_SESSION['cc_type']=$this->request['cc_type'];
		}
		else
		{
			$_SESSION['cc_type']=$this->libFunc->ifSet($_SESSION,'cc_type','');
		}
		
		if($this->libFunc->ifSet($this->request,'cv2',''))
		{
			$_SESSION['cv2']=$this->request['cv2'];
		}
		else
		{
			$_SESSION['cv2']=$this->libFunc->ifSet($_SESSION,'cv2','');
		}
		
		if($this->libFunc->ifSet($this->request,'cc_year',''))
		{
			$_SESSION['cc_year']=$this->request['cc_year'];
		}
		else
		{
			$_SESSION['cc_year']=$this->libFunc->ifSet($_SESSION,'cc_year','');
		}

		if($this->libFunc->ifSet($this->request,'cc_month','0'))
		{
			$_SESSION['cc_month']=$this->request['cc_month'];
		}
		else
		{
			$_SESSION['cc_month']=$this->libFunc->ifSet($_SESSION,'cc_month','');
		}

		if($this->libFunc->ifSet($this->request,'cc_start_year','0'))
		{
			$_SESSION['cc_start_year']=$this->request['cc_start_year'];
		}
		else
		{
			$_SESSION['cc_start_year']=$this->libFunc->ifSet($_SESSION,'cc_start_year','');
		}

		if($this->libFunc->ifSet($this->request,'cc_start_month','0'))
		{
			$_SESSION['cc_start_month']=$this->request['cc_start_month'];
		}
		else
		{
			$_SESSION['cc_start_month']=$this->libFunc->ifSet($_SESSION,'cc_start_month','');
		}

		if($this->libFunc->ifSet($this->request,'issuenumber','0'))
		{
			$_SESSION['issuenumber']=$this->request['issuenumber'];
		}
		else
		{
			$_SESSION['issuenumber']=$this->libFunc->ifSet($_SESSION,'issuenumber','');
		}

		if($this->libFunc->ifSet($this->request,'acct','0'))
		{
			$_SESSION['acct']=$this->request['acct'];
		}
		else
		{
			$_SESSION['acct']=$this->libFunc->ifSet($_SESSION,'acct','0');
		}

		if($this->libFunc->ifSet($this->request,'aba','0'))
		{
			$_SESSION['aba']=$this->request['aba'];
		}
		else
		{
			$_SESSION['aba']=$this->libFunc->ifSet($_SESSION,'aba','0');
		}
		 
		 //----
			
		if(!isset($this->request['bill_state_id']) || empty($this->request['bill_state_id']))
		{
			$this->request['bill_state_id']="";
		}
		else
		{
			$this->request['bill_state']="";
		}
		if(!isset($this->request['ship_state_id']) || empty($this->request['ship_state_id']))
		{
			$this->request['ship_state_id']="";
		}
		else
		{
			$this->request['ship_state']="";
		}

		if(isset($_SESSION['userid']) && !empty($_SESSION['userid']))
		{
			#INSERTING CUSTOMER
			$this->obDb->query="UPDATE ".CUSTOMERS." SET 
			vFirstName='".$this->libFunc->m_addToDB($this->request['first_name'])."',
			vLastName='".$this->libFunc->m_addToDB($this->request['last_name'])."',
			vAddress1='".$this->libFunc->m_addToDB($this->request['address1'])."',
			vAddress2='".$this->libFunc->m_addToDB($this->request['address2'])."',
			vCity='".$this->libFunc->m_addToDB($this->request['city'])."',
			vState='".$this->libFunc->m_addToDB($this->request['bill_state_id'])."',
			vStateName='".$this->libFunc->m_addToDB($this->request['bill_state'])."',
			vCountry='".$this->libFunc->m_addToDB($this->request['bill_country_id'])."',
			vZip='".$this->libFunc->m_addToDB($this->request['zip'])."',
			vCompany ='".$this->libFunc->m_addToDB($this->request['company'])."',
			vPhone ='".$this->libFunc->m_addToDB($this->request['phone'])."',
			vHomePage ='".$this->libFunc->m_addToDB($this->request['homepage'])."'
			WHERE (iCustmerid_PK ='".$_SESSION['userid']."')";
			$this->obDb->updateQuery();
			
			$_SESSION['first_name']		=$this->request['first_name'];
			$_SESSION['last_name']		=$this->request['last_name'];
			$_SESSION['email']				=$this->request['email'];
			$_SESSION['address1']			=$this->request['address1'];
			$_SESSION['address2']			=$this->request['address2'];
			$_SESSION['city']					=$this->request['city'];	
			$_SESSION['bill_state_id']		=$this->request['bill_state_id'];
			$_SESSION['bill_state']			=$this->request['bill_state'];
			$_SESSION['bill_country_id']	=$this->request['bill_country_id'];
			$_SESSION['zip']					=$this->request['zip'];
			$_SESSION['company']			=$this->request['company'];
			$_SESSION['comments']=          $this->libFunc->m_displayContent($this->request['comments']);	
			$_SESSION['phone']				=$this->request['phone'];
			$_SESSION['homepage']		=$this->request['homepage'];
		}
		else
		{
			$_SESSION['customer']			='set';#CUSTOMER DATA IN SESSION
			$this->request['txtpassword']=$this->libFunc->ifSet($this->request,"txtpassword","");
			#MODIFIED BY HSG 16-03-07
			if(empty($this->request['txtpassword'])){
				$_SESSION['withoutlogin']	=1;
			}else{
				$_SESSION['withoutlogin']="";
			}
			$_SESSION['txtpassword']		=$this->request['txtpassword'];
			$_SESSION['first_name']		=$this->request['first_name'];
			$_SESSION['last_name']		=$this->request['last_name'];
			$_SESSION['email']				=$this->request['email'];
			$_SESSION['address1']			=$this->request['address1'];
			$_SESSION['address2']			=$this->request['address2'];
			$_SESSION['city']					=$this->request['city'];	
			$_SESSION['bill_state_id']		=$this->request['bill_state_id'];
			$_SESSION['bill_state']			=$this->request['bill_state'];
			$_SESSION['bill_country_id']	=$this->request['bill_country_id'];
			$_SESSION['zip']					=$this->request['zip'];
			$_SESSION['comments']=$this->libFunc->m_displayContent($this->request['comments']);
            $_SESSION['company']			=$this->request['company'];	
			$_SESSION['phone']				=$this->request['phone'];
			$_SESSION['homepage']		=$this->request['homepage'];
		}
		$_SESSION['alt_ship']=$this->libFunc->ifSet($this->request,"alt_ship",0);

		if(DELIVERY_ADDRESS==1)
		{
			$_SESSION['alt_name']			=$this->request['alt_fName']." ".$this->request['alt_lName'];
			$_SESSION['alt_fName']      = $this->request['alt_fName'];
            $_SESSION['alt_lName']      = $this->request['alt_lName'];
			$_SESSION['alt_address1']		=$this->request['alt_address1'];
			$_SESSION['alt_address2']		=$this->request['alt_address2'];
			$_SESSION['alt_city']			=$this->request['alt_city'];
			$_SESSION['ship_country_id']	=$this->request['ship_country_id'];	

			if(isset($this->request['ship_state_id']) && $this->request['ship_state_id']>0)
			{
				$_SESSION['ship_state_id']=$this->request['ship_state_id'];
			}
			else
			{
				$_SESSION['ship_state']	=$this->request['ship_state'];
				$_SESSION['ship_state_id']="";
			}
			$_SESSION['alt_zip']				=$this->request['alt_zip'];
			$_SESSION['alt_phone']			=$this->request['alt_phone'];

			if($_SESSION['alt_ship']==1)
			{
				$_SESSION['ship_country_id']	 =$this->request['bill_country_id'];
				$_SESSION['ship_state_id']	 =$this->request['bill_state_id'];
			}
		}
		else
		{
			$_SESSION['alt_ship']				=1;
			$_SESSION['alt_name']				=$_SESSION['first_name']." ".$_SESSION['last_name'];
			$_SESSION['alt_fName']      = $_SESSION['first_name'];
            $_SESSION['alt_lName']      = $_SESSION['last_name'];
			$_SESSION['alt_address1']			=$_SESSION['address1'];
			$_SESSION['alt_address2']			=$_SESSION['address2'];
			$_SESSION['alt_city']				=$_SESSION['city'];
			$_SESSION['ship_state_id']	 	=$_SESSION['bill_state_id'];	
			$_SESSION['ship_country_id']	 	=$_SESSION['bill_country_id'];	
			$_SESSION['ship_state']			=$_SESSION['bill_state'];
			$_SESSION['alt_zip']					=$_SESSION['zip'];
			$_SESSION['alt_phone']				=$_SESSION['phone'];
		}

		#CHECKING FOR VAT TAX
		if(!empty($_SESSION['ship_state_id']))
		{
			$this->obDb->query = "SELECT fTax FROM ".STATES." where iStateId_PK  = '".$_SESSION['ship_state_id']."'";
			$row_state = $this->obDb->fetchQuery();
			$_SESSION['VAT']=$row_state[0]->fTax;
		}
		if(!isset($row_state[0]->fTax) || empty($row_state[0]->fTax))
		{
			$this->obDb->query = "SELECT fTax FROM ".COUNTRY." where iCountryId_PK  = '".$_SESSION['ship_country_id']."'";
			$row_country = $this->obDb->fetchQuery();
			
			if($row_country[0]->fTax=="")
			{
				$_SESSION['VAT']=DEFAULTVATTAX;
			}
			else
			{
				$_SESSION['VAT']=$row_country[0]->fTax;
			}
		}
		$_SESSION['mail_list'] = $this->request['mail_list'];

		#CHECKING FOR MEMBER POINTS
		/*if(isset($this->request['member_points']) && $this->request['member_points']=='yes')
		{
			$_SESSION['useMemberPoints']='yes';
		}*/

		$retUrl=$this->libFunc->m_safeUrl(SITE_SAFEURL."ecom/index.php?action=checkout.reviewit");
		header("Location: ".$retUrl);	
		exit;
	}

	#FUNCTION TO UPDATE POSTAGE
	function m_updatePostage()
	{
		$m=$this->request['ship_id'];
		$_SESSION['postageId']=$m;
		$_SESSION['postageMethod']=$this->request['ship_method'][$m];
		if(SPECIAL_POSTAGE){
			$_SESSION['postageMethod'].=" & ".$_SESSION['defPostageMethod'];
		}
		$_SESSION['postagePrice']=$this->request['ship_total'][$m];
								
		$retUrl=$this->libFunc->m_safeUrl(SITE_SAFEURL."ecom/index.php?action=checkout.payment");
		$this->libFunc->m_mosRedirect($retUrl);	
		exit;
	}
	
	
}#END CLASS
?>
