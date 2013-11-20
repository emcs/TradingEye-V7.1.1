<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.1.0
=======================================================================================
*/
defined('_TEEXEC') or die;
	session_start();
	//logout unauthorized user start
	if (isset($_SESSION['uname'])&&$_SESSION['uname']!=''&&!isset($_SESSION['adminFlag']) && !$_SESSION['adminFlag']){ 
		session_destroy(); 
		$_SESSION=array();
		header("Location:".SITE_URL."adminindex.php"); 
	}
	//logout unauthorized user end
	if (isset($_REQUEST['flag'])){
		if ($_REQUEST['flag']=="dashboard"){
		unset ($_SESSION['flag']);
		$_SESSION['dashSelec'] = "class='selected'";
		}
	}

	# Setting up all the get and post variable into an array
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
	include_once($pluginInterface->plugincheck(MODULES_PATH."default/authentication.php"));
	include_once($pluginInterface->plugincheck(MODULES_PATH."default/authorization.php"));
	include_once($pluginInterface->plugincheck(MODULES_PATH."default/admin_prevnext.php"));
	include_once($pluginInterface->plugincheck(MODULES_PATH."default/commonFunctions.php"));
	include_once($pluginInterface->plugincheck(MODULES_PATH."ecom/ecom_admin_controller.php")); 
	include_once($pluginInterface->plugincheck(MODULES_PATH."user/user_admin_controller.php")); 
	include_once($pluginInterface->plugincheck(MODULES_PATH."order/order_admin_controller.php")); 
	include_once($pluginInterface->plugincheck(MODULES_PATH."sales/sales_admin_controller.php")); 
	include_once($pluginInterface->plugincheck(MODULES_PATH."admin/admin_controller.php")); 
	include_once($pluginInterface->plugincheck(MODULES_PATH."default/adminhomedisplay.php"));
	include_once($pluginInterface->plugincheck(MODULES_PATH."default/adminleftmenu.php"));
	$obMainTemplate = new Template();
	$libFunc=new c_libFunctions();
	$obMainTemplate->set_file('hMainTemplate',ADMINTHEMEPATH."default/templates/admin/default.htm");
	$obMainTemplate->set_var("TPL_VAR_SITEURL",SITE_URL);
	$obMainTemplate->set_var("TPL_VAR_SITENAME",htmlspecialchars(SITE_NAME));
	$obMainTemplate->set_var('TPL_VAR_REAL_PATH',$libFunc->real_path());
	$obMainTemplate->set_var("GRAPHICSMAINPATH",SITE_URL."/graphics");
	
	
	

	if(!isset($_SESSION['uname']) || empty($_SESSION['uname']))
	{
		$obUserAdmin=new c_authentication($obDatabase,$obMainTemplate,$attributes);
	}

	$obUserAdmin=new c_leftMenu($obDatabase,$obMainTemplate,$attributes);
	$obUserAdmin=new c_authorization($obDatabase,$obMainTemplate,$attributes);
	global $sModule;
	
	switch($sModule)
	{
		case "ecom":
			$obEcomAdmin=new c_ecomAdminController($obDatabase,$obMainTemplate,$attributes,$libFunc);
		break;
		case "order":
			$obOrdAdmin=new c_orderAdminController($obDatabase,$obMainTemplate,$attributes,$libFunc);
		break;
		case "user":
			$obUserAdmin=new c_userAdminController($obDatabase,$obMainTemplate,$attributes,$libFunc);
		break;
		case "admin":
			$obUserAdmin=new c_adminController($obDatabase,$obMainTemplate,$attributes,$libFunc);
		break;
		case "sales":
			$obUserAdmin=new c_salesAdminController($obDatabase,$obMainTemplate,$attributes,$libFunc);
		break;
		default:
			$obHomeDisplay=new c_homeDisplay($obDatabase,$obMainTemplate,$attributes,$libFunc);
		break;
	}	
	$obMainTemplate->set_var("TPL_VAR_METATITLE",SITE_NAME);
	$obMainTemplate->set_var("TPL_VAR_YEAR",date("Y"));
	$obMainTemplate->set_var("TPL_VAR_THEME_PATH",ADMINTHEMEURLPATH);
	$obMainTemplate->set_var("TPL_VAR_AUTH_TOKEN",$_SESSION['AUTHTOKEN2']);
	$obMainTemplate->pparse('output', 'hMainTemplate');

?>