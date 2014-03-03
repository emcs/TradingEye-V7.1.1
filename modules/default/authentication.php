<?php
/*
=======================================================================================
Copyright: TradingEye
Version: 7.1.1
=======================================================================================
*/
defined('_TEEXEC') or die;
class c_authentication
{
	#CONSTRUCTOR
	function c_authentication($obDatabase,&$obTemplate,$attributes)
	{
		$this->libFunc			=new c_libFunctions();
		$this->obDb			=$obDatabase;
		$this->obTpl			=&$obTemplate;
		$this->request			=$attributes;
		$this->templatePath =ADMINTHEMEPATH."user/templates/admin/";

		if(isset($this->request['action']) && $this->request['action']=="loginCheck")
		{
			if(isset($this->request['mode']) && $this->request['mode']=="forget")
			{
				$this->m_forgetPassword();
			}
			else
			{
				$this->m_loginCheck();
			}
		}
		else
		{
			$this->m_showLoginFrm();
		}
	}

	#FUNCTION TO DISPLAY PRODUCT REPORT
	function m_showLoginFrm()
	{
		$this->ObTpl=new template();
		$this->Template = ADMINTHEMEPATH."default/templates/admin/admin_login.tpl.htm";
		$this->obTpl->set_file("loginPage",$this->Template);
		$this->obTpl->set_block("loginPage","TPL_LOGIN_BLK","login_blk");
		$this->obTpl->set_block("loginPage","TPL_FORGET_BLK","forget_blk");
		$this->obTpl->set_block("loginPage","TPL_FORGETLINK_BLK","forgetlink_blk");
		$this->obTpl->set_var("forgetlink_blk","");
		$this->obTpl->set_var("login_blk","");
		$this->obTpl->set_var("forget_blk","");
		$this->obTpl->set_var("TPL_VAR_YEAR",date("Y"));
	   	$this->obTpl->set_var("TPL_VAR_IPADDRESS",$_SERVER['REMOTE_ADDR']);
		
		$this->obTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		$this->obTpl->set_var("TPL_VAR_SITENAME",SITE_NAME);
		$this->obTpl->set_var('TPL_VAR_REAL_PATH',$this->libFunc->real_path());
		$this->obTpl->set_var("TPL_VAR_SITETITLE",META_TITLE);	
		$this->obTpl->set_var("GRAPHICSMAINPATH",SITE_URL."/graphics");
		
		if(isset($this->request['msg']) && $this->request['msg']==2)
		{
			$this->obTpl->set_var("TPL_VAR_MSG",MSG_INVALID_LOGIN);
		}
		elseif(isset($this->request['msg']) && $this->request['msg']==3)
		{
			$this->obTpl->set_var("TPL_VAR_MSG",MSG_LOGINDETAILS_SENT);
		}
		elseif(isset($this->request['msg']) && $this->request['msg']==4)
		{
			$this->obTpl->set_var("TPL_VAR_MSG",MSG_INVALID_DETAILS);
		}
		elseif(isset($this->request['msg']) && $this->request['msg']==7)
		{
			$this->obTpl->set_var("TPL_VAR_MSG","You have been temporarily blocked. Please try again in 15 minutes.");
		}
		elseif(isset($this->request['msg']) && $this->request['msg']==8)
		{
			$this->obTpl->set_var("TPL_VAR_MSG","You have been temporarily blocked. Please try again in 1 hour. You can unblock your account by resetting your password.");
		}
		elseif(isset($this->request['msg']) && $this->request['msg']==9)
		{
			$this->obTpl->set_var("TPL_VAR_MSG","You have been temporarily blocked. Please try again in 24 hours. You can unblock your account by resetting your password.");
		}
		else
		{
			$this->obTpl->set_var("TPL_VAR_MSG","");
		}
		if(isset($this->request['action']) && $this->request['action']=="forget")
		{
			$this->obTpl->set_var("TPL_VAR_MODE","forget");
			$this->obTpl->parse("forget_blk","TPL_FORGET_BLK");
		}
		else
		{
			$this->obTpl->set_var("TPL_VAR_MODE","login");
			$this->obTpl->parse("login_blk","TPL_LOGIN_BLK");
			$this->obTpl->parse("forgetlink_blk","TPL_FORGETLINK_BLK");
		}
		
	
		$this->obTpl->pparse("return","loginPage");
		exit;
	}

