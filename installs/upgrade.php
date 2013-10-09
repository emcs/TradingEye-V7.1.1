<?php
set_time_limit(0);
include_once('configuration.php');
$obDatabase = new database();
$obDatabase->db_host = DATABASE_HOST;
$obDatabase->db_user = DATABASE_USERNAME;
$obDatabase->db_password = DATABASE_PASSWORD;
$obDatabase->db_port = DATABASE_PORT;
$obDatabase->db_name = DATABASE_NAME;
echo "Step 1<br/>\n";
    $obDatabase->query = "INSERT INTO ".SITESETTINGS." (vDatatype,vSmalltext,sConstantName,iAdminUser)
    SELECT * FROM (SELECT 'ActiveTheme','default','',1) AS tmp
    WHERE NOT EXISTS (
        SELECT (vDatatype) FROM ".SITESETTINGS." WHERE vDatatype='ActiveTheme'
    ) LIMIT 1";
	$result = $obDatabase->updateQuery();
echo "Step 2<br/>\n";
   $obDatabase->query = "INSERT INTO ".SITESETTINGS." (vDatatype,vSmalltext,sConstantName,iAdminUser)
    SELECT * FROM (SELECT 'AdminActiveTheme','default','',1) AS tmp
    WHERE NOT EXISTS (
        SELECT (vDatatype) FROM ".SITESETTINGS." WHERE vDatatype='AdminActiveTheme'
    ) LIMIT 1";
	$result = $obDatabase->updateQuery();
echo "Step 3<br/>\n";
    $obDatabase->query = "INSERT INTO ".SITESETTINGS." (vDatatype,vSmalltext,sConstantName,iAdminUser)
    SELECT * FROM (SELECT 'AdminThemeUrlPath' as one,'".SITE_URL."' as two,'ADMINTHEMEURLPATH' as three,1 as four) AS tmp
    WHERE NOT EXISTS (
        SELECT (vDatatype) FROM ".SITESETTINGS." WHERE vDatatype='AdminThemeUrlPath'
    ) LIMIT 1";
	$result = $obDatabase->updateQuery();
echo "Step 4<br/>\n";
    $obDatabase->query = "INSERT INTO ".SITESETTINGS." (vDatatype,vSmalltext,sConstantName,iAdminUser)
    SELECT * FROM (SELECT 'ThemeUrlPath' as a1,'".SITE_URL."' as a2,'THEMEURLPATH' as a3,1 as a4) AS tmp
    WHERE NOT EXISTS (
        SELECT (vDatatype) FROM ".SITESETTINGS." WHERE vDatatype='ThemeUrlPath'
    ) LIMIT 1";
	$result = $obDatabase->updateQuery();
echo "Step 5<br/>\n";
    $obDatabase->query = "INSERT INTO ".SITESETTINGS." (vDatatype,vSmalltext,sConstantName,iAdminUser)
    SELECT * FROM (SELECT 'AdminThemeRealPath' as a1,'".SITE_PATH."modules/' as a2,'ADMINTHEMEPATH' as a3,1 as a4) AS tmp
    WHERE NOT EXISTS (
        SELECT (vDatatype) FROM ".SITESETTINGS." WHERE vDatatype='AdminThemeRealPath'
    ) LIMIT 1";
	$result = $obDatabase->updateQuery();
echo "Step 6<br/>\n";
    $obDatabase->query = "INSERT INTO ".SITESETTINGS." (vDatatype,vSmalltext,sConstantName,iAdminUser)
    SELECT * FROM (SELECT 'ThemeRealPath' as a1,'".SITE_PATH."modules/' as a2,'THEMEPATH' as a3,1 as a4) AS tmp
    WHERE NOT EXISTS (
        SELECT (vDatatype) FROM ".SITESETTINGS." WHERE vDatatype='ThemeRealPath'
    ) LIMIT 1";
	$result = $obDatabase->updateQuery();
echo "Step 7<br/>\n";
    $obDatabase->query = "INSERT INTO ".SITESETTINGS." (vDatatype,vSmalltext,sConstantName,iAdminUser)
    SELECT * FROM (SELECT 'CardSaveID' as a1,'' as a2,'CS_MERCHANT_ID' as a3,1 as a4) AS tmp
    WHERE NOT EXISTS (
        SELECT (vDatatype) FROM ".SITESETTINGS." WHERE vDatatype='CardSaveID'
    ) LIMIT 1";
	$result = $obDatabase->updateQuery();
