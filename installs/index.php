<?php

/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.0.5
=======================================================================================
*/

define('_TEEXEC',1);
include_once('config.php');

include_once("../libs/template.php");

include_once("../libs/license.php");

include_once("../libs/libFunctions.php");

include_once("../LanguagePacks/English.php");

include_once("../modules/default/commonFunctions.php");

include_once("../admin/admin_messages.php");

include_once("../libs/database.php");



class c_installs

{

	#CONSTRUCTOR

	function c_installs()

	{

		$this->err=0;

		$this->errMsg="";

		$this->obTpl = new Template();

		$this->libFunc=new c_libFunctions();

		$this->license=new licenseCheck(null, $this->libFunc);

		$this->obTpl->set_file('hMainTemplate',"./outer.htm");

		$this->obTpl->set_var("TPL_VAR_YEAR",date('Y'));

		$this->filename="../config/dbConf.php";

		if(!isset($_REQUEST['mode'])){

			$mode="";

		}else{

			$mode=$_REQUEST['mode'];

		}
		$this->strPath = realpath("../") . "/";
		

		$this->request=$_REQUEST;
		$this->upgrade = 0;
		include("../config/dbConf.php");
		if(defined("DATABASE_HOST") && defined("DATABASE_USERNAME") && defined("DATABASE_PASSWORD") && defined("DATABASE_NAME") && defined("DATABASE_PORT"))
		{
			if(defined("TE_VERSION"))
			{
				$version = TE_VERSION;
			}
			else
			{
				$version = 6;
				define("TE_VERSION",$version);
			}
				include("upgrade_config.php");
				if(defined("TE_UPGRADE_VERSION"))
				{
					$newversion = TE_UPGRADE_VERSION;
					if($newversion > $version)
					{
						$this->upgrade = 1;
						$this->prefix = $Prefix;
					}
				}
			if($this->upgrade === 0)
			{			
				$this->obTpl->set_var("TPL_VAR_BODY",$this->m_thanksView());
			}
		}
		else
		{
			$version = 6;
		}
		
		switch($mode)

		{

			case "install2":
				if(file_exists($this->strPath . "installs/install.sql"))
				{
					$this->m_install();
				}
				else
				{
					$this->libFunc->m_mosRedirect("index.php?mode=thanks");
				}
			break;
			case "install":	
				
				if($this->valiadateSystemInfo()){
					$this->obTpl->set_var("TPL_VAR_BODY",$this->m_instalForm());
				}
				else
				{
					die('Failed to pass System Requirements');
				}

			break;

			case "accept":
				if($this->upgrade === 1)
				{
					if(file_exists($this->strPath . "installs/upgrade.sql"))
					{
						$this->m_install();
					}
					else
					{
						$this->libFunc->m_mosRedirect("index.php?mode=thanks&upgrade=1");
					}
					die();
				}
				else
				{
					$this->obTpl->set_var("TPL_VAR_BODY",$this->m_instalForm());
				}

			break;

			case "decline":

				$this->obTpl->set_var("TPL_VAR_BODY",$this->m_declineView());

			break;

			case "thanks":

				$this->obTpl->set_var("TPL_VAR_BODY",$this->m_thanksView());

			break;

			case "begin":

				$this->obTpl->set_var("TPL_VAR_BODY",$this->m_termsView());

			break;

			default:

				$this->obTpl->set_var("TPL_VAR_BODY",$this->m_defaultView());

			break;

		}
		$this->obTpl->set_var("TPL_VAR_PAGE_TITLE","");
		$this->obTpl->pparse("return","hMainTemplate");	

	}



