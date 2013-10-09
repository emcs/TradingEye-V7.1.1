<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.0.5
Function: Configuration file-store various settings
1.SITE URL-...
2.Sitepath-...
3.Sitename
4.Emails
4.Payment gateway settings
************************************
* 5. Database settings in /config/dbConfig.php	*
************************************
default css,default tax,default currency
=======================================================================================
*/
#INCLUDE LIBRARY FILES

#ini_set("memory_limit", "64M"); 
require("libs/license.php");
include_once("libs/database.php");
include_once("libs/template.php");
include_once("libs/upload.php");
include_once("libs/htmlMimeMail.php");
@include_once("config/dbConf.php");
include_once("ckeditor/ckeditor.php");
$position_uri = strrpos($_SERVER['REQUEST_URI'],'/')+1;
$request_uri= substr($_SERVER['REQUEST_URI'],0,$position_uri);	
$request_uri=str_replace('installs/','',$request_uri);
$siteUrl="http://" . $_SERVER['SERVER_NAME'] . $request_uri;
$partsurls=parse_url($siteUrl);
define('REAL_PATH',$partsurls['path']);

#DEFINE DEFAULT CSS
define("DEFAULT_CSS","petrol.css");	

#IMAGES ARE PICKED WITH ABSOLUTE PATH-IF ZERO THEN RELATIVE TO ROOT
define("ABSOLUTE_IMAGE_PATH",0);	


#DEFINE CHECK_GPC- TO CHECK MAGIC QUOTES OR NOT
define("CHECK_GPC",0);
#DATABASE TABLES
//$Prefix="";
define("ADMINUSERS", $Prefix.'tbAdmin_users');	
define("ADMINSECURITY",$Prefix.'tbAdmin_Security');
define("MODULES",$Prefix.'tbModules');
define("CUSTOMERS",$Prefix.'tbUser_Customers');
define("SUPPLIERS",$Prefix.'tbUser_vendors');
define("CONTACTUS",$Prefix.'tbUser_contactus');

define("DEPARTMENTS",$Prefix.'tbShop_Departments');
define("PRODUCTS",$Prefix.'tbShop_Products');
define("CONTENTS",$Prefix.'tbShop_content');
define("FUSIONS",$Prefix.'tbfusion');
define("COUNTRY",$Prefix.'tbCountry');
define("STATES",$Prefix.'tbState');

define("SEARCHES",$Prefix.'Searches');

define("MENUHEADERS", $Prefix.'tbMenuheaders');
define("MENUITEMS",$Prefix.'tbMenuItems');
define("OPTIONS",$Prefix.'tbShop_Options');
define("OPTIONVALUES",$Prefix.'tbShop_OptionValues');
define("CHOICES",$Prefix.'tbShop_choices');
define("VDISCOUNTS",$Prefix.'tbShop_vdiscounts');
define("PRODUCTOPTIONS" ,$Prefix.'tbShop_ProductOptions');
define("PRODUCTCHOICES" ,$Prefix.'tbShop_ProductChoices');
define("PRODUCTKITS",$Prefix.'tbShop_ProductKits');
define("ORDERS",$Prefix.'tbShop_Orders');
define("ORDERPRODUCTS",$Prefix.'tbShop_OrderProducts');
define("ORDEROPTIONS",$Prefix.'tbShop_OrderOptions');
define("ORDERCHOICES",$Prefix.'tbShop_OrderChoices');
define("ORDERKITS",$Prefix.'tbShop_Orderkits');
define("CREDITCARDS",$Prefix.'tbShop_Creditcards');
define("SHIPPINGDETAILS",$Prefix.'tbShop_OrderShip');
define("ATTRIBUTES",$Prefix.'tbAttributes');
define("ATTRIBUTEVALUES",$Prefix.'tbAttributesValue');
define("PRODUCTATTRIBUTES",$Prefix.'tbProductAttributes');

