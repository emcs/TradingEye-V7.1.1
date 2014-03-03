<?php
/*
=======================================================================================
Copyright: TradingEye
Version: 7.1.1
=======================================================================================
*/
defined('_TEEXEC') or die;
class c_comparelistDb
{
	#CONSTRUCTOR
	function c_comparelistDb()
	{
		$this->libFunc=new c_libFunctions();
	}
	# Insert new Wishlist
	function m_insertComparelist()
	{
		$timestamp=time();
		#INSERTING TO WISHLIST
		
		$this->obDb->query="SELECT vQuantity FROM  ".COMPARE." WHERE (iCustomerid_FK='".$_SESSION['userid']."' AND iProductid_FK='".$this->request['mode']."')";
		$rs=$this->obDb->fetchQuery();
		$rsCount=$this->obDb->record_count;
		if($rsCount==0)
		{
			$this->obDb->query="INSERT INTO ".COMPARE." (iCustomerid_FK,iProductid_FK,vQuantity) values('".$_SESSION['userid']."','".$this->request['mode']."',1)";
		
		}
		else
		{
			$this->obDb->query="UPDATE ".COMPARE." SET vQuantity=vQuantity+1 WHERE (iCustomerid_FK='".$_SESSION['userid']."' AND iProductid_FK='".$this->request['mode']."')";
		}

		$this->obDb->updateQuery();

		$retUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=compare.display");
		$this->libFunc->m_mosRedirect($retUrl);	
	}#END FUNCTION



	# MODIFY WISHLIST
	function m_modifyComparelist()
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
			$this->obDb->query="UPDATE ".COMPARE." SET vQuantity='".$this->request['quantity'][$i]."' WHERE (iCompareid_PK='".$this->request['wishlistid'][$i]."')";
			$this->obDb->updateQuery();
		}
		if(isset($this->request['remove']))
		{
			foreach($this->request['remove'] as $r=>$rid)
			{
				 $this->obDb->query="DELETE FROM ".COMPARE." WHERE (iCompareid_PK='".$r."')";
				$this->obDb->updateQuery();
			}
		}

		$retUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=compare.display");
		$this->libFunc->m_mosRedirect($retUrl);	
	}#END FUNCTION




}#CLASS ENDS
?>