	function m_defaultView(){

		$this->obTpl->set_file('hTemplate',"./home.htm");

		$this->obTpl->set_block('hTemplate','START_TPL_BLK','START_TPL_BLKs');

		$this->obTpl->set_block('hTemplate','ERROR_TPL_BLK','ERROR_TPL_BLKs');

		$this->obTpl->set_block('ERROR_TPL_BLK',"TPL_DSPMSG_BLK","dspmsg_blk");

		$this->obTpl->set_var("dspmsg_blk","");
		if($this->upgrade == 1)
		{
		$this->obTpl->set_var("TPL_VAR_UPGRADE","Upgrade ");
		}
		else
		{
		$this->obTpl->set_var("TPL_VAR_UPGRADE","");
		}
		
		if($this->upgrade === 0)
		{
			$this->obTpl->set_var("TPL_VAR_STEP3","<li>3) Install Configuration</li>");
		}
		else
		{
			$this->obTpl->set_var("TPL_VAR_STEP3","");
		}

	    $this->request['seofriendly']= $this->libFunc->ifSet($this->request,'seofriendly',0);



		if(isset($this->request['flag_begin']) && $this->request['flag_begin'] != 8) {

			$this->obTpl->set_var("TPL_VAR_MSG",'Your current settings do not match the Tradingeye setup requirements. Please see the error below and correct this before trying again.');

			$this->obTpl->parse("dspmsg_blk","TPL_DSPMSG_BLK");

		}



		$flag_begin=0;

		



		$this->obTpl->set_var("PHPVERSION",phpversion());

		if (version_compare(phpversion(), '5.3', '>')) {

			$cls='green';

			$flag_begin++;

		}

		else{

			$flag_begin--;

			$cls='red';

		}

		$this->obTpl->set_var("PHPVERSION_CLASS",$cls);

		$this->obTpl->set_var("MINIMUM_PHP_VERSION_NEEDED",MINIMUM_PHP_VERSION_NEEDED);

		$flag=0;

		if (@file_exists('../config/dbConf.php') &&  @is_writable( '../config/dbConf.php' )){

 			$cls='green';

			$config='Writeable';

			$config_name='/config/dbConf.php';

			$flag=1;

			$flag_begin++;

		}

		else {

			if(@is_writable( '../config') && $flag==0) {

				$cls='green';

				$config='Writable';

				$config_name='/config';

				$flag_begin++;

			}elseif($flag==0){

				$cls='red';

				$config='Unwritable';

				$config_name='/config';

				$flag_begin--;

			}else{

				$cls='red';

				$config='Unwritable';

				$config_name='/config/dbconfig.php';

				$flag_begin--;

			}



		}

		$this->obTpl->set_var("CONF_NAME",$config_name);

		$this->obTpl->set_var("CONF",$config);	

		$this->obTpl->set_var("CONF_CLASS",$cls);

		

		

		switch($this->testIoncube()){

			case 1:

				$flag_begin++;

				$cls='green';

				$this->obTpl->set_var('IONCUBEVERSION',$this->getIoncubeLoaderVersion());



			break;

			case 2:

				$cls='red';

				$this->obTpl->set_var('IONCUBEVERSION',$this->getIoncubeLoaderVersion());

				$flag_begin--;

			break;

			case 3:

				$cls='red';

				$this->obTpl->set_var('IONCUBEVERSION','Not loaded');

				$flag_begin--;

			break;

		}// end of switch

		$this->obTpl->set_var("IONCUBE_CLASS",$cls);

		$this->obTpl->set_var('MINIMUM_IONCUBE_LOADER_VERSION_NEEDED',MINIMUM_IONCUBE_LOADER_VERSION_NEEDED);

		$version= curl_version();

		if(is_array($version)){

			 $resu=$version['version'];

		}

		else{

			preg_match_all("/[0-9.]{1,}/",$version,$result);

			$resu=$result[0][0];



		}

	    

		if(extension_loaded('curl') && $resu>=MINIMUM_CURL_VERSION_NEEDED){

			$flag_begin++;

			$cls='green';

		}

		else {

			$cls='red';

			$version='No';	

			$flag_begin--;

		}



		$this->obTpl->set_var("MINIMUM_CURL_VERSION_NEEDED",MINIMUM_CURL_VERSION_NEEDED);

		$this->obTpl->set_var("CURLVERSION",$resu);

		$this->obTpl->set_var("CURL_CLASS",$cls);

		/*New check  Saturday, August 04, 2007 */

		

	 if (function_exists('gd_info')) {	

		$version= gd_info();

		preg_match_all("/[0-9.]{1,}/",$version['GD Version'],$result);

	    $gdversion=$result[0][0];



		if(extension_loaded('gd')  && $gdversion >=MINIMUM_GD_VERSION_NEEDED){

			$cls='green';

			$flag_begin++;

		}

		else {

				$cls='red';

				$gdversion='No';

				$flag_begin--;

			}

	 }

     else {		

			$cls='red';

			$gdversion='GD library not installed';

			$flag_begin--;

	 } 



		$this->obTpl->set_var("MINIMUM_GD_VERSION_NEEDED",MINIMUM_GD_VERSION_NEEDED);

		$this->obTpl->set_var("GDVERSION",$gdversion);

		$this->obTpl->set_var("GD_CLASS",$cls);



		if(@is_writable( '../UserFiles')) {

				$cls='green';

				$userfiles_value='Writable';

				$userfiles_name='/UserFiles';

				$flag_begin++;

			}else{

				$cls='red';

				$userfiles_value='Unwritable';

				$userfiles_name='/UserFiles';

				$flag_begin--;

			}



	   $this->obTpl->set_var("USERFILES_NAME",$userfiles_name);

	   $this->obTpl->set_var("USERFILES_VALUE",$userfiles_value);

	   $this->obTpl->set_var("USERFILES_CLASS",$cls);



	   if(@is_writable( '../images')) {

				$cls='green';

				$images_value='Writable';

				$images_name='/images';

				$flag_begin++;

			}else{

				$cls='red';

				$images_value='Unwritable';

				$images_name='/images';

				$flag_begin--;

			}



	   $this->obTpl->set_var("IMAGES_NAME",$images_name);

	   $this->obTpl->set_var("IMAGES_VALUE",$images_value);

	   $this->obTpl->set_var("IMAGES_CLASS",$cls);



	   if(@is_writable( '../graphics')) {

				$cls='green';

				$graphics_value='Writable';

				$graphics_name='/graphics';

				$flag_begin++;

			}else{

				$cls='red';

				$graphics_value='Unwritable';

				$graphics_name='/graphics';

				$flag_begin--;

			}



	   $this->obTpl->set_var("GRAPHICS_NAME",$graphics_name);

	   $this->obTpl->set_var("GRAPHICS_VALUE",$graphics_value);

	   $this->obTpl->set_var("GRAPHICS_CLASS",$cls);

	

	   if(@is_writable( '../RSS')) {

				$cls='green';

				$rss_value='Writable';

				$rss_name='/RSS';

				$flag_begin++;

			}else{

				$cls='red';

				$rss_value='Unwritable';

				$rss_name='/RSS';

				$flag_begin--;

			}



	   $this->obTpl->set_var("RSS_NAME",$rss_name);

	   $this->obTpl->set_var("RSS_VALUE",$rss_value);

	   $this->obTpl->set_var("RSS_CLASS",$cls);	

	

	

	/*End of new check */

//Monday, August 06, 2007

 /***********************************************************************/

			$port = ( $_SERVER['SERVER_PORT'] == 80 ) ? '' : ":".$_SERVER['SERVER_PORT'];

			$root = $_SERVER['SERVER_NAME'].$port.$_SERVER['PHP_SELF'];

			$root = str_replace("installs/","",$root);

			$root = str_replace("index.php","",$root);

			$url = "http://".$root;

			$this->obTpl->set_var("SITE_URL",$url);

/****************************************************************************/

  

// Dave comment this condition out as SEO is not required

// if($flag_begin==8 && $this->request['seofriendly'] ==1 && $this->request['proceed'] ==='Proceed'){

	if($flag_begin==9 && isset($this->request['proceed']) && $this->request['proceed'] ==='Proceed'){

		   $this->obTpl->set_var("TPL_VAR_IPADDRESS",$_SERVER['REMOTE_ADDR']);

		   $this->obTpl->set_var("SEOFRIENDLY",$this->request['seofriendly']);

		   $this->obTpl->set_var("ERROR_TPL_BLKs",'');

		   $this->obTpl->parse('START_TPL_BLKs','START_TPL_BLK',true);

		}

		else{

			$this->obTpl->set_var("TPL_FLAG_BEGIN",$flag_begin);

			$this->obTpl->set_var("START_TPL_BLKs",'');

			$this->obTpl->parse('ERROR_TPL_BLKs','ERROR_TPL_BLK',true);

			

		}

		return $this->obTpl->parse("return","hTemplate");

	}

	

