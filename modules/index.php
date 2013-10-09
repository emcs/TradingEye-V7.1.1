<?php
session_start();
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.0.5
=======================================================================================
*/
	if(isset($_POST['sessionid']) && $_POST['sessionid'] !=""){
		session_id($_POST['sessionid']);
		//if(isset($_POST['session_data']))
		//$_SESSION=unserialize(base64_decode($_POST['session_data'])); 
	}
	
	define("SESSIONID",session_id());	

	# Setting up all the get and post variable into an array testing comment
	if(!isset($attributes) || !is_array($attributes)) 
	{
		$attributes = array();
		$attributes = array_merge($_GET, $_POST, $_FILES);
	}
	#TO TRIM ALL ATTRIBUTES
	function trim_value(&$value) 
	 { 
		  if(!is_array($value))
		  {
			 $value = trim($value); 
		  }
	 }
	 array_walk($attributes, 'trim_value');

	/*$libFunc=new libFunctions();
	$libFunc->addToDB($attributes);*/

	include_once(MODULES_PATH."default/prevNext.php");
	include_once($pluginInterface->plugincheck(MODULES_PATH."user/user_controller.php")); 
	include_once($pluginInterface->plugincheck(MODULES_PATH."ecom/ecom_controller.php")); 
	include_once(MODULES_PATH."default/commonFunctions.php");	

	include_once(MODULES_PATH."default/homeDisplay.php");
	include_once(MODULES_PATH."default/leftmenu.php");
	$obMainTemplate = new Template();
	
	#SET METATAGS ALSO IN LEFT MENU FILE
	#SET LEFT PANEL FUNCTION CLALLED IN CONSTRUCTOR IN LEFT MENU FILE
	$obUserAdmin=new c_leftMenu($obDatabase,$obMainTemplate,$attributes);
	

	if(isset($_SESSION['uid']) && isset($_SESSION['uname']))
	{
		$obUserAdmin->m_inlineEditor();
	}
	global $sModule;
	switch($sModule)
	{
		case "ecom":
			$obEcomAdmin=new c_ecomController($obDatabase,$obMainTemplate,$attributes);
		break;
		case "user":
			$obUserAdmin=new c_userController($obDatabase,$obMainTemplate,$attributes);
		break;
		default:
			$obHomeDisplay=new c_homeDisplay($obDatabase,$obMainTemplate,$attributes);
		break;
	}	
	$obMainTemplate->set_var("TPL_VAR_CURRENCY",CONST_CURRENCY);
	$libFunc			=new c_libFunctions();
	$myShopUrl		=$libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=ecom.viewcart");
	$obMainTemplate->set_var("TPL_VAR_CARTLINK",$myShopUrl);
	if(isset($_SESSION['grandTotal']))
	{
		$obMainTemplate->set_var("TPL_VAR_GRANDTOTAL",number_format($_SESSION['grandTotal'],2,'.',''));
		$obMainTemplate->set_var("TPL_VAR_TOTALQTY",$_SESSION['totalQty']);
	}
	else
	{
		$obMainTemplate->set_var("TPL_VAR_GRANDTOTAL",'0.00');
		$obMainTemplate->set_var("TPL_VAR_TOTALQTY",'0');
	}

	if($libFunc->ifSet($_SESSION,"cssSelectedFile",""))
	{
		$obMainTemplate->set_var("TPL_VAR_CSSFILE",trim($_SESSION['cssSelectedFile']));
	}
	else
	{
		$obMainTemplate->set_var("TPL_VAR_CSSFILE",trim(DEFAULT_CSS));
	}
	$accessibility		=$libFunc->m_safeUrl(SITE_URL."index.php?action=cms&mode=accessibility");
	$obMainTemplate->set_var("TPL_VAR_ACCESSIBILITY",$accessibility);
	$conditions		=$libFunc->m_safeUrl(SITE_URL."index.php?action=cms&mode=conditions");
	$obMainTemplate->set_var("TPL_VAR_CONDITIONS",$conditions);
	$privacy		=$libFunc->m_safeUrl(SITE_URL."index.php?action=cms&mode=privacy");
	$obMainTemplate->set_var("TPL_VAR_PRIVACY",$privacy);
	$contactus		=$libFunc->m_safeUrl(SITE_URL."index.php?action=contactus");
	$obMainTemplate->set_var("TPL_VAR_CONTACTUS",$contactus);
	$sitemap		=$libFunc->m_safeUrl(SITE_URL."index.php?action=sitemap");
	$obMainTemplate->set_var("TPL_VAR_SITEMAP",$sitemap);
	
	$productRss		=$libFunc->m_safeUrl(SITE_URL."index.php?action=productRss");
	$obMainTemplate->set_var("TPL_VAR_PRODUCTRSS",$productRss);
	$articleRss		=$libFunc->m_safeUrl(SITE_URL."index.php?action=articleRss");
	$obMainTemplate->set_var("TPL_VAR_ARTICLERSS",$articleRss);
	//$layoutFilePath=THEMEPATH."default/templates/main/layout/".$layoutTemplate;
	$layoutFilePath=THEMEPATH."default/templates/main/layout/";
	$obMainTemplate->set_file('hMainTemplate',$layoutFilePath);
	
	$obMainTemplate->set_block("hMainTemplate","TPL_ARTICLERSS_BLK","articlerss_blk");
	$obMainTemplate->set_block("hMainTemplate","TPL_PRODUCTRSS_BLK","productrss_blk");
	
	$obMainTemplate->set_var("articlerss_blk","");
	$obMainTemplate->set_var("productrss_blk","");

	
	if(RSSPRODUCT > 1)
		{
		$obMainTemplate->parse("productrss_blk","TPL_PRODUCTRSS_BLK");
		}
	
	if(RSSARTICLES > 1)
		{
		$obMainTemplate->parse("articlerss_blk","TPL_ARTICLERSS_BLK");
		}
	
		
	$obMainTemplate->set_var("TPL_VAR_HOMEURL",$libFunc->m_safeUrl(SITE_URL));
	$obMainTemplate->set_var("TPL_VAR_SITENAME",htmlspecialchars(SITE_NAME));
	$obMainTemplate->set_var('TPL_VAR_REAL_PATH',$libFunc->real_path());
	$obMainTemplate->set_var("TPL_VAR_SLOGAN",htmlspecialchars(COMPANY_SLOGAN));	
	$obMainTemplate->set_var("TPL_VAR_THEME_PATH",THEMEURLPATH);
	$obMainTemplate->set_var("TPL_VAR_IMAGE_WIDTH", UPLOAD_SMIMAGEWIDTH);
	$obMainTemplate->set_var("TPL_VAR_IMAGE_HEIGHT", UPLOAD_SMIMAGEHEIGHT);
	
	$obDb = new database();
    $obDb->db_host = DATABASE_HOST;
    $obDb->db_user = DATABASE_USERNAME;
    $obDb->db_password = DATABASE_PASSWORD;
    $obDb->db_port = DATABASE_PORT;
    $obDb->db_name = DATABASE_NAME;
   
    $obDb->query = "SELECT * FROM ".COMPANYSETTINGS;
    $row_setting=$obDb->fetchQuery();
    
    if (!$libFunc->m_isNull($row_setting[0]->vLogo)) 
    {
        $obMainTemplate->set_var("TPL_VAR_LOGOIMG", SITE_URL."images/company/".$row_setting[0]->vLogo);

    }
	if(isset($_SESSION['google']) && isset($_SESSION['google']['paid']) && $_SESSION['google']['paid']===1)
	{
		$googleanalytics = "<script type=\"text/javascript\">

							  var _gaq = _gaq || [];
							  _gaq.push(['_setAccount', '".stripslashes(ANALYTICSCODE)."']);
							  _gaq.push(['_trackPageview']);
							  _gaq.push(['_addTrans',
								'".$_SESSION['google']['id']."',
								'".$row_setting[0]->vCname."',
								'".$_SESSION['google']['subtotal']."',
								'".$_SESSION['google']['tax']."',
								'".$_SESSION['google']['shipping']."',
								'".$_SESSION['google']['city']."',
								'".$_SESSION['google']['state']."',
								'".$_SESSION['google']['country']."'
							  ]);";
		foreach($_SESSION['google']['products'] as $key => $value)
		{
			$googleanalytics = $googleanalytics . "
			".$_SESSION['google']['products'][$key];
		}
		$googleanalytics = $googleanalytics . "_gaq.push(['_trackTrans']);

							  (function() {
								var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
								ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
								var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
							  })();

							</script>";
		unset($_SESSION['google']);
	}
	else
	{
		$googleanalytics = "
		
		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', '".stripslashes(ANALYTICSCODE)."']);
		  _gaq.push(['_trackPageview']);

		  (function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();

		";
	}
	  $obMainTemplate->set_var("TPL_VAR_GOOGLEANALYTICS",$googleanalytics);
	  $obMainTemplate->set_var("TPL_VAR_FOOTER",stripslashes(FOOTER_HTML));
	$obMainTemplate->pparse('output', 'hMainTemplate');
?>