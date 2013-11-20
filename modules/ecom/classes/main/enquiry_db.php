<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.1.0
=======================================================================================
*/
defined('_TEEXEC') or die;
class c_enquiryDb
{
	#CONSTRUCTOR
	function c_enquiryDb()
	{
		$this->libFunc=new c_libFunctions();
	}
	
	# INSERT NEW ENQUIRY
	function m_insertEnquiry()
	{
		$timestamp=time();
		#INSERTING TO WISHLIST
		
		$this->obDb->query="SELECT vQuantity FROM  ".WISHLIST." WHERE (iCustomerid_FK='".$_SESSION['uid']."' AND iProductid_FK='".$this->request['mode']."')";
		$rs=$this->obDb->fetchQuery();
		$rsCount=$this->obDb->record_count;
		if($rsCount==0)
		{
			$this->obDb->query="INSERT INTO ".WISHLIST."				(iCustomerid_FK,iProductid_FK,vQuantity) values('".$_SESSION['uid']."','".$this->request['mode']."',1)";
		}
		else
		{
			$this->obDb->query="UPDATE ".WISHLIST." SET vQuantity=vQuantity+1 WHERE (iCustomerid_FK='".$_SESSION['uid']."' AND iProductid_FK='".$this->request['mode']."')";
		}

		$this->obDb->updateQuery();

		$retUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=wishlist.display");
		$this->libFunc->m_mosRedirect($retUrl);	
	}#END FUNCTION

	function m_addWishEmail()
	{
		$libFunc=new c_libFunctions();
		#INSERTING TO WISHLIST
		
		$this->obDb->query="SELECT count(*) as cnt FROM  ".WISHEMAILS." WHERE (iCustomerid_FK='".$_SESSION['uid']."' AND vEmail ='".$this->request['email']."')";
		$rs=$this->obDb->fetchQuery();
		$rsCount=$rs[0]->cnt;
		if($rsCount==0)
		{
			$this->obDb->query="INSERT INTO ".WISHEMAILS."	(iCustomerid_FK,vEmail) values('".$_SESSION['uid']."','".$this->request['email']."')";
			$this->obDb->updateQuery();
		}
		
		$retUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=wishlist.display");
		$this->libFunc->m_mosRedirect($retUrl);	
	}#END FUNCTION


	# MODIFY WISHLIST
	function m_modifyWishlist()
	{
		$libFunc=new c_libFunctions();
		$timestamp=time();
		#UPDATEING TO WISHLIST
		$cnt=count($this->request['wishlistid']);
		for($i=0;$i<$cnt;$i++)
		{
			$this->obDb->query="UPDATE ".WISHLIST." SET vQuantity='".$this->request['quantity'][$i]."' WHERE (iShopWishid_PK='".$this->request['wishlistid'][$i]."')";
			$this->obDb->updateQuery();
		}
		if(isset($this->request['remove']))
		{
			foreach($this->request['remove'] as $r=>$rid)
			{
				 $this->obDb->query="DELETE FROM ".WISHLIST." WHERE (iShopWishid_PK='".$r."')";
				$this->obDb->updateQuery();
			}
		}

		$retUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=wishlist.display");
		$this->libFunc->m_mosRedirect($retUrl);	
	}#END FUNCTION


	function m_removeWishEmail()
	{
		$libFunc=new c_libFunctions();
		if(isset($this->request['emailid']))
		{
			$this->obDb->query="DELETE FROM ".WISHEMAILS."	WHERE  (iWishid_PK='".$this->request['emailid']."')";
			$this->obDb->updateQuery();
		}
		$retUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=wishlist.display");
		$this->libFunc->m_mosRedirect($retUrl);	
	}#END FUNCTION

}#CLASS ENDS
?>