	function m_termsView()	{

		$this->request['seofriendly']= $this->libFunc->ifSet($this->request,'seofriendly',0);

		$this->obTpl->set_file('hTemplate',"./install_terms.htm");

		$this->obTpl->set_var("SEOFRIENDLY",$this->request['seofriendly']);
		
		if($this->upgrade === 0)
		{
			$this->obTpl->set_var("TPL_VAR_STEP3","<li>3) Install Configuration</li>");
		}
		else
		{
			$this->obTpl->set_var("TPL_VAR_STEP3","");
		}

		return $this->obTpl->parse("return","hTemplate");

	}



	function m_instalForm()	{

		$this->obTpl->set_file('hTemplate',"./install_form.htm");

		$this->obTpl->set_block('hTemplate',"TPL_DSPMSG_BLK","dspmsg_blk");

		$this->obTpl->set_var("dspmsg_blk","");

		$this->request['seofriendly']=$this->libFunc->ifSet($this->request,'seofriendly',0);

		if(!$this->libFunc->m_isNull($this->errMsg)){

				$this->obTpl->set_var("TPL_VAR_MSG",$this->errMsg);

				$this->obTpl->parse("dspmsg_blk","TPL_DSPMSG_BLK");

		}else{

				$this->obTpl->set_var("TPL_VAR_MSG",$this->errMsg);

				$this->obTpl->parse("dspmsg_blk","TPL_DSPMSG_BLK");

		}

		if(isset($this->request['msg']))

		{

			if($this->request['msg']==1)

			{

				$this->obTpl->set_var("TPL_VAR_MSG",MSG_FILE_OPEN);

				$this->obTpl->parse("dspmsg_blk","TPL_DSPMSG_BLK");

			}

			elseif($this->request['msg']==2)

			{

				$this->obTpl->set_var("TPL_VAR_MSG",MSG_FILE_NOWRITE);

				$this->obTpl->parse("dspmsg_blk","TPL_DSPMSG_BLK");

			}

			elseif($this->request['msg']==3)

			{

				$this->obTpl->set_var("TPL_VAR_MSG",MSG_FILE_NOWRITABLE);

				$this->obTpl->parse("dspmsg_blk","TPL_DSPMSG_BLK");

			}

			elseif($this->request['msg']==4)

			{

				$this->obTpl->set_var("TPL_VAR_MSG",MSG_SYSTEMSETTING_UPDATED);

				$this->obTpl->parse("dspmsg_blk","TPL_DSPMSG_BLK");

			}

		}



		$this->obTpl->set_var("SEOFRIENDLY",$this->request['seofriendly']);

	

		$this->obTpl->set_var("TPL_VAR_DBNAME","");

		$this->obTpl->set_var("TPL_VAR_DBUNAME","");

		$this->obTpl->set_var("TPL_VAR_DBPASS","");

		$this->obTpl->set_var("TPL_VAR_DBSERVER","");

		$this->obTpl->set_var("TPL_VAR_DBPREFIX","");

		$this->obTpl->set_var("TPL_VAR_SITEURL","");

		$this->obTpl->set_var("TPL_VAR_SMTPSERVER","");

		$this->obTpl->set_var("TPL_VAR_SMTPPORT","");

		$this->obTpl->set_var("TPL_VAR_ADMINNAME","");

		$this->obTpl->set_var("TPL_VAR_ADMINPASS","");

		$this->obTpl->set_var("TPL_VAR_ADMINEMAIL","");

		$this->obTpl->set_var("TPL_VAR_LICENSE", "");



	



		

		

		if(isset($this->request['dsn'])){



			$this->obTpl->set_var("TPL_VAR_DBNAME",$this->request['dsn']);

			$this->obTpl->set_var("TPL_VAR_DBUNAME",$this->request['dbUserName']);

			$this->obTpl->set_var("TPL_VAR_DBPASS",$this->request['dbPassword']);

			$this->obTpl->set_var("TPL_VAR_DBSERVER",$this->request['dbServer']);

			$this->obTpl->set_var("TPL_VAR_DBPREFIX",$this->request['dbPrefix']);

			$this->obTpl->set_var("TPL_VAR_SITEURL",$this->request['siteurl']);

			$this->obTpl->set_var("TPL_VAR_ADMINNAME",$this->request['adminUser']);

			$this->obTpl->set_var("TPL_VAR_ADMINPASS",$this->request['adminPassword']);

			$this->obTpl->set_var("TPL_VAR_ADMINEMAIL",$this->request['adminemail']);

			$this->obTpl->set_var("TPL_VAR_LICENSE",$this->request['license']);

		}

		return $this->obTpl->parse("return","hTemplate");

	}

	