define("POSTAGEZONE",$Prefix.'tbPostagezones');
define("POSTAGEZONEDETAILS",$Prefix.'tbPostagezoneDetails');
define("POSTAGECITY", $Prefix.'tbPostageCity');
define("POSTAGECITYDETAILS",$Prefix.'tbPostageCityDetails');

define("ENQUIRIES",$Prefix.'tbUser_contactus');
define("COMPANYSETTINGS",$Prefix.'tbCompanySettings');
define("SITESETTINGS",$Prefix.'tbsettings');
define("POSTAGE",$Prefix.'tbPostageMethods');
define("POSTAGEDETAILS",$Prefix.'tbPostageMethodDetails');
define("PLUGINS",$Prefix.'tbPlugin_apps');
define("NEWSLETTERS",$Prefix.'tbNewsletter');

define("DISCOUNTS",$Prefix.'tbShop_discounts');
define("GIFTCERTIFICATES",$Prefix.'tbShop_giftcerts');
define("GIFTWRAPS",$Prefix.'tbShop_giftwrap');
define("PROMOTIONS",$Prefix.'tbShop_Promotions');
define("EMAILS",$Prefix.'tbMailroom');
define("LEADS",$Prefix.'tbMailroomLead');
define("LEADLIST",$Prefix.'tbMailroomLeadList');
define("LEADPRODUCT",$Prefix.'tbMailroomLeadProducts');
define("WISHLIST",$Prefix.'tbShop_Wishlist');
define("WISHEMAILS",$Prefix.'tbWish_emails');
define("COMPARE",$Prefix.'tbShop_Compare');

define("REVIEWS",$Prefix.'tbUser_CustomerReviews');
define("REVIEWHELP",$Prefix.'tbUser_CustomerReviewsHelpful');
define("REVIEWRATE",$Prefix.'tbUser_CustomerRatings');
define("FROOGLE_SETTINGS",$Prefix.'tb_frooglesettings');

define("TEMPCART",$Prefix.'temp_cart');
define("TEMPOPTIONS",$Prefix.'temp_options');
define("TEMPCHOICES",$Prefix.'temp_choices');
define("TEMPKITS",$Prefix.'temp_kits');
define("TEMPIMAGES",$Prefix.'temp_images');

define("CONFIRMATIONORDERS",$Prefix.'tbConfirmation_Orders');


if(!defined('DATABASE_HOST')){
	die('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Tradingeye v7.1</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<meta http-equiv="Content-Language" content="en-gb" />
	<meta http-equiv="imagetoolbar" content="false" />
	<meta name="author" content="dpivision.com Ltd" />
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
	<meta name="robots" content="noindex" />
	<style type="text/css">
		html,
		body {
			margin: 0;
			padding: 0;
			}
		body {
			background: #333;
			color: #111;
			font: 62.5%/1.6em "Lucida Grande", Verdana, Arial, sans-serif;
			margin: 0;
			padding: 0;
			text-align: center;
			}
		#container {
			background: #fff;
			border: 5px solid #222;
			border-top: 0;
			color: #333;
			font-size: 1.1em;
			margin: 20px auto 2em auto;
			padding: 0 25px;
			position: relative;
			text-align: left;
			width: 600px;
			}
		#container h1 {
			background: #222;
			color: #fff;
			font-size: 1.5em;
			margin: 0 -25px;
			padding: 1em 0;
			text-align: center;
			}
		#container #footer {
			background: #eee;
			clear: both;
			margin: 2em -25px 0 -25px;
			padding: .5em 25px .8em 25px;
			position: relative;
			text-align: left;
			}
		#container #footer p {
			margin: .5em 0;
			}
		a:link,
		a:visited,
		a:active {
			color: #63a6d9;
			text-decoration: none;
			}
		a:hover {
			color: #111;
			text-decoration: none;
			}
		p,
		h1,
		h2 {
			line-height: 1.6em;
			margin: 1em 0;
			}
		h2 {
			background: none;
			color: #111;
			font-size: 1.6em;
			font-weight: normal;
			margin: 1em 0 .5em 0;
			}
		#container:after {
			clear: both;
			content: ".";
			display: block;
			height: 0;
			visibility: hidden;
			}
		#container {
			display: block;
			}
		/*  \*/
		#container {
			min-height: 1%;
			}
		* html #container {
			height: 1%;
			}
		/*  */
	</style>
