<?php
/*
=======================================================================================
Copyright: TradingEye
Version: 7.1.1
=======================================================================================
*/
defined('_TEEXEC') or die;
class c_enquiryInterface
{
#CONSTRUCTOR
	function c_enquiryInterface()
	{
		$this->libFunc=new c_libFunctions();
		$this->templatePath=THEMEPATH."ecom/templates/main/";
		$this->pageTplPath=THEMEPATH."default/templates/main/";
		$this->largeImage="largeImage.tpl.htm";
		$this->pageTplFile="pager.tpl.htm";
		$this->pageSize="3";
	}

	#FUNCTION TO DISPLAY PRODUCT DETAILS
	function m_showEnquiryForm()
	{
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_ENQUIRY_FILE",$this->enquiryTemplate);
		$this->ObTpl->set_block("TPL_ENQUIRY_FILE","TPL_COUNTRY_BLK","countryblk");
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);	
		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	

		$postUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=enquiry.post");
		$this->ObTpl->set_var("TPL_VAR_POSTURL",$postUrl);
		if(isset($this->request['mode']) && !empty($this->request['mode']))
		{
			#TO DISPLAY THE HEAD NAME
			$this->obDb->query = "SELECT vTitle,vSku,vSeoTitle FROM ".PRODUCTS." WHERE vSeoTitle='".$this->request['mode']."'";
			
			$rowHead = $this->obDb->fetchQuery();
			if($this->obDb->record_count<1)
			{
				$errrorUrl=SITE_URL."index.php?action=error&mode=product";
				$this->libFunc->m_mosRedirect($this->libFunc->m_safeUrl($errrorUrl));
				exit;
			}
			$this->ObTpl->set_var("TPL_VAR_PRODUCTNAME",$this->libFunc->m_displayContent($rowHead[0]->vTitle));

			$this->ObTpl->set_var("TPL_VAR_SKU",$this->libFunc->m_displayContent($rowHead[0]->vSku));
			$this->ObTpl->set_var("TPL_VAR_SEOTITLE",$this->libFunc->m_displayContent($rowHead[0]->vSeoTitle));
		}
		else
		{
			$productUrl=$this->libFunc->m_safeUrl(SITE_URL."index.php");
			$this->libFunc->m_mosRedirect($productUrl);
			$this->ObTpl->set_var("TPL_VAR_PRODUCTNAME","Any Product");
			$this->ObTpl->set_var("TPL_VAR_SKU","");
		}
		$this->obDb->query = "SELECT vCountryName FROM  ".COUNTRY." ORDER BY iSortFlag,vCountryName";
		$row_country = $this->obDb->fetchQuery();
		$row_country_count = $this->obDb->record_count;

		# Loading billing country list		
		for($i=0;$i<$row_country_count;$i++)
		{
			$this->ObTpl->set_var("TPL_COUNTRY_NAME",$this->libFunc->m_displayContent($row_country[$i]->vCountryName));
			$this->ObTpl->parse("countryblk","TPL_COUNTRY_BLK",true);
		}

		return($this->ObTpl->parse("return","TPL_ENQUIRY_FILE"));
	}
#THANKS
	function m_showStatus()
	{
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_VAR_CMS",$this->enquiryTemplate);
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);	
		$this->ObTpl->set_var("TPL_VAR_SITENAME",SITE_NAME);	
		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	

		return($this->ObTpl->parse("return","TPL_VAR_CMS"));
	}
	function m_sendEmail()
	{
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_VAR_ENQUIRYEMAIL",$this->templatePath."enquiryMail.tpl.htm");

		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);	
		$this->ObTpl->set_var("TPL_VAR_SITENAME",SITE_NAME);	
		$this->ObTpl->set_var("TPL_VAR_CURRENCY",CONST_CURRENCY);
	
		$this->ObTpl->set_var("TPL_VAR_PRODUCTNAME",$this->libFunc->m_displayContent($this->request['productName']));
		$this->ObTpl->set_var("TPL_VAR_SKU",$this->libFunc->m_displayContent($this->request['sku']));
		$productUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=ecom.pdetails&mode=".$this->request['seoTitle']);
		$this->ObTpl->set_var("TPL_VAR_PRODUCTURL",$productUrl);
		$this->ObTpl->set_var("TPL_VAR_SKU",$this->libFunc->m_displayContent($this->request['sku']));
		$this->ObTpl->set_var("TPL_VAR_NAME",$this->libFunc->m_displayContent($this->request['custname']));
		$this->ObTpl->set_var("TPL_VAR_EMAIL",$this->libFunc->m_displayContent($this->request['email']));
		$this->ObTpl->set_var("TPL_VAR_ADDRESS1",$this->libFunc->m_displayContent($this->request['address1']));
		$this->ObTpl->set_var("TPL_VAR_ADDRESS2",$this->libFunc->m_displayContent($this->request['address2']));
		$this->ObTpl->set_var("TPL_VAR_PHONE",$this->libFunc->m_displayContent($this->request['Phone']));
		$this->ObTpl->set_var("TPL_VAR_COUNTRY",$this->libFunc->m_displayContent($this->request['sCountry']));
		$this->ObTpl->set_var("TPL_VAR_COMMENTS",nl2br($this->libFunc->m_displayContent($this->request['comments'])));	

		$message =$this->ObTpl->parse("return","TPL_VAR_ENQUIRYEMAIL");
		
		$obMail = new htmlMimeMail();
		$obMail->setReturnPath(ADMIN_EMAIL);
		$obMail->setFrom($this->libFunc->m_displayContent($this->request['custname'])."<".$this->request['email'].">");
		$obMail->setSubject(SITE_NAME." Product Enquiry");
		$obMail->setCrlf("\n"); //to handle mails in Outlook Express
		$htmlcontent=$message;
		$txtcontent=preg_replace("/<([^>]+)>/","",preg_replace("/<br(\/{0,1})>/","\r\n",$message));
		$obMail->setHtml($htmlcontent,$txtcontent);
		$obMail->buildMessage();
	
		$result = $obMail->send(array(ADMIN_EMAIL));

		$retUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=enquiry.status");
		$this->libFunc->m_mosRedirect($retUrl);	
		exit;
	}#END SENDMAIL FUNCTION
	
}#END CLASS
?>