echo "Step 8<br/>\n";
    $obDatabase->query = "INSERT INTO ".SITESETTINGS." (vDatatype,vSmalltext,sConstantName,iAdminUser)
    SELECT * FROM (SELECT 'CardSavePass' as a1,'' as a2,'CS_MERCHANT_PASS' as a3,1 as a4) AS tmp
    WHERE NOT EXISTS (
        SELECT (vDatatype) FROM ".SITESETTINGS." WHERE vDatatype='CardSavePass'
    ) LIMIT 1";
	$result = $obDatabase->updateQuery();
echo "Step 9<br/>\n";
    $obDatabase->query = "INSERT INTO ".SITESETTINGS." (vDatatype,vSmalltext,sConstantName,iAdminUser)
    SELECT * FROM (SELECT 'CardSaveURL' as a1,'' as a2,'CS_GATEWAY_DOMAIN' as a3,1 as a4) AS tmp
    WHERE NOT EXISTS (
        SELECT (vDatatype) FROM ".SITESETTINGS." WHERE vDatatype='CardSaveURL'
    ) LIMIT 1";
	$result = $obDatabase->updateQuery();
echo "Step 9<br/>\n";
    $obDatabase->query = "INSERT INTO ".SITESETTINGS." (vDatatype,vSmalltext,sConstantName,iAdminUser)
    SELECT * FROM (SELECT 'CardSavePORT' as a1,'' as a2,'CS_GATEWAY_PORT' as a3,1 as a4) AS tmp
    WHERE NOT EXISTS (
        SELECT (vDatatype) FROM ".SITESETTINGS." WHERE vDatatype='CardSavePORT'
    ) LIMIT 1";
	$result = $obDatabase->updateQuery();
echo "Step 10<br/>\n";
    $obDatabase->query = "INSERT INTO ".SITESETTINGS." (vDatatype,vSmalltext,sConstantName,iAdminUser)
    SELECT * FROM (SELECT 'CardSaveKey' as a1,'' as a2,'CS_SECRET_KEY' as a3,1 as a4) AS tmp
    WHERE NOT EXISTS (
        SELECT (vDatatype) FROM ".SITESETTINGS." WHERE vDatatype='CardSaveKey'
    ) LIMIT 1";
	$result = $obDatabase->updateQuery();
echo "Step 11<br/>\n";
    $obDatabase->query = "INSERT INTO ".SITESETTINGS." (vDatatype,vSmalltext,sConstantName,iAdminUser)
    SELECT * FROM (SELECT 'CardRSaveMerchantID' as a1,'' as a2,'CSr_MERCHANT_ID' as a3,1 as a4) AS tmp
    WHERE NOT EXISTS (
        SELECT (vDatatype) FROM ".SITESETTINGS." WHERE vDatatype='CardRSaveMerchantID'
    ) LIMIT 1";
	$result = $obDatabase->updateQuery();
echo "Step 12<br/>\n";
    $obDatabase->query = "INSERT INTO ".SITESETTINGS." (vDatatype,vSmalltext,sConstantName,iAdminUser)
    SELECT * FROM (SELECT 'CardRSavePassword' as a1,'' as a2,'CSr_MERCHANT_PASS' as a3,1 as a4) AS tmp
    WHERE NOT EXISTS (
        SELECT (vDatatype) FROM ".SITESETTINGS." WHERE vDatatype='CardRSavePassword'
    ) LIMIT 1";
	$result = $obDatabase->updateQuery();
echo "Step 13<br/>\n";
    $obDatabase->query = "INSERT INTO ".SITESETTINGS." (vDatatype,vSmalltext,sConstantName,iAdminUser)
    SELECT * FROM (SELECT 'CardRSaveKey' as a1,'' as a2,'CSr_KEY' as a3,1 as a4) AS tmp
    WHERE NOT EXISTS (
        SELECT (vDatatype) FROM ".SITESETTINGS." WHERE vDatatype='CardRSaveKey'
    ) LIMIT 1";
	$result = $obDatabase->updateQuery();