</head>
<body>
	<div id="container">  
		<h1>Tradingeye v7 Installation</h1>
		<h2>Incomplete Installation</h2>
		<p>This installation of Tradingeye has not yet been completed. Once you complete the install process, you will have access to your shop front and admin panel.</p>
		<div id="footer">
			<p>Copyright 2013 <a href="http://tradingeye.com/">EMCS Ltd.</a></p>
		</div>
	</div>
</body>
</html>
');
}

# Setting up the database connection
$obDatabase = new database();
$obDatabase->db_host = DATABASE_HOST;
$obDatabase->db_user = DATABASE_USERNAME;
$obDatabase->db_password = DATABASE_PASSWORD;
$obDatabase->db_port = DATABASE_PORT;
$obDatabase->db_name = DATABASE_NAME;
$sql="select sConstantName as constant_value,COALESCE(vSmalltext,tLargetext,nNumberdata,default_value) as data,vDatatype  from ". SITESETTINGS ." where sConstantName <> '' and iAdminUser=1";
$res=$obDatabase->execQry($sql);
if($obDatabase->numRows($res)){
	while($rowConfig=$obDatabase->fetchObj($res)){
		if (!defined($rowConfig->constant_value))
            define($rowConfig->constant_value,$rowConfig->data);
	}// end of while
}// end of if
include_once("libs/libFunctions.php");



#SHOP PATH & NAME SETTINGS
define("GRAPHICS_PATH",SITE_URL."graphics/");
define("MODULES_PATH",SITE_PATH."modules/");

#****** PAYMENT ******#
#GATEWAYS
define("PROTX","protx");
define("VERISIGN","payflowpro");
define("AUTHORIZE","authorizenet");

# PAYPAL
define("PAYPAL_URL", (GATEWAY_TESTMODE==1) ? "https://www.sandbox.paypal.com/cgi-bin/webscr" : "https://www.paypal.com/cgi-bin/webscr");

# AUTHORIZENET
define("AUTHORIZENET_URL", (GATEWAY_TESTMODE==1) ? "https://test.authorize.net/gateway/transact.dll" : "https://secure.authorize.net/gateway/transact.dll");

#PAYPAL DIRECT
define("PAYPALDIRECT_URL", (GATEWAY_TESTMODE==1) ? "https://api-3t.sandbox.paypal.com/nvp" : "https://api-3t.paypal.com/nvp");

