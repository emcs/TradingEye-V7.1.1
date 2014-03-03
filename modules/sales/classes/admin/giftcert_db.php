<?php
/*
=======================================================================================
Copyright: TradingEye
Version: 7.1.1
=======================================================================================
*/
defined('_TEEXEC') or die;
class c_giftCertDb
{
	#CONSTRUCTOR
	function c_giftCertDb()
	{
		$this->libFunc=new c_libFunctions();
	}
	#FUNCTION UPDATE,ADD NEW GIFT CERTIFICATE
	function m_insertGiftCert()
	{
		$timeStamp=time();

		$this->obDb->query = "INSERT INTO  ".GIFTCERTIFICATES." SET  
		vGiftcode 		='".$this->libFunc->m_addToDB($this->request['code'])."', 
		fAmount 		='".$this->libFunc->checkFloatValue($this->request['amount'])."', 
		fRemaining 	='".$this->libFunc->checkFloatValue($this->request['amount'])."', 
		tmBuildDate='".$timeStamp."'" ;
		$this->obDb->updateQuery();
		$this->libFunc->m_mosRedirect(SITE_URL."sales/adminindex.php?action=promotions.giftcert.home&msg=1");	
	}
	
	function m_updateGiftCert()
	{
		$timeStamp=time();
		$this->obDb->query = "UPDATE ".GIFTCERTIFICATES." SET  
				vGiftcode 		='".$this->libFunc->m_addToDB($this->request['code'])."', 
				fAmount 		='".$this->libFunc->checkFloatValue($this->request['amount'])."', 
				fRemaining 	='".$this->libFunc->checkFloatValue($this->request['amount'])."', 
				tmEditDate		='".$timeStamp."'
		WHERE iGiftcertid_PK  ='".$this->request['id']."'"; ;
		$this->obDb->updateQuery();
		$this->libFunc->m_mosRedirect(SITE_URL."sales/adminindex.php?action=promotions.giftcert.dspForm&id=".$this->request['id']."&msg=1");	
	}

	# FUNTION TO DELETE GIFT CERTIFICATE
	function m_giftCertDelete()
	{
		if(isset($this->request['id']) && !empty($this->request['id']))
		{			
				$this->obDb->query = "DELETE FROM ".GIFTCERTIFICATES." WHERE  iGiftcertid_PK =".$this->request['id'];
				$this->obDb->updateQuery();
		}
		$this->libFunc->m_mosRedirect(SITE_URL."sales/adminindex.php?action=promotions.giftcert.home&msg=3");	
	}#EF
}#CLASS ENDS
?>