echo "Step 14<br/>\n";
    $obDatabase->query = "INSERT INTO ".SITESETTINGS." (vDatatype,vSmalltext,sConstantName,iAdminUser)
    SELECT * FROM (SELECT 'CardRSaveDomain' as a1,'' as a2,'CSr_DOMAIN' as a3,1 as a4) AS tmp
    WHERE NOT EXISTS (
        SELECT (vDatatype) FROM ".SITESETTINGS." WHERE vDatatype='CardRSaveDomain'
    ) LIMIT 1";
	$result = $obDatabase->updateQuery();
echo "Step 15<br/>\n";
    $obDatabase->query = "INSERT INTO ".SITESETTINGS." (vDatatype,vSmalltext,sConstantName,iAdminUser)
    SELECT * FROM (SELECT 'CardRSaveResults' as a1,'' as a2,'CSr_RESULTS_DISPLAY' as a3,1 as a4) AS tmp
    WHERE NOT EXISTS (
        SELECT (vDatatype) FROM ".SITESETTINGS." WHERE vDatatype='CardRSaveResults'
    ) LIMIT 1";
	$result = $obDatabase->updateQuery();
echo "Step 16<br/>\n";
    $obDatabase->query = "INSERT INTO ".SITESETTINGS." (vDatatype,vSmalltext,sConstantName,iAdminUser)
    SELECT * FROM (SELECT 'CardRSaveCV2' as a1,'' as a2,'CSr_CV2_MANDATORY' as a3,1 as a4) AS tmp
    WHERE NOT EXISTS (
        SELECT (vDatatype) FROM ".SITESETTINGS." WHERE vDatatype='CardRSaveCV2'
    ) LIMIT 1";
	$result = $obDatabase->updateQuery();
echo "Step 17<br/>\n";
    $obDatabase->query = "INSERT INTO ".SITESETTINGS." (vDatatype,vSmalltext,sConstantName,iAdminUser)
    SELECT * FROM (SELECT 'CardRSaveCurrency' as a1,'' as a2,'CSr_CURRENCY' as a3,1 as a4) AS tmp
    WHERE NOT EXISTS (
        SELECT (vDatatype) FROM ".SITESETTINGS." WHERE vDatatype='CardRSaveCurrency'
    ) LIMIT 1";
	$result = $obDatabase->updateQuery();
echo "Step 18<br/>\n";
    $obDatabase->query = "INSERT INTO ".SITESETTINGS." (vDatatype,vSmalltext,sConstantName,iAdminUser)
    SELECT * FROM (SELECT 'CardSaveCurrency' as a1,'' as a2,'CS_CURRENCY' as a3,1 as a4) AS tmp
    WHERE NOT EXISTS (
        SELECT (vDatatype) FROM ".SITESETTINGS." WHERE vDatatype='CardSaveCurrency'
    ) LIMIT 1";
	$result = $obDatabase->updateQuery();
echo "Step 19<br/>\n";
    $obDatabase->query = "INSERT INTO ".SITESETTINGS." (vDatatype,nNumberdata,sConstantName,iAdminUser)
    SELECT * FROM (SELECT 'hidenovat' as a1,'0' as a2,'HIDENOVAT' as a3,1 as a4) AS tmp
    WHERE NOT EXISTS (
        SELECT (vDatatype) FROM ".SITESETTINGS." WHERE vDatatype='hidenovat'
    ) LIMIT 1";
	$result = $obDatabase->updateQuery();
echo "Step 20<br/>\n";
    $obDatabase->query = "INSERT INTO ".SITESETTINGS." (vDatatype,nNumberdata,sConstantName,iAdminUser)
    SELECT * FROM (SELECT 'IncVatTextFlag' as a1,'0' as a2,'INC_VAT_FLAG' as a3,1 as a4) AS tmp
    WHERE NOT EXISTS (
        SELECT (vDatatype) FROM ".SITESETTINGS." WHERE vDatatype='IncVatTextFlag'
    ) LIMIT 1";
	$result = $obDatabase->updateQuery();
echo "Step 21<br/>\n";
    $obDatabase->query = "INSERT INTO ".SITESETTINGS." (vDatatype,sConstantName,iAdminUser)
    SELECT * FROM (SELECT 'SelectedGateway' as a1,'SELECTED_PAYMENTGATEWAY' as a2,1 as a3) AS tmp
    WHERE NOT EXISTS (
        SELECT (vDatatype) FROM ".SITESETTINGS." WHERE vDatatype='SelectedGateway'
    ) LIMIT 1";
	$result = $obDatabase->updateQuery();
