<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.0.5
=======================================================================================
*/


//mysql injection fix starts
function escape($string){
	
	$hasMagicQuotesEnabled  = (bool)get_magic_quotes_gpc();
	$canEscapeString        = function_exists('mysql_real_escape_string');
	
	if($hasMagicQuotesEnabled){
		$string = stripslashes($string);
	}
	
	if($canEscapeString){
	$string = str_replace("\r\n", "", $string); // Fix rn issue on linebreaks
		if($escaped = @mysql_real_escape_string($string)){
			return $escaped;
		}
	}
	
	$replacements = array(
	'\\'      => '\\\\',
	"\0"      => '\\0',
	"'"       => "\\'",
	'"'       => '\\"',
	"\x1a"    => '\\Z',
	"~~r~~n"  =>  '\r\n',
	);
	
	return strtr($string, $replacements);
}

foreach($_POST as $k=>$v){
	if(is_array($_POST[$k])){
		foreach($_POST[$k] as $k2=>$v2){
			$_POST[$k][$k2] = escape($_POST[$k][$k2]);
			if($k != "tabdesc")
			{
				$_POST[$k][$k2] = htmlentities($_POST[$k][$k2],ENT_QUOTES,"UTF-8");
			}
		}
	}
	else 
	{
		$_POST[$k] = escape($_POST[$k]); 
		if($k != "content")
		{
			$_POST[$k] = htmlentities($_POST[$k],ENT_QUOTES,"UTF-8");
		}
	}
}

foreach($_GET as $k=>$v){
	if(is_array($_GET[$k])){
		foreach($_GET[$k] as $k2=>$v2){
			$_GET[$k][$k2] = escape($_GET[$k][$k2]);
			$_GET[$k][$k2] = htmlentities($_GET[$k][$k2]);
		}
	}
	else 
	{
		$_GET[$k] = escape($_GET[$k]); 
		$_GET[$k] = htmlentities($_GET[$k]);
	}
} 
//mysql injection fix ends

include_once("configuration.php");

require_once("libs/plugins.php");
$pluginInterface = new pluginInterface();
include_once($pluginInterface->plugincheck(MODULES_PATH."adminindex.php"));
?>