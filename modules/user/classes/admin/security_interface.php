<?php
/*
=======================================================================================
Copyright: TradingEye
Version: 7.1.1
=======================================================================================
*/
defined('_TEEXEC') or die;
class c_securityInterface
{
#CONSTRUCTOR
	function  c_securityInterface()
	{
		$this->err=0;
		$this->errMsg="";
		$this->libFunc=new c_libFunctions();
	}

	#FUNCTION TO DISPLAY PACKAGE
	function m_dspAdminUsers()
	{
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_SECURITY_FILE",$this->adminTemplate);

		#SETTING ALL TEMPLATE BLOCKS
		$this->ObTpl->set_block("TPL_SECURITY_FILE","TPL_USER_BLK", "user_blk");
		$this->ObTpl->set_block("TPL_USER_BLK","TPL_SUPERADMIN_BLK", "superuser_blk");
		$this->ObTpl->set_block("TPL_USER_BLK","TPL_LINK_BLK", "link_blk");
		
		#SETTING TEMPLATE VARIABLE
		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	
		$this->ObTpl->set_var("TPL_USERURL",SITE_URL."user/");
		
	
		#TO DISPLAY ADMIN USER
		$this->obDb->query = "SELECT  iAdminid_PK,vUsername,tmBuildDate,tmEditDate,iSuperAdmin   FROM ".ADMINUSERS;
		$queryResult = $this->obDb->fetchQuery();
		$recordCount=$this->obDb->record_count;
		$this->ObTpl->set_var("TPL_VAR_RECORDCOUNT",$recordCount);
		if($recordCount>0)
		{
			#PARSING TPL_USER_BLK
			for($j=0;$j<$recordCount;$j++)
			{			
				$this->ObTpl->set_var("TPL_VAR_NAME",$this->libFunc->m_displayContent($queryResult[$j]->vUsername));
				$this->ObTpl->set_var("TPL_VAR_BUILDDATE",$this->libFunc->dateFormat1($queryResult[$j]->tmBuildDate));
				$this->ObTpl->set_var("TPL_VAR_EDITDATE",$this->libFunc->dateFormat1($queryResult[$j]->tmEditDate));
				$this->ObTpl->set_var("TPL_VAR_ADMINID",$queryResult[$j]->iAdminid_PK);
				if($queryResult[$j]->iSuperAdmin==1)
				{
					$this->ObTpl->parse("superuser_blk","TPL_SUPERADMIN_BLK");
							$this->ObTpl->set_var("link_blk","");
				}
				else
				{
					$this->ObTpl->parse("link_blk","TPL_LINK_BLK");
					$this->ObTpl->set_var("superuser_blk","");
				}
				$this->ObTpl->parse("user_blk","TPL_USER_BLK",true);
			}
		}
		else
		{
				$this->ObTpl->set_var("user_blk","");
		}
		
		return($this->ObTpl->parse("return","TPL_SECURITY_FILE"));
	}