echo "Step 22<br/>\n";
    $obDatabase->query = "INSERT INTO ".SITESETTINGS." (vDatatype,nNumberdata,sConstantName,iAdminUser)
    SELECT * FROM (SELECT 'RecentlyViewedLimit' as a1,'0' as a2,'RVP_LIMIT' as a3,1 as a4) AS tmp
    WHERE NOT EXISTS (
        SELECT (vDatatype) FROM ".SITESETTINGS." WHERE vDatatype='RecentlyViewedLimit'
    ) LIMIT 1";
	$result = $obDatabase->updateQuery();
echo "Step 22<br/>\n";	
	$obDatabase->query = "SELECT * FROM ".CUSTOMERS." LIMIT 1";
	$result = $obDatabase->fetchQuery();
	IF(!isset($result[0]->vRecovery))
	{
		$obDatabase->query = "ALTER IGNORE TABLE ".CUSTOMERS." ADD vRecovery text";
		$result = $obDatabase->updateQuery();
	}
	IF(!isset($result[0]->tRequestTime))
	{
		$obDatabase->query = "ALTER IGNORE TABLE ".CUSTOMERS." ADD tRequestTime text";
		$result = $obDatabase->updateQuery();
	}
 echo "Step 23<br/>\n";  
	$obDatabase->query = "SELECT * FROM ".ORDERS." LIMIT 1";
	$result = $obDatabase->fetchQuery();
	IF(!isset($result[0]->vAltCompany))
	{
		$obDatabase->query = "ALTER IGNORE TABLE ".ORDERS." ADD vAltCompany text";
		$result = $obDatabase->updateQuery();
	}
