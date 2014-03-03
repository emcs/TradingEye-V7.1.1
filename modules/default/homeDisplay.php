<?php
/*
=======================================================================================
Copyright: TradingEye
Version: 7.1.1
=======================================================================================
*/
defined('_TEEXEC') or die;
include_once($pluginInterface->plugincheck(MODULES_PATH."default/classes/main/cmsContent_interface.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."default/classes/main/cmsContent_db.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."sales/classes/admin/feeds_interface.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."default/classes/main/sitemap.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."default/messages.php")); 
class c_homeDisplay
{
	# Class constructor		
	function c_homeDisplay($obDatabase,&$obTemplate,$attributes)
	{
		$this->obDb=$obDatabase;
		$this->obTpl=&$obTemplate;
		$this->request=$attributes;
		$this->templatePath=THEMEPATH."default/templates/main/";
		$this->printMainBlock();
	}
	

	# Event handler
	function printMainBlock()
	{
		if(!isset($this->request['action']))
		{
			$this->request['action']="";
		}
		$action=explode(".",$this->request['action']);
		$obCms=new c_cmsContent();
		$obCms->obDb=$this->obDb;
		$obCms->request=$this->request;

		$obCmsDb=new c_cmsContentDb();
		$obCmsDb->obDb=$this->obDb;
		$obCmsDb->request=$this->request;

		$obSiteMap=new c_siteMap();
		$obSiteMap->obDb=$this->obDb;
		$obSiteMap->request=$this->request;

		$rssInterface=new feed_interface();
		$rssInterface->obDb=$this->obDb;
		$rssInterface->obDb=$this->obDb;
		
		$this->libFunc=new c_libFunctions();
		$comFunc=new c_commonFunctions();
		$comFunc->request=$this->request;

		switch($action[0])
			{
				case "error":
					$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo;&nbsp;Error");
					$comFunc->cmsTemplate=$this->templatePath."error.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$comFunc->m_dspError());
				break;
				case "cms":
					$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo;&nbsp;".$this->request['mode']);
					$obCms->cmsTemplate=$this->templatePath."cmsContent.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obCms->m_showCmsContent());
				break;
				case "sitemap":
					$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo;&nbsp;Sitemap");
					$obSiteMap->siteMapTemplate=$this->templatePath."siteMap.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obSiteMap->m_showSitemap());
				break;
	
				case "download":
					$retUrl=$this->libFunc->m_safeUrl(SITE_URL."index.php?action=download&mode=".$this->request['mode']);
					$_SESSION['referer']=$retUrl;
					$this->libFunc->authenticate();
					unset($_SESSION['referer']);
					$this->obDb->query = "SELECT `iOrderid_PK` FROM ". ORDERS ." WHERE `iCustomerid_FK` = '".$_SESSION['userid'] ."' AND `iOrderStatus` = '1'";
					foreach($this->obDb->fetchQuery() as $result){
						$this->obDb->query = "SELECT COUNT(`iOrderid_FK`) FROM ". ORDERPRODUCTS ." WHERE `iOrderid_FK` = '". $result->iOrderid_PK ."' AND `iProductid_FK` = '".$this->request['mode']."'";
						$record = $this->obDb->fetchQuery();
						if($record[0]->{'COUNT(`iOrderid_FK`)'} > 0){
							$this->obDb->query = "SELECT `vDownloadablefile` FROM ". PRODUCTS." WHERE `iProdid_PK` = '". $this->request['mode']."'";
							$fileresult = $this->obDb->fetchQuery();
							$obCmsDb->libFunc=$this->libFunc;
							$obCmsDb->file = $fileresult[0]->vDownloadablefile;
							$obCmsDb->m_downloadFile();
							$this->libFunc->m_mosRedirect(SITE_URL);
						} else {
							$this->libFunc->m_mosRedirect(SITE_URL);
						}
					}
				break;
				
				case "productRss":
						$this->libFunc->m_mosRedirect(SITE_URL."RSS/productRss.xml");
						exit;
				break;
				
				case "articleRss":
						$this->libFunc->m_mosRedirect(SITE_URL."RSS/articleRss.xml");
						exit;
				break;
				
				case "contactus":
				if(!isset($action[1]))
				{
					$action[1]="";
				}
				switch($action[1])
				{
					case "thanks":
					$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo;&nbsp;Thanks");
					$obCms->cmsTemplate=$this->templatePath."contactsubmit.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obCms->m_showThanks());
					break;
					case "add":
					if($obCms->m_validateContact())#RETURN true if error exist
					{
						$obCms->cmsTemplate=$this->templatePath."contact.tpl.htm";
						$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo;&nbsp;Contact us");
						$this->obTpl->set_var("TPL_VAR_BODY",$obCms->m_showContactForm());
					}
					else
					{
						$obCmsDb->m_addContact();
					}
					break;
					default:
					$obCms->cmsTemplate=$this->templatePath."contact.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo;&nbsp;Contact us");
					$this->obTpl->set_var("TPL_VAR_BODY",$obCms->m_showContactForm());
					break;
				}
				break;
				
				default :
					if(isset($this->request['sid']) && !empty($this->request['sid']))
					{
						$value=$this->request['sid'];
						setcookie("sourceid", $value, time()+3600, "/");
					}
					$obCms->cmsTemplate = $this->templatePath."home.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","");
					$this->obTpl->set_var("SiteUrl",SITE_URL);
					$this->obTpl->set_var('TPL_VAR_BODY', $obCms->m_showHomePage());
				break;
			}
	}#end function
}#end class
?>