	#FUNCTION TO BUILD PACKAGE
	function m_createAdminForm()
	{
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_SECURITY_FILE",$this->adminTemplate);

		#SETTING ALL TEMPLATE BLOCKS
		$this->ObTpl->set_block("TPL_SECURITY_FILE","TPL_MSG_BLK","msg_blk");

		$this->ObTpl->set_block("TPL_SECURITY_FILE","COLUMN_BLK","col_blk");
		$this->ObTpl->set_block("COLUMN_BLK","ROW_BLK","row_blk");
		$this->ObTpl->set_var("TPL_VAR_MSG","");
		#SETTING TEMPLATE VARIABLE
		$this->ObTpl->set_var("msg_blk","");
		$this->ObTpl->set_var("col_blk","");
		$this->ObTpl->set_var("row_blk","");

		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	
		$this->ObTpl->set_var("TPL_USERURL",SITE_URL."user/");
		if(isset($this->request['msg']) && $this->request['msg']==1)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG","Record has been updated successfully");
			$this->ObTpl->parse("msg_blk","TPL_MSG_BLK");
		}
		if($this->err==1)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",$this->errMsg);
			$this->ObTpl->parse("msg_blk","TPL_MSG_BLK");
		}

		$userRs[0]->vUsername="";
		$userRs[0]->vPassword="";
		$userRs[0]->vEmail="";
	
		if(isset($_POST))
		{
			if(isset($this->request['username']))
				$userRs[0]->vUsername=$this->request['username'];
			if(isset($this->request['password']))
				$userRs[0]->vPassword=$this->request['password'];
			if(isset($this->request['email']))
				$userRs[0]->vEmail=$this->request['email'];
		}
	
	
		#START DISPLAY MODULES
		if(isset($this->request['adminid']) && !empty($this->request['adminid']) && is_numeric($this->request['adminid']))
		{
			$this->obDb->query = "SELECT  vUsername,vPassword,vEmail  FROM ".ADMINUSERS." WHERE iAdminid_PK='".$this->request['adminid']."'";
			$userRs = $this->obDb->fetchQuery();
		

			$this->obDb->query = "SELECT  vSecurity  FROM ".ADMINSECURITY." WHERE iUserid_FK='".$this->request['adminid']."'";
			$adminSecurity = $this->obDb->fetchQuery();
			$this->ObTpl->set_var("TPL_VAR_MODE","edit");
			$this->ObTpl->set_var("TPL_VAR_ADMINID",$this->request['adminid']);
		}
		else
		{
			$this->ObTpl->set_var("TPL_VAR_MODE","add");
			$this->ObTpl->set_var("TPL_VAR_USERNAME","");
			$this->ObTpl->set_var("TPL_VAR_PASSWORD","");
			$this->ObTpl->set_var("TPL_VAR_EMAIL","");
			$this->ObTpl->set_var("TPL_VAR_ADMINID","");
			$adminSecurity[0]->vSecurity="";
		}

		$this->ObTpl->set_var("TPL_VAR_USERNAME",$this->libFunc->m_displayContent($userRs[0]->vUsername));
		$this->ObTpl->set_var("TPL_VAR_PASSWORD",$this->libFunc->m_displayContent($userRs[0]->vPassword));
		$this->ObTpl->set_var("TPL_VAR_EMAIL",$this->libFunc->m_displayContent($userRs[0]->vEmail));
		 $moduleArray=explode(",",$adminSecurity[0]->vSecurity);

		$this->obDb->query = "SELECT sName,vDisplayName,mId  FROM ".MODULES." WHERE display=1 ORDER BY mId";
		$moduleResult = $this->obDb->fetchQuery();
		$recordCount=$this->obDb->record_count;
		#PARSING DEPARTMENT BLOCK
		if($recordCount>0)
		{
			for($i=0;$i<$recordCount;$i++)
			{
				$this->ObTpl->set_var("row_blk","");
				for	($j=0;$j<2;$j++)
				{
					if(isset($moduleResult[$i]->mId))
					{
						if(in_array($moduleResult[$i]->mId,$moduleArray))
						{
							$moduleResult[$i]->mId."IN a<br>";
							$this->ObTpl->set_var("SELECTED","checked");
						}
						else
						{
							 $moduleResult[$i]->mId."NOTIN a<br>";
							$this->ObTpl->set_var("SELECTED","");
						}
						$this->ObTpl->set_var("TPL_VAR_MODULENAME",$moduleResult[$i]->vDisplayName);
						$this->ObTpl->set_var("TPL_VAR_MID",$moduleResult[$i]->mId);
						
						$this->ObTpl->parse("row_blk","ROW_BLK",true);
						$i++;
					}
				}
				$i--;
				$this->ObTpl->parse("col_blk","COLUMN_BLK",true);
			}
		}
		else
		{
			$this->ObTpl->set_var("col_blk","");
		}
		#END DISPLAY DEPARETMENT BLOCK
		return($this->ObTpl->parse("return","TPL_SECURITY_FILE"));
	}
	
	#FUNCTION TO VERIFY DATABASE UPDATION
	function m_verifyEditAdmin()
	{
		$this->errMsg="";
		if(empty($this->request['username']))
		{
			$this->err=1;
			$this->errMsg=MSG_USER_EMPTY."<br>";
		}
		if(empty($this->request['email']))
		{
			$this->err=1;
			$this->errMsg.=MSG_USER_EMPTY."<br>";
		}
		#VALIDATING EXISTING OPTION TITLE
		$this->obDb->query = "SELECT iAdminid_PK  FROM ".ADMINUSERS." where vUsername  = '".$this->request['username']."'";
		$row_code = $this->obDb->fetchQuery();
		if($this->obDb->record_count != 0)
		{
			if($row_code[0]->iAdminid_PK !=$this->request['adminid'])
			{
				$this->err=1;
				$this->errMsg.=MSG_USER_EXIST."<br>";
			}
		}
		if($this->err==1)
		{
			return 2;
		}
		else
		{
			return 1;
		}
	}

	#FUNCTION TO VERIFY DATABASE UPDATION
	function m_verifyInsertAdmin()
	{
		$this->errMsg="";
		if(empty($this->request['username']))
		{
			$this->err=1;
			$this->errMsg=MSG_USER_EMPTY."<br>";
		}
		if(empty($this->request['email']))
		{
			$this->err=1;
			$this->errMsg.=MSG_USER_EMPTY."<br>";
		}

		$this->obDb->query = "SELECT iAdminid_PK  FROM ".ADMINUSERS." where vUsername  = '".$this->request['username']."'";
		$row_code = $this->obDb->fetchQuery();
		if($this->obDb->record_count != 0)
		{
			$this->err=1;
			$this->errMsg.=MSG_USER_EXIST."<br>";
		}
		if($this->err==1)
		{
			return 2;
		}
		else
		{
			return 1;
		}
	}
}
?>