# PROTX 
/************************************************
 Author   Date			 Notes
 LKS	  21-June-07	 3D-Secure additions
 Do not modify the lines below.  They set up
 URLs and parameters for the  VSP direct.
************************************************/
if(GATEWAY_TESTMODE==1) {

 $simulator=0;
  /************************************************
  Information and URLs for the simulator site
 ************************************************/
	  if($simulator){
	     define('PROTX_PURCHASE_URL','https://ukvpstest.protx.com/VSPSimulator/VSPDirectGateway.asp');
	     define('PROTX_CALLBACK_URL','https://ukvpstest.protx.com/VSPSimulator/VSPDirectCallback.asp');
	     define('PROTX_REFUND_URL','https://ukvpstest.protx.com/VSPSimulator/VSPServerGateway.asp?Service=VendorRefundTx');
	
	     define('PROTX_RELEASE_URL','https://ukvpstest.protx.com/VSPSimulator/VSPServerGateway.asp?Service=VendorReleaseTx');
	     define('PROTX_REPEAT_URL','https://ukvpstest.protx.com/VSPSimulator/VSPServerGateway.asp?Service=VendorRepeatTx');
	
	 }#End of simulator condition
	 else{
	
	 /************************************************
	  Information and URLs for the test site
	 ************************************************/
		  /*define('PROTX_PURCHASE_URL','https://ukvpstest.protx.com/vspgateway/service/vspdirect-register.vsp'); 
		 define('PROTX_CALLBACK_URL','https://ukvpstest.protx.com/vspgateway/service/direct3dcallback.vsp'); 
		 define('PROTX_REFUND_URL','https://ukvpstest.protx.com/vspgateway/service/refund.vsp');  
		 define('PROTX_REFUND_URL','https://ukvpstest.protx.com/vspgateway/service/refund.vsp');
		 define('PROTX_REPEAT_URL','https://ukvpstest.protx.com/vspgateway/service/repeat.vsp'); */
	     define('PROTX_PURCHASE_URL','https://test.sagepay.com/gateway/service/vspdirect-register.vsp');
		 define('PROTX_CALLBACK_URL','https://test.sagepay.com/gateway/service/direct3dcallback.vsp'); 
		 define('PROTX_RELEASE_URL','https://test.sagepay.com/gateway/service/release.vsp'); 
		 define('PROTX_REFUND_URL','https://test.sagepay.com/gateway/service/refund.vsp');
		 define('PROTX_REPEAT_URL','https://test.sagepay.com/gateway/service/repeat.vsp');
	 }
}#End of test site condition
else { 

	/************************************************   
	 * Information and URLs for the Live site 
	 * **********************************************/  
	 /*define('PROTX_PURCHASE_URL','https://ukvps.protx.com/vspgateway/service/vspdirect-register.vsp');  
	 define('PROTX_CALLBACK_URL','https://ukvps.protx.com/vspgateway/service/direct3dcallback.vsp');
	 define('PROTX_REFUND_URL','https://ukvps.protx.com/vspgateway/service/refund.vsp');
	 define('PROTX_RELEASE_URL','https://ukvps.protx.com/vspgateway/service/release.vsp');  
	 define('PROTX_REPEAT_URL','https://ukvps.protx.com/vspgateway/service/repeat.vsp');*/
	 define('PROTX_REPEAT_URL','https://live.sagepay.com/gateway/service/repeat.vsp');
	 define('PROTX_PURCHASE_URL','https://live.sagepay.com/gateway/service/vspdirect-register.vsp');
	 define('PROTX_CALLBACK_URL','https://live.sagepay.com/gateway/service/direct3dcallback.vsp');
	 define('PROTX_REFUND_URL','https://live.sagepay.com/gateway/service/refund.vsp');
	 define('PROTX_RELEASE_URL','https://live.sagepay.com/gateway/service/release.vsp'); 
}
define('PROTX_URL',PROTX_PURCHASE_URL);
#SECPAY
define("SECPAY_URL", "https://www.secpay.com/java-bin/ValCard");
define("SECPAY_MODE", (GATEWAY_TESTMODE==1) ? "true" : "live"); #TEST ACCOUNT
# VERISIGN
define("VERISIGN_URL", (GATEWAY_TESTMODE==1) ? "test-payflow.verisign.com" : "payflow.verisign.com"); 
define("VERISIGN_PORT",443);

#HSBC- 04-05-07
define("HSBC_URL", "https://www.cpi.hsbc.com/servlet");
define("HSBC_MODE", (GATEWAY_TESTMODE==1) ? "T" : "P"); #TEST ACCOUNT

#WORLDPAY-07-05-07-HSG
define("WORLDPAY_URL", (GATEWAY_TESTMODE==1) ? "https://select-test.wp3.rbsworldpay.com/wcc/purchase" :"https://select.worldpay.com/wcc/purchase");
define("WORLDPAY_TEST_MODE", (GATEWAY_TESTMODE==1) ? 100 : 0);

#OFFLINE SECURETRADING - 08-05-07
define("OFFST_URL", "https://securetrading.net/authorize/form.cgi"); 