	function m_declineView()

	{

		$this->obTpl->set_file('hTemplate',"./decline.htm");

		return $this->obTpl->parse("return","hTemplate");

	}



	function m_thanksView()	{

		$this->obTpl->set_file('hTemplate',"./thanks.htm");
		
		if($this->reqest['upgrade'] == 1)
		{
			$this->obTpl->set_var("TPL_VAR_UPGRADE","Upgrade ");
		}
		else
		{
		$this->obTpl->set_var("TPL_VAR_UPGRADE","");
		}

		return $this->obTpl->parse("return","hTemplate");

	}



	function m_install() {

		#INTIALIZING VALUES

		$this->request['dsn']=$this->libFunc->ifSet($this->request,'dsn');

		$this->request['dbServer']=$this->libFunc->ifSet($this->request,'dbServer');

		$this->request['dbType']=$this->libFunc->ifSet($this->request,'dbType');

		$this->request['dbUserName']=$this->libFunc->ifSet($this->request,'dbUserName');

		$this->request['dbPassword']=$this->libFunc->ifSet($this->request,'dbPassword');

		$this->request['dbPrefix']=$this->libFunc->ifSet($this->request,'dbPrefix','');
		
		if($this->upgrade === 1)
		{
			$this->request['dsn']=DATABASE_NAME;

			$this->request['dbServer']=DATABASE_HOST;

			$this->request['dbUserName']=DATABASE_USERNAME;

			$this->request['dbPassword']=DATABASE_PASSWORD;

			$this->request['dbPrefix']=$this->prefix;
			$newversion = TE_UPGRADE_VERSION;
		}
		else
		{
			$newversion = 7.11;
		}

		if(!	isset($this->request['dbServer'])){

			$this->libFunc->m_mosRedirect("index.php");

		}

			$somecontent='<?php

			   if(("dbConf.php" == $_SERVER[\'SCRIPT_NAME\']))

				{

					 die( "<h2>Direct include access prohibited</h2>");

				}

	define("DATABASE_HOST","'.$this->request['dbServer'].'");

	define("DATABASE_USERNAME","'.$this->request['dbUserName'].'");

	define("DATABASE_PASSWORD","'.$this->request['dbPassword'].'");

	define("DATABASE_NAME","'.$this->request['dsn'].'");

	define("DATABASE_PORT","3306");

	define("TE_VERSION",'.$newversion.');

	$Prefix="'.$this->request['dbPrefix'].'";

?>';



			if (is_writable($this->filename)) 

			{

				if (!$handle = fopen($this->filename, 'w+')) 

				{

					$this->libFunc->m_mosRedirect("./index.php?mode=accept&msg=1");	

				}

				if (!fwrite($handle, $somecontent)) 

				{

					$this->libFunc->m_mosRedirect("./index.php?mode=accept&msg=2");	

				}

			} 

			else 

			{

				$this->libFunc->m_mosRedirect("./index.php?mode=accept&msg=3");	

			}	

		

		

		# Setting up the database connection

		$this->obDb = new database();

		$this->obDb->db_host = $this->request['dbServer'];

		$this->obDb->db_user = $this->request['dbUserName'];

		$this->obDb->db_password = $this->request['dbPassword'];

		$this->obDb->db_port = "3306";

		$this->obDb->db_name = $this->request['dsn'];

		

		#TABLE NAME

	

		$Prefix=$this->request['dbPrefix'];

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

		define("CREDITCARDS",$Prefix.'tbShop_Creditcards ');

		define("SHIPPINGDETAILS",$Prefix.'tbShop_OrderShip');

		define("FROOGLE_SETTINGS",$Prefix.'tb_frooglesettings');

		define("ENQUIRIES",$Prefix.'tbUser_contactus');

		define("COMPANYSETTINGS",$Prefix.'tbCompanySettings');

		define("SITESETTINGS",$Prefix.'tbsettings');

		define("POSTAGE",$Prefix.'tbPostageMethods');

		define("POSTAGEDETAILS",$Prefix.'tbPostageMethodDetails');

		define("PLUGINS",$Prefix.'tbPlugin_apps');



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



		define("REVIEWS",$Prefix.'tbUser_CustomerReviews');

		define("REVIEWHELP",$Prefix.'tbUser_CustomerReviewsHelpful');

		define("REVIEWRATE",$Prefix.'tbUser_CustomerRatings');

		define("TEMPCART",$Prefix.'temp_cart');

		define("TEMPOPTIONS",$Prefix.'temp_options');

		define("TEMPCHOICES",$Prefix.'temp_choices');

		define("TEMPIMAGES",$Prefix.'temp_images');

		

		define("NEWSLETTERS",$Prefix.'tbNewsletter');

		

		define("POSTAGEZONEDETAILS",$Prefix.'tbPostagezoneDetails');

		define("POSTAGEZONE",$Prefix.'tbPostagezones');

		

		define("POSTAGECITY",$Prefix.'tbPostageCity');

		define("POSTAGECITYDETAILS",$Prefix.'tbPostageCityDetails');

		

		define("PRODUCTATTRIBUTES",$Prefix.'tbProductAttributes');

		define("ATTRIBUTES",$Prefix.'tbAttributes');

		define("ATTRIBUTEVALUES",$Prefix.'tbAttributesValue');

		

		define("COMPARE",$Prefix.'tbShop_Compare');

		define("SEARCHES",$Prefix.'Searches');

		

		define("CONFIRMATIONORDERS",$Prefix.'tbConfirmation_Orders');

		

		#FUNCTION TO CREATE DATABASE

		$this->m_createDatabase();

		if($this->upgrade == 0)
		{
		$this->libFunc->m_mosRedirect("index.php?mode=thanks");
		}
		else
		{
		$this->libFunc->m_mosRedirect("index.php?mode=thanks&upgrade=1");
		}

	}#ef



	function valiadateSystemInfo()

	{

		if(!isset($this->request['dbServer']))

		{

			$this->libFunc->m_mosRedirect("index.php");

		}

		$this->errMsg="";

	

		$comFunc=new c_commonFunctions();

		$comFunc->db_host =$this->request['dbServer'];

		$comFunc->db_user =$this->request['dbUserName'];

		$comFunc->db_password =$this->request['dbPassword'];

		$comFunc->db_port = "3306";

		$comFunc->db_name =$this->request['dsn'];

		$comFunc->testTable=$this->request['dbPrefix']."temp_choices";

		$returnValue=$comFunc->checkDatabase();



		if($returnValue!=1)

		{

			$this->err=1;

			$this->errMsg.=$returnValue."<br />";

		}

		else

		{

			$checkPrefix=$comFunc->m_checkPrefix();

			if($checkPrefix!=1)

			{

				$this->err=1;

				$this->errMsg.=$checkPrefix."<br />";

			}

		}		

		if($this->libFunc->m_isNull($this->request['dbServer']))

		{

			$this->err=1;

			$this->errMsg.="Please enter your database server.<br /> ";

		}

		if($this->libFunc->m_isNull($this->request['dsn']))

		{

			$this->err=1;

			$this->errMsg.="Please enter your database name.<br /> ";

		}

		if($this->libFunc->m_isNull($this->request['dbUserName']))

		{

			$this->err=1;

			$this->errMsg.="Please enter your database username.<br /> ";

		}

		

		if($this->libFunc->m_isNull($this->request['dbPassword']))

		{

			$this->err=1;

			$this->errMsg.="Please enter your database password.<br /> ";

		}



		if($this->libFunc->m_isNull($this->request['siteurl']))

		{

			$this->err=1;

			$this->errMsg.="Please enter your secure URL.<br /> ";

		}

		if($this->libFunc->m_isNull($this->request['adminUser']))

		{

			$this->err=1;

			$this->errMsg.="Please enter your preferred admin username.<br /> ";

		}

		if($this->libFunc->m_isNull($this->request['adminPassword']))

		{

			$this->err=1;

			$this->errMsg.="Please enter your preferred admin password.<br /> ";

		}

		if($this->libFunc->m_isNull($this->request['adminemail']))

		{

			$this->err=1;

			$this->errMsg.="Please enter your admin email address.<br /> ";

		}

		if($this->libFunc->m_isNull($this->request['license']))

		{

			$this->err=1;

			$this->errMsg.="Please enter your license key.<br /> ";

		}  else {

			$license = $this->license->DolicenseCheck($this->request['license']);

			if($license['status'] == "Active"){

				if(isset($license['localkey'])){

					$this->localLicense = $license['localkey'];
				}

			} elseif ($license['status'] == "Invalid"){

				$this->err=1;

				$this->errMsg.="Your license key is invalid.<br /> ";

			} elseif ($license['status'] == "Expired"){

				$this->err=1;

				$this->errMsg.="Your license key has expired.<br /> ";

			} elseif ($license['status'] == "Suspended"){

				$this->err=1;

				$this->errMsg.="Your license key has been suspended.<br /> ";

			}

		}

		if (!$handle = @fopen($this->filename, 'w+')) 

		{

			$this->err=1;

			$this->errMsg.="Unable to write file. Please make sure the <strong>/config/</strong> directory exists & is writable.<br /> ";

		}

		else

		{

			fclose($handle);

		}

		return $this->err;

	}


	function m_createDatabase()
	{
		$this->strPath = realpath("../") . "/";
		if(defined("TE_VERSION") && file_exists($this->strPath . "installs/install.sql"))
		{
			if(isset($this->request['plugins']) && $this->request['plugins'] == 1)
			{
				$this->m_getPlugins();
			}
			if(isset($this->request['themes']) && $this->request['themes'] == 1)
			{
				$this->m_getThemes();
			}
			$this->obDb->strPath = $this->strPath;
			
			$version = TE_VERSION;
			$newversion = TE_UPGRADE_VERSION;
			if($this->upgrade === 1 && file_exists($this->strPath . "installs/upgrade.sql"))
			{
				$this->obDb->request = $this->request;
				$_POST['dbPrefix'] = $this->prefix;
				if($this->obDb->ImportSQL($this->strPath . "installs/upgrade.sql",";",1))
				{
					unlink($this->strPath . "installs/upgrade.sql");
				}
			}
			elseif(file_exists($this->strPath . "installs/install.sql") && $this->upgrade === 0 && $this->obDb->ImportSQL($this->strPath . "installs/install.sql"))
			{
				unlink($this->strPath . "installs/install.sql");
				return 1;
			}
			else
			{
				Die("Error importing sql install file");
			}
		}
	}
	
	function getFile($url)
	{
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
	
	function uncompress($srcName, $dstName)
	{
		/*$sfp = gzopen($srcName, "rb");
		$fp = fopen($dstName, "w");
		while ($string = gzread($sfp, 4096)) {
			fwrite($fp, $string, strlen($string));
		}
		gzclose($sfp);
		fclose($fp);*/
	}
	
	function m_getPlugins()
	{
		
		$plugins = $this->getFile('https://objects.dreamhost.com/tradingeye/Plugins/Plugins.tar.gz');
		if($plugins)
		{
			if(file_put_contents($this->strPath."plugins/plugins.tar.gz",$plugins))
			{
				$path = $this->strPath."plugins/plugins.tar.gz";
				$phar = new PharData($path);
				$phar->extractTo($this->strPath, null, true);
				unlink($path);
				return 1;
			}
			else
			{
				die("Unable to save to plugins folder. Check permissions of plugins folder.");
			}
		}
		else
		{
			Die("unable to download plugins");
		}
	}
	
	function m_getThemes()
	{
		$themes = $this->getFile('https://objects.dreamhost.com/tradingeye/Themes/Themes.tar.gz');
		if($themes)
		{
			if(file_put_contents($this->strPath."themes/themes.tar.gz",$themes))
			{
				$path = $this->strPath."themes/themes.tar.gz";
				$phar = new PharData($path);
				$phar->extractTo($this->strPath, null, true);
				unlink($path);
				return 1;
			}
			else
			{
				Die("Unable to save to themes folder. Check permissions of themes folder.");
			}
		}
		else
		{
			Die("Unable to download themes.");
		}
	}



	function testIoncube() { 

			 $already_loaded = extension_loaded('ionCube Loader');

			 if($already_loaded){ 

				if ($Ioncube_installed_version = $this->getIoncubeLoaderVersion()) { 

					if ($Ioncube_installed_version >= MINIMUM_IONCUBE_LOADER_VERSION_NEEDED) { 

						return 1; 

					} else { 

						return 2; 

					}

				} 

			} else { 

					return 3;

			}

	}// end of Ioncube test



	



  function getIoncubeLoaderVersion() {

		ob_start(); 

		phpinfo(INFO_GENERAL); 

		$phpinfo = ob_get_contents(); 

		ob_end_clean(); 

		$phpinfo = str_replace("&nbsp;", " ", $phpinfo); 

		$needle = "with the ionCube PHP Loader v"; 

		if (!$tmp = stristr($phpinfo, $needle)) { 

			 return 'No'; 

		} else {

			$tmp = substr($tmp, strlen($needle), 6); 

			$pieces = explode(",", $tmp);

			return $pieces[0]; 

	 } 

  }	 // end of check Ioncube version



}#ec

	$onInstall=new c_installs();

?>