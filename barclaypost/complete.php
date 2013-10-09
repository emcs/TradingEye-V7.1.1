<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.0.5
=======================================================================================
*/
$thesuccess=trim($_POST["transactionstatus"]);

include_once("../libs/database.php");
include_once("../libs/template.php");
include_once("../libs/libFunctions.php");
include_once("../libs/htmlMimeMail.php");
include_once("../config/dbConf.php");

#DATABASE TABLES

define("ORDERS",$Prefix.'tbShop_Orders');

$obDatabase = new database();
$obDatabase->db_host = DATABASE_HOST;
$obDatabase->db_user = DATABASE_USERNAME;
$obDatabase->db_password = DATABASE_PASSWORD;
$obDatabase->db_port = DATABASE_PORT;
$obDatabase->db_name = DATABASE_NAME;

$orderid=trim($_REQUEST["oid"]);
$theauthcode="ePDQ";

if($orderid != "" && $thesuccess=="Success")
{
      $obDatabase->query ="UPDATE ".ORDERS." SET ";
	  $obDatabase->query.="iPayStatus='1',iOrderStatus='1'";
}
else
{
	$obDatabase->query ="UPDATE ".ORDERS." SET ";
	$obDatabase->query.="iPayStatus='0',iOrderStatus='0' ";
}

$obDatabase->query.=" WHERE iOrderid_PK = '".$orderid."'";
$rowConfig=$obDatabase->updateQuery();

?>