#(BEGIN) SAGEPAY INTEGRATION
#SAGE FORM PAYMENT GATEWAY 16-11-2009
//define ("SAGEFORM_URL","https://test.sagepay.com/simulator/vspformgateway.asp");
define ("SAGEFORM_URL",(GATEWAY_TESTMODE==1) ? "https://test.sagepay.com/gateway/service/vspform-register.vsp" : "https://live.sagepay.com/gateway/service/vspform-register.vsp" );
#(END) SAGEPAY INTEGRATION

#PROTX VSP FORM (live) - MCB, 08-09-08
//define("PROTX_VSP_URL", "https://ukvpstest.protx.com/vspgateway/service/vspform-register.vsp");
//define ("PROTX_VSP_URL", "http://martin.com/dump.php");
define ("PROTX_VSP_URL", "https://ukvpstest.protx.com/vspsimulator/vspformgateway.asp");

#SECURETRADING - 03-09-07
define("SECURETRADING_URL", "https://securetrading.net/authorize/process.cgi");

#BARCLAYS- 08-05-07
define("BARCLAYS_URL", "secure2.epdq.co.uk");

#PROPAY 15-05-10
if(PROPAY_CANADA == "1"){
//propay canada URL:
define("PROPAY_URL", (GATEWAY_TESTMODE==1) ? "https://xmltest.propay.com/API/PropayAPI.aspx" : "https://www.propaycanada.ca/api/propayapi.aspx");
}else{
define("PROPAY_URL", (GATEWAY_TESTMODE==1) ? "https://xmltest.propay.com/API/PropayAPI.aspx" : "https://epay.propay.com/api/propayapi.aspx");
}

#SMTP_AUTH ACTIVATED IF SET 1
if(SMTP_AUTH){
	define('IS_SMTP','smtp');
	define('IS_AUTH',true);
}	
 else{
	define('IS_SMTP','mail');
 	define('IS_AUTH',false);
 }

#VAT TEXT SETTING
if(VAT_TAX_TEXT ==""){
	define("LBL_NOTAX","No VAT");
}else{
	define("LBL_NOTAX","No ".VAT_TAX_TEXT);
}

#TO SELECT PAYMENT GATEWAY
if(PROTX_VENDOR!="" && PROTX_CURRENCY!=""){
	define("DEFAULT_PAYMENTGATEWAY",PROTX);
}elseif(VERISIGN_LOGIN!="" && VERISIGN_PASSWORD!=""){
	define("DEFAULT_PAYMENTGATEWAY",VERISIGN);
}elseif(AUTHORIZEPAYMENT_LOGIN!="" && AUTHORIZEPAYMENT_KEY!=""){
	define("DEFAULT_PAYMENTGATEWAY",AUTHORIZE);
}elseif(SECURETRADING_MERCHANTID!=""){
	define("DEFAULT_PAYMENTGATEWAY","securetrading");
}elseif(PAYPALAPI_USERNAME!="" && PAYPALAPI_PASSWORD!="" && PAYPALAPI_SIGNATURE!=""){
	define("DEFAULT_PAYMENTGATEWAY","paypaldirect");
}elseif(PROPAY_ACCNUMBER!=""){
	//Propay Gateway Integration
	define("DEFAULT_PAYMENTGATEWAY","propay");
			 }elseif(CS_MERCHANTID!=""){
	define("DEFAULT_PAYMENTGATEWAY","Cardsave");
}else{
	define("DEFAULT_PAYMENTGATEWAY","");
}

#
#DATABASE CONFIG FILE
define("DBCONFIG_PATH",SITE_PATH."config/dbConf.php");	

#SETTING POSTAGE METHOD
$obDatabase->query = "SELECT vKey,iDefaultHighest FROM ".POSTAGE." WHERE iStatus='1' AND vKey NOT IN ('special','pweight')";
$activePost=$obDatabase->fetchQuery();
define("DEFAULT_POSTAGE_METHOD",$activePost[0]->vKey);	
if($activePost[0]->vKey === 'codes')
define("DEFAULT_HIGHEST",$activePost[0]->iDefaultHighest);	


