<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.0.5
=======================================================================================
*/
class c_wishlistDb
{
	#CONSTRUCTOR
	function c_wishlistDb()
	{
		$this->libFunc=new c_libFunctions();
	}
	# Insert new Wishlist
	function m_insertWishlist()
	{
		$timestamp=time();
		#INSERTING TO WISHLIST
		
		$this->obDb->query="SELECT vQuantity FROM  ".WISHLIST." WHERE (iCustomerid_FK='".$_SESSION['userid']."' AND iProductid_FK='".$this->request['mode']."')";
		$rs=$this->obDb->fetchQuery();
		$rsCount=$this->obDb->record_count;
		if($rsCount==0)
		{
			$this->obDb->query="INSERT INTO ".WISHLIST." (iCustomerid_FK,iProductid_FK,vQuantity) values('".$_SESSION['userid']."','".$this->request['mode']."',1)";
		}
		else
		{
			$this->obDb->query="UPDATE ".WISHLIST." SET vQuantity=vQuantity+1 WHERE (iCustomerid_FK='".$_SESSION['userid']."' AND iProductid_FK='".$this->request['mode']."')";
		}

		$this->obDb->updateQuery();

		$retUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=wishlist.display");
		$this->libFunc->m_mosRedirect($retUrl);	
	}#END FUNCTION

	function m_addWishEmail()
	{
		#INSERTING TO WISHLIST
		$this->obDb->query="SELECT count(*) as cnt FROM  ".WISHEMAILS." WHERE (iCustomerid_FK='".$_SESSION['userid']."' AND vEmail ='".$this->request['email']."')";
		$rs=$this->obDb->fetchQuery();
		$rsCount=$rs[0]->cnt;
		if($rsCount==0)
		{
			$this->obDb->query="INSERT INTO ".WISHEMAILS."	(iCustomerid_FK,vEmail) values('".$_SESSION['userid']."','".$this->request['email']."')";
			$this->obDb->updateQuery();
		}
		
		$retUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=wishlist.display");
		$this->libFunc->m_mosRedirect($retUrl);	
	}#END FUNCTION


	# MODIFY WISHLIST
	function m_modifyWishlist()
	{
		$timestamp=time();
		#UPDATEING TO WISHLIST
		$cnt=count($this->request['wishlistid']);
		for($i=0;$i<$cnt;$i++)
		{
			if($this->request['quantity'][$i]<1)
			{
				$this->request['quantity'][$i]=1;
			}
			$this->obDb->query="UPDATE ".WISHLIST." SET vQuantity='".$this->request['quantity'][$i]."' WHERE (iShopWishid_PK='".$this->request['wishlistid'][$i]."' AND iCustomerid_FK='".$_SESSION['userid']."')";
			$this->obDb->updateQuery();
		}
		if(isset($this->request['remove']))
		{
			foreach($this->request['remove'] as $r=>$rid)
			{
				 $this->obDb->query="DELETE FROM ".WISHLIST." WHERE (iShopWishid_PK='".$r."') AND iCustomerid_FK='".$_SESSION['userid']."'";
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
			$this->obDb->query="DELETE FROM ".WISHEMAILS."	WHERE  (iWishid_PK='".$this->request['emailid']."' AND iCustomerid_FK='".$_SESSION['userid']."')";
			$this->obDb->updateQuery();
		}
		$retUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=wishlist.display");
		$this->libFunc->m_mosRedirect($retUrl);	
	}#END FUNCTION

}#CLASS ENDS
?>