echo "Step 24<br/>\n";	
	$obDatabase->query = "SELECT * FROM ".COUNTRY." LIMIT 1";
	$result = $obDatabase->fetchQuery();
	IF(!isset($result[0]->iso3))
	{
		echo "Step 25<br/>\n";
		$obDatabase->query = "ALTER IGNORE TABLE ".COUNTRY." ADD iso3 text";
		$result = $obDatabase->updateQuery();
		echo "Step 26<br/>\n";
		//$obDatabase->query = "select * from information_schema.columns where table_name = 'te_temptablezzzzcountry'";
		//$result = $obDatabase->updateQuery();
		//echo "count:".$obDatabase->record_count."<br/>\n";
		$obDatabase->query = "CREATE TABLE `te_temptablezzzzcountry` (`iCountryId_PK` bigint(20) NOT NULL,`iso3` text NOT NULL)";
		$result = $obDatabase->updateQuery();
		$obDatabase->query="INSERT INTO te_temptablezzzzcountry VALUES('1','AFG')";
		$result = $obDatabase->updateQuery();
		$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES('2','ATA');";
		$result = $obDatabase->updateQuery();
		$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES('22','ALB');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(23,'DZA');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(24,'ASM');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(25,'AND');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(26,'AGO');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(27,'AIA');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(29,'ATG');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(30,'ARG');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(280,'ARM');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(31,'AUS');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(32,'ABW');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(33,'AUT');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(34,'AZE');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(35,'BHS');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(36,'BHR');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(38,'BGD');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(39,'BRB');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(40,'BLR');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(41,'BEL');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(42,'BLZ');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(43,'BEN');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(44,'BMU');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(45,'BTN');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(46,'BOL');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(47,'BWA');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(48,'BVT');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(49,'BRA');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(50,'BRN');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(51,'BGR');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(52,'BFA');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(53,'BDI');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(54,'KHM');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(55,'CMR');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(57,'CPV');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(58,'CYM');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(59,'TCD');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(60,'CHL');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(61,'CHN');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(62,'CXR');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(63,'CCK');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(64,'COL');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(65,'COM');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(66,'COG');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(67,'COK');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(68,'CRI');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(88,'HRV');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(89,'CUB');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(90,'CYP');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(91,'CZE');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(92,'DNK');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(93,'DJI');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(94,'DMA');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(95,'DOM');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(97,'ECU');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(98,'EGY');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(99,'SLV');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(100,'GBR');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(101,'GNQ');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(102,'ERI');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(103,'EST');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(104,'ETH');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(105,'FLK');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(106,'FRO');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(107,'FJI');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(108,'FIN');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(109,'FRA');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(110,'GUF');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(111,'PYF');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(112,'GAB');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(113,'GMB');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(114,'GEO');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(115,'DEU');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(116,'GHA');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(117,'GIB');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(118,'GRC');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(119,'GRL');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(120,'GRD');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(121,'GLP');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(122,'GUM');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(123,'GTM');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(124,'GIN');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(125,'GNB');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(126,'GUY');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(127,'HTI');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(128,'HND');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(129,'HKG');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(130,'HUN');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(131,'ISL');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(132,'IND');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(133,'IDN');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(134,'IRN');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(135,'IRQ');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(136,'IRL');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(137,'ISR');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(138,'ITA');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(139,'JAM');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(140,'JPN');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(141,'JOR');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(142,'KAZ');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(143,'KEN');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(144,'KIR');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(145,'NFK');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(146,'PRK');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(147,'KWT');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(148,'KGZ');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(296,'LAO');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(149,'LVA');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(150,'LBN');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(151,'LSO');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(152,'LBR');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(153,'LBY');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(154,'LIE');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(155,'LTU');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(156,'LUX');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(157,'MAC');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(158,'MKD');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(159,'MDG');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(160,'MWI');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(161,'MYS');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(162,'MDV');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(163,'MLI');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(164,'MLT');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(165,'MHL');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(166,'MTQ');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(167,'MRT');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(168,'MUS');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(169,'MYT');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(170,'MEX');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(171,'FSM');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(172,'MDA');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(173,'MCO');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(174,'MNG');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(175,'MSR');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(176,'MAR');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(177,'MOZ');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(178,'MMR');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(179,'NAM');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(180,'NRU');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(181,'NPL');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(182,'NLD');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(183,'ANT');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(184,'NCL');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(185,'NZL');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(186,'NIC');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(188,'NGA');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(191,'NOR');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(192,'OMN');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(193,'PAK');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(194,'PLW');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(195,'PAN');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(196,'PNG');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(197,'PRY');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(198,'PER');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(199,'PHL');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(201,'POL');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(202,'PRT');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(204,'QAT');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(206,'ROM');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(207,'RUS');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(208,'RWA');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(209,'LCA');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(210,'WSM');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(211,'SMR');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(212,'SAU');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(214,'SEN');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(215,'SYC');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(216,'SLE');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(217,'SGP');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(218,'SVK');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(219,'SVN');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(220,'SLB');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(221,'SOM');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(222,'ZAF');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(225,'ESP');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(226,'LKA');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(228,'SDN');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(229,'SUR');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(230,'SWZ');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(231,'SWE');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(232,'CHE');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(233,'SYR');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(234,'TWN');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(235,'TJK');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(236,'TZA');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(237,'THA');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(241,'TON');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(243,'TUN');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(244,'TUR');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(245,'TKM');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(247,'TUV');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(248,'UGA');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(249,'UKR');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(250,'ARE');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(251,'USA');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(252,'URY');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(253,'UZB');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(254,'VUT');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(255,'VAT');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(256,'VEN');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(257,'VNM');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(258,'VIR');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(260,'YEM');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(263,'ZMB');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(264,'ZWE');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(265,'IOT');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(266,'BIH');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(267,'CAF');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(268,'COD');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(269,'CIV');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(270,'HMD');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(271,'NER');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(272,'PSE');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(273,'PCN');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(274,'PRI');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(275,'SHN');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(276,'KNA');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(277,'SPM');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(278,'VCT');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(279,'STP');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(292,'SRB');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(281,'SGS');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(282,'KOR');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(283,'SJM');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(284,'TLS');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(285,'TGO');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(286,'TKL');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(287,'TTO');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(288,'UMI');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(289,'VGB');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(290,'WLF');";
		$result = $obDatabase->updateQuery();$obDatabase->query="INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(291,'ESH');";
		echo "Step 28<br/>\n";
		$obDatabase->query = "UPDATE ".COUNTRY." as C SET iso3=(SELECT T.iso3 FROM te_temptablezzzzcountry as T WHERE C.iCountryId_PK=T.iCountryId_PK)";
		$result = $obDatabase->updateQuery();
		echo "Step 29<br/>\n";
		$obDatabase->query = "DROP TABLE te_temptablezzzzcountry;";
		$result = $obDatabase->updateQuery();
		echo "Step 30<br/>\n";
		$obDatabase->query = "ALTER IGNORE TABLE ".PLUGINS." ADD iMod int;";
		$result = $obDatabase->updateQuery();
		}
		echo "Complete.";
	?>