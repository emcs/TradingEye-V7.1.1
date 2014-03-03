<?php
/*
=======================================================================================
Copyright: TradingEye
Version: 7.1.1
=======================================================================================
*/
defined('_TEEXEC') or die;
class c_authorization
{
	#CONSTRUCTOR
	function c_authorization($obDatabase,&$obMainTemplate,$attributes)
	{
		$this->obDb		=$obDatabase;
		$this->request		=$attributes;
		$this->obTpl		=$obMainTemplate;
		$this->libFunc		=new c_libFunctions();
		$this->m_checkAutherization();
	}

	function m_checkAutherization()
	{
		//echo $this->request['action'];
		if(!isset($this->request['action']))
		{
			$this->request['action']="";
		}
		
		$this->obDb->query= "SELECT vSecurity FROM ".ADMINSECURITY." WHERE iUserid_FK = '".$_SESSION['uid']."'";
		$rsSecurity = $this->obDb->fetchQuery();
		$moduleString=$rsSecurity[0]->vSecurity;
		
		$moduleArray=explode(",",$moduleString);
		$action=explode(".",$this->request['action']);
		if($action[0]=="home")
		{
			if(isset($action[1]) && ($action[1]=='deleteProduct' || $action[1]=='deleteContent'))
			{
				$action[0]='ec_show';
			}
			else
			{
				$action[0]='report';
			}
		}

		$this->obDb->query= "SELECT mId,isAuth FROM ".MODULES." WHERE sName= '".$action[0]."'";
		$rsSecurity = $this->obDb->fetchQuery();
		if($this->obDb->record_count){
			$moduleId=$rsSecurity[0]->mId;
			if(!in_array($moduleId,$moduleArray) && $rsSecurity[0]->isAuth==1)
			{
				$this->libFunc->m_mosRedirect(SITE_URL."user/adminindex.php?action=unauthorized");
			}
		}
	
	}#END AUTHORIZATION
}
?>