	function m_loginCheck()
	{
		$this->libFunc->obDb = $this->obDb;
		$myreturn = $this->libFunc->m_checkAttempts(trim($this->request['username']));
		//print_r($myreturn);
		if($myreturn[0] == 0)
		{
			$this->obDb->query= "select iAdminid_PK,vUsername FROM ".ADMINUSERS." WHERE vUsername  = '".trim($this->request['username'])."' AND vPassword=PASSWORD('".trim($this->request['password'])."')";
			$qryResult = $this->obDb->fetchQuery();
			$rCount=$this->obDb->record_count;
			if($rCount>0) 
			{
				$_SESSION['uid']		= trim($qryResult[0]->iAdminid_PK);
				$_SESSION['KCFINDER']['disabled'] = false;
				$_SESSION['adminFlag'] = "1";
				$_SESSION['uname'] = trim($qryResult[0]->vUsername);
				$_SESSION['dashSelec'] = "class='selected'";
				$this->m_generateToken();
				$this->libFunc->m_mosRedirect(SITE_URL."adminindex.php");
			}
			else
			{
				$this->libFunc->m_addLoginAttempt($myreturn[0],$myreturn[1],$myreturn[2],trim($this->request['username']));
				$this->libFunc->m_mosRedirect(SITE_URL."adminindex.php?msg=2");
			}
		}
		else
		{
			$this->libFunc->m_addLoginAttempt($myreturn[0],$myreturn[1],$myreturn[2],trim($this->request['username']));
			switch($myreturn[0])
			{
				case 1:
					$this->libFunc->m_mosRedirect(SITE_URL."adminindex.php?msg=7");
				break;
				case 2:
					$this->libFunc->m_mosRedirect(SITE_URL."adminindex.php?msg=8");
				break;
				case 3:
					$this->libFunc->m_mosRedirect(SITE_URL."adminindex.php?msg=9");
				break;
			}
		}
	}#END LOGIN CHECK

	function m_generateToken()
	{
		if (function_exists("hash_algos") and in_array("sha512",hash_algos()))
		{
			$token=hash("sha512",mt_rand(0,mt_getrandmax()));
		}
		else
		{
			$token=' ';
			for ($i=0;$i<128;++$i)
			{
				$r=mt_rand(0,35);
				if ($r<26)
				{
					$c=chr(ord('a')+$r);
				}
				else
				{ 
					$c=chr(ord('0')+$r-26);
				} 
				$token.=$c;
			}
		}
		$_SESSION['AUTHTOKEN2'] =  $token;
	}
	
	function m_forgetPassword()
	{
		$this->obDb->query= "select iAdminid_PK,vUsername,vPassword,vEmail FROM ".ADMINUSERS." WHERE vUsername  = '".trim($this->request['username'])."' AND vEmail='".trim($this->request['email'])."'";
		$qryResult = $this->obDb->fetchQuery();
		$rCount=$this->obDb->record_count;
		$uniqID=uniqid (3);
		if($rCount>0) 
		{
			$this->libFunc->obDb = $this->obDb;
			$this->libFunc->m_removeBans($qryResult[0]->vUsername);
			$message ="Hi ".$qryResult[0]->vUsername;
			$message .="<br><br>Here are your login details:";
			$message .="<br><br>Username:&nbsp;".$qryResult[0]->vUsername;
			$message .="<br>Password:&nbsp;".$uniqID;
			$obMail = new htmlMimeMail();
			$obMail->setReturnPath(ADMIN_EMAIL);
			$obMail->setFrom(SITE_NAME."<".ADMIN_EMAIL.">");
			$obMail->setSubject("Login details from ".SITE_NAME);
			$obMail->setCrlf("\n"); //to handle mails in Outlook Express
			$htmlcontent=$message;
			$txtcontent=preg_replace("/<([^>]+)>/","",preg_replace("/<br(\/{0,1})>/","\r\n",$message));
			$obMail->setHtml($htmlcontent,$txtcontent);
			$obMail->buildMessage();
			$result = $obMail->send(array($qryResult[0]->vEmail));


			$this->obDb->query= "UPDATE ".ADMINUSERS." SET vPassword=password('".$uniqID."') WHERE iAdminid_PK=".$qryResult[0]->iAdminid_PK;
			$qryResult = $this->obDb->updateQuery();
			$this->libFunc->m_mosRedirect(SITE_URL."adminindex.php?msg=3");
		}
		else
		{	
			$this->libFunc->m_mosRedirect(SITE_URL."adminindex.php?action=forget&msg=4");
		}
	}#ef
}#ec
?>