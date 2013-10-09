<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.0.5
=======================================================================================
*/
include_once("../configuration.php");

$CpiDirectResultUrl = SITE_SAFEURL."ecom/index.php?action=checkout.process&mode=".$_REQUEST['oid']."&phpsessid=".session_id();
$CpiReturnUrl = SITE_SAFEURL."ecom/index.php?action=checkout.review&mode1=cancel";

// header("Location:".$CpiReturnUrl);
$status=$_POST['transactionstatus'];
$total = $_POST['total'];
$orderid=$_GET['oid'];

//  header("Location:".$CpiDirectResultUrl);
$obDatabase->query ="SELECT fTotalPrice FROM ".ORDERS;
$obDatabase->query.=" WHERE iOrderid_PK = '".$orderid."'";

$row=$obDatabase->fetchQuery();
if($row[0]->fTotalPrice!=$total)
{
  header("Location:".$CpiReturnUrl);  
}
else
{
	$this->obDb->query ="UPDATE ".ORDERS." SET iPayStatus='1',iOrderStatus='1' ";
	header("Location:".$CpiDirectResultUrl);
}


exit;
?>