#POSTAGE BY WEIGHT
$obDatabase->query="SELECT vField1,iStatus FROM ".POSTAGE." P, ".POSTAGEDETAILS." D WHERE iPostId_PK=iPostId_FK AND vKey='pweight'";
$weightPost=$obDatabase->fetchQuery();

#DEFINES DEFAULT WEIGHT
define("ISACTIVE_ITEMWEIGHT",$weightPost[0]->iStatus);
define("DEFAULT_ITEMWEIGHT",$weightPost[0]->vField1);


#SETTING COMPANY INFORMATIONS
$obDatabase->query="SELECT vCname,vPhone,vSlogan,vVatNumber,vRNumber,vCountry FROM ".COMPANYSETTINGS." limit 1";
$rsSettings=$obDatabase->fetchQuery();

if (get_magic_quotes_gpc()) {
	define("SITE_NAME",stripslashes($rsSettings[0]->vCname));
	define("SITE_PHONE",stripslashes($rsSettings[0]->vPhone));
	define("COMPANY_SLOGAN",stripslashes($rsSettings[0]->vSlogan));
	define("COMPANY_VATNUMBER",stripslashes($rsSettings[0]->vVatNumber));
	define("COMPANY_REGISTERNUMBER",stripslashes($rsSettings[0]->vRNumber));
} else {
define("SITE_NAME",$rsSettings[0]->vCname);
define("SITE_PHONE",$rsSettings[0]->vPhone);	
define("COMPANY_SLOGAN",$rsSettings[0]->vSlogan);
define("COMPANY_VATNUMBER",$rsSettings[0]->vVatNumber);
define("COMPANY_REGISTERNUMBER",$rsSettings[0]->vRNumber);
}
#SETTING DEFAULT COUNTRY ACCORDING TO THE COMPANY LKS
if(isset($rsSettings[0]->vCountry) && $rsSettings[0]->vCountry > 0)
	$default_country=$rsSettings[0]->vCountry;
else
	$default_country=100;
define("SELECTED_COUNTRY",$default_country);
#COMPUTER ENCODING TYPES LKS
$computer_encoding=array('1' =>'text/html; charset=iso-8859-1','2' =>'text/html; charset=UTF-8');
define("COMPUTER_ENCODING",serialize($computer_encoding));
# VAT WITH POSTAGE ENABLE DISABLE LKS

#Protx Error Msg
$errmsg_protx_3dsecure=array('NOTCHECKED'=>'No 3D Authentication was attempted for this transaction. Always returned if 3D-Secure is not active on your account.','NOAUTH'=>'The card is not in the 3D-Secure scheme.','CANTAUTH'=>'The card Issuer is not part of the scheme.','NOTAUTHED'=>'The cardholder failed to authenticate themselves with their Issuing Bank.','ATTEMPTONLY'=>'The cardholder attempted to authenticate themselves but the process did not complete.');
define('PROTX_ERROR_MSG',serialize($errmsg_protx_3dsecure));
$errmsg_protx=array('NOTAUTHED'=>'The transaction was not authorised by the acquiring bank. No funds could be taken from the card.','REJECTED'=>'The VSP System rejected the transaction because of the rules you have set on your account.','3DAUTH'=>'The VSP System rejected the transaction because of the rules you have set on your account.');
#ERROR REPORTING ON TEST / LIVE SERVER
if(SYSTEM_STATE==1) {
	ini_set("display_errors", 1);
	error_reporting(E_ALL);	

} else {
	
error_reporting(0);
}


# SAGE ACCOUNTING 2008 VARIABLE
# YOU CAN CHANGE THIS VALUE TO RELEVANT VALUE THAT YOU NEED FOR SAGE, 
# HOWEVER YOU CAN ALSO CHANGE IN ADMIN MODE
define ('SAGE_POST_RECEIPTS_TO','1200');
define ('SAGE_NOMINAL_CODE','4000');


?>