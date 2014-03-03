<?php
/*
=======================================================================================
Copyright: TradingEye
Version: 7.1.1
=======================================================================================
=================================================================================
THESE FOLLOWING DETAIL BELOW IS TAKEN FROM http://www.sitemaps.org/protocol.php
ALWAYS CHECK THE LINK FOR LATEST INFORMATION

Attribute		Description


<urlset>	 	required	
				Encapsulates the file and references the current protocol standard.

<url>	 		required	
				Parent tag for each URL entry. The remaining tags are children of this tag.

<loc>	 		required	
				URL of the page. This URL must begin with the protocol (such as http) and end with a trailing slash, if your web server requires it. 
				This value must be less than 2,048 characters.

<lastmod>		optional	
				The date of last modification of the file. This date should be in W3C Datetime format. This format allows you to omit the time portion, if desired, and use YYYY-MM-DD.
				Note that this tag is separate from the If-Modified-Since (304) header the server can return, and search engines may use the information from both sources differently.

<changefreq>	optional	
				How frequently the page is likely to change. 
				This value provides general information to search engines and may not correlate exactly to how often they crawl the page. 
				Valid values are:
				always
				hourly
				daily
				weekly
				monthly
				yearly
				never

				The value "always" should be used to describe documents that change each time they are accessed. The value "never" should be used to describe archived URLs.
				Please note that the value of this tag is considered a hint and not a command. Even though search engine crawlers may consider this information when making decisions, they may crawl pages marked "hourly" less frequently than that, and they may crawl pages marked "yearly" more frequently than that. Crawlers may periodically crawl pages marked "never" so that they can handle unexpected changes to those pages.

<priority>	 	optional	
				The priority of this URL relative to other URLs on your site. 
				Valid values range from 0.0 to 1.0. 
				This value does not affect how your pages are compared to pages on other sites—it only lets the search engines know which pages you deem most important for the crawlers.
				The default priority of a page is 0.5.
				
Please note that the priority you assign to a page is not likely to influence the position of your URLs in a search engine's result pages. Search engines may use this information when selecting between URLs on the same site, so you can use this tag to increase the likelihood that your most important pages are present in a search index.
Also, please note that assigning a high priority to all of the URLs on your site is not likely to help you. Since the priority is relative, it is only used to select between URLs on your site.

=============
*/



require_once 'sitemap.inc.php';
require_once '../configuration.php';

# SET DATABASE OBJ AS GLOBAL VARIABLE
global $obDatabase;


// create the Sitemap object
$s 			= new Sitemap();
$libFunc	= new c_libFunctions();


# DEFINE PRIORITY VARIABLES 
define("HOMEPAGE_PRIORITY","1.0");
define("DEPARTMENT_PRIORITY","0.9");
define("PRODUCT_PRIORITY","0.7");
define("CONTENT_PRIORITY","0.7");
define("OTHER_PRIORITY","0.5"); # 0.5 IS A DEFAULT VALUE

#DEFINE  CHANGEFREQUENCY VARIABLE 
define("HOMEPAGE_CHANGEFREQ","hourly");
define("DEPARTMENT_CHANGEFREQ","weekly");
define("PRODUCT_CHANGEFREQ","weekly");
define("CONTENT_CHANGEFREQ","weekly");
define("OTHER_CHANGEFREQ","weekly");
define("SITEMAP_CHANGEFREQ","daily");




# ADD THE HOMEPAGE WITH A SLASH "/"
$s->addItem(SITE_URL,'',HOMEPAGE_CHANGEFREQ,HOMEPAGE_PRIORITY);

#======================== PRODUCTS LINKS=====================
	# GETTING PRODUCTS FROM DATABASE
	$obDatabase->query = " SELECT distinct vTitle,vSeoTitle,iProdid_PK,tmBuildDate,tmEditDate FROM ".PRODUCTS." P, ".FUSIONS." F ".
						 " WHERE (P.iProdid_PK=F.iSubId_FK ".
						 " AND vtype='product' ".
						 " AND iState=1) ORDER BY tmBuildDate";
	
	$prodRows		 = $obDatabase->fetchQuery();
	$prodRowsRecord  = $obDatabase->record_count;
	
	#CHECK IF PRODUCT EXISTS
	if ($prodRowsRecord >0 ){
		
		#LOOP THROUGH ALL RECORDS
		for ($i=0; $i< $prodRowsRecord; $i++ ){
			
			$lastmod = "";
			if ($prodRows[$i]->tmEditDate > 0){ 
				$lastmod = $s->searchEngineDateFormat($prodRows[$i]->tmEditDate);	
			}else{
				$lastmod = $s->searchEngineDateFormat($prodRows[$i]->tmBuildDate);
			}		
			$loc = $libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=ecom.pdetails&mode=".$prodRows[$i]->vSeoTitle);
			
			#ADDING DEPARTMENT LINK INTO XML SITEMAP		
			$s->addItem($loc,$lastmod, PRODUCT_CHANGEFREQ, PRODUCT_PRIORITY);
		}
	}

