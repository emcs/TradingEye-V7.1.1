    INSERT INTO {{prefix}}tbsettings (vDatatype,vSmalltext,sConstantName,iAdminUser)
    SELECT * FROM (SELECT 'ActiveTheme','default','',1) AS tmp
    WHERE NOT EXISTS (
        SELECT (vDatatype) FROM {{prefix}}tbsettings WHERE vDatatype='ActiveTheme'
    ) LIMIT 1;
   INSERT INTO {{prefix}}tbsettings (vDatatype,vSmalltext,sConstantName,iAdminUser)
    SELECT * FROM (SELECT 'AdminActiveTheme','default','',1) AS tmp
    WHERE NOT EXISTS (
        SELECT (vDatatype) FROM {{prefix}}tbsettings WHERE vDatatype='AdminActiveTheme'
    ) LIMIT 1;
    INSERT INTO {{prefix}}tbsettings (vDatatype,vSmalltext,sConstantName,iAdminUser)
    SELECT * FROM (SELECT 'AdminThemeUrlPath' as one,'{{safeurl}}' as two,'ADMINTHEMEURLPATH' as three,1 as four) AS tmp
    WHERE NOT EXISTS (
        SELECT (vDatatype) FROM {{prefix}}tbsettings WHERE vDatatype='AdminThemeUrlPath'
    ) LIMIT 1;
    INSERT INTO {{prefix}}tbsettings (vDatatype,vSmalltext,sConstantName,iAdminUser)
    SELECT * FROM (SELECT 'ThemeUrlPath' as a1,'{{safeurl}}' as a2,'THEMEURLPATH' as a3,1 as a4) AS tmp
    WHERE NOT EXISTS (
        SELECT (vDatatype) FROM {{prefix}}tbsettings WHERE vDatatype='ThemeUrlPath'
    ) LIMIT 1;
    INSERT INTO {{prefix}}tbsettings (vDatatype,vSmalltext,sConstantName,iAdminUser)
    SELECT * FROM (SELECT 'AdminThemeRealPath' as a1,'{{sitepath}}modules/' as a2,'ADMINTHEMEPATH' as a3,1 as a4) AS tmp
    WHERE NOT EXISTS (
        SELECT (vDatatype) FROM {{prefix}}tbsettings WHERE vDatatype='AdminThemeRealPath'
    ) LIMIT 1;
    INSERT INTO {{prefix}}tbsettings (vDatatype,vSmalltext,sConstantName,iAdminUser)
    SELECT * FROM (SELECT 'ThemeRealPath' as a1,'{{sitepath}}modules/' as a2,'THEMEPATH' as a3,1 as a4) AS tmp
    WHERE NOT EXISTS (
        SELECT (vDatatype) FROM {{prefix}}tbsettings WHERE vDatatype='ThemeRealPath'
    ) LIMIT 1;
    INSERT INTO {{prefix}}tbsettings (vDatatype,vSmalltext,sConstantName,iAdminUser)
    SELECT * FROM (SELECT 'CardSaveID' as a1,'' as a2,'CS_MERCHANT_ID' as a3,1 as a4) AS tmp
    WHERE NOT EXISTS (
        SELECT (vDatatype) FROM {{prefix}}tbsettings WHERE vDatatype='CardSaveID'
    ) LIMIT 1;
    INSERT INTO {{prefix}}tbsettings (vDatatype,vSmalltext,sConstantName,iAdminUser)
    SELECT * FROM (SELECT 'CardSavePass' as a1,'' as a2,'CS_MERCHANT_PASS' as a3,1 as a4) AS tmp
    WHERE NOT EXISTS (
        SELECT (vDatatype) FROM {{prefix}}tbsettings WHERE vDatatype='CardSavePass'
    ) LIMIT 1;
    INSERT INTO {{prefix}}tbsettings (vDatatype,vSmalltext,sConstantName,iAdminUser)
    SELECT * FROM (SELECT 'CardSaveURL' as a1,'' as a2,'CS_GATEWAY_DOMAIN' as a3,1 as a4) AS tmp
    WHERE NOT EXISTS (
        SELECT (vDatatype) FROM {{prefix}}tbsettings WHERE vDatatype='CardSaveURL'
    ) LIMIT 1;
    INSERT INTO {{prefix}}tbsettings (vDatatype,vSmalltext,sConstantName,iAdminUser)
    SELECT * FROM (SELECT 'CardSavePORT' as a1,'' as a2,'CS_GATEWAY_PORT' as a3,1 as a4) AS tmp
    WHERE NOT EXISTS (
        SELECT (vDatatype) FROM {{prefix}}tbsettings WHERE vDatatype='CardSavePORT'
    ) LIMIT 1;
    INSERT INTO {{prefix}}tbsettings (vDatatype,vSmalltext,sConstantName,iAdminUser)
    SELECT * FROM (SELECT 'CardSaveKey' as a1,'' as a2,'CS_SECRET_KEY' as a3,1 as a4) AS tmp
    WHERE NOT EXISTS (
        SELECT (vDatatype) FROM {{prefix}}tbsettings WHERE vDatatype='CardSaveKey'
    ) LIMIT 1;
    INSERT INTO {{prefix}}tbsettings (vDatatype,vSmalltext,sConstantName,iAdminUser)
    SELECT * FROM (SELECT 'CardRSaveMerchantID' as a1,'' as a2,'CSr_MERCHANT_ID' as a3,1 as a4) AS tmp
    WHERE NOT EXISTS (
        SELECT (vDatatype) FROM {{prefix}}tbsettings WHERE vDatatype='CardRSaveMerchantID'
    ) LIMIT 1;
    INSERT INTO {{prefix}}tbsettings (vDatatype,vSmalltext,sConstantName,iAdminUser)
    SELECT * FROM (SELECT 'CardRSavePassword' as a1,'' as a2,'CSr_MERCHANT_PASS' as a3,1 as a4) AS tmp
    WHERE NOT EXISTS (
        SELECT (vDatatype) FROM {{prefix}}tbsettings WHERE vDatatype='CardRSavePassword'
    ) LIMIT 1;
    INSERT INTO {{prefix}}tbsettings (vDatatype,vSmalltext,sConstantName,iAdminUser)
    SELECT * FROM (SELECT 'CardRSaveKey' as a1,'' as a2,'CSr_KEY' as a3,1 as a4) AS tmp
    WHERE NOT EXISTS (
        SELECT (vDatatype) FROM {{prefix}}tbsettings WHERE vDatatype='CardRSaveKey'
    ) LIMIT 1;
    INSERT INTO {{prefix}}tbsettings (vDatatype,vSmalltext,sConstantName,iAdminUser)
    SELECT * FROM (SELECT 'CardRSaveDomain' as a1,'' as a2,'CSr_DOMAIN' as a3,1 as a4) AS tmp
    WHERE NOT EXISTS (
        SELECT (vDatatype) FROM {{prefix}}tbsettings WHERE vDatatype='CardRSaveDomain'
    ) LIMIT 1;
    INSERT INTO {{prefix}}tbsettings (vDatatype,vSmalltext,sConstantName,iAdminUser)
    SELECT * FROM (SELECT 'CardRSaveResults' as a1,'' as a2,'CSr_RESULTS_DISPLAY' as a3,1 as a4) AS tmp
    WHERE NOT EXISTS (
        SELECT (vDatatype) FROM {{prefix}}tbsettings WHERE vDatatype='CardRSaveResults'
    ) LIMIT 1;
    INSERT INTO {{prefix}}tbsettings (vDatatype,vSmalltext,sConstantName,iAdminUser)
    SELECT * FROM (SELECT 'CardRSaveCV2' as a1,'' as a2,'CSr_CV2_MANDATORY' as a3,1 as a4) AS tmp
    WHERE NOT EXISTS (
        SELECT (vDatatype) FROM {{prefix}}tbsettings WHERE vDatatype='CardRSaveCV2'
    ) LIMIT 1;
    INSERT INTO {{prefix}}tbsettings (vDatatype,vSmalltext,sConstantName,iAdminUser)
    SELECT * FROM (SELECT 'CardRSaveCurrency' as a1,'' as a2,'CSr_CURRENCY' as a3,1 as a4) AS tmp
    WHERE NOT EXISTS (
        SELECT (vDatatype) FROM {{prefix}}tbsettings WHERE vDatatype='CardRSaveCurrency'
    ) LIMIT 1;
    INSERT INTO {{prefix}}tbsettings (vDatatype,vSmalltext,sConstantName,iAdminUser)
    SELECT * FROM (SELECT 'CardSaveCurrency' as a1,'' as a2,'CS_CURRENCY' as a3,1 as a4) AS tmp
    WHERE NOT EXISTS (
        SELECT (vDatatype) FROM {{prefix}}tbsettings WHERE vDatatype='CardSaveCurrency'
    ) LIMIT 1;
    INSERT INTO {{prefix}}tbsettings (vDatatype,nNumberdata,sConstantName,iAdminUser)
    SELECT * FROM (SELECT 'hidenovat' as a1,'0' as a2,'HIDENOVAT' as a3,1 as a4) AS tmp
    WHERE NOT EXISTS (
        SELECT (vDatatype) FROM {{prefix}}tbsettings WHERE vDatatype='hidenovat'
    ) LIMIT 1;
    INSERT INTO {{prefix}}tbsettings (vDatatype,nNumberdata,sConstantName,iAdminUser)
    SELECT * FROM (SELECT 'IncVatTextFlag' as a1,'0' as a2,'INC_VAT_FLAG' as a3,1 as a4) AS tmp
    WHERE NOT EXISTS (
        SELECT (vDatatype) FROM {{prefix}}tbsettings WHERE vDatatype='IncVatTextFlag'
    ) LIMIT 1;
    INSERT INTO {{prefix}}tbsettings (vDatatype,sConstantName,iAdminUser)
    SELECT * FROM (SELECT 'SelectedGateway' as a1,'SELECTED_PAYMENTGATEWAY' as a2,1 as a3) AS tmp
    WHERE NOT EXISTS (
        SELECT (vDatatype) FROM {{prefix}}tbsettings WHERE vDatatype='SelectedGateway'
    ) LIMIT 1;
	INSERT INTO {{prefix}}tbsettings (vDatatype,nNumberdata,sConstantName,iAdminUser)
    SELECT * FROM (SELECT 'RecentlyViewedLimit' as a1,'0' as a2,'RVP_LIMIT' as a3,1 as a4) AS tmp
    WHERE NOT EXISTS (
        SELECT (vDatatype) FROM {{prefix}}tbsettings WHERE vDatatype='RecentlyViewedLimit'
    ) LIMIT 1;
	ALTER IGNORE TABLE {{prefix}}tbUser_Customers ADD vRecovery text;
	ALTER IGNORE TABLE {{prefix}}tbUser_Customers ADD tRequestTime text;
	ALTER IGNORE TABLE {{prefix}}tbUser_Customers ADD iRegistered int;
	ALTER IGNORE TABLE {{prefix}}tbUser_Customers ADD iStatus int;
	ALTER IGNORE TABLE {{prefix}}tbCountry ADD iStatus int;
	ALTER IGNORE TABLE {{prefix}}tbShop_Orders ADD vAuthCode text;
	ALTER IGNORE TABLE {{prefix}}tbShop_Orders ADD vAltCompany text;
	ALTER IGNORE TABLE {{prefix}}tbCountry ADD iso3 text;
	CREATE TABLE `te_temptablezzzzcountry` (`iCountryId_PK` bigint(20) NOT NULL,`iso3` text NOT NULL);
	
	INSERT INTO te_temptablezzzzcountry VALUES('1','AFG');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES('2','ATA');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES('22','ALB');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(23,'DZA');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(24,'ASM');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(25,'AND');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(26,'AGO');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(27,'AIA');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(29,'ATG');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(30,'ARG');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(280,'ARM');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(31,'AUS');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(32,'ABW');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(33,'AUT');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(34,'AZE');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(35,'BHS');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(36,'BHR');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(38,'BGD');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(39,'BRB');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(40,'BLR');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(41,'BEL');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(42,'BLZ');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(43,'BEN');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(44,'BMU');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(45,'BTN');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(46,'BOL');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(47,'BWA');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(48,'BVT');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(49,'BRA');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(50,'BRN');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(51,'BGR');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(52,'BFA');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(53,'BDI');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(54,'KHM');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(55,'CMR');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(57,'CPV');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(58,'CYM');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(59,'TCD');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(60,'CHL');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(61,'CHN');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(62,'CXR');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(63,'CCK');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(64,'COL');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(65,'COM');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(66,'COG');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(67,'COK');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(68,'CRI');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(88,'HRV');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(89,'CUB');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(90,'CYP');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(91,'CZE');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(92,'DNK');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(93,'DJI');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(94,'DMA');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(95,'DOM');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(97,'ECU');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(98,'EGY');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(99,'SLV');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(100,'GBR');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(101,'GNQ');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(102,'ERI');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(103,'EST');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(104,'ETH');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(105,'FLK');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(106,'FRO');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(107,'FJI');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(108,'FIN');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(109,'FRA');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(110,'GUF');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(111,'PYF');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(112,'GAB');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(113,'GMB');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(114,'GEO');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(115,'DEU');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(116,'GHA');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(117,'GIB');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(118,'GRC');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(119,'GRL');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(120,'GRD');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(121,'GLP');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(122,'GUM');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(123,'GTM');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(124,'GIN');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(125,'GNB');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(126,'GUY');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(127,'HTI');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(128,'HND');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(129,'HKG');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(130,'HUN');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(131,'ISL');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(132,'IND');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(133,'IDN');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(134,'IRN');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(135,'IRQ');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(136,'IRL');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(137,'ISR');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(138,'ITA');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(139,'JAM');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(140,'JPN');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(141,'JOR');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(142,'KAZ');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(143,'KEN');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(144,'KIR');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(145,'NFK');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(146,'PRK');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(147,'KWT');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(148,'KGZ');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(296,'LAO');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(149,'LVA');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(150,'LBN');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(151,'LSO');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(152,'LBR');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(153,'LBY');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(154,'LIE');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(155,'LTU');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(156,'LUX');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(157,'MAC');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(158,'MKD');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(159,'MDG');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(160,'MWI');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(161,'MYS');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(162,'MDV');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(163,'MLI');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(164,'MLT');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(165,'MHL');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(166,'MTQ');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(167,'MRT');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(168,'MUS');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(169,'MYT');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(170,'MEX');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(171,'FSM');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(172,'MDA');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(173,'MCO');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(174,'MNG');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(175,'MSR');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(176,'MAR');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(177,'MOZ');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(178,'MMR');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(179,'NAM');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(180,'NRU');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(181,'NPL');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(182,'NLD');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(183,'ANT');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(184,'NCL');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(185,'NZL');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(186,'NIC');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(188,'NGA');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(191,'NOR');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(192,'OMN');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(193,'PAK');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(194,'PLW');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(195,'PAN');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(196,'PNG');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(197,'PRY');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(198,'PER');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(199,'PHL');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(201,'POL');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(202,'PRT');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(204,'QAT');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(206,'ROM');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(207,'RUS');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(208,'RWA');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(209,'LCA');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(210,'WSM');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(211,'SMR');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(212,'SAU');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(214,'SEN');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(215,'SYC');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(216,'SLE');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(217,'SGP');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(218,'SVK');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(219,'SVN');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(220,'SLB');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(221,'SOM');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(222,'ZAF');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(225,'ESP');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(226,'LKA');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(228,'SDN');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(229,'SUR');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(230,'SWZ');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(231,'SWE');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(232,'CHE');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(233,'SYR');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(234,'TWN');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(235,'TJK');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(236,'TZA');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(237,'THA');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(241,'TON');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(243,'TUN');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(244,'TUR');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(245,'TKM');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(247,'TUV');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(248,'UGA');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(249,'UKR');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(250,'ARE');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(251,'USA');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(252,'URY');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(253,'UZB');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(254,'VUT');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(255,'VAT');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(256,'VEN');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(257,'VNM');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(258,'VIR');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(260,'YEM');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(263,'ZMB');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(264,'ZWE');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(265,'IOT');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(266,'BIH');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(267,'CAF');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(268,'COD');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(269,'CIV');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(270,'HMD');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(271,'NER');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(272,'PSE');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(273,'PCN');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(274,'PRI');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(275,'SHN');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(276,'KNA');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(277,'SPM');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(278,'VCT');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(279,'STP');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(292,'SRB');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(281,'SGS');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(282,'KOR');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(283,'SJM');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(284,'TLS');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(285,'TGO');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(286,'TKL');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(287,'TTO');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(288,'UMI');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(289,'VGB');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(290,'WLF');
	INSERT INTO te_temptablezzzzcountry (`iCountryId_PK`, `iso3`) VALUES(291,'ESH');
	UPDATE {{prefix}}tbCountry as C SET iso3=(SELECT T.iso3 FROM te_temptablezzzzcountry as T WHERE C.iCountryId_PK=T.iCountryId_PK);
	DROP TABLE te_temptablezzzzcountry;
	ALTER IGNORE TABLE {{prefix}}tbPlugin_apps ADD iMod int;