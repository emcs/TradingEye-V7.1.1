<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.0.5
=======================================================================================
*/
class c_cmsContentDb
{
	#CONSTRUCTOR
	function c_cmsContentDb()
	{
		$this->libFunc=new c_libFunctions();
	}
	
	#FUNCTION UPDATE,ADD NEW CONTACT
	function m_addContact()
	{
		$timeStamp=time();
		$this->request['sName']=$this->libFunc->ifSet($this->request,"sName","");
		$this->request['sAddress1']=$this->libFunc->ifSet($this->request,"sAddress1","");
		$this->request['sAddress2']=$this->libFunc->ifSet($this->request,"sAddress2","");
		$this->request['sWorkPhone']=$this->libFunc->ifSet($this->request,"sWorkPhone","");
		$this->request['sCountry']=$this->libFunc->ifSet($this->request,"sCountry","");
		$this->request['sComments']=$this->libFunc->ifSet($this->request,"sComments","");

		$this->obDb->query = "INSERT INTO  ".CONTACTUS." SET  
		vName 		='".$this->libFunc->m_addToDB($this->request['sName'])."', 
		vEmail 		='".$this->libFunc->m_addToDB($this->request['sEmail'])."', 
		vAddress1	='".$this->libFunc->m_addToDB($this->request['sAddress1'])."', 
		vAddress2	='".$this->libFunc->m_addToDB($this->request['sAddress2'])."', 
		vWorkPhone='".$this->libFunc->m_addToDB($this->request['sWorkPhone'])."', 
		vCountry 	='".$this->libFunc->m_addToDB($this->request['sCountry'])."', 
		vComments ='".$this->libFunc->m_addToDB($this->request['sComments'])."', 
		tmAddDate ='".$timeStamp."'" ;
		$this->obDb->updateQuery();
		$this->m_sendMail();
		$thanksUrl=$this->libFunc->m_safeUrl(SITE_URL."index.php?action=contactus.thanks");
		$this->libFunc->m_mosRedirect($thanksUrl);	
	}

	#FUNCTION SENDMAIL
	function m_sendMail()
	{
		$libFunc=new c_libFunctions();
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_VAR_CMS",MODULES_PATH."default/templates/main/contactmail.tpl.htm");
		$this->ObTpl->set_var("TPL_VAR_NAME",$this->libFunc->m_displayContent($this->request['sName']));
		$this->ObTpl->set_var("TPL_VAR_ADDRESS1",$this->libFunc->m_displayContent($this->request['sAddress1']));
		$this->ObTpl->set_var("TPL_VAR_ADDRESS2",$this->libFunc->m_displayContent($this->request['sAddress2']));
		$this->ObTpl->set_var("TPL_VAR_PHONE",$this->libFunc->m_displayContent($this->request['sWorkPhone']));
		$this->ObTpl->set_var("TPL_VAR_COUNTRY",$this->libFunc->m_displayContent($this->request['sCountry']));
		$this->ObTpl->set_var("TPL_VAR_COMMENTS",nl2br($this->libFunc->m_displayContent($this->request['sComments'])));
		$message ="========================================<br />";
		$message.="Contact request from ".SITE_NAME."<br />";
		$message.="========================================<br />";
	 	$message.=$this->ObTpl->parse("return","TPL_VAR_CMS");
		 $this->request['sName']."<".$this->libFunc->m_displayContent1($this->request['sEmail']).">";
		$obMail = new htmlMimeMail();
		$obMail->setReturnPath(ADMIN_EMAIL);
		$this->libFunc->m_displayContent($this->request['sName'])."<".$this->libFunc->m_displayContent1($this->request['sEmail']).">";

		$obMail->setFrom($this->libFunc->m_displayContent1("\"".$this->request['sName']."\"")."<".$this->libFunc->m_displayContent1($this->request['sEmail']).">");
		$obMail->setSubject("Contact request from ".SITE_NAME);
		$obMail->setCrlf("\n"); //to handle mails in Outlook Express
		$htmlcontent=$message;
		$txtcontent=preg_replace("/<([^>]+)>/","",preg_replace("/<br(\/{0,1})>/","\r\n",$message));
		$obMail->setHtml($htmlcontent,$txtcontent);
		$obMail->buildMessage();
		$result = $obMail->send(array(ENQUIRY_EMAIL));
	}#END MAIL FUNCTION

	#FUNCTION DOWNLOAD FILE
	function m_downloadFile()
	{
		if(!isset($this->request['mode']) || empty($this->request['mode']))
		{
			$errrorUrl=SITE_URL."index.php?action=error&mode=content";
			$this->libFunc->m_mosRedirect($this->libFunc->m_safeUrl($errrorUrl));
		}			

		if($this->libFunc->m_checkFileExist($this->file,"files"))
		{
			
			$this->obDb->query = "SELECT iNoOfDownloads FROM ".CUSTOMERS." WHERE   iCustmerid_PK='".$_SESSION['userid']."'"; 
			$rsCustomer=$this->obDb->fetchQuery();
			if($rsCustomer[0]->iNoOfDownloads>0)
			{		
				//$this->obDb->query = "UPDATE  ".CUSTOMERS." SET iNoOfDownloads=iNoOfDownloads-1 WHERE   iCustmerid_PK='".$_SESSION['userid']."'"; 
				//$this->obDb->updateQuery();
				$file=SITE_PATH."images/files/".$this->file;
				$this->libFunc->m_forceFileDownload($file);
			}
			else
			{
				$errrorUrl=SITE_URL."index.php?action=error&mode=downloadlimit";
				$this->libFunc->m_mosRedirect($this->libFunc->m_safeUrl($errrorUrl));
			}
		}
		else
		{
			$errrorUrl=SITE_URL."index.php?action=error&mode=fileexist";
			$this->libFunc->m_mosRedirect($this->libFunc->m_safeUrl($errrorUrl));
		}
	}#EF	
}#CLASS ENDS
?>