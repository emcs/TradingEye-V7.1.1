<?php
/*
=======================================================================================
Copyright: TradingEye
Version: 7.1.1
=======================================================================================
*/
defined('_TEEXEC') or die;
class c_securityDb
{
	#CONSTRUCTOR
	function c_securityDb()
	{
		$this->libFunc=new c_libFunctions();	
	}
	#FUNCTION UPDATE,ADD NEW PACKAGE
	function m_insertAdmin()
	{
		$timeStamp=time();
		$this->obDb->query = "INSERT INTO  ".ADMINUSERS." SET  
		vUsername='".$this->request['username']."', vPassword=PASSWORD('".$this->request['password']."'), vEmail='".$this->request['email']."', tmBuildDate='".$timeStamp."'" ;

		$this->obDb->updateQuery();
		$adminid=$this->obDb->last_insert_id;
		
		if(!isset($this->request['moduleid']))
		{
			$this->request['moduleid']="";
			$modid="";
		}
		if(!empty($this->request['moduleid']))
		{
			$modids="";
			$moduleid=$this->request['moduleid'];
			foreach($moduleid as $mid)
			{
				$modids.=$mid.",";
				$modid=substr($modids,0,-1);
			}
		}
		$this->obDb->query = "INSERT INTO ".ADMINSECURITY." SET   
		iUserid_FK ='".$adminid."', vSecurity ='".$modid."'" ;
		$this->obDb->updateQuery();
		$this->libFunc->m_mosRedirect(SITE_URL."user/adminindex.php?action=security.createAdmin&adminid=".$adminid);	
	}#EF
		
	function m_updateAdmin()
	{
		$timeStamp=time();
		$this->obDb->query = "UPDATE ".ADMINUSERS." SET  
		vUsername='".$this->request['username']."',";
		if(!empty($this->request['password']))
		{
			$this->obDb->query.="vPassword=PASSWORD('".$this->request['password']."'), ";
		}

		$this->obDb->query.="
		tmEditDate='".$timeStamp."',vEmail='".$this->request['email']."' WHERE iAdminid_PK='".$this->request['adminid']."'"; ;

		$this->obDb->updateQuery();
		$adminid=$this->obDb->last_insert_id;
		
		if(!isset($this->request['moduleid']))
		{
			$this->request['moduleid']="";
			$modid="";
		}
		if(!empty($this->request['moduleid']))
		{
			$modids="";
			$moduleid=$this->request['moduleid'];
			foreach($moduleid as $mid)
			{
				$modids.=$mid.",";
				$modid=substr($modids,0,-1);
			}
		}
		$this->obDb->query = "UPDATE ".ADMINSECURITY." SET   
		 vSecurity ='".$modid."' WHERE iUserid_FK ='".$this->request['adminid']."'" ;
		$this->obDb->updateQuery();
		$this->libFunc->m_mosRedirect(SITE_URL."user/adminindex.php?action=security.createAdmin&msg=1&adminid=".$this->request['adminid']);	
	}#EF

	# FUNTION TO DELETE ADMIN USER
	function m_deleteAdmin()
	{
		if(isset($this->request['adminid']) && !empty($this->request['adminid']))
		{			
			$this->obDb->query = "SELECT iSuperAdmin FROM ".ADMINUSERS." WHERE  iAdminid_PK =".$this->request['adminid'];
				$rs=$this->obDb->fetchQuery();
			if($rs[0]->iSuperAdmin!=1)
			{
				$this->obDb->query = "DELETE FROM ".ADMINUSERS." WHERE  iAdminid_PK =".$this->request['adminid'];
				$this->obDb->updateQuery();
				$this->obDb->query = "DELETE FROM ".ADMINSECURITY." WHERE  iUserid_FK  =".$this->request['adminid'];
				$this->obDb->updateQuery();
			}
		}
			
		$this->libFunc->m_mosRedirect(SITE_URL."user/adminindex.php?action=security.home");	
		exit;		
	}#EF
}#CLASS ENDS
?>