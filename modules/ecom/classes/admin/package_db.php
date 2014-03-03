<?php
/*
=======================================================================================
Copyright: TradingEye
Version: 7.1.1
=======================================================================================
*/
defined('_TEEXEC') or die;
	class c_packageDb
	{
		#CONSTRUCTOR
		function c_packageDb()
		{
			$this->libFunc=new c_libFunctions();
		}
		#FUNCTION UPDATE,ADD NEW PACKAGE
		function m_updatePackage()
		{
			if(isset($this->request['pid']))
			{
				if(empty($this->request['kitid']))
				{
					$this->obDb->query = "SELECT count(*) as totalCnt from ".PRODUCTKITS." WHERE  iKitId='".$this->request['pid']."'";
					$rsCount = $this->obDb->fetchQuery();
					$recordCount=$rsCount[0]->totalCnt;
					if($recordCount==0)
					{
						/*$this->obDb->query = "INSERT INTO  ".PRODUCTKITS." SET  iKitId ='".$this->request['pid']."', iProdId_FK='".$this->request['pid']."'" ;
						$this->obDb->updateQuery();*/
						$this->obDb->query = "UPDATE ".PRODUCTS." SET  iKit=1 WHERE iProdId_PK='".$this->request['pid']."'" ;
						$this->obDb->updateQuery();
					}
					$this->request['kitid']=$this->request['pid'];
				}	
				else
				{
					$this->obDb->query = "SELECT max(iSort) as maxsort from ".PRODUCTKITS." WHERE  iKitId='".$this->request['kitid']."'";
					$rsSort = $this->obDb->fetchQuery();
					$sort=$rsSort[0]->maxsort+1;
					$this->obDb->query = "SELECT count(*) as totalCnt from ".PRODUCTKITS." WHERE  iKitId='".$this->request['kitid']."' AND iProdId_FK='".$this->request['pid']."'";
					$rsCount = $this->obDb->fetchQuery();
					$recordCount=$rsCount[0]->totalCnt;
					if($recordCount==0)
					{
						$this->obDb->query = "INSERT INTO  ".PRODUCTKITS." SET  iKitId ='".$this->request['kitid']."',iSort='$sort', iProdId_FK='".$this->request['pid']."'" ;
						$this->obDb->updateQuery();
					}
				}
			}
			$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_package.build&kitid=".$this->request['kitid']."&postOwner=".$this->request['postowner']);	
			exit;	
		}
	

		# function to delete an department,product,article	
		function m_deletePackItem()
		{
			if(isset($this->request['kid']) && !empty($this->request['kid']))
			{
				 $this->obDb->query = "DELETE FROM ".PRODUCTKITS." where iKitId_PK =".$this->request['kid'];
				$this->obDb->updateQuery();
			}
			$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_package.build&kitid=".$this->request['kitid']."&postOwner=".$this->request['postowner']);	
			exit;		
		}

		#FUNCTION TO UPDATE HOME FOR EACH PACKAGE
		function m_updateHome()
		{
			if(isset($this->request['itemid']))
			{
				$cnt=count($this->request['itemid']);
				for($i=0;$i<$cnt;$i++)
				{
					$this->obDb->query="UPDATE ".PRODUCTKITS." set
					 `iQty`='".$this->request['qty'][$i]."',`iSort`='".$this->request['sort'][$i]."' where iKitid_PK ='".$this->request['itemid'][$i]."'";
					$this->obDb->updateQuery();
				}
			}
			
			$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_package.build&kitid=".$this->request['kitid']."&postOwner=".$this->request['postowner']);	
			exit;
		}

		#FUNCTION TO UPDATE HOME FOR PACKAGES
		function m_updatePackHome()
		{

			$this->obDb->query="UPDATE ".PRODUCTS." set `iKit`='2' where iKit ='1'";
			$this->obDb->updateQuery();
			if(isset($this->request['status']))
			{
				$status=$this->request['status'];
				foreach($status as $stid=>$stval)
				{
					$this->obDb->query="UPDATE ".PRODUCTS." set
					 `iKit`='".$stval."' where iProdid_PK ='".$stid."'";
					$this->obDb->updateQuery();
				}
			}
			
			$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_package.home");	
			exit;
		}
	
		#FUNCTION TO DISAMBLE PACKAGE
		function m_disamblePack()
		{
			if(isset($this->request['kitid']) && !empty($this->request['kitid']))
			{
				 $this->obDb->query = "DELETE FROM ".PRODUCTKITS." where iKitId =".$this->request['kitid'];
				$this->obDb->updateQuery();
				if(!isset($this->request['template']))
				{
					$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_package.disamble&msg=1&kitid=".$this->request['kitid']);	
					exit;	
				}
				else
				{
					$this->obDb->query = "UPDATE ".PRODUCTS." SET iKit='0', vTemplate='".$this->request['template']."'  where iProdId_PK ='".$this->request['kitid']."'";
					$this->obDb->updateQuery();
				}
			}
			$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_package.home");	
			exit;		
		}
		

}#CLASS ENDS
?>