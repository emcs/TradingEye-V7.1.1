<?php
include("../config/dbConf.php");
mysql_connect(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_PORT) or die(mysql_error());
mysql_select_db(DATABASE_NAME) or die(mysql_error());

$q=mysql_query("SELECT vDatatype,vSmalltext,nNumberdata FROM  {$Prefix}tbsettings WHERE vDatatype='CSPass' OR vDatatype='CSBaseURL' OR vDatatype='CSPort' OR vDatatype='CSRMerchantID' OR vDatatype='CSRCallback' OR vDatatype LIKE 'SITEPATH' OR vDatatype='CSRPreshared' OR vDatatype='CSRMerchantPass' ORDER BY iSettingid");
while($sql=mysql_fetch_array($q)){
if($sql['vDatatype']=="CSRMerchantID") $MerchantID =$sql['vSmalltext'];
if($sql['vDatatype']=="CSRMerchantPass") $Password = $sql['vSmalltext'];
if($sql['vDatatype']=="CSPort") $PaymentProcessorPort = $sql['vSmalltext'];
if($sql['vDatatype']=="CSBaseURL") $SiteSecureBaseURL = $sql['vSmalltext'];
if($sql['vDatatype']=="SITEPATH") define("SITE_PATH", $sql['vSmalltext']);
 
} 
$PaymentProcessorDomain = "cardsaveonlinepayments.com";  
  

if ($PaymentProcessorPort == 443)
{
	$PaymentProcessorFullDomain = $PaymentProcessorDomain."/";
}
else
{
	$PaymentProcessorFullDomain = $PaymentProcessorDomain.":".$PaymentProcessorPort."/";
}
?>