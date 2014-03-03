<?php
/*
=======================================================================================
Copyright: TradingEye
Version: 7.1.1
=======================================================================================
*/
  include_once('../configuration.php');
  $callback_url=SITE_SAFEURL."ecom/index.php";
	echo "<SCRIPT LANGUAGE=\"Javascript\"> function OnLoadEvent() {document.form.submit(); }</SCRIPT>";  
	echo "<html><head><title>3D Secure Verification</title></head>" .    "<body OnLoad=\"OnLoadEvent();\">" .    "<FORM name=\"form\" action=\"" . $callback_url . "\"method=\"POST\">"; 
	foreach($_REQUEST as $key => $value)
	echo "<input type=\"hidden\" name=\"" . $key."\" value=\"" .$value. "\" />"; 

	echo "<input type=\"hidden\" name=\"action\" value=\"checkout.process\" />"; 
	
	echo "<NOSCRIPT><center><p>Please click the button below to go back to your site</p><input type=\"submit\" value=\"Go\"/></p></center></NOSCRIPT>".    "</form></body></html>"; 
	exit;
?>