#======================== CONTENTS LINKS=====================
	# GETTING CONTENTS FROM DATABASE
	$obDatabase->query = " SELECT distinct vTitle,vSeoTitle,iContentid_PK,tmBuildDate,tmEditDate FROM ".CONTENTS." C, ".FUSIONS." F ".
						 " WHERE (C.iContentid_PK=F.iSubId_FK ".
						 " AND vtype='content' ".
						 " AND iState=1) ORDER BY tmBuildDate";
	
	$contentRows		 = $obDatabase->fetchQuery();
	$contentRowsRecord  = $obDatabase->record_count;
	
	#CHECK IF PRODUCT EXISTS
	if ($prodRowsRecord >0 ){
		
		#LOOP THROUGH ALL RECORDS
		for ($i=0; $i< $contentRowsRecord; $i++ ){
			
			$lastmod = "";
			if ($contentRows[$i]->tmEditDate > 0){ 
				$lastmod = $s->searchEngineDateFormat($contentRows[$i]->tmEditDate);	
			}else{
				$lastmod = $s->searchEngineDateFormat($contentRows[$i]->tmBuildDate);
			}		
			$loc = $libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=ecom.cdetails&mode=".$contentRows[$i]->vSeoTitle);
			
			#ADDING DEPARTMENT LINK INTO XML SITEMAP		
			$s->addItem($loc,$lastmod, PRODUCT_CHANGEFREQ, PRODUCT_PRIORITY);
		}
	}

#======================== DEPARTMENTS LINKS=====================
# GETTING DEPARTMENTS FROM DATABASE
	$obDatabase->query = " SELECT vTitle,vSeoTitle,iDeptid_PK,tmBuildDate,tmEditDate FROM ".DEPARTMENTS." D, ".FUSIONS." F ".
						 " WHERE (D.iDeptid_PK=F.iSubId_FK ".
						 " AND vtype='department' ".
						 " AND iState=1 AND iDisplayInNav=1) ORDER BY tmBuildDate";
	
	$deptRows		 = $obDatabase->fetchQuery();
	$deptRowsRecord  = $obDatabase->record_count;
	
	#CHECK IF DEPARTMENT EXISTS
	if ($deptRowsRecord >0 ){
		
		#LOOP THROUGH ALL RECORDS
		for ($i=0; $i< $deptRowsRecord; $i++ ){
			
			$lastmod = "";
			if ($deptRows[$i]->tmEditDate > 0){ 
				$lastmod = $s->searchEngineDateFormat($deptRows[$i]->tmEditDate);	
			}else{
				$lastmod = $s->searchEngineDateFormat($deptRows[$i]->tmBuildDate);
			}		
			$loc = $libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=ecom.details&mode=".$deptRows[$i]->vSeoTitle);
			
			#ADDING DEPARTMENT LINK INTO XML SITEMAP		
			$s->addItem($loc,$lastmod, DEPARTMENT_CHANGEFREQ, DEPARTMENT_PRIORITY);
		}
	}


#======================== OTHER LINKS =====================
	#Contact Form
	$s->addItem($libFunc->m_safeUrl(SITE_URL."index.php?action=contactus"),'',OTHER_CHANGEFREQ,"0.9");

	#Search 
	$s->addItem($libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=ecom.search"),'',OTHER_CHANGEFREQ,OTHER_PRIORITY);
	
	#Registration Form
	$s->addItem($libFunc->m_safeUrl(SITE_URL."user/index.php?action=user.signupForm"),'',OTHER_CHANGEFREQ,OTHER_PRIORITY);
	
	#accessibility Page
	$s->addItem($libFunc->m_safeUrl(SITE_URL."index.php?action=cms&mode=accessibility"),'',OTHER_CHANGEFREQ,OTHER_PRIORITY);
	
	#Dynamic Sitemap
	$s->addItem($libFunc->m_safeUrl(SITE_URL."index.php?action=sitemap"),'',SITEMAP_CHANGEFREQ,OTHER_PRIORITY);
	
	#T&C
	$s->addItem($libFunc->m_safeUrl(SITE_URL."index.php?action=cms&mode=conditions"),'',OTHER_CHANGEFREQ,OTHER_PRIORITY);
		
	#Privacy
	$s->addItem($libFunc->m_safeUrl(SITE_URL."index.php?action=cms&mode=privacy"),'',OTHER_CHANGEFREQ,OTHER_PRIORITY);
	
	
	
// output sitemap
if (isset($_GET['target'])){
	// generate Google sitemap
	if (($target = $_GET['target']) == 'google'){
		echo $s->generateGoogleSitemap();
	}	
	// generate Yahoo sitemap
	else if ($target == 'yahoo'){
			echo $s->generateYahooSitemap();
		 }#elseif
}#if
?>